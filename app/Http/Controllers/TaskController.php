<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskType;
use App\Models\Client;
use App\Models\TaskComment;
use App\Models\TaskAttachment;
use App\Models\TaskLog;
use App\Models\User;
use App\Services\ImageOptimizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if(!$user->can('view tasks'), 403, 'Unauthorized access to tasks.');

        $isSuperAdmin = $user->hasRole('super-admin') || $user->isDefaultSuperAdmin();
        $canViewAll   = $isSuperAdmin || $user->can('view all tasks');
        $baseQuery    = $canViewAll ? Task::query() : Task::where(function($q) use ($user) {
            $q->where('assigned_to', $user->id)->orWhere('created_by', $user->id);
        });

        // Common filter closure (Task Type, Client, Priority, Assignee, Search, Date Range)
        $applyCommonFilters = function($q) use ($request) {
            // Task Type
            if ($request->filled('task_type_id')) {
                $q->where('task_type_id', $request->task_type_id);
            } elseif ($request->filled('type')) {
                if (is_numeric($request->type)) {
                    $q->where('task_type_id', $request->type);
                } else {
                    $q->where(function($sq) use ($request) {
                        $sq->where('type', $request->type)
                           ->orWhereHas('taskType', fn($tq) => $tq->where('slug', $request->type));
                    });
                }
            }

            // Client, Priority, Assignee
            if ($request->filled('client_id'))   $q->where('client_id', $request->client_id);
            if ($request->filled('priority'))    $q->where('priority', $request->priority);
            if ($request->filled('assigned_to')) $q->where('assigned_to', $request->assigned_to);

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $q->where(function($sq) use ($search) {
                    $sq->where('title', 'like', '%'.$search.'%')
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

            // Date Filtering
            $dateType = $request->get('date_type', 'deadline');
            $dateColumn = match($dateType) {
                'created', 'created_at' => 'created_at',
                'completed', 'completed_at' => 'completed_at',
                default => 'deadline',
            };

            if ($request->filled('date_preset')) {
                switch ($request->date_preset) {
                    case 'today':
                        $q->whereDate($dateColumn, now()->toDateString());
                        break;
                    case 'week':
                        $q->whereBetween($dateColumn, [now()->startOfWeek()->toDateTimeString(), now()->endOfWeek()->toDateTimeString()]);
                        break;
                    case 'month':
                        $q->whereBetween($dateColumn, [now()->startOfMonth()->toDateTimeString(), now()->endOfMonth()->toDateTimeString()]);
                        break;
                    case 'overdue':
                        $q->where('status', '!=', 'completed')
                          ->whereNotNull('deadline')
                          ->whereDate('deadline', '<', now()->toDateString());
                        break;
                }
            } else {
                if ($request->filled('date_from')) {
                    $q->whereDate($dateColumn, '>=', $request->date_from);
                }
                if ($request->filled('date_to')) {
                    $q->whereDate($dateColumn, '<=', $request->date_to);
                }
            }
        };

        // Calculate Status Counts
        $countQuery = clone $baseQuery;
        $applyCommonFilters($countQuery);

        $statusCounts = [
            'all'          => (clone $countQuery)->count(),
            'pending'      => (clone $countQuery)->where('status', 'pending')->count(),
            'in_progress'  => (clone $countQuery)->where('status', 'in_progress')->count(),
            'on_hold'      => (clone $countQuery)->where('status', 'on_hold')->count(),
            'under_review' => (clone $countQuery)->where('status', 'under_review')->count(),
            'completed'    => (clone $countQuery)->where('status', 'completed')->count(),
        ];

        // Main Query
        $query = clone $baseQuery;
        $query->with(['client', 'assignedTo', 'createdBy', 'taskType', 'comments']);
        $applyCommonFilters($query);

        // Status Filter (Default to 'pending')
        $selectedStatus = $request->get('status', 'pending');
        if ($selectedStatus !== 'all' && in_array($selectedStatus, ['pending', 'in_progress', 'on_hold', 'under_review', 'completed'])) {
            $query->where('status', $selectedStatus);
        }

        $tasks     = $query->latest()->paginate(15)->withQueryString();
        $clients   = Client::active()->orderBy('company_name')->orderBy('name')->get();
        $users     = User::where('is_active', true)->orderBy('name')->get();
        $taskTypes = TaskType::active()->ordered()->get();

        return view('tasks.index', compact('tasks', 'clients', 'users', 'taskTypes', 'statusCounts', 'selectedStatus', 'isSuperAdmin'));
    }

    public function create()
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if(!$user->can('create tasks'), 403, 'Unauthorized to create tasks.');

        $clients   = Client::where('is_active', true)->orderBy('company_name')->orderBy('name')->get();
        $users     = User::where('is_active', true)->with('roles')->orderBy('name')->get();
        $taskTypes = TaskType::active()->ordered()->get();

        return view('tasks.create', compact('clients', 'users', 'taskTypes'));
    }

    public function store(Request $request, ImageOptimizerService $optimizer)
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if(!$user->can('create tasks'), 403, 'Unauthorized to create tasks.');

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'client_id'     => 'nullable|exists:clients,id',
            'task_type_id'  => 'required|exists:task_types,id',
            'priority'      => 'required|in:high,medium,low',
            'status'        => 'required|in:pending,in_progress,on_hold,under_review,completed',
            'assigned_to'   => 'nullable|exists:users,id',
            'deadline'      => 'nullable|date',
            'attachments'   => 'nullable|array',
            'attachments.*' => 'nullable|file|max:30720',
            'images'        => 'nullable|array',
            'images.*'      => 'nullable|file|max:30720',
        ]);

        $taskType = TaskType::find($validated['task_type_id']);
        $validated['type'] = $taskType?->slug ?? 'other';
        $validated['created_by'] = Auth::id();

        if ($validated['status'] === 'completed') {
            $validated['completed_at'] = now();
        }

        $task = Task::create($validated);

        // Process attachments / images
        $uploadedFiles = array_merge(
            $request->file('attachments', []) ?: [],
            $request->file('images', []) ?: []
        );

        $attachmentCount = 0;
        foreach ($uploadedFiles as $file) {
            if ($file && $file->isValid()) {
                $meta = $optimizer->optimizeAndStore($file, 'task-attachments');
                $task->attachments()->create([
                    'user_id'   => Auth::id(),
                    'file_name' => $meta['file_name'],
                    'file_path' => $meta['file_path'],
                    'file_type' => $meta['file_type'],
                    'file_size' => $meta['file_size'],
                ]);
                $attachmentCount++;
            }
        }

        // Log creation
        TaskLog::create([
            'task_id'     => $task->id,
            'user_id'     => Auth::id(),
            'action'      => 'created',
            'description' => $attachmentCount > 0 ? "Task created with {$attachmentCount} image(s)/attachment(s)" : 'Task created',
        ]);

        return redirect()->route('tasks.show', $task)->with('success', 'Task created successfully!');
    }

    public function show(Task $task)
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if(!$user->can('view tasks'), 403, 'Unauthorized access to task details.');

        $isSuperAdmin = $user->hasRole('super-admin') || $user->isDefaultSuperAdmin();
        $canViewAll   = $isSuperAdmin || $user->can('view all tasks');
        $isAssigned   = (int)$task->assigned_to === (int)$user->id;
        $isCreator    = (int)$task->created_by === (int)$user->id;

        // If user cannot view all tasks and is not assigned or creator, deny
        if (!$canViewAll && !$isAssigned && !$isCreator) {
            abort(403, 'You are not assigned to this task.');
        }

        $task->load(['client', 'assignedTo.roles', 'createdBy', 'comments.user', 'attachments.user', 'logs.user', 'taskType']);
        $users = User::where('is_active', true)->with('roles')->orderBy('name')->get();
        return view('tasks.show', compact('task', 'users'));
    }

    public function edit(Task $task)
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if(!$user->can('edit tasks'), 403, 'Unauthorized to edit tasks.');

        $clients   = Client::where('is_active', true)->orderBy('company_name')->orderBy('name')->get();
        $users     = User::where('is_active', true)->with('roles')->orderBy('name')->get();
        $taskTypes = TaskType::active()->ordered()->get();
        $task->load('attachments.user');

        return view('tasks.edit', compact('task', 'clients', 'users', 'taskTypes'));
    }

    public function update(Request $request, Task $task, ImageOptimizerService $optimizer)
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if(!$user->can('edit tasks'), 403, 'Unauthorized to edit tasks.');

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'client_id'     => 'nullable|exists:clients,id',
            'task_type_id'  => 'required|exists:task_types,id',
            'priority'      => 'required|in:high,medium,low',
            'status'        => 'required|in:pending,in_progress,on_hold,under_review,completed',
            'assigned_to'   => 'nullable|exists:users,id',
            'deadline'      => 'nullable|date',
            'attachments'   => 'nullable|array',
            'attachments.*' => 'nullable|file|max:30720',
            'images'        => 'nullable|array',
            'images.*'      => 'nullable|file|max:30720',
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

        // Process attachments / images
        $uploadedFiles = array_merge(
            $request->file('attachments', []) ?: [],
            $request->file('images', []) ?: []
        );

        $attachmentCount = 0;
        foreach ($uploadedFiles as $file) {
            if ($file && $file->isValid()) {
                $meta = $optimizer->optimizeAndStore($file, 'task-attachments');
                $task->attachments()->create([
                    'user_id'   => Auth::id(),
                    'file_name' => $meta['file_name'],
                    'file_path' => $meta['file_path'],
                    'file_type' => $meta['file_type'],
                    'file_size' => $meta['file_size'],
                ]);
                $attachmentCount++;
            }
        }

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

        if ($attachmentCount > 0) {
            TaskLog::create([
                'task_id'     => $task->id,
                'user_id'     => $user->id,
                'action'      => 'attachment_added',
                'description' => "Uploaded {$attachmentCount} new image(s)/attachment(s)",
            ]);
        }

        return redirect()->route('tasks.show', $task)->with('success', 'Task updated successfully!');
    }

    public function updateAssignee(Request $request, Task $task)
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if(!$user->can('assign tasks'), 403, 'Unauthorized to assign tasks.');

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
        /** @var User $user */
        $user = Auth::user();
        abort_if(!$user->can('delete tasks'), 403, 'Unauthorized to delete tasks.');

        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted.');
    }

    public function updateStatus(Request $request, Task $task)
    {
        /** @var User $user */
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

    public function uploadAttachment(Request $request, Task $task, ImageOptimizerService $optimizer)
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if(!$user->can('view tasks'), 403, 'Unauthorized.');

        $isSuperAdmin = $user->hasRole('super-admin') || $user->isDefaultSuperAdmin();
        $canViewAll   = $isSuperAdmin || $user->can('view all tasks');
        $isAssigned   = (int)$task->assigned_to === (int)$user->id;
        $isCreator    = (int)$task->created_by === (int)$user->id;

        if (!$canViewAll && !$isAssigned && !$isCreator) {
            abort(403, 'You are not assigned to this task.');
        }

        $request->validate([
            'attachments'   => 'nullable|array',
            'attachments.*' => 'nullable|file|max:30720',
            'images'        => 'nullable|array',
            'images.*'      => 'nullable|file|max:30720',
        ]);

        $uploadedFiles = array_merge(
            $request->file('attachments', []) ?: [],
            $request->file('images', []) ?: []
        );

        if (empty($uploadedFiles)) {
            return back()->with('error', 'Please select at least one image or file.');
        }

        $attachmentCount = 0;
        foreach ($uploadedFiles as $file) {
            if ($file && $file->isValid()) {
                $meta = $optimizer->optimizeAndStore($file, 'task-attachments');
                $task->attachments()->create([
                    'user_id'   => $user->id,
                    'file_name' => $meta['file_name'],
                    'file_path' => $meta['file_path'],
                    'file_type' => $meta['file_type'],
                    'file_size' => $meta['file_size'],
                ]);
                $attachmentCount++;
            }
        }

        TaskLog::create([
            'task_id'     => $task->id,
            'user_id'     => $user->id,
            'action'      => 'attachment_added',
            'description' => "Uploaded {$attachmentCount} image(s)/file(s)",
        ]);

        return back()->with('success', "{$attachmentCount} image(s)/file(s) uploaded and optimized successfully!");
    }

    public function viewAttachment(Task $task, TaskAttachment $attachment)
    {
        $path = storage_path('app/public/' . $attachment->file_path);
        if (!file_exists($path)) {
            abort(404, 'Attachment file not found on disk.');
        }

        return response()->file($path, [
            'Content-Type'        => $attachment->file_type ?: (mime_content_type($path) ?: 'application/octet-stream'),
            'Content-Disposition' => 'inline; filename="' . addslashes($attachment->file_name) . '"',
        ]);
    }

    public function downloadAttachment(Task $task, TaskAttachment $attachment)
    {
        $path = storage_path('app/public/' . $attachment->file_path);
        if (!file_exists($path)) {
            abort(404, 'Attachment file not found on disk.');
        }

        return response()->download($path, $attachment->file_name);
    }

    public function deleteAttachment(Task $task, TaskAttachment $attachment)
    {
        /** @var User $user */
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super-admin') || $user->isDefaultSuperAdmin();
        $isUploader   = (int)$attachment->user_id === (int)$user->id;
        $isCreator    = (int)$task->created_by === (int)$user->id;

        if (!$isSuperAdmin && !$isUploader && !$isCreator && !$user->can('edit tasks')) {
            abort(403, 'Unauthorized to delete this attachment.');
        }

        if ($attachment->file_path) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $fileName = $attachment->file_name;
        $attachment->delete();

        TaskLog::create([
            'task_id'     => $task->id,
            'user_id'     => $user->id,
            'action'      => 'attachment_deleted',
            'description' => "Deleted attachment: {$fileName}",
        ]);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Attachment deleted']);
        }

        return back()->with('success', 'Attachment deleted successfully.');
    }

    public function addComment(Request $request, Task $task)
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if(!$user->can('add task comments') && !$user->can('view tasks'), 403, 'Unauthorized to comment.');

        $isSuperAdmin = $user->hasRole('super-admin') || $user->isDefaultSuperAdmin();
        $canViewAll   = $isSuperAdmin || $user->can('view all tasks');
        $isAssigned   = (int)$task->assigned_to === (int)$user->id;
        $isCreator    = (int)$task->created_by === (int)$user->id;

        if (!$canViewAll && !$isAssigned && !$isCreator) {
            abort(403, 'You are not assigned to this task.');
        }

        $request->validate(['comment' => 'required|string|max:2000']);

        $comment = $task->comments()->create([
            'user_id' => $user->id,
            'comment' => $request->comment,
        ]);

        TaskLog::create([
            'task_id'     => $task->id,
            'user_id'     => $user->id,
            'action'      => 'comment_added',
            'description' => 'Added a comment',
        ]);

        return back()->with('success', 'Comment added!');
    }

    public function deleteComment(Task $task, TaskComment $comment)
    {
        /** @var User $user */
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super-admin') || $user->isDefaultSuperAdmin();
        if ((int)$comment->user_id !== (int)$user->id && !$user->can('delete task comments') && !$isSuperAdmin) {
            abort(403, 'Unauthorized to delete this comment.');
        }
        $comment->delete();
        return back()->with('success', 'Comment deleted.');
    }

    public function kanban(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if(!$user->can('view tasks'), 403, 'Unauthorized access to kanban.');

        $isSuperAdmin = $user->hasRole('super-admin') || $user->isDefaultSuperAdmin();
        $canViewAll   = $isSuperAdmin || $user->can('view all tasks');

        $statuses = ['pending', 'in_progress', 'on_hold', 'under_review', 'completed'];
        $taskTypes = TaskType::active()->ordered()->get();

        foreach ($statuses as $status) {
            $query = $canViewAll ? Task::query() : Task::where(function($q) use ($user) {
                $q->where('assigned_to', $user->id)->orWhere('created_by', $user->id);
            });
            $kanban[$status] = $query->where('status', $status)
                ->with(['client', 'assignedTo', 'taskType'])
                ->orderBy('priority')
                ->orderBy('deadline')
                ->get();
        }

        return view('tasks.kanban', compact('kanban', 'taskTypes'));
    }
}
