<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Operator Briefing — {{ $assignment->event->title }}</title>
    <style>
        /* Reset */
        body, table, td, p, a, li, blockquote { -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; }
        table, td { mso-table-lspace:0pt; mso-table-rspace:0pt; }
        img { -ms-interpolation-mode:bicubic; border:0; outline:none; text-decoration:none; }
        body { margin:0; padding:0; background:#F2F2F2; font-family:Arial,'Helvetica Neue',Helvetica,sans-serif; }

        /* Layout */
        .email-wrap   { max-width:620px; margin:0 auto; }
        .email-body   { background:#ffffff; }

        /* Header */
        .hdr          { background:#003366; border-bottom:4px solid #C8102E; padding:0; }
        .hdr-inner    { padding:24px 32px; display:flex; align-items:center; }
        .hdr-logo     { background:#C8102E; width:52px; height:52px; display:inline-flex; align-items:center; justify-content:center; vertical-align:middle; }
        .hdr-logo-txt { font-size:11px; font-weight:bold; color:#ffffff; letter-spacing:.05em; text-align:center; line-height:1.25; text-transform:uppercase; }
        .hdr-text     { display:inline-block; vertical-align:middle; margin-left:16px; }
        .hdr-org      { font-size:15px; font-weight:bold; color:#ffffff; letter-spacing:.04em; text-transform:uppercase; margin:0; }
        .hdr-sub      { font-size:11px; color:rgba(255,255,255,.55); text-transform:uppercase; letter-spacing:.06em; margin:3px 0 0; }

        /* Hero band */
        .hero         { background:linear-gradient(135deg,#001f40 0%,#003366 60%,#004080 100%); padding:28px 32px 24px; border-bottom:2px solid #C8102E; }
        .hero-eyebrow { font-size:10px; font-weight:bold; color:#C8102E; text-transform:uppercase; letter-spacing:.18em; margin:0 0 8px; }
        .hero-title   { font-size:24px; font-weight:bold; color:#ffffff; margin:0 0 14px; line-height:1.2; }
        .hero-chips   { margin:0; padding:0; list-style:none; }
        .hero-chip    { display:inline-block; background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.2); color:rgba(255,255,255,.88); font-size:12px; font-weight:bold; padding:4px 12px; margin:0 6px 6px 0; }

        /* Greeting */
        .greeting     { padding:28px 32px 0; }
        .greeting-hi  { font-size:20px; font-weight:bold; color:#003366; margin:0 0 10px; }
        .greeting-p   { font-size:14px; color:#2d4a6b; line-height:1.6; margin:0 0 14px; }

        /* Section heading */
        .section      { padding:20px 32px 0; }
        .section-head { font-size:10px; font-weight:bold; text-transform:uppercase; letter-spacing:.14em; color:#003366; margin:0 0 10px; padding-bottom:7px; border-bottom:2px solid #003366; }

        /* Detail rows */
        .details-table { width:100%; border-collapse:collapse; font-size:13px; }
        .details-table td { padding:8px 10px; border-bottom:1px solid #dde2e8; vertical-align:top; }
        .details-table td:first-child { font-weight:bold; color:#6b7f96; text-transform:uppercase; font-size:10px; letter-spacing:.08em; width:110px; padding-top:10px; }
        .details-table td:last-child  { color:#001f40; font-weight:bold; }
        .details-table tr:last-child td { border-bottom:none; }

        /* Shift pills */
        .shift-pill   { display:inline-block; padding:2px 9px; font-size:11px; font-weight:bold; margin:2px 4px 2px 0; border:1px solid; }
        .shift-work   { background:#e8eef5; border-color:rgba(0,51,102,.25); color:#003366; }
        .shift-break  { background:#fef9ec; border-color:#e8c96a; color:#8a5c00; }

        /* Frequency table */
        .freq-table   { width:100%; border-collapse:collapse; font-size:13px; }
        .freq-table th { background:#003366; color:rgba(255,255,255,.8); font-size:10px; font-weight:bold; text-transform:uppercase; letter-spacing:.1em; padding:7px 10px; text-align:left; }
        .freq-table td { padding:8px 10px; border-bottom:1px solid #dde2e8; color:#001f40; vertical-align:middle; }
        .freq-table tr:last-child td { border-bottom:none; }
        .freq-table tr:nth-child(even) td { background:#fafbfc; }
        .tier-badge   { display:inline-block; padding:1px 7px; font-size:10px; font-weight:bold; text-transform:uppercase; letter-spacing:.04em; border:1px solid; white-space:nowrap; }
        .tier-pri     { background:#e8eef5; border-color:rgba(0,51,102,.3); color:#003366; }
        .tier-sec     { background:#eef7f2; border-color:#b8ddc9; color:#1a6b3c; }
        .tier-fal     { background:#fef9ec; border-color:#e8c96a; color:#8a5c00; }

        /* Equipment list */
        .equip-list   { margin:0; padding:0; list-style:none; }
        .equip-item   { font-size:13px; color:#001f40; padding:6px 0; border-bottom:1px solid #dde2e8; display:flex; align-items:center; gap:8px; }
        .equip-item:last-child { border-bottom:none; }
        .equip-box    { width:14px; height:14px; border:2px solid #dde2e8; flex-shrink:0; display:inline-block; }

        /* Briefing notes */
        .notes-box    { background:#fef9ec; border-left:4px solid #e8c96a; padding:12px 16px; font-size:13px; color:#001f40; line-height:1.6; margin:0; }

        /* CTA — check-in section */
        .cta-section  { margin:24px 32px 0; background:#003366; border-top:3px solid #C8102E; padding:24px; }
        .cta-title    { font-size:16px; font-weight:bold; color:#ffffff; margin:0 0 8px; }
        .cta-body     { font-size:13px; color:rgba(255,255,255,.8); line-height:1.6; margin:0 0 20px; }
        .cta-row      { display:table; width:100%; }
        .cta-left     { display:table-cell; vertical-align:middle; }
        .cta-btn      { display:inline-block; background:#C8102E; color:#ffffff; font-size:14px; font-weight:bold; text-decoration:none; text-transform:uppercase; letter-spacing:.06em; padding:14px 28px; }
        .cta-btn:hover{ background:#a50e26; }
        .cta-right    { display:table-cell; vertical-align:middle; text-align:right; width:110px; }
        .cta-qr-wrap  { background:#ffffff; padding:6px; display:inline-block; }
        .cta-qr-label { font-size:9px; color:rgba(255,255,255,.5); text-align:center; text-transform:uppercase; letter-spacing:.08em; margin-top:6px; }

        /* Steps */
        .steps        { margin:0; padding:0; list-style:none; counter-reset:steps; }
        .step         { display:flex; gap:14px; padding:10px 0; border-bottom:1px solid #dde2e8; font-size:13px; color:#2d4a6b; line-height:1.5; counter-increment:steps; }
        .step:last-child { border-bottom:none; }
        .step-num     { background:#003366; color:#ffffff; font-size:11px; font-weight:bold; width:24px; height:24px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:1px; }
        .step-num     { min-width:24px; text-align:center; line-height:24px; }

        /* Footer */
        .email-footer { background:#001f40; padding:20px 32px; }
        .footer-line  { font-size:11px; color:rgba(255,255,255,.4); margin:0 0 4px; line-height:1.5; }
        .footer-line a{ color:rgba(255,255,255,.55); text-decoration:none; }

        /* Spacer */
        .spacer       { height:24px; }
        .spacer-sm    { height:14px; }

        /* Responsive */
        @media only screen and (max-width:640px) {
            .email-wrap   { width:100% !important; }
            .hero         { padding:20px 20px 18px !important; }
            .greeting, .section { padding-left:20px !important; padding-right:20px !important; }
            .cta-section  { margin-left:20px !important; margin-right:20px !important; padding:18px !important; }
            .cta-row      { display:block !important; }
            .cta-right    { display:block !important; text-align:left !important; margin-top:16px !important; width:auto !important; }
            .email-footer { padding:16px 20px !important; }
            .hero-title   { font-size:20px !important; }
        }
    </style>
</head>
<body>
@php
    $a     = $assignment;
    $event = $a->event;
    $user  = $a->user;

    // Normalise shifts
    $shifts = $a->shifts ?? [];
    if (empty($shifts) && ($a->start_time || $a->end_time)) {
        $shifts = [[
            'type'  => 'shift',
            'start' => $a->start_time ? substr($a->start_time,0,5) : null,
            'end'   => $a->end_time   ? substr($a->end_time,  0,5) : null,
            'label' => '',
        ]];
    }

    // Equipment
    $equipItems = $a->equipment_items ?? [];
    if (empty($equipItems) && $a->equipment) {
        $equipItems = array_filter(array_map('trim', explode(',', $a->equipment)));
    }

    // Brief URL & QR
    $briefUrl = $a->briefingUrl();
    $qrUrl    = 'https://api.qrserver.com/v1/create-qr-code/?size=100x100&margin=4&data=' . urlencode($briefUrl);

    // Has any frequency
    $hasFreq = $a->frequency || $a->secondary_frequency || $a->fallback_frequency;
@endphp

{{-- Pre-header (shown in email client preview) --}}
<div style="display:none;max-height:0;overflow:hidden;mso-hide:all;">
    Your briefing for {{ $event->title }} on {{ $event->starts_at?->format('D j M Y') ?? 'upcoming event' }} — scan the QR code to check in on the day.
    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
</div>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#F2F2F2;">
<tr><td align="center" style="padding:24px 16px;">

<table class="email-wrap" width="620" cellpadding="0" cellspacing="0" border="0">

    {{-- HEADER --}}
    <tr><td class="hdr">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr><td style="padding:20px 32px;">
            <table cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="hdr-logo" width="52" height="52">
                    <span class="hdr-logo-txt">RAY<br>NET</span>
                </td>
                <td style="padding-left:16px;">
                    <p class="hdr-org" style="font-size:15px;font-weight:bold;color:#ffffff;letter-spacing:.04em;text-transform:uppercase;margin:0;">{{ \App\Helpers\RaynetSetting::groupName() }}</p>
                    <p class="hdr-sub" style="font-size:11px;color:rgba(255,255,255,.55);text-transform:uppercase;letter-spacing:.06em;margin:3px 0 0;">Group Reference {{ \App\Helpers\RaynetSetting::groupNumber() }}</p>
                </td>
            </tr>
            </table>
        </td></tr>
        </table>
    </td></tr>

    {{-- HERO --}}
    <tr><td class="hero">
        <p class="hero-eyebrow" style="font-size:10px;font-weight:bold;color:#C8102E;text-transform:uppercase;letter-spacing:.18em;margin:0 0 8px;">Operator Briefing</p>
        <h1 class="hero-title" style="font-size:24px;font-weight:bold;color:#ffffff;margin:0 0 14px;line-height:1.2;">{{ $event->title }}</h1>
        <table cellpadding="0" cellspacing="0" border="0"><tr><td>
            @if ($event->starts_at)
                <span class="hero-chip" style="display:inline-block;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:rgba(255,255,255,.88);font-size:12px;font-weight:bold;padding:4px 12px;margin:0 6px 6px 0;">📅 {{ $event->starts_at->format('D j M Y') }}</span>
                <span class="hero-chip" style="display:inline-block;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:rgba(255,255,255,.88);font-size:12px;font-weight:bold;padding:4px 12px;margin:0 6px 6px 0;">🕐 {{ $event->starts_at->format('H:i') }}{{ $event->ends_at ? ' – '.$event->ends_at->format('H:i') : '' }}</span>
            @endif
            @if ($event->location)
                <span class="hero-chip" style="display:inline-block;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:rgba(255,255,255,.88);font-size:12px;font-weight:bold;padding:4px 12px;margin:0 6px 6px 0;">📍 {{ $event->location }}</span>
            @endif
            <span class="hero-chip" style="display:inline-block;background:rgba(26,107,60,.35);border:1px solid rgba(26,107,60,.6);color:rgba(255,255,255,.95);font-size:12px;font-weight:bold;padding:4px 12px;margin:0 6px 6px 0;">{{ $a->statusLabel() }}</span>
        </td></tr></table>
    </td></tr>

    {{-- BODY --}}
    <tr><td class="email-body">

        {{-- Greeting --}}
        <div class="greeting" style="padding:28px 32px 0;">
            <h2 class="greeting-hi" style="font-size:20px;font-weight:bold;color:#003366;margin:0 0 10px;">Hello {{ $user->name }},</h2>
            <p class="greeting-p" style="font-size:14px;color:#2d4a6b;line-height:1.6;margin:0 0 14px;">
                You have been assigned as <strong>{{ $a->role ?: 'an operator' }}</strong> for the above event. This email contains your personal briefing with all the details you need for the day.
            </p>
            @if ($a->callsign)
            <p class="greeting-p" style="font-size:14px;color:#2d4a6b;line-height:1.6;margin:0;">
                Your callsign for this event is <strong style="color:#003366;">{{ $a->callsign }}</strong>.
            </p>
            @endif
        </div>
        <div class="spacer"></div>

        {{-- ── CHECK-IN CTA ── --}}
        <div class="cta-section" style="margin:0 32px;background:#003366;border-top:3px solid #C8102E;padding:24px;">
            <h3 class="cta-title" style="font-size:16px;font-weight:bold;color:#ffffff;margin:0 0 8px;">📱 Check In on the Day</h3>
            <p class="cta-body" style="font-size:13px;color:rgba(255,255,255,.8);line-height:1.6;margin:0 0 20px;">
                When you arrive at the event, visit your personal brief page to check in. You can also record breaks and check out at the end of your shift — this keeps the event controller informed of who is on site at all times.
            </p>
            <table cellpadding="0" cellspacing="0" border="0" width="100%">
            <tr>
                <td valign="middle">
                    @if ($briefUrl !== '#')
                        <a href="{{ $briefUrl }}" class="cta-btn" style="display:inline-block;background:#C8102E;color:#ffffff;font-size:13px;font-weight:bold;text-decoration:none;text-transform:uppercase;letter-spacing:.06em;padding:13px 24px;">
                            Open My Brief →
                        </a>
                    @endif
                </td>
                @if ($briefUrl !== '#')
                <td valign="middle" align="right" width="116">
                    <div class="cta-qr-wrap" style="background:#ffffff;padding:6px;display:inline-block;">
                        <img src="{{ $qrUrl }}" width="100" height="100" alt="QR code" style="display:block;">
                    </div>
                    <p class="cta-qr-label" style="font-size:9px;color:rgba(255,255,255,.5);text-align:center;text-transform:uppercase;letter-spacing:.08em;margin:6px 0 0;">Scan to open</p>
                </td>
                @endif
            </tr>
            </table>
        </div>
        <div class="spacer"></div>

        {{-- ── HOW TO USE ── --}}
        <div class="section" style="padding:0 32px;">
            <p class="section-head" style="font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.14em;color:#003366;margin:0 0 10px;padding-bottom:7px;border-bottom:2px solid #003366;">How the Check-In Page Works</p>
            <table cellpadding="0" cellspacing="0" border="0" width="100%">
            @foreach ([
                ['✓ Check In',     'Tap <strong>Check In</strong> when you arrive on site. This tells the event controller you\'re operational.'],
                ['⏸ Start Break',  'Use <strong>Start Break</strong> when you step away — for lunch, a comfort break, etc. Your duty time pauses.'],
                ['▶ End Break',    'Tap <strong>End Break</strong> when you return and are ready to resume operations.'],
                ['⏹ Check Out',    'When your shift is complete, tap <strong>Check Out</strong>. The event controller will be notified.'],
            ] as [$step, $desc])
            <tr><td style="padding:9px 0;border-bottom:1px solid #dde2e8;">
                <table cellpadding="0" cellspacing="0" border="0"><tr>
                    <td width="28" valign="top" style="padding-top:1px;">
                        <span style="display:inline-block;background:#003366;color:#ffffff;font-size:11px;font-weight:bold;width:24px;height:24px;text-align:center;line-height:24px;">{{ $loop->iteration }}</span>
                    </td>
                    <td style="padding-left:10px;font-size:13px;color:#2d4a6b;line-height:1.5;">
                        <strong style="color:#001f40;">{{ $step }}</strong> — {!! $desc !!}
                    </td>
                </tr></table>
            </td></tr>
            @endforeach
            </table>
        </div>
        <div class="spacer"></div>

        {{-- ── SCHEDULE ── --}}
        <div class="section" style="padding:0 32px;">
            <p class="section-head" style="font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.14em;color:#003366;margin:0 0 10px;padding-bottom:7px;border-bottom:2px solid #003366;">🕐 Your Schedule</p>
            <table class="details-table" cellpadding="0" cellspacing="0" border="0" width="100%" style="font-size:13px;">
                @if ($a->report_time)
                <tr>
                    <td style="font-weight:bold;color:#6b7f96;text-transform:uppercase;font-size:10px;letter-spacing:.08em;width:110px;padding:9px 10px;border-bottom:1px solid #dde2e8;vertical-align:top;">Report Time</td>
                    <td style="color:#001f40;font-weight:bold;padding:9px 10px;border-bottom:1px solid #dde2e8;font-size:15px;">{{ substr($a->report_time,0,5) }}</td>
                </tr>
                @endif
                @if (!empty($shifts))
                <tr>
                    <td style="font-weight:bold;color:#6b7f96;text-transform:uppercase;font-size:10px;letter-spacing:.08em;width:110px;padding:9px 10px;border-bottom:1px solid #dde2e8;vertical-align:top;">Shifts</td>
                    <td style="padding:9px 10px;border-bottom:1px solid #dde2e8;">
                        @foreach ($shifts as $sh)
                        @php $isBreak = ($sh['type'] ?? 'shift') === 'break'; @endphp
                        <span class="{{ $isBreak ? 'shift-break' : 'shift-work' }}" style="display:inline-block;padding:3px 10px;font-size:12px;font-weight:bold;margin:2px 4px 2px 0;border:1px solid;{{ $isBreak ? 'background:#fef9ec;border-color:#e8c96a;color:#8a5c00;' : 'background:#e8eef5;border-color:rgba(0,51,102,.25);color:#003366;' }}">
                            {{ $isBreak ? '⏸' : '▶' }}
                            {{ $sh['start'] ?? '?' }} – {{ $sh['end'] ?? '?' }}
                            {{ !empty($sh['label']) ? '· '.$sh['label'] : '' }}
                        </span>
                        @endforeach
                    </td>
                </tr>
                @endif
                @if ($a->depart_time)
                <tr>
                    <td style="font-weight:bold;color:#6b7f96;text-transform:uppercase;font-size:10px;letter-spacing:.08em;width:110px;padding:9px 10px;vertical-align:top;">Depart Time</td>
                    <td style="color:#001f40;font-weight:bold;padding:9px 10px;font-size:15px;">{{ substr($a->depart_time,0,5) }}</td>
                </tr>
                @endif
            </table>
        </div>
        <div class="spacer"></div>

        {{-- ── POSITION ── --}}
        @if ($a->location_name || $a->grid_ref || $a->what3words)
        <div class="section" style="padding:0 32px;">
            <p class="section-head" style="font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.14em;color:#003366;margin:0 0 10px;padding-bottom:7px;border-bottom:2px solid #003366;">📍 Your Position</p>
            <table class="details-table" cellpadding="0" cellspacing="0" border="0" width="100%" style="font-size:13px;">
                @if ($a->location_name)
                <tr>
                    <td style="font-weight:bold;color:#6b7f96;text-transform:uppercase;font-size:10px;letter-spacing:.08em;width:110px;padding:9px 10px;border-bottom:1px solid #dde2e8;vertical-align:top;">Location</td>
                    <td style="color:#001f40;font-weight:bold;padding:9px 10px;border-bottom:1px solid #dde2e8;">{{ $a->location_name }}</td>
                </tr>
                @endif
                @if ($a->grid_ref)
                <tr>
                    <td style="font-weight:bold;color:#6b7f96;text-transform:uppercase;font-size:10px;letter-spacing:.08em;width:110px;padding:9px 10px;border-bottom:1px solid #dde2e8;vertical-align:top;">OS Grid</td>
                    <td style="color:#001f40;font-weight:bold;padding:9px 10px;border-bottom:1px solid #dde2e8;">{{ $a->grid_ref }}</td>
                </tr>
                @endif
                @if ($a->what3words)
                <tr>
                    <td style="font-weight:bold;color:#6b7f96;text-transform:uppercase;font-size:10px;letter-spacing:.08em;width:110px;padding:9px 10px;vertical-align:top;">What3Words</td>
                    <td style="padding:9px 10px;"><a href="https://what3words.com/{{ $a->what3words }}" style="color:#C8102E;text-decoration:none;font-weight:bold;">///{{ $a->what3words }}</a></td>
                </tr>
                @endif
            </table>
        </div>
        <div class="spacer"></div>
        @endif

        {{-- ── FREQUENCIES ── --}}
        @if ($hasFreq)
        <div class="section" style="padding:0 32px;">
            <p class="section-head" style="font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.14em;color:#003366;margin:0 0 10px;padding-bottom:7px;border-bottom:2px solid #003366;">📻 Your Frequencies</p>
            <table class="freq-table" cellpadding="0" cellspacing="0" border="0" width="100%">
                <thead>
                    <tr>
                        <th style="background:#003366;color:rgba(255,255,255,.8);font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;padding:7px 10px;text-align:left;">Tier</th>
                        <th style="background:#003366;color:rgba(255,255,255,.8);font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;padding:7px 10px;text-align:left;">Frequency</th>
                        <th style="background:#003366;color:rgba(255,255,255,.8);font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;padding:7px 10px;text-align:left;">Mode</th>
                        <th style="background:#003366;color:rgba(255,255,255,.8);font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;padding:7px 10px;text-align:left;">CTCSS</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($a->frequency)
                    <tr>
                        <td style="padding:9px 10px;border-bottom:1px solid #dde2e8;"><span class="tier-badge tier-pri" style="display:inline-block;padding:1px 7px;font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.04em;border:1px solid;background:#e8eef5;border-color:rgba(0,51,102,.3);color:#003366;">★ Primary</span></td>
                        <td style="padding:9px 10px;border-bottom:1px solid #dde2e8;font-weight:bold;font-size:15px;color:#001f40;">{{ $a->frequency }}</td>
                        <td style="padding:9px 10px;border-bottom:1px solid #dde2e8;color:#001f40;">{{ $a->mode }}</td>
                        <td style="padding:9px 10px;border-bottom:1px solid #dde2e8;color:#001f40;">{{ $a->ctcss_tone ?: '—' }}</td>
                    </tr>
                    @endif
                    @if ($a->secondary_frequency)
                    <tr>
                        <td style="padding:9px 10px;border-bottom:1px solid #dde2e8;"><span class="tier-badge tier-sec" style="display:inline-block;padding:1px 7px;font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.04em;border:1px solid;background:#eef7f2;border-color:#b8ddc9;color:#1a6b3c;">Secondary</span></td>
                        <td style="padding:9px 10px;border-bottom:1px solid #dde2e8;font-weight:bold;font-size:15px;color:#001f40;">{{ $a->secondary_frequency }}</td>
                        <td style="padding:9px 10px;border-bottom:1px solid #dde2e8;color:#001f40;">{{ $a->secondary_mode ?? '—' }}</td>
                        <td style="padding:9px 10px;border-bottom:1px solid #dde2e8;color:#001f40;">{{ $a->secondary_ctcss ?? '—' }}</td>
                    </tr>
                    @endif
                    @if ($a->fallback_frequency)
                    <tr>
                        <td style="padding:9px 10px;"><span class="tier-badge tier-fal" style="display:inline-block;padding:1px 7px;font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.04em;border:1px solid;background:#fef9ec;border-color:#e8c96a;color:#8a5c00;">Fallback</span></td>
                        <td style="padding:9px 10px;font-weight:bold;font-size:15px;color:#001f40;">{{ $a->fallback_frequency }}</td>
                        <td style="padding:9px 10px;color:#001f40;">{{ $a->fallback_mode ?? '—' }}</td>
                        <td style="padding:9px 10px;color:#001f40;">{{ $a->fallback_ctcss ?? '—' }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="spacer"></div>
        @endif

        {{-- ── EQUIPMENT ── --}}
        @if (!empty($equipItems))
        <div class="section" style="padding:0 32px;">
            <p class="section-head" style="font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.14em;color:#003366;margin:0 0 10px;padding-bottom:7px;border-bottom:2px solid #003366;">🎒 Equipment to Bring</p>
            <table cellpadding="0" cellspacing="0" border="0" width="100%">
            @foreach ($equipItems as $item)
            <tr><td style="padding:7px 0;border-bottom:1px solid #dde2e8;">
                <table cellpadding="0" cellspacing="0" border="0"><tr>
                    <td width="22" valign="middle"><div style="width:14px;height:14px;border:2px solid #dde2e8;"></div></td>
                    <td style="padding-left:8px;font-size:13px;color:#001f40;">{{ $item }}</td>
                </tr></table>
            </td></tr>
            @endforeach
            </table>
        </div>
        <div class="spacer"></div>
        @endif

        {{-- ── BRIEFING NOTES ── --}}
        @if ($a->briefing_notes)
        <div class="section" style="padding:0 32px;">
            <p class="section-head" style="font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.14em;color:#003366;margin:0 0 10px;padding-bottom:7px;border-bottom:2px solid #003366;">📋 Briefing Notes</p>
            <div class="notes-box" style="background:#fef9ec;border-left:4px solid #e8c96a;padding:13px 16px;font-size:13px;color:#001f40;line-height:1.6;">
                {{ $a->briefing_notes }}
            </div>
        </div>
        <div class="spacer"></div>
        @endif

        {{-- ── REMINDER CTA ── --}}
        @if ($briefUrl !== '#')
        <div style="margin:0 32px;background:#e8eef5;border:1px solid rgba(0,51,102,.15);border-left:4px solid #003366;padding:16px 20px;">
            <p style="font-size:13px;color:#003366;font-weight:bold;margin:0 0 6px;">Remember — check in when you arrive</p>
            <p style="font-size:12px;color:#2d4a6b;line-height:1.5;margin:0 0 12px;">Keep this email handy or save the link to your phone. You can also scan the QR code in the section above.</p>
            <a href="{{ $briefUrl }}" style="display:inline-block;background:#003366;color:#ffffff;font-size:12px;font-weight:bold;text-decoration:none;text-transform:uppercase;letter-spacing:.06em;padding:10px 20px;">Open My Brief →</a>
        </div>
        @endif

        <div class="spacer"></div>
        <div class="spacer-sm"></div>

    </td></tr>

    {{-- FOOTER --}}
    <tr><td class="email-footer" style="background:#001f40;padding:20px 32px;">
        <p class="footer-line" style="font-size:11px;color:rgba(255,255,255,.4);margin:0 0 4px;line-height:1.5;">
            {{ \App\Helpers\RaynetSetting::groupName() }} Group · Group Reference {{ \App\Helpers\RaynetSetting::groupNumber() }} · Member of RAYNET-UK
        </p>
        <p class="footer-line" style="font-size:11px;color:rgba(255,255,255,.4);margin:0 0 4px;line-height:1.5;">
            This email was sent to {{ $user->email }} because you are assigned to {{ $event->title }}.
        </p>
        <p class="footer-line" style="font-size:11px;color:rgba(255,255,255,.3);margin:0;line-height:1.5;">
            If you believe this was sent in error, please contact your group controller.
        </p>
    </td></tr>

</table>

</td></tr>
</table>
</body>
</html>