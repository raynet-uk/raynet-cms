<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ResetAnnualAttendanceStats extends Command
{
    protected $signature   = 'raynet:reset-annual-stats {--force : Run even if it is not 1 September}';
    protected $description = 'Resets annual attendance stats for all users on 1 September each year';

    public function handle(): void
    {
        $today = now();

        // Only run on 1 September unless --force is passed
        if (!$this->option('force') && !($today->month === 9 && $today->day === 1)) {
            $this->info("Not 1 September — skipping. Use --force to run anyway.");
            return;
        }

        $count = User::count();

        User::query()->update([
            'attended_event_this_year'    => false,
            'events_attended_this_year'   => 0,
            'volunteering_hours_this_year' => 0,
        ]);

        $this->info("✓ Annual stats reset for {$count} users on " . $today->format('d M Y') . ".");
    }
}
