<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class ForcePasswordChange
{
    /**
     * Force users to change password if flagged or expired.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        // Not logged in → nothing to enforce
        if (! $user) {
            return $next($request);
        }
        // 180-day expiry check
        $expired = $user->password_changed_at
            ? $user->password_changed_at->lt(now()->subDays(180))
            : false;
        if ($user->force_password_reset || $expired) {
            // Allow these routes so they don't get stuck in a loop
            if (! $request->routeIs(
                'password.change',
                'password.update',
                'logout',
                'verification.notice',
                'verification.verify',
                'verification.send',
            )) {
                return redirect()->route('password.change');
            }
        }
        return $next($request);
    }
}