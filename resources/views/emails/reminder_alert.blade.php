<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Reminder Alert' }}</title>
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
            color: #ff7828;
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
            background: #fff7ed;
            color: #ea580c;
            border: 1px solid #ffedd5;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }
        .reminder-title {
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
            background: linear-gradient(135deg, #ff7828 0%, #ea580c 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
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
            <p>Automated Reminder System</p>
        </div>
        <div class="body">
            <span class="badge-type">{{ $reminder->type_label }}</span>
            <h2 class="reminder-title">{{ $reminder->title }}</h2>

            <p style="color: #475569; font-size: 14px; margin-top: 0;">Hello <strong>{{ $user->name }}</strong>, this is an alert for your scheduled reminder.</p>

            <table class="details-table">
                <tr>
                    <td class="details-label">⏰ Scheduled Time</td>
                    <td class="details-value"><strong>{{ $reminder->remind_at->format('d M, Y — h:i A') }}</strong></td>
                </tr>
                @if($reminder->client)
                <tr>
                    <td class="details-label">🏢 Client</td>
                    <td class="details-value">{{ $reminder->client->company_name ?? $reminder->client->name }}</td>
                </tr>
                @endif
                @if($reminder->task)
                <tr>
                    <td class="details-label">📋 Related Task</td>
                    <td class="details-value">{{ $reminder->task->title }}</td>
                </tr>
                @endif
                <tr>
                    <td class="details-label">📌 Type</td>
                    <td class="details-value">{{ $reminder->type_label }}</td>
                </tr>
            </table>

            @if($reminder->description)
            <div class="note-box">
                <strong style="display:block; margin-bottom: 4px; color: #1e40af;">Note / Details:</strong>
                {!! nl2br(e($reminder->description)) !!}
            </div>
            @endif

            <div class="btn-wrapper">
                <a href="{{ $reminder->id ? route('reminders.show', $reminder) : route('reminders.index') }}" class="btn-action">
                    View Reminder in Dashboard &rarr;
                </a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.<br>
            You received this notification because email alerts were enabled for this reminder.
        </div>
    </div>
</body>
</html>
