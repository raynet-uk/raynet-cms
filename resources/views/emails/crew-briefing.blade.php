<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, sans-serif; font-size: 14px; color: #001f40; background: #f4f5f7; }
.wrap { max-width: 620px; margin: 0 auto; background: #fff; }
.header { background: #003366; border-bottom: 3px solid #C8102E; padding: 1.5rem; }
.header-logo { background: #C8102E; display: inline-block; padding: 5px 12px; font-size: 11px; font-weight: bold; color: #fff; letter-spacing: .08em; text-transform: uppercase; margin-bottom: .65rem; }
.header-title { font-size: 20px; font-weight: bold; color: #fff; line-height: 1.2; margin-bottom: .25rem; }
.header-sub { font-size: 12px; color: rgba(255,255,255,.55); }
.body { padding: 1.75rem 1.5rem; }
.greeting { font-size: 15px; font-weight: bold; color: #003366; margin-bottom: .85rem; }
.custom-msg { background: #e8eef5; border-left: 4px solid #003366; padding: 1rem 1.1rem; font-size: 14px; line-height: 1.7; margin-bottom: 1.5rem; color: #2d4a6b; white-space: pre-line; }
.section-title { font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: .14em; color: #6b7f96; margin-bottom: .5rem; margin-top: 1.25rem; padding-bottom: .3rem; border-bottom: 2px solid #003366; }
.detail-table { width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: .5rem; }
.detail-table td { padding: .45rem .7rem; border: 1px solid #dde2e8; vertical-align: top; }
.detail-table td:first-child { font-weight: bold; color: #003366; background: #f4f5f7; width: 140px; }
.shift-pill { display: inline-block; background: #e8eef5; border: 1px solid rgba(0,51,102,.2); color: #003366; font-size: 12px; font-weight: bold; padding: 3px 9px; margin: 2px; }
.shift-pill.break { background: #fffbec; border-color: #e8c96a; color: #8a5c00; }
.equip-list { margin: 0; padding: 0; list-style: none; }
.equip-list li { padding: .3rem .5rem; border-bottom: 1px solid #f0f0f0; font-size: 13px; display: flex; align-items: center; gap: .4rem; }
.equip-list li::before { content: "☐"; font-size: 14px; color: #003366; flex-shrink: 0; }
.emergency-box { background: #fdf0f2; border: 1px solid rgba(200,16,46,.2); border-left: 4px solid #C8102E; padding: .85rem 1rem; font-size: 13px; margin-top: .5rem; }
.btn-wrap { text-align: center; margin: 1.75rem 0; }
.btn { display: inline-block; background: #003366; color: #fff; font-weight: bold; font-size: 14px; padding: .85rem 2rem; text-decoration: none; margin: .25rem; }
.btn-pdf { background: #C8102E; }
.notes-box { background: #fffbec; border: 1px solid #e8c96a; border-left: 4px solid #c8a030; padding: .85rem 1rem; font-size: 13px; line-height: 1.65; margin-top: .5rem; }
.footer { background: #003366; padding: 1rem 1.5rem; text-align: center; font-size: 11px; color: rgba(255,255,255,.45); line-height: 1.7; }
.footer a { color: rgba(255,255,255,.65); text-decoration: none; }
.restricted { background: #C8102E; color: #fff; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: .1em; padding: 4px 10px; display: inline-block; margin-bottom: 1rem; }
</style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <div class="header-logo">RAYNET</div>
        <div class="header-title">{{ $assignment->event->title }}</div>
        <div class="header-sub">{{ \App\Helpers\RaynetSetting::groupName() }} · Operator Briefing</div>
    </div>
    <div class="body">
        <div class="restricted">Restricted — Authorised Personnel Only</div>

        <div class="greeting">Hi {{ $assignment->user->name }}@if($assignment->callsign) ({{ $assignment->callsign }})@endif,</div>

        <p style="font-size:14px;color:#2d4a6b;line-height:1.7;margin-bottom:1.25rem;">
            Please find your personal briefing for <strong>{{ $assignment->event->title }}</strong> below.
            Keep this document confidential and bring it with you on the day.
        </p>

        @if(!empty($customMessage))
        <div class="section-title">Message from Group Controller</div>
        <div class="custom-msg">{{ $customMessage }}</div>
        @endif

        {{-- EVENT DETAILS --}}
        <div class="section-title">Event Details</div>
        <table class="detail-table">
            <tr><td>Event</td><td><strong>{{ $assignment->event->title }}</strong></td></tr>
            <tr><td>Date</td><td>{{ $assignment->event->starts_at?->format('l j F Y') }}</td></tr>
            <tr><td>Time</td><td>{{ $assignment->event->starts_at?->format('H:i') }}{{ $assignment->event->ends_at ? ' – '.$assignment->event->ends_at->format('H:i') : '' }}</td></tr>
            <tr><td>Location</td><td>{{ $assignment->event->location ?: '—' }}</td></tr>
            @if($assignment->event->type)<tr><td>Type</td><td>{{ $assignment->event->type->name }}</td></tr>@endif
            @if($assignment->event->description)<tr><td>Description</td><td>{{ Str::limit($assignment->event->description, 300) }}</td></tr>@endif
        </table>

        {{-- YOUR ASSIGNMENT --}}
        <div class="section-title">Your Assignment</div>
        <table class="detail-table">
            <tr><td>Role</td><td><strong>{{ $assignment->role ?: '—' }}</strong></td></tr>
            <tr><td>Callsign</td><td>{{ $assignment->callsign ?: '—' }}</td></tr>
            <tr><td>Status</td><td>{{ ucfirst($assignment->status) }}</td></tr>
            @if($assignment->report_time)<tr><td>Report Time</td><td><strong style="color:#8a5c00;">{{ substr($assignment->report_time,0,5) }}</strong></td></tr>@endif
            @if($assignment->depart_time)<tr><td>Depart Time</td><td>{{ substr($assignment->depart_time,0,5) }}</td></tr>@endif
        </table>

        {{-- SHIFTS --}}
        @php
            $shifts = $assignment->shifts ?? [];
            if (is_string($shifts)) $shifts = json_decode($shifts, true) ?? [];
        @endphp
        @if(!empty($shifts))
        <div class="section-title">Your Shifts</div>
        <div style="margin-bottom:.5rem;">
            @foreach($shifts as $shift)
                @if(($shift['type']??'shift') === 'shift')
                    <span class="shift-pill">🕐 {{ $shift['start']??'?' }} – {{ $shift['end']??'?' }}{{ !empty($shift['label']) ? ' · '.$shift['label'] : '' }}</span>
                @else
                    <span class="shift-pill break">⏸ Break {{ $shift['start']??'?' }} – {{ $shift['end']??'?' }}</span>
                @endif
            @endforeach
        </div>
        @endif

        {{-- POSITION --}}
        @if($assignment->location_name || $assignment->grid_ref || $assignment->lat)
        <div class="section-title">Your Position</div>
        <table class="detail-table">
            @if($assignment->location_name)<tr><td>Location</td><td>{{ $assignment->location_name }}</td></tr>@endif
            @if($assignment->grid_ref)<tr><td>Grid Ref</td><td><strong>{{ $assignment->grid_ref }}</strong></td></tr>@endif
            @if($assignment->what3words)<tr><td>What3Words</td><td>///{{ $assignment->what3words }}</td></tr>@endif
            @if($assignment->lat && $assignment->lng)<tr><td>Coordinates</td><td>{{ number_format($assignment->lat,5) }}, {{ number_format($assignment->lng,5) }}</td></tr>@endif
        </table>
        @endif

        {{-- FREQUENCY --}}
        @if($assignment->frequency)
        <div class="section-title">Frequencies</div>
        <table class="detail-table">
            <tr><td>Primary</td><td><strong>{{ $assignment->frequency }} {{ $assignment->mode }}</strong>{{ $assignment->ctcss_tone ? ' · CTCSS '.$assignment->ctcss_tone : '' }}</td></tr>
            @if($assignment->secondary_frequency)<tr><td>Secondary</td><td>{{ $assignment->secondary_frequency }} {{ $assignment->secondary_mode ?? '' }}</td></tr>@endif
            @if($assignment->fallback_frequency)<tr><td>Fallback</td><td>{{ $assignment->fallback_frequency }} {{ $assignment->fallback_mode ?? '' }}</td></tr>@endif
            @if($assignment->channel_label)<tr><td>Channel</td><td>{{ $assignment->channel_label }}</td></tr>@endif
        </table>
        @endif

        {{-- EQUIPMENT --}}
        @php
            $equipItems = $assignment->equipment_items ?? [];
            if (is_string($equipItems)) $equipItems = json_decode($equipItems, true) ?? [];
        @endphp
        @if(!empty($equipItems) || $assignment->equipment)
        <div class="section-title">Equipment to Bring</div>
        <ul class="equip-list">
            @foreach($equipItems as $item)
                <li>{{ $item }}</li>
            @endforeach
            @if($assignment->equipment && empty($equipItems))
                <li>{{ $assignment->equipment }}</li>
            @endif
        </ul>
        @endif

        {{-- VEHICLE --}}
        @if($assignment->has_vehicle)
        <div class="section-title">Vehicle</div>
        <table class="detail-table">
            <tr><td>Vehicle</td><td>Yes{{ $assignment->vehicle_reg ? ' — '.$assignment->vehicle_reg : '' }}</td></tr>
        </table>
        @endif

        {{-- BRIEFING NOTES --}}
        @if($assignment->briefing_notes)
        <div class="section-title">Briefing Notes</div>
        <div class="notes-box">{{ $assignment->briefing_notes }}</div>
        @endif

        {{-- EMERGENCY CONTACT --}}
        @if($assignment->emergency_contact_name)
        <div class="section-title">Emergency Contact</div>
        <div class="emergency-box">
            <strong>{{ $assignment->emergency_contact_name }}</strong>
            @if($assignment->emergency_contact_phone) · <strong>{{ $assignment->emergency_contact_phone }}</strong>@endif
        </div>
        @endif

        {{-- ACTIONS --}}
        <div class="btn-wrap">
            @if($assignment->briefing_token)
            <a href="{{ $assignment->briefingUrl() }}" class="btn">View Online Briefing →</a>
            @endif
            <a href="{{ route('admin.events.assignments.briefing-pdf', $assignment->id) }}" class="btn btn-pdf">⬇ Download PDF</a>
        </div>

        <p style="font-size:12px;color:#6b7f96;line-height:1.65;margin-top:1rem;">
            This briefing was issued by {{ \App\Helpers\RaynetSetting::groupName() }} on {{ now()->format('j M Y \a\t H:i') }}.
            If you have any questions please contact your group controller.
        </p>
    </div>
    <div class="footer">
        {{ \App\Helpers\RaynetSetting::groupName() }} (Group {{ \App\Helpers\RaynetSetting::groupNumber() }}) · Member of RAYNET-UK<br>
        <a href="{{ url('/') }}">{{ url('/') }}</a> · RESTRICTED — AUTHORISED PERSONNEL ONLY
    </div>
</div>
</body>
</html>
