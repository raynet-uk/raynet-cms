<x-emails.layouts.lms
    headerTitle="Don't forget your training"
    headerSubtitle="You've got unfinished business">

<div class="body">
    <div class="greeting">Hello {{ $user->name }},</div>
    <p class="text">
        It looks like you haven't made any progress on your RAYNET training course in a while.
        You're {{ $progressPct }}% of the way through — don't let it go to waste!
    </p>

    <div class="highlight-box">
        <div class="course-title">{{ $course->title }}</div>
        <div class="course-meta">
            {{ ucfirst($course->difficulty) }}
            @if($course->category) · {{ $course->category }}@endif
        </div>
        <div class="progress-wrap" style="margin-top:12px;">
            <div class="progress-label">
                <span>Progress</span>
                <span>{{ $progressPct }}% complete</span>
            </div>
            <div class="progress-track">
                <div class="progress-fill" style="width:{{ $progressPct }}%;background:#0288d1;"></div>
            </div>
        </div>
    </div>

    @if($dueDate)
    <div class="callout">
        <div class="callout-title">⚠ Due Date Approaching</div>
        <div class="callout-text">
            This course is due on <strong>{{ $dueDate }}</strong>.
            Make sure you complete it in time — your Group Controller can see your progress.
        </div>
    </div>
    @endif

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
    <p class="text" style="font-size:12px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;color:#6b7f96;margin-bottom:10px;">
        🏅 Still waiting for you
    </p>
    @foreach($course->unlocks_badge_ids as $bid)
    @if(isset($badgeMap[(int)$bid]))
    <div class="badge-row">
        <div class="badge-hex">
            <svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                <polygon points="16,2 28,9 28,23 16,30 4,23 4,9" fill="#dde2e8" stroke="#c8d4e0" stroke-width="1.5"/>
                <text x="16" y="21" text-anchor="middle" font-family="Arial,sans-serif" font-size="9" font-weight="bold" fill="rgba(0,0,0,.25)">🔒</text>
            </svg>
        </div>
        <div>
            <div class="badge-name">{{ $badgeMap[(int)$bid] }}</div>
            <div class="badge-sub">Complete the course to unlock this badge</div>
        </div>
        <div class="badge-pill badge-pill-navy">🔒 Locked</div>
    </div>
    @endif
    @endforeach
    @endif

    <div class="btn-wrap" style="margin-top:28px;">
        <a href="{{ url('/my-training/' . $course->slug) }}" class="btn btn-teal">
            ▶ Continue Where I Left Off
        </a>
    </div>

    <div class="divider"></div>

    <p class="text" style="font-size:12px;color:#9aa3ae;text-align:center;">
        You can complete this course at any time. If you have questions, contact your Group Controller.
    </p>
</div>

</x-emails.layouts.lms>