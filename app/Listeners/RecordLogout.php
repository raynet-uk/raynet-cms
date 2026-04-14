<?php

namespace App\Listeners;

use App\Models\LoginHistory;
use Illuminate\Auth\Events\Logout;

class RecordLogout
{
    public function handle(Logout $event): void
    {
        try {
            if (! $event->user) return;

            // Update the most recent successful login for this user that hasn't been logged out yet
            LoginHistory::where('user_id', $event->user->id)
                ->where('successful', true)
                ->whereNull('logged_out_at')
                ->latest('logged_in_at')
                ->first()
                ?->update(['logged_out_at' => now()]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('RecordLogout failed: ' . $e->getMessage());
        }
    }
}
