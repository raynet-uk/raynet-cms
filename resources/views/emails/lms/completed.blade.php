<x-emails.layouts.lms
    headerTitle="Course Complete 🏅"
    headerSubtitle="Congratulations — well done!">

@php
$badgeColours = [
    1=>'#003366',2=>'#0277bd',3=>'#1a7a3c',4=>'#b45309',5=>'#C8102E',
    101=>'#5b21b6',102=>'#5b21b6',
    111=>'#0f766e',112=>'#0f766e',113=>'#0f766e',114=>'#0f766e',115=>'#0f766e',116=>'#0f766e',
    121=>'#be185d',122=>'#be185d',123=>'#be185d',124=>'#be185d',
    201=>'#374151',202=>'#374151',
];
@endphp

{{-- Success banner --}}
<div class="success-banner">
    <div class="success-icon">🎓</div>
    <div class="success-title">{{ $user->name }}</div>
    <div class="success-sub">has successfully completed</div>
    <div style="font-size:18px;font-weight:bold;color:#fff;margin-top:8px;padding:0 20px;">
        {{ $course->title }}
    </div>
</div>

<div class="body">
    <p class="text">
        Congratulations on completing <strong>{{ $course->title }}</strong>.
        Your progress has been recorded and your Group Controller has been notified.
    </p>

    @if($certificate)
    <div class="highlight-box" style="border-left-color:#1a6b3c;background:#eef7f2;">
        <div class="course-title" style="color:#1a6b3c;">🏅 Certificate Issued</div>
        <div class="course-meta">Certificate No: {{ $certificate->certificate_number }}</div>
        <div class="course-meta">Issued: {{ $certificate->issued_at->format('d F Y') }}</div>
    </div>

    <div class="btn-wrap">
        <a href="{{ url('/my-training/certificate/' . $course->id) }}" class="btn btn-green">
            🏅 View &amp; Download Certificate
        </a>
    </div>
    @endif

    @if(!empty($unlockedBadges))
    <div class="divider"></div>
    <p class="text" style="font-size:12px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;color:#6b7f96;margin-bottom:12px;">
        🏅 Badges unlocked
    </p>
    @foreach($unlockedBadges as $badge)
    @php $colour = $badgeColours[$badge['id']] ?? '#003366'; @endphp
    <div class="badge-row" style="background:#f5f8ff;border-color:#dde2e8;">
        <div class="badge-hex">
            <svg width="36" height="36" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg">
                <polygon points="18,2 32,10 32,26 18,34 4,26 4,10"
                         fill="{{ $colour }}" stroke="{{ $colour }}" stroke-width="1.5"/>
                <polygon points="18,3 31,10 26,3" fill="rgba(255,255,255,.12)" stroke="none"/>
                <text x="18" y="23" text-anchor="middle" font-family="Arial,sans-serif"
                      font-size="9" font-weight="bold" fill="#fff">✓</text>
            </svg>
        </div>
        <div>
            <div class="badge-name">{{ $badge['label'] }}</div>
            <div class="badge-sub">Badge unlocked · Visible on your profile</div>
        </div>
        <div class="badge-pill badge-pill-green">✓ Earned</div>
    </div>
    @endforeach
    @endif

    <div class="divider"></div>

    <p class="text">
        Your training badge{{ count($unlockedBadges) > 1 ? 's are' : ' is' }} now visible on your member profile.
        Keep up the excellent work — there are more courses available in the training portal.
    </p>

    <div class="btn-wrap">
        <a href="{{ url('/my-training') }}" class="btn">Browse More Courses</a>
    </div>
</div>

</x-emails.layouts.lms>