<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->canDo($permission)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Unauthorized action.',
                'message' => "You do not have permission to perform [{$permission}]."
            ], 403);
        }

        abort(403, "Access Denied. You do not have the required permission [{$permission}] to access this resource.");
    }
}
