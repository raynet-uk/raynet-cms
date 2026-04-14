<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;

class RedirectIfNotInstalled
{
    public function handle(Request $request, Closure $next)
    {
        // Skip if already on install route
        if ($request->is('install*')) {
            return $next($request);
        }

        try {
            if (Setting::get('installed', '0') !== '1') {
                return redirect()->route('install.index');
            }
        } catch (\Throwable $e) {
            // DB not ready — redirect to install
            return redirect()->route('install.index');
        }

        return $next($request);
    }
}
