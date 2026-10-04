<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    /**
     * Display the settings management view.
     */
    public function index()
    {
        abort_if(!auth()->user()->can('view settings'), 403, 'Unauthorized access to settings.');

        $allSettings = Setting::all()->pluck('value', 'key');
        return view('settings.index', compact('allSettings'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        abort_if(!auth()->user()->can('edit settings'), 403, 'Unauthorized to modify settings.');

        $group = $request->input('_group', 'all');

        // Validate branding files if present
        $request->validate([
            'app_logo'           => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'app_favicon'        => 'nullable|mimes:ico,png,jpg,jpeg,svg,webp|max:1024',
            'company_email'      => 'nullable|email',
        ]);

        $data = $request->except(['_token', '_method', '_group', 'test_email', 'app_logo', 'app_favicon', 'remove_app_logo', 'remove_app_favicon']);

        // Handle Branding Files Upload
        $uploadDir = public_path('uploads/branding');
        if (!file_exists($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }

        // 1. Logo upload or removal
        if ($request->hasFile('app_logo')) {
            $logoFile = $request->file('app_logo');
            $logoName = 'logo_' . time() . '.' . $logoFile->getClientOriginalExtension();
            $logoFile->move($uploadDir, $logoName);

            $oldLogo = Setting::get('app_logo');
            if ($oldLogo && file_exists(public_path($oldLogo))) {
                @unlink(public_path($oldLogo));
            }
            Setting::set('app_logo', 'uploads/branding/' . $logoName, 'system', 'image');
        } elseif ($request->boolean('remove_app_logo')) {
            $oldLogo = Setting::get('app_logo');
            if ($oldLogo && file_exists(public_path($oldLogo))) {
                @unlink(public_path($oldLogo));
            }
            Setting::set('app_logo', '', 'system', 'image');
        }

        // 2. Favicon upload or removal
        if ($request->hasFile('app_favicon')) {
            $favFile = $request->file('app_favicon');
            $favName = 'favicon_' . time() . '.' . $favFile->getClientOriginalExtension();
            $favFile->move($uploadDir, $favName);

            $oldFav = Setting::get('app_favicon');
            if ($oldFav && file_exists(public_path($oldFav))) {
                @unlink(public_path($oldFav));
            }
            Setting::set('app_favicon', 'uploads/branding/' . $favName, 'system', 'image');
        } elseif ($request->boolean('remove_app_favicon')) {
            $oldFav = Setting::get('app_favicon');
            if ($oldFav && file_exists(public_path($oldFav))) {
                @unlink(public_path($oldFav));
            }
            Setting::set('app_favicon', '', 'system', 'image');
        }

        // Handle boolean checkboxes that might not be present when unchecked
        if ($group === 'notification' || $group === 'all') {
            $notificationKeys = [
                'notify_task_assigned',
                'notify_task_status_changed',
                'notify_task_overdue',
                'notify_reminder_due'
            ];
            foreach ($notificationKeys as $key) {
                $data[$key] = $request->has($key) ? '1' : '0';
            }
        }

        foreach ($data as $key => $value) {
            // Determine group
            $settingGroup = 'system';
            if (str_starts_with($key, 'mail_')) {
                $settingGroup = 'smtp';
            } elseif (str_starts_with($key, 'notify_')) {
                $settingGroup = 'notification';
            }

            Setting::set($key, $value ?? '', $settingGroup);
        }

        Setting::clearCache();

        $anchor = match($group) {
            'smtp' => '#section-smtp',
            'notification' => '#section-notifications',
            default => '#section-general',
        };

        return redirect()->to(route('settings.index') . $anchor)
            ->with('success', 'Settings updated successfully!');
    }

    /**
     * Send a test email to verify SMTP configuration.
     */
    public function sendTestEmail(Request $request)
    {
        abort_if(!auth()->user()->can('send test email') && !auth()->user()->can('edit settings'), 403, 'Unauthorized to send test emails.');

        $request->validate([
            'test_email' => 'required|email',
        ]);

        $recipient = $request->input('test_email');
        $appName   = Setting::get('app_name', 'WorkFlow Pro');

        // Dynamic SMTP configuration from input or database
        $mailer     = $request->input('mail_mailer', Setting::get('mail_mailer', 'smtp'));
        $host       = $request->input('mail_host', Setting::get('mail_host', 'smtp.gmail.com'));
        $port       = (int) $request->input('mail_port', Setting::get('mail_port', 587));
        $username   = $request->input('mail_username', Setting::get('mail_username', ''));
        $password   = $request->input('mail_password', Setting::get('mail_password', ''));
        $encryption = $request->input('mail_encryption', Setting::get('mail_encryption', 'tls'));
        $fromEmail  = $request->input('mail_from_address', Setting::get('mail_from_address', 'noreply@workflowpro.com'));
        $fromName   = $request->input('mail_from_name', Setting::get('mail_from_name', $appName));

        if ($encryption === 'null' || empty($encryption)) {
            $encryption = null;
        }

        // Apply configuration dynamically at runtime
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

        try {
            Mail::raw("Hello,\n\nThis is a test email sent from {$appName} to verify that your SMTP email settings are working correctly.\n\nConfiguration Tested:\n- Mailer: {$mailer}\n- Host: {$host}:{$port}\n- Encryption: " . ($encryption ?: 'None') . "\n- From: {$fromName} <{$fromEmail}>\n- Sent at: " . now()->toDayDateTimeString() . "\n\nBest regards,\n{$appName} Team", function ($message) use ($recipient, $fromEmail, $fromName, $appName) {
                $message->to($recipient)
                        ->from($fromEmail, $fromName)
                        ->subject("{$appName} — SMTP Test Email");
            });

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Test email sent successfully to {$recipient}!",
                ]);
            }

            return redirect()->to(route('settings.index') . '#section-smtp-test')
                ->with('success', "Test email sent successfully to {$recipient}!");
        } catch (\Throwable $e) {
            Log::error('SMTP Test Email Failed: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send test email: ' . $e->getMessage(),
                ], 422);
            }

            return redirect()->to(route('settings.index') . '#section-smtp-test')
                ->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }
}
