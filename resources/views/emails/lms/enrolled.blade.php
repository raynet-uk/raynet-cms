<x-emails.layouts.lms
    headerTitle="You've been enrolled on a new course"
    headerSubtitle="{{ \App\Helpers\RaynetSetting::groupName() }} Training Portal">

<div class="body">
    <div class="greeting">Hello {{ $user->name }},</div>
    <p class="text">
        Your Group Controller has enrolled you on the following RAYNET training course.
        Click the button below to get started — you can complete it at your own pace.
    </p>

    <div class="highlight-box">
        <div class="course-title">{{ $course->title }}</div>
        <div class="course-meta">
            {{ ucfirst($course->difficulty) }}
            @if($course->category) · {{ $course->category }}@endif
            @if($course->estimated_hours) · ~{{ $course->estimated_hours }} hours@endif
        </div>
        @if($course->description)
        <p style="font-size:13px;color:#2d4a6b;margin-top:10px;line-height:1.6;">{{ $course->description }}</p>
        @endif
    </div>

    <div class="meta-grid">
        @if($dueDate)
        <div class="meta-row">
            <div class="meta-label">Due Date</div>
            <div class="meta-val" style="color:#C8102E;">{{ $dueDate }}</div>
        </div>
        @endif
        <div class="meta-row">
            <div class="meta-label">Certificate</div>
            <div class="meta-val">{{ $course->certificate_enabled ? '✓ Issued on completion' : 'Not available' }}</div>
        </div>
        @if($course->estimated_hours)
        <div class="meta-row">
            <div class="meta-label">Duration</div>
            <div class="meta-val">~{{ $course->estimated_hours }} hours</div>
        </div>
        @endif
    </div>

    @if(!empty($course->unlocks_badge_ids))
    @php
    $badgeMap = [
        1=>'Operator',2=>'Checkpoint Supervisor',3=>'Net Controller',
        4=>'Event Manager',5=>'Response Manager',
        101=>'Power Systems',102=>'Digital Modes',
        111=>'Mapping',112=>'Severe Weather',113=>'First Aid Comms',
        114=>'Marathon Ops',115=>'Air Support',116=>'Water Ops',
        121=>'GDPR',122=>'Media Liaison',123=>'Safeguarding',124=>'No Secret Codes',
        201=>'Antennas',202=>'NVIS',
    ];
    @endphp
    <div class="divider"></div>
    <p class="text" style="font-size:12px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;color:#6b7f96;margin-bottom:10px;">
        🏅 Badges you'll earn on completion
    </p>
    @foreach($course->unlocks_badge_ids as $bid)
    @if(isset($badgeMap[(int)$bid]))
    <div class="badge-row">
        <div class="badge-hex">
            <svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                <polygon points="16,2 28,9 28,23 16,30 4,23 4,9" fill="#dde2e8" stroke="#c8d4e0" stroke-width="1.5"/>
                <text x="16" y="21" text-anchor="middle" font-family="Arial,sans-serif" font-size="8" font-weight="bold" fill="rgba(0,0,0,.3)">?</text>
            </svg>
        </div>
        <div>
            <div class="badge-name">{{ $badgeMap[(int)$bid] }}</div>
            <div class="badge-sub">Unlocks when you complete this course</div>
        </div>
        <div class="badge-pill badge-pill-navy">🔒 Locked</div>
    </div>
    @endif
    @endforeach
    @endif

    <div class="btn-wrap">
        <a href="{{ url('/my-training/' . $course->slug) }}" class="btn">▶ Start Course Now</a>
    </div>

    <p class="text" style="font-size:12px;color:#9aa3ae;text-align:center;">
        If you have any questions about this course, please contact your Group Controller.
    </p>
</div>

</x-emails.layouts.lms>