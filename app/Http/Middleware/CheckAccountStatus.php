<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            // 1. Check account lockout or non-active status
            if ($user->status === 'Suspended') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->withErrors([
                    'login' => 'Your account has been suspended. Please contact the administrator.'
                ]);
            }

            if ($user->status === 'Inactive') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->withErrors([
                    'login' => 'Your account is deactivated. Please contact the administrator.'
                ]);
            }

            if ($user->isLocked()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->withErrors([
                    'login' => 'Your account is temporarily locked due to security reasons. Please try again later or contact the administrator.'
                ]);
            }

            // 2. Check forced password change requirement
            if ($user->must_change_password) {
                if (!$request->routeIs('password.force_change*') && !$request->routeIs('logout')) {
                    return redirect()->route('password.force_change')
                        ->with('warning', 'You must change your temporary password before proceeding.');
                }
            }
        }

        return $next($request);
    }
}
