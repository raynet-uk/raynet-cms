<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class CheckSuspended
{
    /**
     * Routes the suspended user can still access so they aren't
     * completely locked out of all HTTP responses.
     */
    protected array $except = [
        'logout',
        'admin/login',
        'admin/logout',
        'suspended',
        'email/verify',
        'email/verify/*',
        'email/verification-notification',
    ];
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && $user->suspended_at !== null) {
            // Allow logout and the suspended page itself through
            foreach ($this->except as $path) {
                if ($request->is($path)) {
                    return $next($request);
                }
            }
            // If it's an AJAX/JSON request, return a 403 JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your account has been suspended.',
                ], 403);
            }
            return response()->view('auth.suspended', [
                'message' => $user->suspension_message
                    ?: 'Your account has been suspended. Please contact an administrator.',
            ], 403);
        }
        return $next($request);
    }
}