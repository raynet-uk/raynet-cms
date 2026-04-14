<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;

class NotInstalled
{
    public function handle(Request $request, Closure $next)
    {
        try {
            if (Setting::get('installed', '0') === '1') {
                return redirect('/');
            }
        } catch (\Throwable $e) {
            // DB not ready yet — allow install to proceed
        }
        return $next($request);
    }
}
