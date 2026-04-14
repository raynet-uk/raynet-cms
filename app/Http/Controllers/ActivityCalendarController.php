<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Carbon\Carbon;

class ActivityCalendarController extends Controller
{
    public function show(Request $request, int $year = null, int $month = null)
    {
        $user = auth()->user();

        // Default to current month
        $year  = $year  ?? now()->year;
        $month = $month ?? now()->month;

        $current   = Carbon::create($year, $month, 1);
        $prevMonth = $current->copy()->subMonth();
        $nextMonth = $current->copy()->addMonth();

        // Logs for this month
        $logs = ActivityLog::where('user_id', $user->id)
            ->whereYear('event_date', $year)
            ->whereMonth('event_date', $month)
            ->orderBy('event_date')
            ->get();

        // Index logs by day-of-month for fast calendar lookup
        $byDay = $logs->groupBy(fn($l) => $l->event_date->day);

        // Calendar grid: pad to start on Monday
        $startDow  = $current->dayOfWeek;                  // 0=Sun…6=Sat
        $startDow  = ($startDow === 0) ? 6 : $startDow - 1; // convert to Mon=0
        $daysInMonth = $current->daysInMonth;

        // Monthly totals
        $monthHours  = round($logs->sum('hours'), 1);
        $monthEvents = $logs->count();

        // Full year summary for the sidebar
        $yearStart = now()->month >= 9
            ? Carbon::create(now()->year, 9, 1)
            : Carbon::create(now()->year - 1, 9, 1);
        $yearEnd   = $yearStart->copy()->addYear()->subDay();
        $yearLabel = $yearStart->format('M Y') . ' – ' . $yearEnd->format('M Y');

        // Monthly breakdown for sidebar (all months in current RAYNET year with any logs)
        $yearLogs = ActivityLog::where('user_id', $user->id)
            ->whereBetween('event_date', [$yearStart->toDateString(), $yearEnd->toDateString()])
            ->get()
            ->groupBy(fn($l) => $l->event_date->format('Y-m'));

        return view('members.activity-calendar', compact(
            'user', 'current', 'prevMonth', 'nextMonth',
            'byDay', 'startDow', 'daysInMonth',
            'monthHours', 'monthEvents', 'logs',
            'yearStart', 'yearEnd', 'yearLabel', 'yearLogs'
        ));
    }
}
