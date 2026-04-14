{{-- resources/views/committee/dashboard.blade.php --}}
@extends('committee.layout')

@section('committee-content')

<div class="committee-page-header">
    <h1>Command Desk</h1>
    <p>Live operational picture — {{ now()->format('l j F Y') }}</p>
</div>

{{-- ── Headline Score Row ─────────────────────────────────────────────────── --}}
<div style="display:grid; grid-template-columns: auto 1fr; gap:20px; margin-bottom:28px; align-items:stretch;">

    {{-- Big Score --}}
    <div class="committee-panel" style="padding:28px 36px; text-align:center; min-width:200px;">
        <div style="font-size:11px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#9ca3af; margin-bottom:8px;">OVERALL READINESS</div>
        <div style="font-size:64px; font-weight:700; color:var(--raynet-navy); line-height:1;">
            {{ $metrics['overall_score'] }}
        </div>
        <div style="font-size:14px; color:#6b7280; margin-bottom:12px;">/ 100</div>
        @php
            $band = $metrics['readiness_band'];
            $bandColour = match(true) {
                str_contains($band, 'strong')      => 'green',
                str_contains($band, 'Deployable')  => 'blue',
                str_contains($band, 'Limited')     => 'amber',
                str_contains($band, 'Development') => 'orange',
                default                            => 'red',
            };
        @endphp
        <span class="pill pill--{{ $bandColour }}" style="font-size:12px;">{{ $band }}</span>
        <div style="margin-top:12px; font-size:13px; color:#374151;">
            Assurance Grade: <strong>{{ $metrics['assurance_grade'] }}</strong>
            &nbsp;({{ $metrics['assurance_pct'] }}% evidence current)
        </div>
    </div>

    {{-- Category bars --}}
    <div class="committee-panel">
        <div class="committee-panel__header">
            <h3 class="committee-panel__title">Category Breakdown</h3>
            <a href="{{ route('committee.readiness.index') }}" class="btn btn--secondary btn--sm">Full Detail →</a>
        </div>
        <div class="committee-panel__body" style="padding:12px 20px;">
            @foreach($metrics['categories'] as $cat)
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:10px;">
                <div style="flex:0 0 200px; font-size:12px; color:#374151;">{{ $cat['name'] }}</div>
                <div style="flex:1; display:flex; align-items:center; gap:8px;">
                    <div class="progress-bar" style="flex:1;">
                        <div class="progress-bar__fill progress-bar__fill--{{ $cat['status'] }}"
                             style="width:{{ $cat['pct'] }}%;"></div>
                    </div>
                    <span style="font-size:11px; font-weight:700; width:40px; text-align:right; color:#374151;">
                        {{ number_format($cat['points'], 1) }}/{{ $cat['weight'] }}
                    </span>
                </div>
                <span class="pill pill--{{ $cat['status'] }}" style="flex:0 0 48px; justify-content:center;">
                    {{ ucfirst($cat['status']) }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ── Operational Metrics ────────────────────────────────────────────────── --}}
<div class="metric-grid">
    <div class="metric-card {{ $ops60 >= 4 ? 'metric-card--green' : ($ops60 >= 2 ? 'metric-card--amber' : 'metric-card--red') }}">
        <div class="metric-card__label">Ops ≤60 min</div>
        <div class="metric-card__value">{{ $ops60 }}</div>
        <div class="metric-card__sub">named operators</div>
    </div>

    <div class="metric-card {{ $ops120 >= 8 ? 'metric-card--green' : ($ops120 >= 4 ? 'metric-card--amber' : 'metric-card--red') }}">
        <div class="metric-card__label">Ops ≤120 min</div>
        <div class="metric-card__value">{{ $ops120 }}</div>
        <div class="metric-card__sub">incl. wider area</div>
    </div>

    <div class="metric-card {{ $trainingCurrencyPct >= 80 ? 'metric-card--green' : ($trainingCurrencyPct >= 50 ? 'metric-card--amber' : 'metric-card--red') }}">
        <div class="metric-card__label">Training Currency</div>
        <div class="metric-card__value">{{ $trainingCurrencyPct }}%</div>
        <div class="metric-card__sub">induction current</div>
    </div>

    <div class="metric-card {{ $equipmentPct >= 90 ? 'metric-card--green' : ($equipmentPct >= 70 ? 'metric-card--amber' : 'metric-card--red') }}">
        <div class="metric-card__label">Equipment</div>
        <div class="metric-card__value">{{ $equipmentPct }}%</div>
        <div class="metric-card__sub">serviceable</div>
    </div>

    <div class="metric-card {{ $totalNets > 0 && $operationalNets === $totalNets ? 'metric-card--green' : ($operationalNets > 0 ? 'metric-card--amber' : 'metric-card--red') }}">
        <div class="metric-card__label">Networks</div>
        <div class="metric-card__value">{{ $operationalNets }}/{{ $totalNets }}</div>
        <div class="metric-card__sub">operational</div>
    </div>

    <div class="metric-card {{ $overdueActions === 0 ? 'metric-card--green' : 'metric-card--red' }}">
        <div class="metric-card__label">Open Actions</div>
        <div class="metric-card__value">{{ $openActions }}</div>
        <div class="metric-card__sub">{{ $overdueActions }} overdue</div>
    </div>
</div>

{{-- ── Lower row: Next Exercise + Key Risks ──────────────────────────────── --}}
<div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">

    {{-- Next exercise --}}
    <div class="committee-panel">
        <div class="committee-panel__header">
            <h3 class="committee-panel__title">Next Exercise / Activity</h3>
            <a href="{{ route('committee.exercises.index') }}" class="btn btn--secondary btn--sm">Log →</a>
        </div>
        <div class="committee-panel__body">
            @if($nextExercise)
                <div style="font-size:22px; font-weight:700; color:var(--raynet-navy);">
                    {{ $nextExercise->date->format('j M Y') }}
                </div>
                <div style="font-size:14px; margin-top:4px; color:#374151;">{{ $nextExercise->activity }}</div>
                <div style="margin-top:6px;">
                    <span class="pill pill--blue">{{ $nextExercise->typeLabel() }}</span>
                    @if($nextExercise->lead)
                        <span style="font-size:12px; color:#6b7280; margin-left:8px;">Lead: {{ $nextExercise->lead }}</span>
                    @endif
                </div>
                <div style="font-size:12px; color:#9ca3af; margin-top:6px;">
                    in {{ now()->diffInDays($nextExercise->date) }} days
                </div>
            @else
                <p style="font-size:13px; color:#9ca3af; margin:0;">No upcoming exercises logged.</p>
                <a href="{{ route('committee.exercises.create') }}" class="btn btn--primary btn--sm" style="margin-top:12px;">Log exercise</a>
            @endif
        </div>
    </div>

    {{-- Key Risks --}}
    <div class="committee-panel">
        <div class="committee-panel__header">
            <h3 class="committee-panel__title">Key Risks</h3>
            <a href="{{ route('committee.risks.index') }}" class="btn btn--secondary btn--sm">Risk register →</a>
        </div>
        <div class="committee-panel__body" style="padding: 0;">
            @forelse($keyRisks as $risk)
                <div style="display:flex; align-items:center; gap:10px; padding:10px 20px; border-bottom:1px solid #f3f4f6;">
                    <div style="flex:0 0 32px; height:32px; border-radius:6px; background:{{ match($risk->riskColour()) {
                        'red' => '#fee2e2', 'orange' => '#ffedd5', default => '#fef9c3'
                    } }}; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; color:{{ match($risk->riskColour()) {
                        'red' => '#b91c1c', 'orange' => '#c2410c', default => '#92400e'
                    } }};">
                        {{ $risk->riskScore() }}
                    </div>
                    <div style="flex:1; font-size:13px; color:#374151;">{{ $risk->title }}</div>
                    <span class="pill pill--{{ $risk->riskColour() }}">{{ $risk->riskLabel() }}</span>
                </div>
            @empty
                <p style="font-size:13px; color:#9ca3af; padding:20px; margin:0;">No high risks recorded.</p>
            @endforelse
        </div>
    </div>

</div>

@endsection
