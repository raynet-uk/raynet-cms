<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Allow Passport OAuth and OIDC routes through without admin check
        if ($request->is('oauth/*') || $request->is('.well-known/*')) {
            return $next($request);
        }

        $user = $request->user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Admin access required.');
        }
        return $next($request);
    }
}