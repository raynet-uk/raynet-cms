<?php

namespace App\Listeners;

use App\Models\LoginHistory;
use Illuminate\Auth\Events\Failed;

class RecordLoginFailure
{
    public function handle(Failed $event): void
    {
        try {
            LoginHistory::create([
                'user_id'      => $event->user?->id,
                'email'        => $event->credentials['email'] ?? null,
                'ip_address'   => request()->ip(),
                'user_agent'   => request()->userAgent(),
                'successful'   => false,
                'guard'        => $event->guard,
                'logged_in_at' => now(),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('RecordLoginFailure failed: ' . $e->getMessage());
        }
    }
}
