<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #001f40; }
.header { background: #003366; border-bottom: 3px solid #C8102E; padding: 14px 18px; margin-bottom: 16px; }
.header-logo { background: #C8102E; display: inline-block; padding: 3px 8px; font-size: 9px; font-weight: bold; color: #fff; letter-spacing: .08em; text-transform: uppercase; margin-bottom: 5px; }
.header-title { font-size: 17px; font-weight: bold; color: #fff; margin-bottom: 2px; }
.header-sub { font-size: 10px; color: rgba(255,255,255,.6); }
.header-right { float: right; text-align: right; color: rgba(255,255,255,.5); font-size: 9px; margin-top: 4px; }
.body { padding: 0 18px 18px; }
.restricted { background: #C8102E; color: #fff; font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: .1em; padding: 3px 8px; display: inline-block; margin-bottom: 10px; }
.greeting { font-size: 13px; font-weight: bold; color: #003366; margin-bottom: 8px; }
.custom-msg { background: #e8eef5; border-left: 3px solid #003366; padding: 8px 10px; font-size: 11px; line-height: 1.6; margin-bottom: 12px; }
.section-title { font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: .14em; color: #6b7f96; margin-bottom: 4px; margin-top: 12px; padding-bottom: 2px; border-bottom: 2px solid #003366; }
.detail-table { width: 100%; border-collapse: collapse; font-size: 11px; margin-bottom: 6px; }
.detail-table td { padding: 4px 7px; border: 1px solid #dde2e8; vertical-align: top; }
.detail-table .label { font-weight: bold; color: #003366; background: #f4f5f7; width: 120px; }
.two-col { display: table; width: 100%; }
.col { display: table-cell; width: 48%; vertical-align: top; padding-right: 2%; }
.col:last-child { padding-right: 0; padding-left: 2%; }
.shift-pill { display: inline-block; background: #e8eef5; border: 1px solid rgba(0,51,102,.2); color: #003366; font-size: 10px; font-weight: bold; padding: 2px 7px; margin: 2px; }
.shift-pill.break { background: #fffbec; border-color: #e8c96a; color: #8a5c00; }
.equip-list { margin: 0; padding: 0; list-style: none; }
.equip-list li { padding: 2px 4px; border-bottom: 1px solid #f0f0f0; font-size: 11px; }
.equip-list li::before { content: "\2610  "; }
.notes-box { background: #fffbec; border-left: 3px solid #c8a030; padding: 7px 9px; font-size: 11px; line-height: 1.6; }
.emergency-box { background: #fdf0f2; border-left: 3px solid #C8102E; padding: 7px 9px; font-size: 11px; }
.sig-block { margin-top: 18px; padding-top: 12px; border-top: 1px solid #dde2e8; }
.sig-table { width: 100%; border-collapse: collapse; }
.sig-table td { width: 33%; padding: 4px 6px; vertical-align: bottom; }
.sig-line { border-bottom: 1px solid #001f40; height: 28px; margin-bottom: 3px; }
.sig-label { font-size: 8px; text-transform: uppercase; letter-spacing: .08em; color: #9aa3ae; }
.footer { margin-top: 16px; padding-top: 8px; border-top: 2px solid #003366; text-align: center; font-size: 9px; color: #9aa3ae; }
.page-break { page-break-after: always; }
</style>
</head>
<body>

<div class="header">
    <div class="header-right">
        Group {{ \App\Helpers\RaynetSetting::groupNumber() }}<br>
        Issued: {{ now()->format('j M Y H:i') }}<br>
        RESTRICTED
    </div>
    <div class="header-logo">RAYNET</div>
    <div class="header-title">{{ $assignment->event->title }}</div>
    <div class="header-sub">{{ \App\Helpers\RaynetSetting::groupName() }} · Operator Briefing Sheet</div>
</div>

<div class="body">
    <div class="restricted">Restricted — Authorised Personnel Only</div>

    <div class="greeting">{{ $assignment->user->name }}@if($assignment->callsign) ({{ $assignment->callsign }})@endif — Personal Briefing</div>

    @if(!empty($customMessage))
    <div class="section-title">Message from Group Controller</div>
    <div class="custom-msg">{{ $customMessage }}</div>
    @endif

    <div class="two-col">
        <div class="col">
            <div class="section-title">Event Details</div>
            <table class="detail-table">
                <tr><td class="label">Event</td><td><strong>{{ $assignment->event->title }}</strong></td></tr>
                <tr><td class="label">Date</td><td>{{ $assignment->event->starts_at?->format('D j M Y') }}</td></tr>
                <tr><td class="label">Time</td><td>{{ $assignment->event->starts_at?->format('H:i') }}{{ $assignment->event->ends_at ? ' – '.$assignment->event->ends_at->format('H:i') : '' }}</td></tr>
                <tr><td class="label">Location</td><td>{{ $assignment->event->location ?: '—' }}</td></tr>
            </table>

            <div class="section-title">Your Assignment</div>
            <table class="detail-table">
                <tr><td class="label">Role</td><td><strong>{{ $assignment->role ?: '—' }}</strong></td></tr>
                <tr><td class="label">Callsign</td><td>{{ $assignment->callsign ?: '—' }}</td></tr>
                @if($assignment->report_time)<tr><td class="label">Report</td><td><strong>{{ substr($assignment->report_time,0,5) }}</strong></td></tr>@endif
                @if($assignment->depart_time)<tr><td class="label">Depart</td><td>{{ substr($assignment->depart_time,0,5) }}</td></tr>@endif
                @if($assignment->has_vehicle)<tr><td class="label">Vehicle</td><td>Yes{{ $assignment->vehicle_reg ? ' — '.$assignment->vehicle_reg : '' }}</td></tr>@endif
            </table>

            @php $shifts = $assignment->shifts ?? []; if(is_string($shifts)) $shifts = json_decode($shifts,true)??[]; @endphp
            @if(!empty($shifts))
            <div class="section-title">Shifts</div>
            @foreach($shifts as $shift)
                @if(($shift['type']??'shift')==='shift')
                    <span class="shift-pill">{{ $shift['start']??'?' }}–{{ $shift['end']??'?' }}{{ !empty($shift['label'])?' · '.$shift['label']:'' }}</span>
                @else
                    <span class="shift-pill break">Break {{ $shift['start']??'?' }}–{{ $shift['end']??'?' }}</span>
                @endif
            @endforeach
            @endif
        </div>

        <div class="col">
            @if($assignment->location_name || $assignment->grid_ref)
            <div class="section-title">Position</div>
            <table class="detail-table">
                @if($assignment->location_name)<tr><td class="label">Location</td><td>{{ $assignment->location_name }}</td></tr>@endif
                @if($assignment->grid_ref)<tr><td class="label">Grid Ref</td><td><strong>{{ $assignment->grid_ref }}</strong></td></tr>@endif
                @if($assignment->what3words)<tr><td class="label">W3W</td><td>///{{ $assignment->what3words }}</td></tr>@endif
                @if($assignment->lat && $assignment->lng)<tr><td class="label">Coords</td><td>{{ number_format($assignment->lat,5) }}, {{ number_format($assignment->lng,5) }}</td></tr>@endif
            </table>
            @endif

            @if($assignment->frequency)
            <div class="section-title">Frequencies</div>
            <table class="detail-table">
                <tr><td class="label">Primary</td><td><strong>{{ $assignment->frequency }} {{ $assignment->mode }}</strong>{{ $assignment->ctcss_tone ? ' CTCSS '.$assignment->ctcss_tone : '' }}</td></tr>
                @if($assignment->secondary_frequency)<tr><td class="label">Secondary</td><td>{{ $assignment->secondary_frequency }} {{ $assignment->secondary_mode??'' }}</td></tr>@endif
                @if($assignment->fallback_frequency)<tr><td class="label">Fallback</td><td>{{ $assignment->fallback_frequency }} {{ $assignment->fallback_mode??'' }}</td></tr>@endif
            </table>
            @endif

            @php $equipItems = $assignment->equipment_items??[]; if(is_string($equipItems)) $equipItems=json_decode($equipItems,true)??[]; @endphp
            @if(!empty($equipItems))
            <div class="section-title">Equipment</div>
            <ul class="equip-list">
                @foreach($equipItems as $item)<li>{{ $item }}</li>@endforeach
            </ul>
            @endif
        </div>
    </div>

    @if($assignment->briefing_notes)
    <div class="section-title">Briefing Notes</div>
    <div class="notes-box">{{ $assignment->briefing_notes }}</div>
    @endif

    @if($assignment->emergency_contact_name)
    <div class="section-title">Emergency Contact</div>
    <div class="emergency-box">
        <strong>{{ $assignment->emergency_contact_name }}</strong>
        @if($assignment->emergency_contact_phone) · <strong>{{ $assignment->emergency_contact_phone }}</strong>@endif
        @if($assignment->medical_notes)<br><span style="color:#C8102E;font-size:10px;">Medical: {{ $assignment->medical_notes }}</span>@endif
    </div>
    @endif

    <div class="sig-block">
        <table class="sig-table">
            <tr>
                <td><div class="sig-line"></div><div class="sig-label">Operator Signature &amp; Callsign</div></td>
                <td><div class="sig-line"></div><div class="sig-label">Event Controller</div></td>
                <td><div class="sig-line"></div><div class="sig-label">Time Checked In</div></td>
            </tr>
        </table>
    </div>
</div>

<div class="footer">
    {{ \App\Helpers\RaynetSetting::groupName() }} (Group {{ \App\Helpers\RaynetSetting::groupNumber() }}) · Member of RAYNET-UK · RESTRICTED — DO NOT LEAVE UNATTENDED
</div>

</body>
</html>
