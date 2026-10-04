<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskType;
use App\Models\Client;
use App\Models\TaskComment;
use App\Models\TaskLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $user = Auth::user();
        abort_if(!$user->can('view tasks'), 403, 'Unauthorized access to tasks.');

        $canViewAll = $user->can('view all tasks');
        $query = $canViewAll ? Task::query() : Task::where('assigned_to', $user->id);

        $query->with(['client', 'assignedTo', 'createdBy', 'taskType']);

        // Filters
        if ($request->filled('task_type_id')) {
            $query->where('task_type_id', $request->task_type_id);
        } elseif ($request->filled('type')) {
            if (is_numeric($request->type)) {
                $query->where('task_type_id', $request->type);
            } else {
                $query->where(function($q) use ($request) {
                    $q->where('type', $request->type)
                      ->orWhereHas('taskType', fn($tq) => $tq->where('slug', $request->type));
                });
            }
        }

        if ($request->filled('priority'))    $query->where('priority', $request->priority);
        if ($request->filled('status'))      $query->where('status', $request->status);
        if ($request->filled('client_id'))   $query->where('client_id', $request->client_id);
        if ($request->filled('assigned_to')) $query->where('assigned_to', $request->assigned_to);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                  ->orWhere('description', 'like', '%'.$search.'%')
                  ->orWhereHas('client', function($cq) use ($search) {
                      $cq->where('name', 'like', '%'.$search.'%')
                         ->orWhere('company_name', 'like', '%'.$search.'%')
                         ->orWhere('system_url', 'like', '%'.$search.'%')
                         ->orWhere('phone', 'like', '%'.$search.'%');
                  })
                  ->orWhereHas('taskType', function($tq) use ($search) {
                      $tq->where('name', 'like', '%'.$search.'%');
                  });
            });
        }

        $tasks     = $query->latest()->paginate(15)->withQueryString();
        $clients   = Client::active()->orderBy('company_name')->orderBy('name')->get();
        $users     = User::where('is_active', true)->orderBy('name')->get();
        $taskTypes = TaskType::active()->ordered()->get();

        return view('tasks.index', compact('tasks', 'clients', 'users', 'taskTypes'));
    }

    public function create()
    {
        abort_if(!Auth::user()->can('create tasks'), 403, 'Unauthorized to create tasks.');

        $clients   = Client::where('is_active', true)->orderBy('company_name')->orderBy('name')->get();
        $users     = User::where('is_active', true)->with('roles')->orderBy('name')->get();
        $taskTypes = TaskType::active()->ordered()->get();

        return view('tasks.create', compact('clients', 'users', 'taskTypes'));
    }

    public function store(Request $request)
    {
        abort_if(!Auth::user()->can('create tasks'), 403, 'Unauthorized to create tasks.');

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'client_id'    => 'nullable|exists:clients,id',
            'task_type_id' => 'required|exists:task_types,id',
            'priority'     => 'required|in:high,medium,low',
            'status'       => 'required|in:pending,in_progress,on_hold,under_review,completed',
            'assigned_to'  => 'nullable|exists:users,id',
            'deadline'     => 'nullable|date',
        ]);

        $taskType = TaskType::find($validated['task_type_id']);
        $validated['type'] = $taskType?->slug ?? 'other';
        $validated['created_by'] = Auth::id();

        if ($validated['status'] === 'completed') {
            $validated['completed_at'] = now();
        }

        $task = Task::create($validated);

        // Log creation
        TaskLog::create([
            'task_id'     => $task->id,
            'user_id'     => Auth::id(),
            'action'      => 'created',
            'description' => 'Task created',
        ]);

        return redirect()->route('tasks.show', $task)->with('success', 'Task created successfully!');
    }

    public function show(Task $task)
    {
        $user = Auth::user();
        abort_if(!$user->can('view tasks'), 403, 'Unauthorized access to task details.');

        // If user cannot view all tasks and is not assigned or creator, deny
        if (!$user->can('view all tasks') && $task->assigned_to !== $user->id && $task->created_by !== $user->id) {
            abort(403, 'You are not assigned to this task.');
        }

        $task->load(['client', 'assignedTo.roles', 'createdBy', 'comments.user', 'attachments.user', 'logs.user', 'taskType']);
        $users = User::where('is_active', true)->with('roles')->orderBy('name')->get();
        return view('tasks.show', compact('task', 'users'));
    }

    public function edit(Task $task)
    {
        abort_if(!Auth::user()->can('edit tasks'), 403, 'Unauthorized to edit tasks.');

        $clients   = Client::where('is_active', true)->orderBy('company_name')->orderBy('name')->get();
        $users     = User::where('is_active', true)->with('roles')->orderBy('name')->get();
        $taskTypes = TaskType::active()->ordered()->get();

        return view('tasks.edit', compact('task', 'clients', 'users', 'taskTypes'));
    }

    public function update(Request $request, Task $task)
    {
        $user = Auth::user();
        abort_if(!$user->can('edit tasks'), 403, 'Unauthorized to edit tasks.');

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'client_id'    => 'nullable|exists:clients,id',
            'task_type_id' => 'required|exists:task_types,id',
            'priority'     => 'required|in:high,medium,low',
            'status'       => 'required|in:pending,in_progress,on_hold,under_review,completed',
            'assigned_to'  => 'nullable|exists:users,id',
            'deadline'     => 'nullable|date',
        ]);

        $taskType = TaskType::find($validated['task_type_id']);
        $validated['type'] = $taskType?->slug ?? 'other';

        $oldStatus = $task->status;
        $isSuperAdmin = $user->hasRole('super-admin') || $user->isDefaultSuperAdmin();

        // Enforce: only assigned user or Super Admin can change task status
        if ($oldStatus !== $validated['status'] && !$isSuperAdmin && (int)$task->assigned_to !== (int)$user->id) {
            return back()->withInput()->withErrors(['status' => 'You can only change the status of tasks assigned to you.']);
        }

        if ($validated['status'] === 'completed' && $oldStatus !== 'completed') {
            $validated['completed_at'] = now();
        } elseif ($validated['status'] !== 'completed') {
            $validated['completed_at'] = null;
        }

        $task->update($validated);

        // Log update
        if ($oldStatus !== $validated['status']) {
            TaskLog::create([
                'task_id'     => $task->id,
                'user_id'     => $user->id,
                'action'      => 'status_changed',
                'description' => "Status changed from {$oldStatus} to {$validated['status']}",
                'meta'        => ['old' => $oldStatus, 'new' => $validated['status']],
            ]);
        }

        return redirect()->route('tasks.show', $task)->with('success', 'Task updated successfully!');
    }

    public function updateAssignee(Request $request, Task $task)
    {
        abort_if(!Auth::user()->can('assign tasks'), 403, 'Unauthorized to assign tasks.');

        $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $oldAssignee = $task->assignedTo?->name ?? 'Unassigned';
        $task->update(['assigned_to' => $request->assigned_to]);
        $newAssignee = $task->fresh()->assignedTo?->name ?? 'Unassigned';

        TaskLog::create([
            'task_id'     => $task->id,
            'user_id'     => Auth::id(),
            'action'      => 'assigned',
            'description' => "Task reassigned from {$oldAssignee} to {$newAssignee}",
            'meta'        => ['old' => $oldAssignee, 'new' => $newAssignee],
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => "Task assigned to {$newAssignee}",
                'assignee' => $newAssignee,
            ]);
        }

        return back()->with('success', "Task assigned to {$newAssignee}!");
    }

    public function destroy(Task $task)
    {
        abort_if(!Auth::user()->can('delete tasks'), 403, 'Unauthorized to delete tasks.');

        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted.');
    }

    public function updateStatus(Request $request, Task $task)
    {
        $user = Auth::user();
        abort_if(!$user->can('change task status'), 403, 'Unauthorized to change task status.');

        $isSuperAdmin = $user->hasRole('super-admin') || $user->isDefaultSuperAdmin();

        // Enforce: only assigned user or Super Admin can change task status
        if (!$isSuperAdmin && (int)$task->assigned_to !== (int)$user->id) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only change the status of tasks assigned to you.',
                ], 403);
            }
            abort(403, 'You can only change the status of tasks assigned to you.');
        }

        $request->validate(['status' => 'required|in:pending,in_progress,on_hold,under_review,completed']);

        $oldStatus = $task->status;
        $task->update([
            'status'       => $request->status,
            'completed_at' => $request->status === 'completed' ? now() : null,
        ]);

        TaskLog::create([
            'task_id'     => $task->id,
            'user_id'     => $user->id,
            'action'      => 'status_changed',
            'description' => "Status changed from {$oldStatus} to {$request->status}",
            'meta'        => ['old' => $oldStatus, 'new' => $request->status],
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status'  => $request->status,
            ]);
        }

        return back()->with('success', 'Status updated successfully!');
    }

    public function addComment(Request $request, Task $task)
    {
        abort_if(!Auth::user()->can('add task comments') && !Auth::user()->can('view tasks'), 403, 'Unauthorized to comment.');

        $request->validate(['comment' => 'required|string|max:2000']);

        $comment = $task->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        TaskLog::create([
            'task_id'     => $task->id,
            'user_id'     => Auth::id(),
            'action'      => 'comment_added',
            'description' => 'Added a comment',
        ]);

        return back()->with('success', 'Comment added!');
    }

    public function deleteComment(Task $task, TaskComment $comment)
    {
        $user = Auth::user();
        if ($comment->user_id !== $user->id && !$user->can('delete task comments')) {
            abort(403, 'Unauthorized to delete this comment.');
        }
        $comment->delete();
        return back()->with('success', 'Comment deleted.');
    }

    public function kanban(Request $request)
    {
        $user = Auth::user();
        abort_if(!$user->can('view tasks'), 403, 'Unauthorized access to kanban.');

        $canViewAll = $user->can('view all tasks');

        $statuses = ['pending', 'in_progress', 'on_hold', 'under_review', 'completed'];
        $taskTypes = TaskType::active()->ordered()->get();

        foreach ($statuses as $status) {
            $query = $canViewAll ? Task::query() : Task::where('assigned_to', $user->id);
            $kanban[$status] = $query->where('status', $status)
                ->with(['client', 'assignedTo', 'taskType'])
                ->orderBy('priority')
                ->orderBy('deadline')
                ->get();
        }

        return view('tasks.kanban', compact('kanban', 'taskTypes'));
    }
}
