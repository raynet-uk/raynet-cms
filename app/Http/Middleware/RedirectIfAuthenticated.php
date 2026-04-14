<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * This runs on routes using the "guest" middleware, e.g. /login.
     * If the user is already authenticated, send them to the members’ hub.
     */
    public function handle(Request $request, Closure $next, string ...$guards)
    {
        // Default guard if none provided
        $guards = $guards ?: [null];

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Already logged in – send to members’ hub
                return redirect()->route('members');
            }
        }

        return $next($request);
    }
}