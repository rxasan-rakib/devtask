 @extends('layouts.app')
 @section('title', 'Dashboard')
 @section('page-title', 'Dashboard')

 @section('content')
     <!-- Stat Cards -->
     <div class="row g-3 mb-4">
         <div class="col-6 col-xl-3">
             <div class="stat-card">
                 <div class="d-flex align-items-center justify-content-between mb-3">
                     <div class="stat-icon" style="background:rgba(99,102,241,.15);">
                         <i data-lucide="list-checks" style="width:20px;height:20px;color:#6366f1;"></i>
                     </div>
                     <span class="badge" style="background:rgba(99,102,241,.15);color:#6366f1;">Total</span>
                 </div>
                 <div class="stat-value">{{ $stats['total_tasks'] }}</div>
                 <div class="stat-label">Total Tasks</div>
             </div>
         </div>
         <div class="col-6 col-xl-3">
             <div class="stat-card">
                 <div class="d-flex align-items-center justify-content-between mb-3">
                     <div class="stat-icon" style="background:rgba(245,158,11,.15);">
                         <i data-lucide="clock" style="width:20px;height:20px;color:#f59e0b;"></i>
                     </div>
                     <span class="badge" style="background:rgba(245,158,11,.15);color:#f59e0b;">Active</span>
                 </div>
                 <div class="stat-value">{{ $stats['in_progress'] }}</div>
                 <div class="stat-label">In Progress</div>
             </div>
         </div>
         <div class="col-6 col-xl-3">
             <div class="stat-card">
                 <div class="d-flex align-items-center justify-content-between mb-3">
                     <div class="stat-icon" style="background:rgba(239,68,68,.15);">
                         <i data-lucide="alert-triangle" style="width:20px;height:20px;color:#ef4444;"></i>
                     </div>
                     <span class="badge" style="background:rgba(239,68,68,.15);color:#ef4444;">Urgent</span>
                 </div>
                 <div class="stat-value text-danger">{{ $stats['overdue'] }}</div>
                 <div class="stat-label">Overdue Tasks</div>
             </div>
         </div>
         <div class="col-6 col-xl-3">
             <div class="stat-card">
                 <div class="d-flex align-items-center justify-content-between mb-3">
                     <div class="stat-icon" style="background:rgba(16,185,129,.15);">
                         <i data-lucide="check-circle" style="width:20px;height:20px;color:#10b981;"></i>
                     </div>
                     <span class="badge" style="background:rgba(16,185,129,.15);color:#10b981;">Today</span>
                 </div>
                 <div class="stat-value" style="color:#10b981;">{{ $stats['completed_today'] }}</div>
                 <div class="stat-label">Completed Today</div>
             </div>
         </div>
     </div>

     <div class="row g-3 mb-4">
         <!-- Weekly Chart -->
         <div class="col-lg-7">
             <div class="card-glass h-100 d-flex flex-column">
                 <div class="d-flex align-items-center justify-content-between mb-3">
                     <div>
                         <h6 class="mb-0 fw-600" style="font-size:.9rem;">Weekly Activity</h6>
                         <span style="font-size:.75rem;color:var(--text-muted);">Completed vs Created</span>
                     </div>
                     <span class="badge" style="background:var(--accent-light);color:var(--accent);font-size:.75rem;">Last
                         7 days</span>
                 </div>
                 <div style="position: relative; height: 230px; width: 100%; min-height: 230px;">
                     <canvas id="weeklyChart"></canvas>
                 </div>
             </div>
         </div>

         <!-- Task Type Breakdown -->
         <div class="col-lg-5">
             <div class="card-glass h-100 d-flex flex-column">
                 <div class="d-flex align-items-center justify-content-between mb-3">
                     <div>
                         <h6 class="mb-0 fw-600" style="font-size:.9rem;">Task Type Breakdown</h6>
                         <span style="font-size:.75rem;color:var(--text-muted);">By Category</span>
                     </div>
                     <span class="badge" style="background:var(--bg-hover);color:var(--text-muted);font-size:.75rem;">All
                         Time</span>
                 </div>
                 <div class="flex-grow-1" style="max-height: 230px; overflow-y: auto; padding-right: 4px;">
                     @if ($taskTypes->count())
                         @foreach ($taskTypes as $t)
                             @php
                                 $count = $t->tasks_count ?? $t->count ?? 0;
                                 $pct   = $stats['total_tasks'] > 0 ? round(($count / $stats['total_tasks']) * 100) : 0;
                                 $color = $t->color ?: '#6366f1';
                                 $icon  = $t->icon ?: 'tag';
                             @endphp
                             <div class="mb-3">
                                 <div class="d-flex justify-content-between align-items-center mb-1">
                                     <span style="font-size:.8rem;font-weight:500;" class="d-flex align-items-center gap-1">
                                         <i data-lucide="{{ $icon }}" style="width:13px;height:13px;color:{{ $color }};"></i>
                                         {{ $t->name }}
                                     </span>
                                     <span style="font-size:.8rem;color:var(--text-muted);">
                                         {{ $count }} ({{ $pct }}%)
                                     </span>
                                 </div>
                                 <div style="height:6px;background:var(--bg-hover);border-radius:3px;overflow:hidden;">
                                     <div
                                         style="height:100%;width:{{ $pct }}%;background:{{ $color }};border-radius:3px;transition:width .5s;">
                                     </div>
                                 </div>
                             </div>
                         @endforeach
                     @else
                         <div style="text-align:center;padding:2rem;color:var(--text-muted);">
                             <i data-lucide="pie-chart"
                                 style="width:32px;height:32px;margin-bottom:.75rem;display:block;margin-inline:auto;opacity:.4;"></i>
                             No tasks yet
                         </div>
                     @endif
                 </div>
             </div>
         </div>
     </div>

     <div class="row g-3">
         <!-- Overdue Tasks -->
         @if ($overdueTasks->count())
             <div class="col-12">
                 <div class="card-glass" style="border-color:rgba(239,68,68,.3);">
                     <div class="d-flex align-items-center gap-2 mb-3">
                         <i data-lucide="alert-triangle" style="width:17px;height:17px;color:#ef4444;"></i>
                         <h6 class="mb-0 fw-600" style="font-size:.9rem;color:#ef4444;">Overdue Tasks
                             ({{ $overdueTasks->count() }})</h6>
                     </div>
                     <div class="table-responsive">
                         <table class="table table-dark-custom mb-0">
                             <thead>
                                 <tr>
                                     <th>Task</th>
                                     <th>Client</th>
                                     <th>Type</th>
                                     <th>Priority</th>
                                     <th>Deadline</th>
                                     <th>Assigned To</th>
                                     <th></th>
                                 </tr>
                             </thead>
                             <tbody>
                                 @foreach ($overdueTasks as $task)
                                     <tr class="overdue-row">
                                         <td>
                                             <a href="{{ route('tasks.show', $task) }}"
                                                 style="color:var(--text-primary);text-decoration:none;font-weight:500;">
                                                 {{ Str::limit($task->title, 40) }}
                                             </a>
                                         </td>
                                         <td style="color:var(--text-muted);">{{ $task->client?->name ?? '—' }}</td>
                                         <td><span class="badge"
                                                 style="background:var(--bg-hover);color:var(--text-muted);">{{ $task->type_label }}</span>
                                         </td>
                                         <td><span
                                                 class="badge badge-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span>
                                         </td>
                                         <td><span class="overdue-badge">{{ $task->deadline->format('d M Y') }}</span></td>
                                         <td style="color:var(--text-muted);">{{ $task->assignedTo?->name ?? '—' }}</td>
                                         <td><a href="{{ route('tasks.show', $task) }}" class="btn-primary-custom"
                                                 style="padding:.3rem .75rem;font-size:.75rem;">View</a></td>
                                     </tr>
                                 @endforeach
                             </tbody>
                         </table>
                     </div>
                 </div>
             </div>
         @endif

         <!-- Today's Tasks -->
         <div class="col-lg-6">
             <div class="card-glass h-100 d-flex flex-column mb-0">
                 <div class="d-flex align-items-center justify-content-between mb-3 flex-shrink-0">
                     <h6 class="mb-0 fw-600" style="font-size:.9rem; color: var(--text-primary);">
                         <i data-lucide="calendar"
                             style="width:16px;height:16px;display:inline;margin-right:6px;color:var(--accent);"></i>
                         Today's Tasks
                     </h6>
                     <a href="{{ route('tasks.index') }}" style="font-size:.75rem;color:var(--accent); text-decoration:none;" class="hover-accent">View all</a>
                 </div>
                 <div class="flex-grow-1" style="max-height: 270px; overflow-y: auto; padding-right: 4px; scrollbar-width: thin;">
                     @forelse($todayTasks as $task)
                         <div class="d-flex align-items-center gap-3 mb-2 p-2 rounded"
                             style="background:var(--bg-hover); border: 1px solid var(--border);">
                             <div style="flex:1;min-width:0;">
                                 <div
                                     style="font-size:.85rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                     <a href="{{ route('tasks.show', $task) }}"
                                         style="color:var(--text-primary);text-decoration:none;" class="hover-accent">{{ $task->title }}</a>
                                 </div>
                                 <div style="font-size:.72rem;color:var(--text-muted); margin-top: 2px;">
                                     {{ $task->client?->company_name ?? $task->client?->name ?? 'No client' }}</div>
                             </div>
                             <span class="badge badge-{{ $task->priority }}" style="font-size: .68rem;">{{ ucfirst($task->priority) }}</span>
                         </div>
                     @empty
                         <div class="text-center py-4" style="color:var(--text-muted); font-size: .84rem;">
                             <i data-lucide="check-circle"
                                 style="width:28px;height:28px;display:block;margin:0 auto .5rem;opacity:.35;"></i>
                             No tasks due today
                         </div>
                     @endforelse
                 </div>
             </div>
         </div>

         <!-- Upcoming Reminders -->
         <div class="col-lg-6">
             <div class="card-glass h-100 d-flex flex-column mb-0">
                 <div class="d-flex align-items-center justify-content-between mb-3 flex-shrink-0">
                     <h6 class="mb-0 fw-600" style="font-size:.9rem; color: var(--text-primary);">
                         <i data-lucide="bell"
                             style="width:16px;height:16px;display:inline;margin-right:6px;color:var(--warning);"></i>
                         Upcoming Reminders
                     </h6>
                     <a href="{{ route('reminders.create') }}" style="font-size:.75rem;color:var(--accent); text-decoration:none;" class="hover-accent">+ Add</a>
                 </div>
                 <div class="flex-grow-1" style="max-height: 270px; overflow-y: auto; padding-right: 4px; scrollbar-width: thin;">
                     @forelse($upcomingReminders as $reminder)
                         <div class="d-flex align-items-center gap-3 mb-2 p-2 rounded"
                             style="background:var(--bg-hover); border: 1px solid var(--border);">
                             <div class="stat-icon"
                                 style="background:rgba(245,158,11,.12);width:32px;height:32px;flex-shrink:0;">
                                 <i data-lucide="{{ $reminder->type_icon }}"
                                     style="width:14px;height:14px;color:#f59e0b;"></i>
                             </div>
                             <div style="flex:1;min-width:0;">
                                 <div
                                     style="font-size:.85rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                     {{ $reminder->title }}</div>
                                 <div style="font-size:.72rem;color:var(--text-muted); margin-top: 2px;">
                                     {{ $reminder->remind_at->diffForHumans() }}</div>
                             </div>
                             <span class="badge"
                                 style="background:rgba(245,158,11,.15);color:#f59e0b;font-size:.65rem;">{{ $reminder->type_label }}</span>
                         </div>
                     @empty
                         <div class="text-center py-4" style="color:var(--text-muted); font-size: .84rem;">
                             <i data-lucide="bell-off"
                                 style="width:28px;height:28px;display:block;margin:0 auto .5rem;opacity:.35;"></i>
                             No upcoming reminders
                         </div>
                     @endforelse
                 </div>
             </div>
         </div>

         <!-- Upcoming Deadlines -->
         <div class="col-lg-6">
             <div class="card-glass h-100 d-flex flex-column mb-0">
                 <div class="d-flex align-items-center justify-content-between mb-3 flex-shrink-0">
                     <h6 class="mb-0 fw-600" style="font-size:.9rem; color: var(--text-primary);">
                         <i data-lucide="calendar-clock"
                             style="width:16px;height:16px;display:inline;margin-right:6px;color:var(--info);"></i>
                         Upcoming Deadlines (7 days)
                     </h6>
                     <span class="badge" style="background: rgba(14,165,233,0.12); color: #0ea5e9; font-size: .72rem;">
                         {{ $upcomingTasks->count() }} Due Soon
                     </span>
                 </div>
                 <div class="flex-grow-1" style="max-height: 270px; overflow-y: auto; padding-right: 4px; scrollbar-width: thin;">
                     @forelse($upcomingTasks as $task)
                         <div class="d-flex align-items-center gap-3 mb-2 p-2 rounded"
                             style="background:var(--bg-hover); border: 1px solid var(--border);">
                             <div style="flex:1;min-width:0;">
                                 <div
                                     style="font-size:.85rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                     <a href="{{ route('tasks.show', $task) }}"
                                         style="color:var(--text-primary);text-decoration:none;" class="hover-accent">{{ $task->title }}</a>
                                 </div>
                                 <div class="d-flex align-items-center gap-2 mt-1" style="font-size:.72rem;color:var(--text-muted);">
                                     @if($task->client)
                                         <span class="text-truncate" style="max-width: 140px; color: var(--text-primary); font-weight: 500;">
                                             <i data-lucide="building-2" style="width:10px;height:10px;display:inline;margin-right:2px;color:#ff7828;"></i>
                                             {{ $task->client->company_name ?? $task->client->name }}
                                         </span>
                                         <span>&bull;</span>
                                     @endif
                                     <span><i data-lucide="clock" style="width:10px;height:10px;display:inline;margin-right:2px;"></i>{{ $task->deadline->format('d M, h:i A') }}</span>
                                 </div>
                             </div>
                             <span class="badge badge-{{ $task->priority }}" style="font-size: .68rem;">{{ ucfirst($task->priority) }}</span>
                         </div>
                     @empty
                         <div class="text-center py-4" style="color:var(--text-muted); font-size: .84rem;">
                             <i data-lucide="calendar-check" style="width:28px;height:28px;display:block;margin:0 auto .5rem;opacity:.35;"></i>
                             No upcoming deadlines
                         </div>
                     @endforelse
                 </div>
             </div>
         </div>

         <!-- Recent Activity (Scrollable Feed) -->
         <div class="col-lg-6">
             <div class="card-glass h-100 d-flex flex-column mb-0">
                 <div class="d-flex align-items-center justify-content-between mb-3 flex-shrink-0">
                     <h6 class="mb-0 fw-600" style="font-size:.9rem; color: var(--text-primary);">
                         <i data-lucide="activity"
                             style="width:16px;height:16px;display:inline;margin-right:6px;color:var(--success);"></i>
                         Recent Activity
                     </h6>
                     <span class="badge" style="background: rgba(16,185,129,0.12); color: #10b981; font-size: .72rem;">
                         Live Feed
                     </span>
                 </div>
                 <div class="flex-grow-1" style="max-height: 270px; overflow-y: auto; padding-right: 6px; scrollbar-width: thin;">
                     @forelse($recentActivity as $log)
                         <div class="d-flex align-items-start gap-2 mb-2 pb-2 border-bottom" style="border-color: var(--border) !important;">
                             <img src="{{ $log->user?->avatar_url ?? 'https://ui-avatars.com/api/?name=User&background=4f46e5&color=fff&size=128' }}"
                                 style="width:26px;height:26px;border-radius:50%;flex-shrink:0;border: 1px solid var(--border);" alt="{{ $log->user?->name ?? 'User' }}">
                             <div style="font-size:.79rem; flex: 1; min-width: 0; line-height: 1.35;">
                                 <div>
                                     <strong style="color:var(--text-primary);">{{ $log->user?->name ?? 'System' }}</strong>
                                     <span style="color:var(--text-muted);"> {{ $log->description }} on </span>
                                     <a href="{{ $log->task ? route('tasks.show', $log->task) : '#' }}"
                                         style="color:var(--accent);text-decoration:none; font-weight: 550;" class="hover-accent">{{ Str::limit($log->task?->title ?? 'Task', 28) }}</a>
                                 </div>
                                 <div style="font-size:.69rem;color:var(--text-muted);margin-top:2px;">
                                     <i data-lucide="clock" style="width:10px;height:10px;display:inline;margin-right:2px;opacity:.7;"></i>
                                     {{ $log->created_at->diffForHumans() }}
                                 </div>
                             </div>
                         </div>
                     @empty
                         <div class="text-center py-4" style="color:var(--text-muted); font-size: .84rem;">
                             <i data-lucide="history" style="width:28px;height:28px;display:block;margin:0 auto .5rem;opacity:.35;"></i>
                             No recent activity recorded
                         </div>
                     @endforelse
                 </div>
             </div>
         </div>
     </div>
 @endsection

 @push('scripts')
     <script>
         // Weekly Chart
         const ctx = document.getElementById('weeklyChart').getContext('2d');
         const weeklyData = @json($weeklyData);
         new Chart(ctx, {
             type: 'bar',
             data: {
                 labels: weeklyData.map(d => d.date),
                 datasets: [{
                         label: 'Completed',
                         data: weeklyData.map(d => d.completed),
                         backgroundColor: 'rgba(16,185,129,0.7)',
                         borderRadius: 4,
                     },
                     {
                         label: 'Created',
                         data: weeklyData.map(d => d.created),
                         backgroundColor: 'rgba(99,102,241,0.5)',
                         borderRadius: 4,
                     }
                 ]
             },
             options: {
                 responsive: true,
                 maintainAspectRatio: false,
                 plugins: {
                     legend: {
                         labels: {
                             color: '#94a3b8',
                             font: {
                                 size: 11
                             }
                         }
                     }
                 },
                 scales: {
                     x: {
                         ticks: {
                             color: '#94a3b8',
                             font: {
                                 size: 11
                             }
                         },
                         grid: {
                             color: 'rgba(148,163,184,0.08)'
                         }
                     },
                     y: {
                         ticks: {
                             color: '#94a3b8',
                             font: {
                                 size: 11
                             },
                             stepSize: 1
                         },
                         grid: {
                             color: 'rgba(148,163,184,0.08)'
                         }
                     }
                 }
             }
         });
     </script>
 @endpush
