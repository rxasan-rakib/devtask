<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\Reminder;
use App\Models\Setting;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ReminderService
{
    /**
     * Configure runtime SMTP mailer based on stored settings.
     */
    public static function configureMailer(): void
    {
        $mailer     = Setting::get('mail_mailer', config('mail.default', 'smtp'));
        $host       = Setting::get('mail_host', config('mail.mailers.smtp.host', 'smtp.gmail.com'));
        $port       = (int) Setting::get('mail_port', config('mail.mailers.smtp.port', 587));
        $username   = Setting::get('mail_username', config('mail.mailers.smtp.username', ''));
        $password   = Setting::get('mail_password', config('mail.mailers.smtp.password', ''));
        $encryption = Setting::get('mail_encryption', config('mail.mailers.smtp.encryption', 'tls'));
        $appName    = Setting::get('app_name', config('app.name', 'WorkFlow Pro'));
        $fromEmail  = Setting::get('mail_from_address', config('mail.from.address', 'noreply@workflowpro.com'));
        $fromName   = Setting::get('mail_from_name', $appName);

        if ($encryption === 'null' || empty($encryption) || $encryption === 'none') {
            $encryption = null;
        }

        Config::set('mail.default', $mailer);
        Config::set('mail.mailers.smtp', [
            'transport'  => 'smtp',
            'host'       => $host,
            'port'       => $port,
            'encryption' => $encryption,
            'username'   => $username,
            'password'   => $password,
            'timeout'    => 15,
            'auth_mode'  => null,
            'stream'     => [
                'ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                ],
            ],
        ]);
        Config::set('mail.from.address', $fromEmail);
        Config::set('mail.from.name', $fromName);

        // Purge cached MailManager transport instances so runtime config takes immediate effect
        try {
            Mail::purge('smtp');
            Mail::purge();
        } catch (\Throwable $e) {
            // Ignore if mail purge is not supported
        }
    }

    /**
     * Process and dispatch all due reminders and overdue tasks.
     *
     * @param int|null $userId Optional filter for a specific user.
     * @return array List of processed notification summaries for in-app alerting.
     */
    public static function processDueReminders(?int $userId = null): array
    {
        // 1. Configure mailer once
        static::configureMailer();

        $appName   = Setting::get('app_name', 'WorkFlow Pro');
        $fromEmail = Setting::get('mail_from_address', config('mail.from.address', 'noreply@workflowpro.com'));
        $fromName  = Setting::get('mail_from_name', $appName);
        $emailReminderEnabled = Setting::get('notify_reminder_due', '1') === '1';
        $emailTaskOverdueEnabled = Setting::get('notify_task_overdue', '1') === '1';

        $processedList = [];

        // ─── PART A: Process Due Reminders ───
        $query = Reminder::with(['user', 'client', 'task'])
            ->active() // Only not-done / not-completed reminders
            ->where('remind_at', '<=', now());

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $dueReminders = $query->get();

        foreach ($dueReminders as $reminder) {
            $user = $reminder->user;

            // 1. Create In-App Notification if not already generated
            $existingNotif = AppNotification::where('user_id', $reminder->user_id)
                ->where('type', 'reminder')
                ->whereJsonContains('data->reminder_id', $reminder->id)
                ->first();

            $isFirstAlert = false;
            if (!$existingNotif) {
                $isFirstAlert = true;
                AppNotification::create([
                    'user_id'    => $reminder->user_id,
                    'type'       => 'reminder',
                    'title'      => 'Reminder: ' . $reminder->title,
                    'message'    => $reminder->description ?: "Reminder scheduled for {$reminder->remind_at->format('d M, Y h:i A')}",
                    'action_url' => route('reminders.show', $reminder),
                    'icon'       => $reminder->type_icon ?: 'bell',
                    'color'      => '#ff7828',
                    'data'       => [
                        'reminder_id' => $reminder->id,
                        'type'        => $reminder->type,
                        'type_label'  => $reminder->type_label,
                        'remind_at'   => $reminder->remind_at->toDateTimeString(),
                    ],
                    'read_at'    => null, // Must remain UNREAD until user clicks/taps it
                ]);
            }

            // 2. Dispatch Email (only once per reminder)
            if ($user && !empty($user->email) && $reminder->notify_email && $emailReminderEnabled && !$reminder->email_sent) {
                try {
                    $subject = "[{$appName} Reminder] {$reminder->title} ({$reminder->type_label})";

                    Mail::send('emails.reminder_alert', [
                        'reminder' => $reminder,
                        'user'     => $user,
                        'appName'  => $appName,
                        'subject'  => $subject,
                    ], function ($message) use ($user, $subject, $fromEmail, $fromName) {
                        $message->to($user->email, $user->name)
                                ->from($fromEmail, $fromName)
                                ->subject($subject);
                    });

                    $reminder->update([
                        'email_sent'    => true,
                        'email_sent_at' => now(),
                        'notified_at'   => now(),
                    ]);

                    Log::info("Reminder email sent for Reminder #{$reminder->id} to {$user->email}");
                } catch (\Throwable $e) {
                    Log::error("Failed sending reminder email #{$reminder->id}: " . $e->getMessage());
                }
            } else {
                if (!$reminder->notified_at) {
                    $reminder->update(['notified_at' => now()]);
                }
            }

            // 3. Add to live alert prompt if this was newly triggered or within recent minutes
            if ($isFirstAlert || ($reminder->notified_at && $reminder->notified_at->diffInMinutes(now()) <= 2)) {
                $processedList[] = [
                    'id'             => 'reminder_' . $reminder->id,
                    'type'           => 'reminder',
                    'title'          => $reminder->title,
                    'type_label'     => $reminder->type_label,
                    'type_icon'      => $reminder->type_icon,
                    'client_name'    => $reminder->client ? ($reminder->client->company_name ?? $reminder->client->name) : null,
                    'task_title'     => $reminder->task ? $reminder->task->title : null,
                    'remind_at'      => $reminder->remind_at->format('d M, h:i A'),
                    'description'    => $reminder->description,
                    'url'            => route('reminders.show', $reminder),
                    'notify_browser' => (bool) $reminder->notify_browser,
                ];
            }

            // 4. Handle recurring reminders without auto-marking done
            if ($reminder->is_recurring && $reminder->recur_interval && $reminder->email_sent) {
                $nextTime = match ($reminder->recur_interval) {
                    'daily'   => $reminder->remind_at->copy()->addDay(),
                    'weekly'  => $reminder->remind_at->copy()->addWeek(),
                    'monthly' => $reminder->remind_at->copy()->addMonth(),
                    default   => null,
                };

                if ($nextTime && $nextTime->isFuture()) {
                    $reminder->update([
                        'remind_at'   => $nextTime,
                        'email_sent'  => false,
                        'notified_at' => null,
                    ]);
                }
            }
        }

        // ─── PART B: Process Overdue Tasks ───
        $taskQuery = Task::with(['assignedTo', 'createdBy', 'client', 'taskType'])
            ->where('status', '!=', 'completed')
            ->whereNotNull('deadline')
            ->where('deadline', '<=', now());

        if ($userId) {
            $taskQuery->where(function($tq) use ($userId) {
                $tq->where('assigned_to', $userId)->orWhere('created_by', $userId);
            });
        }

        $overdueTasks = $taskQuery->get();

        foreach ($overdueTasks as $task) {
            // Find target users to notify (assigned user and creator)
            $targetUserIds = array_filter(array_unique([$task->assigned_to, $task->created_by]));
            if (empty($targetUserIds) && $userId) {
                $targetUserIds = [$userId];
            }

            foreach ($targetUserIds as $targetId) {
                if ($userId && $targetId !== $userId) {
                    continue;
                }

                $targetUser = User::find($targetId);
                if (!$targetUser) continue;

                // Check if notification already created in last 24 hours to prevent spamming
                $existingTaskNotif = AppNotification::where('user_id', $targetId)
                    ->where('type', 'task_deadline')
                    ->whereJsonContains('data->task_id', $task->id)
                    ->where('created_at', '>=', now()->subHours(24))
                    ->first();

                if (!$existingTaskNotif) {
                    AppNotification::create([
                        'user_id'    => $targetId,
                        'type'       => 'task_deadline',
                        'title'      => 'Task Overdue: ' . $task->title,
                        'message'    => "Deadline was {$task->deadline->format('d M, Y')}. Priority: " . ucfirst($task->priority) . ", Status: " . ucwords(str_replace('_', ' ', $task->status)),
                        'action_url' => route('tasks.show', $task),
                        'icon'       => 'alert-triangle',
                        'color'      => '#ef4444',
                        'data'       => [
                            'task_id'  => $task->id,
                            'priority' => $task->priority,
                            'status'   => $task->status,
                            'deadline' => $task->deadline->toDateTimeString(),
                        ],
                        'read_at'    => null,
                    ]);

                    // Send email if enabled
                    if ($emailTaskOverdueEnabled && !empty($targetUser->email)) {
                        try {
                            $subject = "[{$appName} Alert] Task Overdue: {$task->title}";
                            Mail::send('emails.task_alert', [
                                'task'             => $task,
                                'user'             => $targetUser,
                                'appName'          => $appName,
                                'subject'          => $subject,
                                'alertTypeLabel'   => 'Task Deadline Alert',
                                'badgeLabel'       => '⚠️ OVERDUE TASK',
                                'alertMessage'     => 'this task has exceeded its scheduled deadline and requires your attention.',
                            ], function ($message) use ($targetUser, $subject, $fromEmail, $fromName) {
                                $message->to($targetUser->email, $targetUser->name)
                                        ->from($fromEmail, $fromName)
                                        ->subject($subject);
                            });
                            Log::info("Task overdue email sent for Task #{$task->id} to {$targetUser->email}");
                        } catch (\Throwable $e) {
                            Log::error("Failed sending task overdue email #{$task->id}: " . $e->getMessage());
                        }
                    }

                    $processedList[] = [
                        'id'             => 'task_' . $task->id,
                        'type'           => 'task_deadline',
                        'title'          => 'Overdue: ' . $task->title,
                        'type_label'     => 'Task Overdue',
                        'type_icon'      => 'alert-triangle',
                        'client_name'    => $task->client ? ($task->client->company_name ?? $task->client->name) : null,
                        'task_title'     => $task->title,
                        'remind_at'      => $task->deadline->format('d M, h:i A'),
                        'description'    => "Task deadline passed on " . $task->deadline->format('d M, Y'),
                        'url'            => route('tasks.show', $task),
                        'notify_browser' => true,
                    ];
                }
            }
        }

        return $processedList;
    }
}
