<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\LoginHistory;
use App\Models\AuditLog;
use App\Models\SecuritySetting;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
            'captcha' => 'required|integer',
        ]);

        $captchaCode = session('captcha_code');
        if ($request->captcha != $captchaCode) {
            $this->generateCaptcha();
            return back()->withErrors(['captcha' => 'Incorrect security verification math challenge.'])->withInput();
        }

        $loginInput = trim($request->login);

        // Find user by email, username, or name
        $user = User::where('email', $loginInput)
            ->orWhere('username', $loginInput)
            ->orWhere('name', $loginInput)
            ->first();

        $maxAttempts = (int) SecuritySetting::get('max_login_attempts', 5);
        $lockoutMinutes = (int) SecuritySetting::get('lockout_minutes', 15);

        // If user doesn't exist at all
        if (!$user) {
            LoginHistory::create([
                'user_id' => null,
                'identifier' => $loginInput,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'Failed',
                'failure_reason' => 'User does not exist',
                'created_at' => now(),
            ]);

            $this->generateCaptcha();
            return back()->withErrors([
                'login' => 'The provided credentials do not match our records.',
            ])->onlyInput('login');
        }

        // Check if account is locked
        if ($user->isLocked()) {
            LoginHistory::create([
                'user_id' => $user->id,
                'identifier' => $loginInput,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'Locked',
                'failure_reason' => 'Account is currently locked',
                'created_at' => now(),
            ]);

            $lockExpiry = $user->locked_until ? $user->locked_until->diffForHumans() : 'until unlocked by administrator';
            $this->generateCaptcha();
            return back()->withErrors([
                'login' => "Your account is temporarily locked ({$lockExpiry}). Please contact your system administrator.",
            ])->onlyInput('login');
        }

        // Check if account is suspended or inactive
        if ($user->status === 'Suspended') {
            LoginHistory::create([
                'user_id' => $user->id,
                'identifier' => $loginInput,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'Failed',
                'failure_reason' => 'Account is suspended',
                'created_at' => now(),
            ]);

            $this->generateCaptcha();
            return back()->withErrors([
                'login' => 'Your account has been suspended. Please contact the administrator.',
            ])->onlyInput('login');
        }

        if ($user->status === 'Inactive') {
            LoginHistory::create([
                'user_id' => $user->id,
                'identifier' => $loginInput,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'Failed',
                'failure_reason' => 'Account is inactive',
                'created_at' => now(),
            ]);

            $this->generateCaptcha();
            return back()->withErrors([
                'login' => 'Your account is deactivated. Please contact the administrator.',
            ])->onlyInput('login');
        }

        // Verify password
        if (!Hash::check($request->password, $user->password_hash)) {
            $user->increment('failed_login_attempts');
            $newAttempts = $user->failed_login_attempts;

            if ($newAttempts >= $maxAttempts) {
                $user->update([
                    'status' => 'Locked',
                    'locked_until' => now()->addMinutes($lockoutMinutes),
                ]);

                LoginHistory::create([
                    'user_id' => $user->id,
                    'identifier' => $loginInput,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'status' => 'Locked',
                    'failure_reason' => "Exceeded maximum failed attempts ({$maxAttempts})",
                    'created_at' => now(),
                ]);

                AuditLog::create([
                    'user_id' => $user->id,
                    'action' => 'account_locked',
                    'module' => 'users',
                    'table_name' => 'users',
                    'record_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'description' => "Account locked after {$maxAttempts} failed login attempts",
                    'created_at' => now(),
                ]);

                $this->generateCaptcha();
                return back()->withErrors([
                    'login' => "Too many failed attempts. Your account has been temporarily locked for {$lockoutMinutes} minutes.",
                ])->onlyInput('login');
            }

            LoginHistory::create([
                'user_id' => $user->id,
                'identifier' => $loginInput,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'Failed',
                'failure_reason' => 'Incorrect password',
                'created_at' => now(),
            ]);

            $remaining = $maxAttempts - $newAttempts;
            $this->generateCaptcha();
            return back()->withErrors([
                'login' => "Incorrect credentials. {$remaining} attempt(s) remaining before account lockout.",
            ])->onlyInput('login');
        }

        // Authentication Successful
        $user->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        LoginHistory::create([
            'user_id' => $user->id,
            'identifier' => $loginInput,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'Success',
            'failure_reason' => null,
            'created_at' => now(),
        ]);

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'user_login',
            'module' => 'auth',
            'table_name' => 'users',
            'record_id' => $user->id,
            'ip_address' => $request->ip(),
            'description' => 'User logged into system successfully',
            'created_at' => now(),
        ]);

        if ($user->must_change_password) {
            return redirect()->route('password.force_change')
                ->with('warning', 'Notice: You are using a temporary password. You must set a new password before continuing.');
        }

        if (!$user->isSuperAdmin() && !$user->canDo('dashboard.view')) {
            return redirect()->to($user->getLandingUrl());
        }

        return redirect()->intended(route('dashboard'));
    }


    public function logout(Request $request)
    {
        $userId = Auth::id();

        if ($userId) {
            AuditLog::create([
                'user_id' => $userId,
                'action' => 'user_logout',
                'module' => 'auth',
                'table_name' => 'users',
                'record_id' => $userId,
                'ip_address' => $request->ip(),
                'description' => 'User logged out of system',
                'created_at' => now(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been successfully signed out.');
    }

    public function showForceChangePassword()
    {
        return view('auth.force_change_password');
    }

    public function forceChangePassword(Request $request)
    {
        $minLength = (int) SecuritySetting::get('password_min_length', 8);

        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'string', 'confirmed', 'min:' . $minLength],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password_hash)) {
            return back()->withErrors(['current_password' => 'The provided current password is incorrect.']);
        }

        if (Hash::check($request->password, $user->password_hash)) {
            return back()->withErrors(['password' => 'Your new password cannot be identical to your temporary password.']);
        }

        $user->update([
            'password_hash' => Hash::make($request->password),
            'must_change_password' => false,
            'password_changed_at' => now(),
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'force_password_changed',
            'module' => 'users',
            'table_name' => 'users',
            'record_id' => $user->id,
            'ip_address' => $request->ip(),
            'description' => 'User replaced temporary password with a permanent secure password',
            'created_at' => now(),
        ]);

        $landingUrl = ($user->isSuperAdmin() || $user->canDo('dashboard.view')) ? route('dashboard') : $user->getLandingUrl();
        return redirect()->to($landingUrl)->with('success', 'Your password has been successfully updated.');
    }


    public function showForgotPassword()
    {
        return view('auth.forgot_password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();

        if ($user) {
            $token = Str::random(64);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                ['token' => Hash::make($token), 'created_at' => now()]
            );

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'password_reset_requested',
                'module' => 'auth',
                'table_name' => 'password_reset_tokens',
                'record_id' => $user->id,
                'ip_address' => $request->ip(),
                'description' => "Password reset token requested for {$email}",
                'created_at' => now(),
            ]);

            // Flash a test link so developers / local testers can test without external SMTP server!
            $resetUrl = route('password.reset', ['token' => $token, 'email' => $email]);
            session()->flash('demo_reset_url', $resetUrl);
        }

        return back()->with('status', 'If your email address is registered, a password reset link has been dispatched.');
    }

    public function showResetPassword(Request $request, $token)
    {
        return view('auth.reset_password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $minLength = (int) SecuritySetting::get('password_min_length', 8);

        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'string', 'confirmed', 'min:' . $minLength],
        ]);

        $email = strtolower(trim($request->email));
        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'This password reset token is invalid or has expired.']);
        }

        // Check 60 minute token expiration
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return back()->withErrors(['email' => 'This password reset link has expired. Please request a new one.']);
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'We could not find an account with that email address.']);
        }

        $user->update([
            'password_hash' => Hash::make($request->password),
            'must_change_password' => false,
            'password_changed_at' => now(),
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'status' => 'Active',
        ]);

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'password_reset_completed',
            'module' => 'auth',
            'table_name' => 'users',
            'record_id' => $user->id,
            'ip_address' => $request->ip(),
            'description' => "Password reset completed via token for {$email}",
            'created_at' => now(),
        ]);

        return redirect()->route('login')->with('success', 'Your password has been successfully reset! Please sign in with your new password.');
    }

    public function generateCaptchaApi()
    {
        $this->generateCaptcha();
        return response()->json([
            'question' => session('captcha_num1') . ' + ' . session('captcha_num2') . ' = ?',
        ]);
    }

    private function generateCaptcha()
    {
        $n1 = rand(2, 9);
        $n2 = rand(1, 9);
        session(['captcha_num1' => $n1, 'captcha_num2' => $n2, 'captcha_code' => $n1 + $n2]);
    }
}
