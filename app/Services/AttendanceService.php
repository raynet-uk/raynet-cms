<?php

namespace App\Services;

use App\Models\User;
use App\Models\ActivityLog;
use Carbon\Carbon;

class AttendanceService
{
    /**
     * Mark a user as having attended an event, credit their hours,
     * and write an individual ActivityLog row for the calendar.
     *
     * @param User        $user
     * @param float       $eventHours
     * @param string|null $eventName   Optional label shown on the calendar
     * @param string|null $eventDate   ISO date string — defaults to today
     * @param int|null    $loggedBy    Admin user ID
     */
    public static function recordAttendance(
        User    $user,
        float   $eventHours,
        ?string $eventName = null,
        ?string $eventDate = null,
        ?int    $loggedBy  = null
    ): void {
        // Write the individual log entry for the calendar
        ActivityLog::create([
            'user_id'    => $user->id,
            'event_name' => $eventName,
            'event_date' => $eventDate ?? now()->toDateString(),
            'hours'      => $eventHours,
            'logged_by'  => $loggedBy,
        ]);

        // Update the annual counter columns on the user
        $user->attended_event_this_year     = true;
        $user->events_attended_this_year    = $user->events_attended_this_year + 1;
        $user->volunteering_hours_this_year = round(
            $user->volunteering_hours_this_year + $eventHours, 1
        );
        $user->save();
    }

    /**
     * Reverse a previously recorded attendance (e.g. admin unchecks a member).
     *
     * @param User  $user
     * @param float $eventHours  Hours to deduct
     */
    public static function removeAttendance(User $user, float $eventHours): void
    {
        $newCount = max(0, $user->events_attended_this_year - 1);
        $newHours = max(0, round($user->volunteering_hours_this_year - $eventHours, 1));

        $user->events_attended_this_year    = $newCount;
        $user->volunteering_hours_this_year = $newHours;
        $user->attended_event_this_year     = $newCount > 0;
        $user->save();
    }

    /**
     * Remove an ActivityLog entry and deduct from the user's annual totals.
     * Use this when deleting a specific log row.
     */
    public static function removeLog(ActivityLog $log): void
    {
        $user = User::find($log->user_id);

        if ($user) {
            self::removeAttendance($user, $log->hours);
        }

        $log->delete();
    }

    /**
     * Rebuild the user's annual counters from the activity_logs table.
     * Use after a manual override or data import to resync totals.
     */
    public static function rebuildAnnualTotals(User $user): void
    {
        $yearStart = self::currentYearStart();
        $yearEnd   = $yearStart->copy()->addYear()->subDay();

        $logs = ActivityLog::where('user_id', $user->id)
            ->whereBetween('event_date', [$yearStart->toDateString(), $yearEnd->toDateString()])
            ->get();

        $user->events_attended_this_year    = $logs->count();
        $user->volunteering_hours_this_year = round($logs->sum('hours'), 1);
        $user->attended_event_this_year     = $logs->count() > 0;
        $user->save();
    }

    /**
     * Recalculate stats for a user from scratch based on an event_attendances pivot.
     * Kept for compatibility — use rebuildAnnualTotals() for activity_logs-based rebuilds.
     */
    public static function recalculateFromAttendances(User $user): void
    {
        $yearStart = self::currentYearStart();
        $yearEnd   = $yearStart->copy()->addYear()->subDay();

        $rows = \DB::table('event_attendances')
            ->join('events', 'events.id', '=', 'event_attendances.event_id')
            ->where('event_attendances.user_id', $user->id)
            ->where('event_attendances.attended', true)
            ->whereBetween('events.event_date', [$yearStart, $yearEnd])
            ->select('events.hours')
            ->get();

        $user->events_attended_this_year    = $rows->count();
        $user->volunteering_hours_this_year = round($rows->sum('hours'), 1);
        $user->attended_event_this_year     = $rows->count() > 0;
        $user->save();
    }

    /**
     * Returns the start of the current RAYNET year (1 September).
     */
    public static function currentYearStart(): Carbon
    {
        $now = now();
        return $now->month >= 9
            ? Carbon::create($now->year, 9, 1)
            : Carbon::create($now->year - 1, 9, 1);
    }
}