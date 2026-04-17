<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OperatorBriefingMail;
use App\Models\Event;
use App\Models\EventAssignment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EventAssignmentController extends Controller
{
    // ── Index ──────────────────────────────────────────────────────────────────

    public function index(Event $event): View
    {
        $assignments = EventAssignment::with('user')
            ->where('event_id', $event->id)
            ->orderByRaw("FIELD(status,'confirmed','standby','pending','declined')")
            ->orderBy('report_time')
            ->get();

        $assignedIds      = $assignments->pluck('user_id');
        $availableMembers = User::whereNotIn('id', $assignedIds)
            ->orderBy('name')
            ->get(['id', 'name', 'callsign', 'email']);

        // Fetch unavailability periods that overlap the event date range
        $eventFrom = $event->starts_at ? $event->starts_at->toDateString() : null;
        $eventTo   = ($event->ends_at ?? $event->starts_at)?->toDateString();
        $unavailableUserIds = collect();
        if ($eventFrom) {
            $allUserIds = $availableMembers->pluck('id')->merge($assignments->pluck('user_id'));
            $unavailableUserIds = \App\Models\MemberUnavailability::query()
                ->whereIn('user_id', $allUserIds)
                ->where('from_date', '<=', $eventTo ?? $eventFrom)
                ->where('to_date',   '>=', $eventFrom)
                ->pluck('user_id')
                ->unique();
        }

        $pastEvents = Event::whereHas('assignments')
            ->where('id', '!=', $event->id)
            ->orderByDesc('starts_at')
            ->take(20)
            ->get(['id', 'title', 'starts_at']);

        $stats = [
            'total'      => $assignments->count(),
            'confirmed'  => $assignments->where('status', 'confirmed')->count(),
            'standby'    => $assignments->where('status', 'standby')->count(),
            'pending'    => $assignments->where('status', 'pending')->count(),
            'declined'   => $assignments->where('status', 'declined')->count(),
            'mapped'     => $assignments->whereNotNull('lat')->whereNotNull('lng')->count(),
            'vehicles'   => $assignments->where('has_vehicle', true)->count(),
            'first_aid'  => $assignments->where('first_aid_trained', true)->count(),
        ];

        return view('admin.events.assignments', compact(
            'event',
            'assignments',
            'availableMembers',
            'unavailableUserIds',
            'pastEvents',
            'stats',
        ));
    }

    // ── Store ──────────────────────────────────────────────────────────────────

   public function store(Request $request, Event $event)
{
    $request->validate([
        'user_ids'   => 'required|array|min:1',
        'user_ids.*' => 'exists:users,id',
        'status'     => 'nullable|string',
    ]);

    $shared = $request->except('user_ids', '_token');

    foreach ($request->user_ids as $userId) {
        if ($event->assignments()->where('user_id', $userId)->exists()) {
            continue;
        }
        $event->assignments()->create(array_merge($shared, [
            'user_id'         => $userId,
                'coverage_radius_m' => $request->input('coverage_radius_m', 0) ?? 0,
            'shifts'          => $request->shifts_json ? json_decode($request->shifts_json, true) : null,
            'equipment_items' => $request->equipment_items_json ? json_decode($request->equipment_items_json, true) : null,
        ]));
    }

    return redirect()->back()->with('status', 'Crew members assigned successfully.');
}

    // ── Update ─────────────────────────────────────────────────────────────────

    public function update(Request $request, EventAssignment $assignment): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,standby,declined',
        ]);

        $data = $this->prepareAssignmentData($request);

        if ($assignment->status !== $data['status']) {
            $data['status_changed_at'] = now();
        }

        $assignment->update($data);

        return redirect()
            ->route('admin.events.assignments', $assignment->event_id)
            ->with('status', 'Assignment updated successfully.');
    }

    // ── Destroy ────────────────────────────────────────────────────────────────

    public function destroy(EventAssignment $assignment): RedirectResponse
    {
        $eventId = $assignment->event_id;
        $assignment->delete();

        return redirect()
            ->route('admin.events.assignments', $eventId)
            ->with('status', 'Operator removed from event.');
    }

    // ── Update position (AJAX) ─────────────────────────────────────────────────

    public function updatePosition(Request $request, EventAssignment $assignment): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $assignment->update([
            'lat' => $request->input('lat'),
            'lng' => $request->input('lng'),
        ]);

        return response()->json(['ok' => true]);
    }

    // ── Attendance status (AJAX poll for crew map pulse animations) ───────────

    public function attendanceStatus(Event $event): JsonResponse
    {
        $data = EventAssignment::where('event_id', $event->id)
            ->select('id', 'attendance_status')
            ->get()
            ->map(fn($a) => [
                'id'                => $a->id,
                'attendance_status' => $a->attendance_status,
            ]);

        return response()->json($data);
    }

    // ── Reset attendance (admin) ──────────────────────────────────────────────

    public function resetAttendance(EventAssignment $assignment): RedirectResponse
    {
        $assignment->update([
            'attendance_status' => 'not_arrived',
            'attendance_log'    => [],
        ]);

        return back()->with('success', "Attendance reset for {$assignment->user->name}.");
    }

    public function bulkStatus(Request $request, Event $event): JsonResponse
    {
        $request->validate([
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer|exists:event_assignments,id',
            'status' => 'required|in:pending,confirmed,standby,declined',
        ]);

        EventAssignment::whereIn('id', $request->input('ids'))
            ->where('event_id', $event->id)
            ->update([
                'status'            => $request->input('status'),
                'status_changed_at' => now(),
            ]);

        return response()->json(['ok' => true]);
    }

    // ── Duplicate crew ─────────────────────────────────────────────────────────

    public function duplicateCrew(Request $request, Event $event): RedirectResponse
    {
        $request->validate([
            'source_event_id' => 'required|exists:events,id',
        ]);

        $source  = EventAssignment::where('event_id', $request->input('source_event_id'))->get();
        $skipped = 0;
        $copied  = 0;

        foreach ($source as $asgn) {
            if (EventAssignment::where('event_id', $event->id)
                    ->where('user_id', $asgn->user_id)->exists()) {
                $skipped++;
                continue;
            }

            EventAssignment::create(array_merge(
                $asgn->only([
                    'user_id', 'role', 'callsign',
                    'frequency', 'mode', 'ctcss_tone', 'channel_label',
                    'secondary_frequency', 'secondary_mode', 'secondary_ctcss',
                    'fallback_frequency', 'fallback_mode', 'fallback_ctcss',
                    'location_name', 'grid_ref', 'what3words', 'lat', 'lng',
                    'coverage_radius_m', 'has_vehicle', 'vehicle_reg',
                    'first_aid_trained', 'equipment', 'equipment_items', 'briefing_notes',
                ]),
                [
                    'event_id'      => $event->id,
                    'status'        => 'pending',
                    'briefing_sent' => false,
                    'shifts'        => null,
                    'report_time'   => null,
                    'start_time'    => null,
                    'end_time'      => null,
                    'depart_time'   => null,
                ]
            ));

            $copied++;
        }

        $msg = "{$copied} operator(s) copied. All statuses set to Pending.";
        if ($skipped) {
            $msg .= " {$skipped} already-assigned operator(s) skipped.";
        }

        return redirect()
            ->route('admin.events.assignments', $event->id)
            ->with('status', $msg);
    }

    // ── Send briefing emails ───────────────────────────────────────────────────

    public function sendBriefings(Request $request, Event $event): RedirectResponse
    {
        $assignments = EventAssignment::with(['user', 'event'])
            ->where('event_id', $event->id)
            ->whereIn('status', ['confirmed', 'standby'])
            ->get();

        $sent   = 0;
        $failed = 0;

        foreach ($assignments as $asgn) {
            // Skip if no email address
            if (empty($asgn->user->email)) {
                $failed++;
                continue;
            }

            try {
                Mail::to($asgn->user->email, $asgn->user->name)
                    ->send(new OperatorBriefingMail($asgn));

                $asgn->update([
                    'briefing_sent'    => true,
                    'briefing_sent_at' => now(),
                ]);

                $sent++;
            } catch (\Throwable $e) {
                $failed++;
                \Illuminate\Support\Facades\Log::error(
                    'Briefing email failed for assignment ' . $asgn->id,
                    ['error' => $e->getMessage()]
                );
            }
        }

        $msg = "Briefing emails sent to {$sent} operator(s).";
        if ($failed) {
            $msg .= " {$failed} failed (check logs).";
        }

        return redirect()
            ->route('admin.events.assignments', $event->id)
            ->with('status', $msg);
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    private function prepareAssignmentData(Request $request): array
    {
        $shifts = null;
        if ($request->filled('shifts_json')) {
            $decoded = json_decode($request->input('shifts_json'), true);
            if (is_array($decoded)) {
                $shifts = array_values(
                    array_filter($decoded, fn($s) => isset($s['type'])
                        && in_array($s['type'], ['shift', 'break'], true)
                        && !empty($s['start']))
                );
            }
        }

        $equipmentItems = null;
        if ($request->filled('equipment_items_json')) {
            $decoded = json_decode($request->input('equipment_items_json'), true);
            if (is_array($decoded)) {
                $equipmentItems = array_values(
                    array_filter($decoded, fn($v) => is_string($v) && trim($v) !== '')
                );
            }
        }

        return [
            'role'                   => $request->input('role'),
            'callsign'               => $request->input('callsign'),
            'frequency'              => $request->input('frequency') ?: null,
            'mode'                   => $request->input('mode', 'FM'),
            'ctcss_tone'             => $request->input('ctcss_tone') ?: null,
            'channel_label'          => $request->input('channel_label') ?: null,
            'secondary_frequency'    => $request->input('secondary_frequency') ?: null,
            'secondary_mode'         => $request->input('secondary_mode') ?: null,
            'secondary_ctcss'        => $request->input('secondary_ctcss') ?: null,
            'fallback_frequency'     => $request->input('fallback_frequency') ?: null,
            'fallback_mode'          => $request->input('fallback_mode') ?: null,
            'fallback_ctcss'         => $request->input('fallback_ctcss') ?: null,
            'location_name'          => $request->input('location_name') ?: null,
            'grid_ref'               => $request->input('grid_ref') ?: null,
            'what3words'             => $request->input('what3words') ?: null,
            'lat'                    => $request->input('lat') !== '' ? $request->input('lat') : null,
            'lng'                    => $request->input('lng') !== '' ? $request->input('lng') : null,
            'coverage_radius_m'      => (int) $request->input('coverage_radius_m', 0),
            'report_time'            => $request->input('report_time') ?: null,
            'depart_time'            => $request->input('depart_time') ?: null,
            'start_time'             => $this->firstShiftStart($shifts),
            'end_time'               => $this->lastShiftEnd($shifts),
            'shifts'                 => $shifts,
            'has_vehicle'            => $request->boolean('has_vehicle'),
            'vehicle_reg'            => $request->input('vehicle_reg') ?: null,
            'first_aid_trained'      => $request->boolean('first_aid_trained'),
            'equipment'              => $request->input('equipment') ?: null,
            'equipment_items'        => $equipmentItems,
            'briefing_notes'         => $request->input('briefing_notes') ?: null,
            'medical_notes'          => $request->input('medical_notes') ?: null,
            'emergency_contact_name' => $request->input('emergency_contact_name') ?: null,
            'emergency_contact_phone'=> $request->input('emergency_contact_phone') ?: null,
            'status'                 => $request->input('status', 'pending'),
            'status_note'            => $request->input('status_note') ?: null,
        ];
    }

    private function firstShiftStart(?array $shifts): ?string
    {
        if (empty($shifts)) return null;
        foreach ($shifts as $s) {
            if (($s['type'] ?? 'shift') === 'shift' && !empty($s['start'])) {
                return $s['start'];
            }
        }
        return null;
    }

    private function lastShiftEnd(?array $shifts): ?string
    {
        if (empty($shifts)) return null;
        $last = null;
        foreach ($shifts as $s) {
            if (($s['type'] ?? 'shift') === 'shift' && !empty($s['end'])) {
                $last = $s['end'];
            }
        }
        return $last;
    }
}
    // ── Notify crew ────────────────────────────────────────────────────────────
    public function notifyCrew(Request $request, \App\Models\Event $event)
    {
        $request->validate([
            'notify_type'    => 'required|in:custom,reminder',
            'custom_message' => 'required_if:notify_type,custom|nullable|string|max:2000',
            'notify_status'  => 'required|array',
        ]);

        $statuses    = $request->notify_status;
        $assignments = $event->assignments()
            ->with('user', 'event')
            ->whereIn('status', $statuses)
            ->get();

        $sent = 0;
        foreach ($assignments as $assignment) {
            if (!$assignment->user->email) continue;
            \Illuminate\Support\Facades\Mail::to($assignment->user->email)
                ->send(new \App\Mail\CrewNotification(
                    $assignment,
                    $request->notify_type,
                    $request->custom_message ?? ''
                ));
            $sent++;
        }

        return redirect()->back()->with('status', "Notification sent to {$sent} crew member(s).");
    }
