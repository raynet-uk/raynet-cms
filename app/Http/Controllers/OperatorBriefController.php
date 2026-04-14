<?php

namespace App\Http\Controllers;

use App\Models\EventAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Public (no auth required) controller for the operator briefing / check-in page.
 * Access is gated by the unguessable briefing_token stored on each assignment.
 */
class OperatorBriefController extends Controller
{
    // ── Show the brief page ───────────────────────────────────────────────────

    public function show(string $token): View
    {
        $assignment = EventAssignment::with(['user', 'event', 'event.type'])
            ->where('briefing_token', $token)
            ->firstOrFail();

        // Prepare event geo data for the geolocation gate
        $event = $assignment->event;

        $eventPolygon = null;
        if ($event->event_polygon) {
            $eventPolygon = is_array($event->event_polygon)
                ? $event->event_polygon
                : json_decode($event->event_polygon, true);
        }

        $eventPin = null;
        if ($event->event_lat && $event->event_lng) {
            $eventPin = ['lat' => (float) $event->event_lat, 'lng' => (float) $event->event_lng];
        }

        $eventPois = null;
        if ($event->event_pois) {
            $eventPois = is_array($event->event_pois)
                ? $event->event_pois
                : json_decode($event->event_pois, true);
        }

        $eventRoute = null;
        if ($event->event_route) {
            $eventRoute = is_array($event->event_route)
                ? $event->event_route
                : json_decode($event->event_route, true);
        }

        return view('operator.brief', compact('assignment', 'eventPolygon', 'eventPin', 'eventPois', 'eventRoute'));
    }

    // ── Attendance actions ────────────────────────────────────────────────────

    public function checkIn(Request $request, string $token): RedirectResponse
    {
        $assignment = $this->findOrFail($token);

        if (! $assignment->canCheckIn()) {
            return back()->with('attend_error', 'You have already checked in.');
        }

        $assignment->recordAttendance('check_in', $request->input('note'));

        return redirect()
            ->route('operator.brief', $token)
            ->with('attend_success', 'Checked in successfully. Good luck!');
    }

    public function breakStart(Request $request, string $token): RedirectResponse
    {
        $assignment = $this->findOrFail($token);

        if (! $assignment->canStartBreak()) {
            return back()->with('attend_error', 'Cannot start a break right now.');
        }

        $assignment->recordAttendance('break_start', $request->input('note'));

        return redirect()
            ->route('operator.brief', $token)
            ->with('attend_success', 'Break started.');
    }

    public function breakEnd(Request $request, string $token): RedirectResponse
    {
        $assignment = $this->findOrFail($token);

        if (! $assignment->canEndBreak()) {
            return back()->with('attend_error', 'No active break to end.');
        }

        $assignment->recordAttendance('break_end', $request->input('note'));

        return redirect()
            ->route('operator.brief', $token)
            ->with('attend_success', 'Break ended — welcome back.');
    }

    public function checkOut(Request $request, string $token): RedirectResponse
    {
        $assignment = $this->findOrFail($token);

        if (! $assignment->canCheckOut()) {
            return back()->with('attend_error', 'Cannot check out right now.');
        }

        $assignment->recordAttendance('check_out', $request->input('note'));

        return redirect()
            ->route('operator.brief', $token)
            ->with('attend_success', 'Checked out. Thanks for your service today!');
    }

    // ── Private ───────────────────────────────────────────────────────────────

    private function findOrFail(string $token): EventAssignment
    {
        return EventAssignment::with(['user', 'event'])
            ->where('briefing_token', $token)
            ->firstOrFail();
    }
}