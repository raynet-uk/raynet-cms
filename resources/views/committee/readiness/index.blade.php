{{-- resources/views/committee/readiness/index.blade.php --}}
@extends('committee.layout')

@section('committee-content')

<div class="committee-page-header">
    <h1>Readiness & Assurance</h1>
    <p>Scoring overview. To update scores, use the matrix. Evidence must be current within 12 months to count toward assurance.</p>
</div>

<div style="display:flex; gap:16px; margin-bottom:28px; align-items:center;">
    <a href="{{ route('committee.readiness.matrix') }}" class="btn btn--primary">Update Scores</a>
    <a href="{{ route('committee.readiness.lrf') }}" class="btn btn--secondary">LRF Report</a>
    <span style="font-size:13px; color:#6b7280; margin-left:auto;">
        Overall: <strong style="color:var(--raynet-navy); font-size:18px;">{{ $metrics['overall_score'] }}/100</strong>
        &nbsp;·&nbsp; Grade: <strong>{{ $metrics['assurance_grade'] }}</strong>
        &nbsp;·&nbsp; {{ $metrics['assurance_pct'] }}% evidence current
    </span>
</div>

<div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(320px,1fr)); gap:20px;">
    @foreach($metrics['categories'] as $cat)
    <div class="committee-panel">
        <div class="committee-panel__header">
            <h3 class="committee-panel__title">{{ $cat['name'] }}</h3>
            <div style="display:flex; align-items:center; gap:8px;">
                <span style="font-size:20px; font-weight:700; color:var(--raynet-navy);">
                    {{ number_format($cat['points'], 1) }}
                </span>
                <span style="font-size:12px; color:#9ca3af;">/ {{ $cat['weight'] }}</span>
                <span class="pill pill--{{ $cat['status'] }}">{{ $cat['pct'] }}%</span>
            </div>
        </div>
        <div class="committee-panel__body" style="padding:12px 20px;">
            @foreach($cat['indicators'] as $ind)
            @php $score = $ind->score; $raw = $score?->raw_score ?? 0; @endphp
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
                <span style="font-size:11px; font-weight:700; color:var(--raynet-navy); width:30px; flex-shrink:0; font-family:monospace;">
                    {{ $ind->code }}
                </span>
                <div style="flex:1;">
                    <div style="font-size:11px; color:#374151; line-height:1.3;">{{ $ind->indicator_name }}</div>
                    @if($score?->evidence_date)
                        <div style="font-size:10px; color:{{ $score->isEvidenceCurrent() ? '#15803d' : '#dc2626' }}; margin-top:1px;">
                            Evidence: {{ $score->evidence_date->format('j M Y') }}
                            {{ $score->isEvidenceCurrent() ? '✓' : '⚠ Stale' }}
                        </div>
                    @endif
                </div>
                {{-- Score badge --}}
                @php
                    $colours = ['#fee2e2','#ffedd5','#fef9c3','#dbeafe','#d1fae5','#dcfce7'];
                    $tcolours = ['#b91c1c','#c2410c','#92400e','#1d4ed8','#065f46','#15803d'];
                @endphp
                <div style="width:28px; height:28px; border-radius:6px; background:{{ $colours[$raw] ?? '#f3f4f6' }};
                            display:flex; align-items:center; justify-content:center;
                            font-size:14px; font-weight:700; color:{{ $tcolours[$raw] ?? '#374151' }}; flex-shrink:0;">
                    {{ $raw }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

{{-- Guidance box --}}
<div class="committee-panel" style="margin-top:20px;">
    <div class="committee-panel__header">
        <h3 class="committee-panel__title">Scoring Guide</h3>
    </div>
    <div class="committee-panel__body">
        <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:12px;">
            @foreach([0=>'Absent',1=>'Ad hoc',2=>'Basic',3=>'Functional',4=>'Robust',5=>'Proven'] as $n => $label)
            @php $c = ['#fee2e2','#ffedd5','#fef9c3','#dbeafe','#d1fae5','#dcfce7'][$n]; @endphp
            <div style="display:flex; align-items:center; gap:6px; background:{{ $c }}; padding:6px 12px; border-radius:6px;">
                <strong style="font-size:13px;">{{ $n }}</strong>
                <span style="font-size:12px;">{{ $label }}</span>
            </div>
            @endforeach
        </div>
        <div style="display:flex; gap:20px; flex-wrap:wrap; font-size:12px; color:#6b7280;">
            @foreach([
                'Not fit to present externally' => '< 40',
                'Developmental' => '40–54',
                'Limited capability' => '55–69',
                'Deployable with manageable gaps' => '70–84',
                'Operationally strong' => '85–100',
            ] as $band => $range)
            <div><strong>{{ $range }}</strong> — {{ $band }}</div>
            @endforeach
        </div>
    </div>
</div>

@endsection
