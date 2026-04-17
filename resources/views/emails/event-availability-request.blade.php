<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, sans-serif; font-size: 14px; color: #001f40; background: #f4f5f7; }
.wrap { max-width: 600px; margin: 0 auto; background: #fff; }
.header { background: #003366; border-bottom: 3px solid #C8102E; padding: 1.5rem; }
.header-logo { background: #C8102E; display: inline-block; padding: 5px 12px; font-size: 11px; font-weight: bold; color: #fff; letter-spacing: .08em; text-transform: uppercase; margin-bottom: .65rem; }
.header-title { font-size: 20px; font-weight: bold; color: #fff; line-height: 1.2; margin-bottom: .25rem; }
.header-sub { font-size: 12px; color: rgba(255,255,255,.55); }
.body { padding: 1.75rem 1.5rem; }
.greeting { font-size: 15px; font-weight: bold; color: #003366; margin-bottom: .85rem; }
.intro { font-size: 14px; line-height: 1.7; color: #2d4a6b; margin-bottom: 1.5rem; }
.event-box { background: #e8eef5; border: 1px solid rgba(0,51,102,.15); border-left: 4px solid #003366; padding: 1rem 1.1rem; margin-bottom: 1.75rem; }
.event-box-title { font-size: 16px; font-weight: bold; color: #003366; margin-bottom: .65rem; }
.event-row { display: flex; gap: .5rem; font-size: 13px; margin-bottom: .35rem; color: #2d4a6b; }
.event-row-label { font-weight: bold; color: #003366; min-width: 80px; }
.cta-section { text-align: center; margin: 1.75rem 0; }
.cta-label { font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: .12em; color: #6b7f96; margin-bottom: 1rem; }
.btn-row { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
.btn-available { display: inline-block; background: #1a6b3c; color: #fff; font-weight: bold; font-size: 14px; padding: .85rem 2rem; text-decoration: none; letter-spacing: .02em; }
.btn-unavailable { display: inline-block; background: #C8102E; color: #fff; font-weight: bold; font-size: 14px; padding: .85rem 2rem; text-decoration: none; letter-spacing: .02em; }
.note { font-size: 12px; color: #6b7f96; line-height: 1.65; padding: .85rem 1rem; background: #f4f5f7; border: 1px solid #dde2e8; margin-bottom: 1.5rem; }
.footer { background: #003366; padding: 1rem 1.5rem; text-align: center; font-size: 11px; color: rgba(255,255,255,.45); line-height: 1.7; }
.footer a { color: rgba(255,255,255,.65); text-decoration: none; }
</style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <div class="header-logo">RAYNET</div>
        <div class="header-title">{{ $event->title }}</div>
        <div class="header-sub">{{ \App\Helpers\RaynetSetting::groupName() }} · Availability Request</div>
    </div>
    <div class="body">
        <div class="greeting">Hi {{ $member->name }}@if($member->callsign) ({{ $member->callsign }})@endif,</div>
        <p class="intro">
            We're planning our team for an upcoming event and would like to know if you're available to attend.
            Please let us know using the buttons below — it only takes one click.
        </p>

        <div class="event-box">
            <div class="event-box-title">{{ $event->title }}</div>
            @if($event->starts_at)
            <div class="event-row"><span class="event-row-label">📅 Date</span><span>{{ $event->starts_at->format('l j F Y') }}</span></div>
            <div class="event-row"><span class="event-row-label">🕐 Time</span><span>{{ $event->starts_at->format('H:i') }}{{ $event->ends_at ? ' – '.$event->ends_at->format('H:i') : '' }}</span></div>
            @endif
            @if($event->location)
            <div class="event-row"><span class="event-row-label">📍 Location</span><span>{{ $event->location }}</span></div>
            @endif
            @if($event->type)
            <div class="event-row"><span class="event-row-label">🏷 Type</span><span>{{ $event->type->name }}</span></div>
            @endif
            @if($event->description)
            <div style="margin-top:.65rem;padding-top:.65rem;border-top:1px solid rgba(0,51,102,.15);font-size:13px;color:#2d4a6b;line-height:1.6;">{{ Str::limit($event->description, 200) }}</div>
            @endif
        </div>

        <div class="cta-section">
            <div class="cta-label">Please declare your availability</div>
            <div class="btn-row">
                <a href="{{ $availableUrl }}" class="btn-available">✓ &nbsp; I'm Available</a>
                <a href="{{ $unavailableUrl }}" class="btn-unavailable">✕ &nbsp; Not Available</a>
            </div>
        </div>

        <div class="note">
            💡 Clicking a button will record your availability for this event. You can change your response at any time by visiting the
            <a href="{{ url('/members') }}" style="color:#003366;font-weight:bold;">members area</a>
            or clicking the other button in a future email.
        </div>

        <p style="font-size:12px;color:#6b7f96;line-height:1.65;">
            This message was sent by {{ \App\Helpers\RaynetSetting::groupName() }}. If you did not expect this email or have any questions, please contact your group controller.
        </p>
    </div>
    <div class="footer">
        {{ \App\Helpers\RaynetSetting::groupName() }} (Group {{ \App\Helpers\RaynetSetting::groupNumber() }}) · Member of RAYNET-UK<br>
        <a href="{{ url('/') }}">{{ url('/') }}</a>
    </div>
</div>
</body>
</html>
