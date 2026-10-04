<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Reminder;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Display the Reports & Analytics dashboard.
     */
    public function index(Request $request)
    {
        abort_if(!auth()->user()->can('view reports'), 403, 'Unauthorized. You do not have permission to view reports.');

        // Timeframe filter resolution
        $timeframe = $request->input('timeframe', 'this_month');
        $customStart = $request->input('start_date');
        $customEnd = $request->input('end_date');

        [$startDate, $endDate] = $this->resolveDateRange($timeframe, $customStart, $customEnd);

        // Base Query with filters
        $taskQuery = Task::query();

        if ($startDate && $endDate) {
            $taskQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($request->filled('client_id')) {
            $taskQuery->where('client_id', $request->input('client_id'));
        }

        if ($request->filled('assigned_to')) {
            $taskQuery->where('assigned_to', $request->input('assigned_to'));
        }

        if ($request->filled('task_type_id')) {
            $taskQuery->where('task_type_id', $request->input('task_type_id'));
        } elseif ($request->filled('type')) {
            if (is_numeric($request->type)) {
                $taskQuery->where('task_type_id', $request->type);
            } else {
                $taskQuery->where(function($q) use ($request) {
                    $q->where('type', $request->type)
                      ->orWhereHas('taskType', fn($tq) => $tq->where('slug', $request->type));
                });
            }
        }

        if ($request->filled('priority')) {
            $taskQuery->where('priority', $request->input('priority'));
        }

        if ($request->filled('status')) {
            $taskQuery->where('status', $request->input('status'));
        }

        // Summary KPI Metrics
        $tasksInPeriod = (clone $taskQuery)->count();
        $completedTasks = (clone $taskQuery)->where('status', 'completed')->count();
        $inProgressTasks = (clone $taskQuery)->where('status', 'in_progress')->count();
        $pendingTasks = (clone $taskQuery)->where('status', 'pending')->count();
        $cancelledTasks = (clone $taskQuery)->where('status', 'cancelled')->count();

        $completionRate = $tasksInPeriod > 0 ? round(($completedTasks / $tasksInPeriod) * 100, 1) : 0;

        // Overdue tasks count (deadline passed and not completed)
        $overdueTasks = (clone $taskQuery)
            ->whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->where('status', '!=', 'completed')
            ->count();

        // Average Resolution Time (in hours / days) for completed tasks
        $completedWithDates = (clone $taskQuery)
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->get(['created_at', 'completed_at']);

        $avgHours = 0;
        if ($completedWithDates->count() > 0) {
            $totalHours = $completedWithDates->sum(function ($task) {
                return $task->created_at ? $task->created_at->diffInHours($task->completed_at) : 0;
            });
            $avgHours = round($totalHours / $completedWithDates->count(), 1);
        }

        // Status Breakdown
        $statusCounts = [
            'completed'   => $completedTasks,
            'in_progress' => $inProgressTasks,
            'pending'     => $pendingTasks,
            'cancelled'   => $cancelledTasks,
        ];

        // Priority Breakdown
        $priorityCounts = [
            'urgent' => (clone $taskQuery)->where('priority', 'urgent')->count(),
            'high'   => (clone $taskQuery)->where('priority', 'high')->count(),
            'medium' => (clone $taskQuery)->where('priority', 'medium')->count(),
            'low'    => (clone $taskQuery)->where('priority', 'low')->count(),
        ];

        // Monthly Trend: Last 6 Months
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $mStart = Carbon::now()->subMonths($i)->startOfMonth();
            $mEnd = Carbon::now()->subMonths($i)->endOfMonth();
            $mLabel = $mStart->format('M Y');

            $createdCount = Task::whereBetween('created_at', [$mStart, $mEnd])->count();
            $compCount = Task::where('status', 'completed')
                ->where(function ($q) use ($mStart, $mEnd) {
                    $q->whereBetween('completed_at', [$mStart, $mEnd])
                      ->orWhere(function ($sub) use ($mStart, $mEnd) {
                          $sub->whereNull('completed_at')
                              ->whereBetween('updated_at', [$mStart, $mEnd]);
                      });
                })->count();

            $monthlyTrend[] = [
                'month'     => $mLabel,
                'created'   => $createdCount,
                'completed' => $compCount,
            ];
        }

        // Top Clients by Task Volume
        $topClients = Client::withCount(['tasks' => function ($q) use ($startDate, $endDate) {
                if ($startDate && $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                }
            }])
            ->orderByDesc('tasks_count')
            ->take(6)
            ->get();

        // Team Member Productivity Leaderboard
        $teamProductivity = User::withCount([
                'assignedTasks as total_tasks' => function ($q) use ($startDate, $endDate) {
                    if ($startDate && $endDate) {
                        $q->whereBetween('created_at', [$startDate, $endDate]);
                    }
                },
                'assignedTasks as completed_tasks' => function ($q) use ($startDate, $endDate) {
                    $q->where('status', 'completed');
                    if ($startDate && $endDate) {
                        $q->whereBetween('created_at', [$startDate, $endDate]);
                    }
                },
                'assignedTasks as in_progress_tasks' => function ($q) use ($startDate, $endDate) {
                    $q->where('status', 'in_progress');
                    if ($startDate && $endDate) {
                        $q->whereBetween('created_at', [$startDate, $endDate]);
                    }
                },
                'assignedTasks as overdue_tasks' => function ($q) use ($startDate, $endDate) {
                    $q->where('status', '!=', 'completed')
                      ->whereNotNull('deadline')
                      ->where('deadline', '<', now());
                    if ($startDate && $endDate) {
                        $q->whereBetween('created_at', [$startDate, $endDate]);
                    }
                },
            ])
            ->where('is_active', true)
            ->orderByDesc('completed_tasks')
            ->take(8)
            ->get();

        // Recent Task Activity in current filter
        $recentFilteredTasks = (clone $taskQuery)
            ->with(['client', 'assignedTo'])
            ->latest('created_at')
            ->take(10)
            ->get();

        // Dropdown data
        $allClients   = Client::orderBy('company_name')->orderBy('name')->get();
        $allUsers     = User::where('is_active', true)->orderBy('name')->get();
        $allTaskTypes = TaskType::active()->ordered()->get();

        return view('reports.index', compact(
            'tasksInPeriod',
            'completedTasks',
            'inProgressTasks',
            'pendingTasks',
            'cancelledTasks',
            'completionRate',
            'overdueTasks',
            'avgHours',
            'statusCounts',
            'priorityCounts',
            'monthlyTrend',
            'topClients',
            'teamProductivity',
            'recentFilteredTasks',
            'allClients',
            'allUsers',
            'allTaskTypes',
            'timeframe',
            'customStart',
            'customEnd',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export data to CSV based on current filters.
     */
    public function export(Request $request): StreamedResponse
    {
        abort_if(!auth()->user()->can('export data'), 403, 'Unauthorized. You do not have permission to export data.');

        $exportType = $request->input('type', 'tasks');
        $timeframe = $request->input('timeframe', 'all_time');
        $customStart = $request->input('start_date');
        $customEnd = $request->input('end_date');

        [$startDate, $endDate] = $this->resolveDateRange($timeframe, $customStart, $customEnd);

        $dateSuffix = now()->format('Y-m-d_His');

        if ($exportType === 'clients') {
            $fileName = "clients_report_{$dateSuffix}.csv";
            $headers = [
                'Content-Type'        => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            ];

            $callback = function () {
                $file = fopen('php://output', 'w');
                // Header row
                fputcsv($file, ['ID', 'Client Name', 'Company', 'Email', 'Phone', 'Type', 'Status', 'System URL', 'Total Tasks', 'Created At']);

                $clients = Client::withCount('tasks')->orderBy('name')->get();
                foreach ($clients as $client) {
                    fputcsv($file, [
                        $client->id,
                        $client->name,
                        $client->company_name ?? 'N/A',
                        $client->email ?? 'N/A',
                        $client->phone ?? 'N/A',
                        $client->type_label,
                        $client->is_active ? 'Active' : 'Inactive',
                        $client->system_url ?? 'N/A',
                        $client->tasks_count,
                        $client->created_at ? $client->created_at->format('Y-m-d H:i') : 'N/A',
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        if ($exportType === 'team') {
            $fileName = "team_productivity_report_{$dateSuffix}.csv";
            $headers = [
                'Content-Type'        => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            ];

            $callback = function () use ($startDate, $endDate) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['User ID', 'Name', 'Email', 'Designation', 'Role', 'Status', 'Assigned Tasks', 'Completed Tasks', 'In Progress', 'Overdue Tasks']);

                $users = User::withCount([
                    'assignedTasks as total_tasks',
                    'assignedTasks as completed_tasks' => fn($q) => $q->where('status', 'completed'),
                    'assignedTasks as in_progress_tasks' => fn($q) => $q->where('status', 'in_progress'),
                    'assignedTasks as overdue_tasks' => fn($q) => $q->where('status', '!=', 'completed')->whereNotNull('deadline')->where('deadline', '<', now()),
                ])->get();

                foreach ($users as $u) {
                    fputcsv($file, [
                        $u->id,
                        $u->name,
                        $u->email,
                        $u->designation ?? 'N/A',
                        $u->getRoleNames()->first() ?? 'Member',
                        $u->is_active ? 'Active' : 'Inactive',
                        $u->total_tasks,
                        $u->completed_tasks,
                        $u->in_progress_tasks,
                        $u->overdue_tasks,
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Default: Tasks Export
        $fileName = "tasks_report_{$dateSuffix}.csv";
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () use ($request, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Task ID', 'Title', 'Client', 'Type', 'Priority',
                'Status', 'Assigned To', 'Created By', 'Deadline',
                'Completed At', 'Created At'
            ]);

            $taskQuery = Task::with(['client', 'assignedTo', 'createdBy', 'taskType']);

            if ($startDate && $endDate) {
                $taskQuery->whereBetween('created_at', [$startDate, $endDate]);
            }
            if ($request->filled('client_id')) {
                $taskQuery->where('client_id', $request->input('client_id'));
            }
            if ($request->filled('assigned_to')) {
                $taskQuery->where('assigned_to', $request->input('assigned_to'));
            }
            if ($request->filled('task_type_id')) {
                $taskQuery->where('task_type_id', $request->input('task_type_id'));
            } elseif ($request->filled('type')) {
                if (is_numeric($request->type)) {
                    $taskQuery->where('task_type_id', $request->type);
                } else {
                    $taskQuery->where(function($q) use ($request) {
                        $q->where('type', $request->type)
                          ->orWhereHas('taskType', fn($tq) => $tq->where('slug', $request->type));
                    });
                }
            }
            if ($request->filled('priority')) {
                $taskQuery->where('priority', $request->input('priority'));
            }
            if ($request->filled('status')) {
                $taskQuery->where('status', $request->input('status'));
            }

            $taskQuery->chunk(100, function ($tasks) use ($file) {
                foreach ($tasks as $task) {
                    fputcsv($file, [
                        $task->id,
                        $task->title,
                        $task->client ? ($task->client->company_name ?? $task->client->name) : 'Internal',
                        $task->type_label,
                        ucfirst($task->priority),
                        ucwords(str_replace('_', ' ', $task->status)),
                        $task->assignedTo ? $task->assignedTo->name : 'Unassigned',
                        $task->createdBy ? $task->createdBy->name : 'System',
                        $task->deadline ? $task->deadline->format('Y-m-d H:i') : 'None',
                        $task->completed_at ? $task->completed_at->format('Y-m-d H:i') : 'N/A',
                        $task->created_at ? $task->created_at->format('Y-m-d H:i') : 'N/A',
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Resolve date range from preset or custom strings.
     */
    protected function resolveDateRange(string $timeframe, ?string $start, ?string $end): array
    {
        if ($timeframe === 'custom' && $start && $end) {
            return [
                Carbon::parse($start)->startOfDay(),
                Carbon::parse($end)->endOfDay(),
            ];
        }

        return match ($timeframe) {
            'today'        => [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()],
            'yesterday'    => [Carbon::yesterday()->startOfDay(), Carbon::yesterday()->endOfDay()],
            'last_7_days'  => [Carbon::now()->subDays(6)->startOfDay(), Carbon::now()->endOfDay()],
            'last_30_days' => [Carbon::now()->subDays(29)->startOfDay(), Carbon::now()->endOfDay()],
            'this_month'   => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'last_month'   => [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()],
            'this_year'    => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            'all_time'     => [null, null],
            default        => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };
    }
}
