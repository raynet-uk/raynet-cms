<?php

namespace App\Listeners;

use App\Models\LoginHistory;
use Illuminate\Auth\Events\Login;

class RecordLoginHistory
{
    public function handle(Login $event): void
    {
        try {
            LoginHistory::create([
                'user_id'      => $event->user->id,
                'email'        => $event->user->email,
                'ip_address'   => request()->ip(),
                'user_agent'   => request()->userAgent(),
                'successful'   => true,
                'guard'        => $event->guard,
                'logged_in_at' => now(),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('RecordLoginHistory failed: ' . $e->getMessage());
        }
    }
}
