<?php
namespace App\Http\Controllers;

use App\Models\EventAssignment;
use App\Models\OperatorGpsPing;
use App\Models\OperatorMessage;
use App\Models\OperatorMessageAck;
use App\Models\OperatorSosAlert;
use App\Models\OperatorWelfareCheck;
use App\Models\OperatorWelfareResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class OperatorTrackingController extends Controller
{
    // ── GPS Ping from operator brief ─────────────────────────────────────────
    public function ping(Request $request, string $token): JsonResponse
    {
        $assignment = EventAssignment::with('event')
            ->where('briefing_token', $token)->firstOrFail();

        $data = $request->validate([
            'lat'         => 'required|numeric|between:-90,90',
            'lng'         => 'required|numeric|between:-180,180',
            'accuracy'    => 'nullable|numeric',
            'heading'     => 'nullable|numeric',
            'speed'       => 'nullable|numeric',
            'battery'     => 'nullable|integer|between:0,100',
        ]);

        OperatorGpsPing::create([
            'assignment_id'    => $assignment->id,
            'event_id'         => $assignment->event_id,
            'lat'              => $data['lat'],
            'lng'              => $data['lng'],
            'accuracy_m'       => isset($data['accuracy']) ? (int)$data['accuracy'] : null,
            'heading'          => isset($data['heading']) ? (int)$data['heading'] : null,
            'speed_ms'         => isset($data['speed']) ? (int)($data['speed'] * 10) : null,
            'battery_pct'      => $data['battery'] ?? null,
            'is_dead_reckoned' => false,
            'pinged_at'        => now(),
        ]);

        // Check for geofence breach
        $geofenceAlert = null;
        if ($assignment->lat && $assignment->lng && $assignment->coverage_radius_m > 0) {
            $dist = $this->haversineMetres($data['lat'], $data['lng'], $assignment->lat, $assignment->lng);
            if ($dist > $assignment->coverage_radius_m * 1.2) {
                $geofenceAlert = [
                    'distance_m' => (int)$dist,
                    'radius_m'   => $assignment->coverage_radius_m,
                ];
            }
        }

        // Return any pending messages for this operator
        $ackedIds = OperatorMessageAck::where('assignment_id', $assignment->id)
            ->pluck('message_id')->toArray();

        $messages = OperatorMessage::where('event_id', $assignment->event_id)
            ->where(function($q) use ($assignment) {
                $q->whereNull('assignment_id')
                  ->orWhere('assignment_id', $assignment->id);
            })
            ->whereNotIn('id', $ackedIds)
            ->orderBy('created_at')
            ->get(['id','type','body','payload','requires_ack','created_at'])
            ->map(fn($m) => [
                'id'           => $m->id,
                'type'         => $m->type,
                'body'         => $m->body,
                'payload'      => $m->payload,
                'requires_ack' => $m->requires_ack,
                'sent_at'      => $m->created_at->toIso8601String(),
            ]);

        // Check for pending welfare check
        $welfarePrompt = null;
        $welfareCheck = OperatorWelfareCheck::where('event_id', $assignment->event_id)
            ->where('active', true)->latest()->first();
        if ($welfareCheck) {
            $existing = OperatorWelfareResponse::where('welfare_check_id', $welfareCheck->id)
                ->where('assignment_id', $assignment->id)
                ->where('responded', false)
                ->latest('prompted_at')->first();
            if ($existing && $existing->prompted_at->diffInMinutes(now()) < 10) {
                $welfarePrompt = ['response_id' => $existing->id];
            }
        }

        return response()->json([
            'ok'             => true,
            'messages'       => $messages,
            'geofence_alert' => $geofenceAlert,
            'welfare_prompt' => $welfarePrompt,
        ]);
    }

    // ── SOS Alert ────────────────────────────────────────────────────────────
    public function sos(Request $request, string $token): JsonResponse
    {
        $assignment = EventAssignment::with(['user', 'event'])
            ->where('briefing_token', $token)->firstOrFail();

        $data = $request->validate([
            'lat'     => 'nullable|numeric',
            'lng'     => 'nullable|numeric',
            'message' => 'nullable|string|max:500',
        ]);

        $alert = OperatorSosAlert::create([
            'assignment_id' => $assignment->id,
            'event_id'      => $assignment->event_id,
            'lat'           => $data['lat'] ?? null,
            'lng'           => $data['lng'] ?? null,
            'message'       => $data['message'] ?? null,
        ]);

        // Email all admins/super-admins
        try {
            $admins = \App\Models\User::role(['admin','super-admin'])->get();
            $callsign = $assignment->callsign ?: ($assignment->user->callsign ?? $assignment->user->name);
            $location = $data['lat'] && $data['lng']
                ? "https://maps.google.com/?q={$data['lat']},{$data['lng']}"
                : 'Location unknown';
            foreach ($admins as $admin) {
                if (!$admin->email) continue;
                Mail::send([], [], function($m) use ($admin, $assignment, $callsign, $location, $alert, $data) {
                    $m->to($admin->email, $admin->name)
                      ->subject("🆘 SOS ALERT — {$callsign} — " . \App\Helpers\RaynetSetting::groupName())
                      ->html("
                        <h2 style='color:#C8102E;'>🆘 SOS Alert</h2>
                        <p><strong>Operator:</strong> {$callsign}</p>
                        <p><strong>Event:</strong> {$assignment->event->title}</p>
                        <p><strong>Position:</strong> {$assignment->location_name}</p>
                        <p><strong>Message:</strong> " . ($data['message'] ?? 'No message') . "</p>
                        <p><strong>Location:</strong> <a href='{$location}'>{$location}</a></p>
                        <p><strong>Time:</strong> " . now()->format('H:i:s d/m/Y') . "</p>
                        <p><a href='" . url('/admin/events/'.$assignment->event_id.'/live') . "' style='background:#C8102E;color:#fff;padding:10px 20px;text-decoration:none;border-radius:4px;'>View Live Ops Dashboard</a></p>
                      ");
                });
            }
        } catch (\Throwable $e) {}

        return response()->json(['ok' => true, 'alert_id' => $alert->id]);
    }

    // ── Acknowledge message ──────────────────────────────────────────────────
    public function ackMessage(Request $request, string $token, int $messageId): JsonResponse
    {
        $assignment = EventAssignment::where('briefing_token', $token)->firstOrFail();

        OperatorMessageAck::firstOrCreate([
            'message_id'    => $messageId,
            'assignment_id' => $assignment->id,
        ], ['acked_at' => now()]);

        return response()->json(['ok' => true]);
    }

    // ── Welfare check response ───────────────────────────────────────────────
    public function welfareOk(Request $request, string $token): JsonResponse
    {
        $assignment = EventAssignment::where('briefing_token', $token)->firstOrFail();
        $responseId = $request->input('response_id');

        OperatorWelfareResponse::where('id', $responseId)
            ->where('assignment_id', $assignment->id)
            ->update(['responded' => true, 'responded_at' => now()]);

        return response()->json(['ok' => true]);
    }

    // ── Admin: get live state for event ─────────────────────────────────────
    public function liveState(Request $request, int $eventId): JsonResponse
    {
        $this->requireEventAccess($eventId);

        $assignments = EventAssignment::with('user')
            ->where('event_id', $eventId)
            ->whereIn('attendance_status', ['checked_in','on_break','checked_out'])
            ->get();

        $operators = $assignments->map(function($a) {
            // Latest GPS ping
            $ping = OperatorGpsPing::where('assignment_id', $a->id)
                ->orderByDesc('pinged_at')->first();

            // Trail — last 30 mins
            $trail = OperatorGpsPing::where('assignment_id', $a->id)
                ->where('pinged_at', '>=', now()->subMinutes(30))
                ->orderBy('pinged_at')
                ->get(['lat','lng','pinged_at','is_dead_reckoned'])
                ->map(fn($p) => [
                    'lat' => $p->lat, 'lng' => $p->lng,
                    'at'  => $p->pinged_at->toIso8601String(),
                    'dr'  => $p->is_dead_reckoned,
                ]);

            $lastSeen = $ping ? $ping->pinged_at->diffInSeconds(now()) : null;
            $callsign = $a->callsign ?: ($a->user->callsign ?? $a->user->name);

            // Geofence check
            $geofenceBreach = false;
            if ($ping && $a->lat && $a->lng && $a->coverage_radius_m > 0) {
                $dist = $this->haversineMetres($ping->lat, $ping->lng, $a->lat, $a->lng);
                $geofenceBreach = $dist > $a->coverage_radius_m * 1.2;
            }

            // Welfare check status
            $welfareCheck = OperatorWelfareCheck::where('event_id', $a->event_id)
                ->where('active', true)->latest()->first();
            $welfareStatus = 'ok';
            if ($welfareCheck) {
                $resp = OperatorWelfareResponse::where('welfare_check_id', $welfareCheck->id)
                    ->where('assignment_id', $a->id)
                    ->latest('prompted_at')->first();
                if ($resp && !$resp->responded && $resp->prompted_at->diffInMinutes(now()) > 15) {
                    $welfareStatus = 'overdue';
                } elseif ($resp && !$resp->responded) {
                    $welfareStatus = 'pending';
                }
            }

            return [
                'id'               => $a->id,
                'callsign'         => $callsign,
                'name'             => $a->user->name,
                'role'             => $a->role,
                'location_name'    => $a->location_name,
                'assigned_lat'     => $a->lat,
                'assigned_lng'     => $a->lng,
                'coverage_radius'  => $a->coverage_radius_m,
                'status'           => $a->attendance_status,
                'lat'              => $ping?->lat,
                'lng'              => $ping?->lng,
                'heading'          => $ping?->heading,
                'battery'          => $ping?->battery_pct,
                'accuracy'         => $ping?->accuracy_m,
                'last_seen_s'      => $lastSeen,
                'is_dead_reckoned' => $ping?->is_dead_reckoned ?? false,
                'geofence_breach'  => $geofenceBreach,
                'welfare_status'   => $welfareStatus,
                'trail'            => $trail,
            ];
        });

        // Active SOS alerts
        $sos = OperatorSosAlert::where('event_id', $eventId)
            ->whereNull('resolved_at')
            ->with('assignment.user')
            ->get()
            ->map(fn($s) => [
                'id'       => $s->id,
                'callsign' => $s->assignment->callsign ?: ($s->assignment->user->callsign ?? $s->assignment->user->name),
                'lat'      => $s->lat,
                'lng'      => $s->lng,
                'message'  => $s->message,
                'at'       => $s->created_at->toIso8601String(),
            ]);

        return response()->json([
            'operators'   => $operators,
            'sos_alerts'  => $sos,
            'server_time' => now()->toIso8601String(),
        ]);
    }

    // ── Admin: send message ──────────────────────────────────────────────────
    public function sendMessage(Request $request, int $eventId): JsonResponse
    {
        $this->requireEventAccess($eventId);

        $data = $request->validate([
            'type'          => 'required|in:info,warning,urgent,frequency_change',
            'body'          => 'required|string|max:500',
            'assignment_id' => 'nullable|integer',
            'requires_ack'  => 'boolean',
            'payload'       => 'nullable|array',
        ]);

        $msg = OperatorMessage::create([
            'event_id'      => $eventId,
            'assignment_id' => $data['assignment_id'] ?? null,
            'sent_by'       => auth()->id(),
            'type'          => $data['type'],
            'body'          => $data['body'],
            'payload'       => $data['payload'] ?? null,
            'requires_ack'  => $data['requires_ack'] ?? false,
        ]);

        return response()->json(['ok' => true, 'message_id' => $msg->id]);
    }

    // ── Admin: resolve SOS ───────────────────────────────────────────────────
    public function resolveSos(Request $request, int $eventId, int $alertId): JsonResponse
    {
        $this->requireEventAccess($eventId);

        OperatorSosAlert::where('id', $alertId)
            ->where('event_id', $eventId)
            ->update(['resolved_by' => auth()->id(), 'resolved_at' => now()]);

        return response()->json(['ok' => true]);
    }

    // ── Admin: set welfare check ─────────────────────────────────────────────
    public function setWelfareCheck(Request $request, int $eventId): JsonResponse
    {
        $this->requireEventAccess($eventId);

        $data = $request->validate([
            'interval_minutes' => 'required|integer|min:5|max:120',
            'active'           => 'boolean',
        ]);

        $check = OperatorWelfareCheck::updateOrCreate(
            ['event_id' => $eventId, 'active' => true],
            ['interval_minutes' => $data['interval_minutes'], 'created_by' => auth()->id()]
        );

        // Create welfare prompts for all checked-in operators
        if ($data['active'] ?? true) {
            $assignments = EventAssignment::where('event_id', $eventId)
                ->whereIn('attendance_status', ['checked_in','on_break'])->get();
            foreach ($assignments as $a) {
                OperatorWelfareResponse::create([
                    'welfare_check_id' => $check->id,
                    'assignment_id'    => $a->id,
                    'responded'        => false,
                    'prompted_at'      => now(),
                ]);
            }
        }

        return response()->json(['ok' => true, 'check_id' => $check->id]);
    }

    // ── Admin: timeline/heatmap data ────────────────────────────────────────
    public function replayData(Request $request, int $eventId): JsonResponse
    {
        $this->requireEventAccess($eventId);

        $pings = OperatorGpsPing::where('event_id', $eventId)
            ->with('assignment.user')
            ->orderBy('pinged_at')
            ->get()
            ->map(fn($p) => [
                'assignment_id' => $p->assignment_id,
                'callsign'      => $p->assignment->callsign ?: ($p->assignment->user->callsign ?? $p->assignment->user->name),
                'lat'           => $p->lat,
                'lng'           => $p->lng,
                'battery'       => $p->battery_pct,
                'at'            => $p->pinged_at->toIso8601String(),
                'dr'            => $p->is_dead_reckoned,
            ]);

        return response()->json(['pings' => $pings]);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────
    // ── Admin: live dashboard view ──────────────────────────────────────────
    public function liveDashboard(int $eventId): \Illuminate\View\View
    {
        $this->requireEventAccess($eventId);
        $event = \App\Models\Event::with('assignments.user')->findOrFail($eventId);
        $assignments = $event->assignments()->with('user')->get();
        return view('admin.events.live', compact('event', 'assignments'));
    }

    private function haversineMetres(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R    = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a    = sin($dLat/2)**2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2)**2;
        return $R * 2 * atan2(sqrt($a), sqrt(1-$a));
    }

    private function requireEventAccess(int $eventId): void
    {
        abort_unless(auth()->check(), 403);
        // Allow admins or users with event assignment management permission
        $user = auth()->user();
        abort_unless(
            $user->hasRole(['admin','super-admin']) || $user->hasPermissionTo('manage events'),
            403
        );
    }
}
