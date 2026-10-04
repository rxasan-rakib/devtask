<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use App\Models\Client;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ReminderController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!Gate::allows('view reminders'), 403, 'Unauthorized access to reminders.');

        $query = Reminder::where('user_id', Auth::id())->with(['client', 'task']);

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('company_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('task', function ($tq) use ($search) {
                      $tq->where('title', 'like', "%{$search}%");
                  });
            });
        }

        // Type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Status
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->active()->where('remind_at', '>', now());
            } elseif ($request->status === 'sent' || $request->status === 'done') {
                $query->done();
            } elseif ($request->status === 'overdue') {
                $query->active()->where('remind_at', '<=', now());
            }
        }

        // Timeframe
        if ($request->filled('timeframe')) {
            if ($request->timeframe === 'today') {
                $query->whereDate('remind_at', today());
            } elseif ($request->timeframe === 'week') {
                $query->whereBetween('remind_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($request->timeframe === 'month') {
                $query->whereBetween('remind_at', [now()->startOfMonth(), now()->endOfMonth()]);
            }
        }

        // Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('remind_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('remind_at', '<=', $request->date_to);
        }

        $reminders = $query->orderBy('remind_at', 'asc')->paginate(15)->withQueryString();
        $clients   = Client::where('is_active', true)->orderBy('name')->get();
        $tasks     = Task::whereIn('status', ['pending', 'in_progress'])->orderBy('title')->get();

        // Summary Counts
        $pendingCount = Reminder::where('user_id', Auth::id())->active()->where('remind_at', '>', now())->count();
        $overdueCount = Reminder::where('user_id', Auth::id())->active()->where('remind_at', '<=', now())->count();
        $doneCount    = Reminder::where('user_id', Auth::id())->done()->count();
        $totalCount   = Reminder::where('user_id', Auth::id())->count();

        return view('reminders.index', compact('reminders', 'clients', 'tasks', 'pendingCount', 'overdueCount', 'doneCount', 'totalCount'));
    }

    public function create()
    {
        abort_if(!Gate::allows('create reminders'), 403, 'Unauthorized to create reminders.');

        $clients = Client::where('is_active', true)->orderBy('company_name')->orderBy('name')->get();
        $tasks   = Task::whereIn('status', ['pending', 'in_progress'])->orderBy('title')->get();
        return view('reminders.create', compact('clients', 'tasks'));
    }

    public function store(Request $request)
    {
        abort_if(!Gate::allows('create reminders'), 403, 'Unauthorized to create reminders.');

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'type'           => 'required|in:meeting,domain_expiry,hosting_renewal,task_deadline,client_followup,system_update,custom',
            'client_id'      => 'nullable|exists:clients,id',
            'task_id'        => 'nullable|exists:tasks,id',
            'remind_at'      => 'required|date|after:now',
            'notify_email'   => 'boolean',
            'notify_browser' => 'boolean',
            'is_recurring'   => 'boolean',
            'recur_interval' => 'nullable|string',
        ]);

        $validated['user_id']        = Auth::id();
        $validated['notify_email']   = $request->boolean('notify_email', true);
        $validated['notify_browser'] = $request->boolean('notify_browser', true);
        $validated['is_recurring']   = $request->boolean('is_recurring', false);
        $validated['is_completed']   = false;
        $validated['is_sent']        = false;
        $validated['email_sent']     = false;

        $reminder = Reminder::create($validated);

        return redirect()->route('reminders.index')->with('success', 'Reminder scheduled successfully!');
    }

    public function show(Reminder $reminder)
    {
        abort_if(!Gate::allows('view reminders'), 403, 'Unauthorized access to reminder details.');
        return view('reminders.show', compact('reminder'));
    }

    public function edit(Reminder $reminder)
    {
        abort_if(!Gate::allows('edit reminders'), 403, 'Unauthorized to edit reminders.');

        $clients = Client::where('is_active', true)->orderBy('company_name')->orderBy('name')->get();
        $tasks   = Task::whereIn('status', ['pending', 'in_progress'])->orderBy('title')->get();
        return view('reminders.edit', compact('reminder', 'clients', 'tasks'));
    }

    public function update(Request $request, Reminder $reminder)
    {
        abort_if(!Gate::allows('edit reminders'), 403, 'Unauthorized to edit reminders.');

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'type'           => 'required|in:meeting,domain_expiry,hosting_renewal,task_deadline,client_followup,system_update,custom',
            'client_id'      => 'nullable|exists:clients,id',
            'task_id'        => 'nullable|exists:tasks,id',
            'remind_at'      => 'required|date',
            'notify_email'   => 'boolean',
            'notify_browser' => 'boolean',
        ]);

        $validated['notify_email']   = $request->boolean('notify_email');
        $validated['notify_browser'] = $request->boolean('notify_browser');

        // If remind_at changed to future, reset email_sent status
        if ($reminder->remind_at != $validated['remind_at'] && strtotime($validated['remind_at']) > time()) {
            $validated['email_sent']    = false;
            $validated['email_sent_at'] = null;
            $validated['notified_at']   = null;
        }

        $reminder->update($validated);
        return redirect()->route('reminders.index')->with('success', 'Reminder updated successfully!');
    }

    public function destroy(Reminder $reminder)
    {
        abort_if(!Gate::allows('delete reminders'), 403, 'Unauthorized to delete reminders.');

        $reminder->delete();
        return redirect()->route('reminders.index')->with('success', 'Reminder deleted.');
    }

    public function markSent(Reminder $reminder)
    {
        return $this->markDone($reminder);
    }

    public function markDone(Reminder $reminder)
    {
        abort_if(!Gate::allows('mark reminders sent') && $reminder->user_id !== Auth::id(), 403);

        $reminder->update([
            'is_completed' => true,
            'is_sent'      => true,
            'completed_at' => now(),
            'sent_at'      => now(),
        ]);

        return back()->with('success', 'Reminder marked as Done!');
    }

    public function markPending(Reminder $reminder)
    {
        abort_if(!Gate::allows('mark reminders sent') && $reminder->user_id !== Auth::id(), 403);

        $reminder->update([
            'is_completed' => false,
            'is_sent'      => false,
            'completed_at' => null,
            'sent_at'      => null,
        ]);

        return back()->with('success', 'Reminder status reopened as Pending.');
    }

    /**
     * Check, dispatch emails, and return due reminders for in-app alert prompts.
     */
    public function checkDue(Request $request)
    {
        $alerts = \App\Services\ReminderService::processDueReminders(Auth::id());

        return response()->json([
            'status' => 'success',
            'count'  => count($alerts),
            'alerts' => $alerts,
        ]);
    }
}
