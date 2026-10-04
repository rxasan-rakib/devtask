<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use App\Services\ReminderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming OTP password reset request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => "We couldn't find an account matching that email address."]);
        }

        $sent = $this->sendOtpToUser($user);

        if (!$sent) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Unable to send OTP email at the moment. Please verify SMTP settings or try again.']);
        }

        return redirect()->route('password.reset.otp', ['email' => $user->email])
            ->with('status', 'A 6-digit OTP code has been sent to your email.');
    }

    /**
     * Resend OTP code.
     */
    public function resendOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        $sent = $this->sendOtpToUser($user);

        if (!$sent) {
            return back()->withErrors(['email' => 'Failed to resend OTP. Please check your mail settings.']);
        }

        return back()->with('status', 'A new 6-digit OTP code has been sent to your email.');
    }

    /**
     * Generate OTP and send via configured mailer.
     */
    protected function sendOtpToUser(User $user): bool
    {
        try {
            $otp = (string) random_int(100000, 999999);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token'      => Hash::make($otp),
                    'created_at' => now(),
                ]
            );

            ReminderService::configureMailer();

            $appName   = Setting::get('app_name', 'WorkFlow Pro');
            $fromEmail = Setting::get('mail_from_address', config('mail.from.address', 'noreply@workflowpro.com'));
            $fromName  = Setting::get('mail_from_name', config('mail.from.name', $appName));
            $subject   = "[{$appName}] Password Reset OTP: {$otp}";

            Mail::send('emails.password_reset_otp', [
                'user'     => $user,
                'userName' => $user->name,
                'otp'      => $otp,
                'appName'  => $appName,
                'subject'  => $subject,
            ], function ($message) use ($user, $subject, $fromEmail, $fromName) {
                $message->to($user->email, $user->name)
                        ->from($fromEmail, $fromName)
                        ->subject($subject);
            });

            Log::info("Password reset OTP sent to {$user->email}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send password reset OTP to {$user->email}: " . $e->getMessage());
            return false;
        }
    }
}
