<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the OTP password reset view.
     */
    public function createOtp(Request $request): View
    {
        $email = $request->query('email') ?? session('password_reset_email') ?? old('email');
        return view('auth.reset-password-otp', compact('email'));
    }

    /**
     * Handle verification of OTP and password update.
     */
    public function storeOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'email', 'exists:users,email'],
            'otp'      => ['required', 'string', 'digits:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'otp.digits' => 'The OTP code must be exactly 6 digits.',
        ]);

        $tokenRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$tokenRecord) {
            return back()->withInput($request->only('email'))
                ->withErrors(['otp' => 'No active OTP request found for this email. Please request a new OTP.']);
        }

        // Check 10-minute expiry
        $createdAt = Carbon::parse($tokenRecord->created_at);
        if ($createdAt->addMinutes(10)->isPast()) {
            return back()->withInput($request->only('email'))
                ->withErrors(['otp' => 'This OTP code has expired. Please click "Resend Code" to get a new one.']);
        }

        // Check OTP match
        if (!Hash::check($request->otp, $tokenRecord->token) && $tokenRecord->token !== $request->otp) {
            return back()->withInput($request->only('email'))
                ->withErrors(['otp' => 'Invalid OTP code. Please enter the correct 6-digit code received in your email.']);
        }

        // OTP is valid, reset password
        $user = User::where('email', $request->email)->firstOrFail();
        $user->forceFill([
            'password'       => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        // Delete used OTP
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        event(new PasswordReset($user));

        return redirect()->route('login')
            ->with('status', 'Your password has been successfully reset! You can now log in.');
    }
}
