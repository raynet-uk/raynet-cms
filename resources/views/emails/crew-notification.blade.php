<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body { font-family: Arial, sans-serif; font-size: 14px; color: #001f40; background: #f4f5f7; margin: 0; padding: 0; }
.wrap { max-width: 600px; margin: 0 auto; background: #fff; }
.header { background: #003366; border-bottom: 3px solid #C8102E; padding: 1.25rem 1.5rem; }
.header-logo { background: #C8102E; display: inline-block; padding: 6px 12px; font-size: 12px; font-weight: bold; color: #fff; letter-spacing: .06em; text-transform: uppercase; margin-bottom: .5rem; }
.header-title { font-size: 18px; font-weight: bold; color: #fff; margin: 0; }
.header-sub { font-size: 12px; color: rgba(255,255,255,.6); margin-top: 4px; }
.body { padding: 1.5rem; }
.greeting { font-size: 15px; font-weight: bold; color: #003366; margin-bottom: 1rem; }
.message-box { background: #e8eef5; border-left: 4px solid #003366; padding: .85rem 1rem; font-size: 14px; line-height: 1.65; margin-bottom: 1.25rem; white-space: pre-line; }
.section-title { font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: .14em; color: #6b7f96; margin-bottom: .5rem; margin-top: 1.25rem; }
.detail-table { width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 1rem; }
.detail-table td { padding: .45rem .6rem; border: 1px solid #dde2e8; }
.detail-table td:first-child { font-weight: bold; color: #003366; background: #f4f5f7; width: 140px; }
.shift-pill { display: inline-block; background: #e8eef5; border: 1px solid rgba(0,51,102,.2); color: #003366; font-size: 12px; font-weight: bold; padding: 2px 8px; margin: 2px; }
.footer { background: #003366; padding: 1rem 1.5rem; text-align: center; font-size: 11px; color: rgba(255,255,255,.5); }
.footer a { color: rgba(255,255,255,.7); }
.btn { display: inline-block; background: #C8102E; color: #fff; font-weight: bold; font-size: 13px; padding: .65rem 1.5rem; text-decoration: none; margin: 1rem 0; }
</style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <div class="header-logo">RAYNET</div>
        <div class="header-title">{{ $assignment->event->title }}</div>
        <div class="header-sub">{{ \App\Helpers\RaynetSetting::groupName() }}</div>
    </div>
    <div class="body">
        <div class="greeting">Hi {{ $assignment->user->name }}@if($assignment->callsign) ({{ $assignment->callsign }})@endif,</div>

        @if($type === 'custom')
            <div class="message-box">{{ $customMessage }}</div>
        @else
            <p style="margin-bottom:1rem;">This is a reminder about your upcoming assignment for <strong>{{ $assignment->event->title }}</strong>.</p>
        @endif

        @if($type === 'reminder')
        <div class="section-title">Your Assignment Details</div>
        <table class="detail-table">
            <tr><td>Event</td><td><strong>{{ $assignment->event->title }}</strong></td></tr>
            <tr><td>Date</td><td>{{ $assignment->event->starts_at?->format('l j F Y') }}</td></tr>
            <tr><td>Time</td><td>{{ $assignment->event->starts_at?->format('H:i') }}{{ $assignment->event->ends_at ? ' – '.$assignment->event->ends_at->format('H:i') : '' }}</td></tr>
            <tr><td>Location</td><td>{{ $assignment->event->location ?: '—' }}</td></tr>
            <tr><td>Your Role</td><td>{{ $assignment->role ?: '—' }}</td></tr>
            @if($assignment->callsign)
            <tr><td>Callsign</td><td>{{ $assignment->callsign }}</td></tr>
            @endif
            @if($assignment->report_time)
            <tr><td>Report Time</td><td><strong>{{ substr($assignment->report_time,0,5) }}</strong></td></tr>
            @endif
            @if($assignment->frequency)
            <tr><td>Frequency</td><td>{{ $assignment->frequency }} {{ $assignment->mode }}{{ $assignment->ctcss_tone ? ' (CTCSS '.$assignment->ctcss_tone.')' : '' }}</td></tr>
            @endif
            @if($assignment->location_name)
            <tr><td>Your Position</td><td>{{ $assignment->location_name }}{{ $assignment->grid_ref ? ' · '.$assignment->grid_ref : '' }}</td></tr>
            @endif
        </table>

        @php
            $shifts = $assignment->shifts ?? [];
            if (is_string($shifts)) $shifts = json_decode($shifts, true) ?? [];
        @endphp
        @if(!empty($shifts))
        <div class="section-title">Your Shifts</div>
        @foreach($shifts as $shift)
            @if(($shift['type']??'shift') === 'shift')
                <span class="shift-pill">🕐 {{ $shift['start']??'?' }} – {{ $shift['end']??'?' }}{{ !empty($shift['label']) ? ' · '.$shift['label'] : '' }}</span>
            @else
                <span class="shift-pill" style="background:#fffbec;border-color:#e8c96a;color:#8a5c00;">⏸ Break {{ $shift['start']??'?' }} – {{ $shift['end']??'?' }}</span>
            @endif
        @endforeach
        @endif

        @if($assignment->briefing_notes)
        <div class="section-title">Briefing Notes</div>
        <div class="message-box" style="border-color:#c8a030;background:#fffbec;">{{ $assignment->briefing_notes }}</div>
        @endif

        @if($assignment->briefing_token)
        <div style="text-align:center;margin:1.25rem 0;">
            <a href="{{ $assignment->briefingUrl() }}" class="btn">View My Full Briefing →</a>
        </div>
        @endif
        @endif

        <p style="font-size:12px;color:#6b7f96;margin-top:1.5rem;">
            This message was sent by {{ \App\Helpers\RaynetSetting::groupName() }}. If you have any questions, please contact your group controller.
        </p>
    </div>
    <div class="footer">
        {{ \App\Helpers\RaynetSetting::groupName() }} (Group {{ \App\Helpers\RaynetSetting::groupNumber() }}) · Member of RAYNET-UK<br>
        <a href="{{ url('/') }}">{{ url('/') }}</a>
    </div>
</div>
</body>
</html>
