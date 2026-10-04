<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskType;
use App\Models\Client;
use App\Models\Reminder;
use App\Models\User;
use App\Models\TaskLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if(!$user->can('view dashboard'), 403, 'Unauthorized access to dashboard.');

        $canViewAll = $user->can('view all tasks stats') || $user->can('view all tasks');
        $canViewClients = $user->can('view clients');
        $canViewUsers = $user->can('view users');

        // Task stats
        $taskQuery = $canViewAll ? Task::query() : Task::where('assigned_to', $user->id);

        $stats = [
            'total_tasks'     => (clone $taskQuery)->count(),
            'pending'         => (clone $taskQuery)->where('status', 'pending')->count(),
            'in_progress'     => (clone $taskQuery)->where('status', 'in_progress')->count(),
            'completed_today' => (clone $taskQuery)->where('status', 'completed')
                                    ->whereDate('completed_at', today())->count(),
            'overdue'         => (clone $taskQuery)->where('status', '!=', 'completed')
                                    ->where('deadline', '<', now())->count(),
            'total_clients'   => $canViewClients ? Client::count() : 0,
            'total_users'     => $canViewUsers ? User::count() : 0,
        ];

        // Today's tasks
        $todayTasks = (clone $taskQuery)
            ->with(['client', 'assignedTo', 'taskType'])
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereDate('deadline', today())
            ->orderBy('priority', 'asc')
            ->take(5)
            ->get();

        // Overdue tasks
        $overdueTasks = (clone $taskQuery)
            ->with(['client', 'assignedTo', 'taskType'])
            ->where('status', '!=', 'completed')
            ->where('deadline', '<', now())
            ->orderBy('deadline', 'asc')
            ->take(5)
            ->get();

        // Upcoming deadlines (next 7 days)
        $upcomingTasks = (clone $taskQuery)
            ->with(['client', 'assignedTo', 'taskType'])
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereBetween('deadline', [now(), now()->addDays(7)])
            ->orderBy('deadline', 'asc')
            ->take(5)
            ->get();

        // Upcoming reminders
        $upcomingReminders = Reminder::where('user_id', $user->id)
            ->where('is_sent', false)
            ->where('remind_at', '>', now())
            ->orderBy('remind_at', 'asc')
            ->with(['client'])
            ->take(5)
            ->get();

        // Recent activity (task logs)
        $recentActivity = TaskLog::with(['task.taskType', 'user'])
            ->whereHas('task')
            ->when(!$canViewAll, fn($q) => $q->whereHas('task', fn($tq) =>
                $tq->where('assigned_to', $user->id)->orWhere('created_by', $user->id)
            ))
            ->latest()
            ->take(20)
            ->get();

        // Weekly chart data (last 7 days)
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $weeklyData[] = [
                'date'      => $date->format('D'),
                'completed' => Task::when(!$canViewAll, fn($q) => $q->where('assigned_to', $user->id))
                                    ->where('status', 'completed')
                                    ->whereDate('completed_at', $date)
                                    ->count(),
                'created'   => Task::when(!$canViewAll, fn($q) => $q->where('created_by', $user->id))
                                    ->whereDate('created_at', $date)
                                    ->count(),
            ];
        }

        // Task type breakdown
        $taskTypes = TaskType::withCount(['tasks' => function($q) use ($canViewAll, $user) {
            if (!$canViewAll) {
                $q->where('assigned_to', $user->id);
            }
        }])
        ->whereHas('tasks', function($q) use ($canViewAll, $user) {
            if (!$canViewAll) {
                $q->where('assigned_to', $user->id);
            }
        })
        ->orderByDesc('tasks_count')
        ->get();

        return view('dashboard.index', compact(
            'stats', 'todayTasks', 'overdueTasks', 'upcomingTasks',
            'upcomingReminders', 'recentActivity', 'weeklyData', 'taskTypes'
        ));
    }
}
