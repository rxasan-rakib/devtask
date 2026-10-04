<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Task Alert' }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 24px 12px;
            color: #1e293b;
            line-height: 1.6;
        }
        .container {
            max-width: 580px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            padding: 24px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 4px 0 0 0;
            color: #ef4444;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .body {
            padding: 30px;
        }
        .badge-type {
            display: inline-block;
            background: #fef2f2;
            color: #ef4444;
            border: 1px solid #fee2e2;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }
        .task-title {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 16px 0;
            line-height: 1.35;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 18px 0;
            background: #f8fafc;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .details-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #edf2f7;
            font-size: 14px;
        }
        .details-table tr:last-child td {
            border-bottom: none;
        }
        .details-label {
            width: 35%;
            color: #64748b;
            font-weight: 600;
        }
        .details-value {
            width: 65%;
            color: #0f172a;
            font-weight: 500;
        }
        .note-box {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 14px 18px;
            border-radius: 0 8px 8px 0;
            margin: 20px 0;
            font-size: 14px;
            color: #1e3a8a;
        }
        .btn-wrapper {
            text-align: center;
            margin: 28px 0 10px 0;
        }
        .btn-action {
            display: inline-block;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }
        .footer {
            background: #f8fafc;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $appName }}</h1>
            <p>{{ $alertTypeLabel ?? 'Task Deadline Alert' }}</p>
        </div>
        <div class="body">
            <span class="badge-type">{{ $badgeLabel ?? '⚠️ OVERDUE TASK' }}</span>
            <h2 class="task-title">{{ $task->title }}</h2>

            <p style="color: #475569; font-size: 14px; margin-top: 0;">Hello <strong>{{ $user->name }}</strong>, {{ $alertMessage ?? 'this task has exceeded its scheduled deadline.' }}</p>

            <table class="details-table">
                @if($task->deadline)
                <tr>
                    <td class="details-label">⏰ Deadline</td>
                    <td class="details-value"><strong style="color: #ef4444;">{{ $task->deadline->format('d M, Y — h:i A') }}</strong> ({{ $task->deadline->diffForHumans() }})</td>
                </tr>
                @endif
                <tr>
                    <td class="details-label">📌 Priority</td>
                    <td class="details-value"><span style="text-transform: capitalize; font-weight: 600;">{{ $task->priority }}</span></td>
                </tr>
                <tr>
                    <td class="details-label">📊 Status</td>
                    <td class="details-value"><span style="text-transform: capitalize; font-weight: 600;">{{ str_replace('_', ' ', $task->status) }}</span></td>
                </tr>
                @if($task->client)
                <tr>
                    <td class="details-label">🏢 Client</td>
                    <td class="details-value">{{ $task->client->company_name ?? $task->client->name }}</td>
                </tr>
                @endif
                @if($task->taskType)
                <tr>
                    <td class="details-label">🏷️ Type</td>
                    <td class="details-value">{{ $task->taskType->name }}</td>
                </tr>
                @endif
            </table>

            @if($task->description)
            <div class="note-box">
                <strong style="display:block; margin-bottom: 4px; color: #1e40af;">Task Description:</strong>
                {!! nl2br(e(Str::limit($task->description, 300))) !!}
            </div>
            @endif

            <div class="btn-wrapper">
                <a href="{{ route('tasks.show', $task) }}" class="btn-action">
                    View Task in Dashboard &rarr;
                </a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.<br>
            You received this notification because task alerts are enabled in your workspace.
        </div>
    </div>
</body>
</html>
