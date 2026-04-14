{{-- resources/views/committee/readiness/lrf.blade.php --}}
@extends('committee.layout')

@section('committee-content')

<div class="committee-page-header">
    <h1>LRF Reporting</h1>
    <p>External-facing capability summary for Local Resilience Forum partners. Review all service levels before issue.</p>
</div>

<div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:24px;">

    {{-- Published statement --}}
    <div class="committee-panel">
        <div class="committee-panel__header">
            <h3 class="committee-panel__title">Generated Statement</h3>
            <button onclick="copyStatement()" class="btn btn--secondary btn--sm">Copy</button>
        </div>
        <div class="committee-panel__body">
            <div id="statement-text" style="font-size:12px; line-height:1.7; color:#374151;
                 background:var(--raynet-grey); padding:14px; border-radius:6px;
                 border-left:4px solid var(--raynet-navy);">
                {{ $statement }}
            </div>
        </div>
    </div>

    {{-- Readiness summary box --}}
    <div class="committee-panel">
        <div class="committee-panel__header">
            <h3 class="committee-panel__title">Current Headline Figures</h3>
        </div>
        <div class="committee-panel__body">
            <div style="display:flex; gap:12px; margin-bottom:16px;">
                <div style="text-align:center; flex:1; padding:12px; background:var(--raynet-grey); border-radius:6px;">
                    <div style="font-size:32px; font-weight:700; color:var(--raynet-navy);">{{ $metrics['overall_score'] }}</div>
                    <div style="font-size:11px; color:#6b7280;">/ 100</div>
                </div>
                <div style="text-align:center; flex:1; padding:12px; background:var(--raynet-grey); border-radius:6px;">
                    <div style="font-size:32px; font-weight:700; color:var(--raynet-navy);">{{ $metrics['assurance_grade'] }}</div>
                    <div style="font-size:11px; color:#6b7280;">Assurance</div>
                </div>
                <div style="text-align:center; flex:1; padding:12px; background:var(--raynet-grey); border-radius:6px;">
                    <div style="font-size:32px; font-weight:700; color:var(--raynet-navy);">{{ $metrics['assurance_pct'] }}%</div>
                    <div style="font-size:11px; color:#6b7280;">Evidence current</div>
                </div>
            </div>

            @foreach($metrics['categories'] as $cat)
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
                <span class="pill pill--{{ $cat['status'] }}" style="flex:0 0 44px; justify-content:center;">
                    {{ $cat['pct'] }}%
                </span>
                <span style="font-size:12px; color:#374151;">{{ $cat['name'] }}</span>
            </div>
            @endforeach

            <div style="margin-top:12px; padding:10px; background:#fef9c3; border-radius:6px; border:1px solid #fde68a;">
                <div style="font-size:11px; font-weight:700; color:#92400e; margin-bottom:4px;">⚠ Partner-facing rule</div>
                <div style="font-size:11px; color:#92400e;">
                    Never publish the score number alone. Always accompany with service levels, geography, duration sustainable, and caveats.
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Service levels form --}}
<div class="committee-panel">
    <div class="committee-panel__header">
        <h3 class="committee-panel__title">Declared Service Levels</h3>
        <p style="font-size:11px; color:#9ca3af; margin:0;">Edit the values you are prepared to declare externally. Be honest.</p>
    </div>
    <div class="committee-panel__body">
        <form action="{{ route('committee.readiness.service-levels') }}" method="POST" class="committee-form" style="max-width:100%;">
            @csrf

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                @php
                    $labels = [
                        'organisation_name'   => 'Organisation name',
                        'operating_area'      => 'Operating area / footprint',
                        'operators_60min'     => 'Operators deployable ≤60 min',
                        'operators_120min'    => 'Operators deployable ≤120 min',
                        'oncall_team_leader'  => 'On-call team leader available?',
                        'voice_modes'         => 'Voice modes available',
                        'alternative_bearers' => 'Alternative bearers (data modes)',
                        'endurance_hours'     => 'Independent endurance (hours)',
                        'sustain_24hr'        => '24-hour operation capability',
                        'geographic_limits'   => 'Geographic limitations',
                        'key_caveats'         => 'Key caveats / assumptions',
                    ];
                    $hints = [
                        'operators_60min'    => 'Use names you can evidence inside the Liverpool footprint',
                        'alternative_bearers'=> 'List only current, maintained bearers you can evidence',
                        'key_caveats'        => 'State boundaries plainly — honest caveats increase credibility',
                    ];
                @endphp

                @foreach($serviceLevels as $sl)
                @php $label = $labels[$sl->key] ?? $sl->key; $hint = $hints[$sl->key] ?? null; @endphp
                <div class="form-group">
                    <label>{{ $label }}</label>
                    <input type="hidden" name="levels[{{ $loop->index }}][key]" value="{{ $sl->key }}">
                    @if(in_array($sl->key, ['geographic_limits','key_caveats','operating_area']))
                        <textarea name="levels[{{ $loop->index }}][value]"
                                  rows="2" style="resize:vertical;">{{ $sl->value }}</textarea>
                    @else
                        <input type="text" name="levels[{{ $loop->index }}][value]" value="{{ $sl->value }}">
                    @endif
                    @if($hint)
                        <div class="hint">{{ $hint }}</div>
                    @endif
                </div>
                @endforeach
            </div>

            <div style="margin-top:20px; display:flex; gap:10px;">
                <button type="submit" class="btn btn--primary">Save service levels</button>
                <a href="{{ route('committee.readiness.index') }}" class="btn btn--secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

{{-- Capability narrative (static, from client brief) --}}
<div class="committee-panel" style="margin-top:20px;">
    <div class="committee-panel__header">
        <h3 class="committee-panel__title">Capability Narrative — LRF Partners</h3>
        <span style="font-size:11px; color:#9ca3af;">Companion note to the Readiness Index</span>
    </div>
    <div class="committee-panel__body" style="font-size:13px; line-height:1.8; color:#374151;">
        <p><strong>Purpose</strong> — Liverpool RAYNET is a volunteer auxiliary communications capability based in Liverpool and intended to support local resilience partners with disciplined communications, message handling, and liaison tasks where primary systems are degraded, overloaded, or temporarily unavailable.</p>
        <p><strong>Likely role</strong> — The model is weighted towards the tasks Liverpool RAYNET is most likely to perform credibly: rapid local mobilisation within the Liverpool footprint; short-notice deployment of trained operators; temporary links between sites; formal traffic handling and logs; and support to rendezvous points, welfare centres, local authority coordination points, and similar defined locations.</p>
        <p><strong>Assurance method</strong> — The readiness score is evidence-based. High scores require named operators, recent test dates, current records, and proof under exercise or operational conditions. Low scores should be read as improvement priorities rather than as marketing problems.</p>
        <p><strong>Operating boundaries</strong> — This remains a volunteer capability. Availability depends on member turnout, safety, access, transport, power, internet conditions, and the clarity of the task requested.</p>
        <p style="background:#fef9c3; padding:10px 14px; border-radius:6px; border-left:3px solid #fde68a;">
            <strong>Important caveat:</strong> This summary is deliberately conservative. It should be published with scope, time-to-deploy, duration sustainable, and clear operating assumptions. Liverpool RAYNET is an auxiliary volunteer capability and should not be described as a substitute for statutory primary systems.
        </p>
    </div>
</div>

@push('scripts')
<script>
function copyStatement() {
    const text = document.getElementById('statement-text').innerText;
    navigator.clipboard.writeText(text).then(() => {
        alert('Statement copied to clipboard.');
    });
}
</script>
@endpush

@endsection
