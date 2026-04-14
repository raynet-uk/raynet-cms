{{-- resources/views/committee/readiness/matrix.blade.php --}}
@extends('committee.layout')

@section('committee-content')

<div class="committee-page-header">
    <h1>Scoring Matrix</h1>
    <p>Score each indicator 0–5 using the anchors. High scores require named evidence and current dates.</p>
</div>

<div style="margin-bottom:20px;">
    <a href="{{ route('committee.readiness.index') }}" class="btn btn--secondary btn--sm">← Back to overview</a>
</div>

@foreach($indicators as $category => $catIndicators)
<div class="committee-panel" style="margin-bottom:20px;">
    <div class="committee-panel__header">
        <h3 class="committee-panel__title">{{ $category }}</h3>
        <span style="font-size:12px; color:#9ca3af;">Weight: {{ $catIndicators->first()->category_weight }} pts</span>
    </div>
    <div class="committee-panel__body" style="padding:0 20px;">

        {{-- Column headers --}}
        <div class="score-row" style="padding-bottom:8px; border-bottom:2px solid #e5e7eb; margin-bottom:4px;">
            <div style="font-size:11px; font-weight:700; color:#9ca3af; text-transform:uppercase;">Code</div>
            <div style="font-size:11px; font-weight:700; color:#9ca3af; text-transform:uppercase;">Indicator</div>
            <div style="font-size:11px; font-weight:700; color:#9ca3af; text-transform:uppercase;">Score &amp; Evidence</div>
        </div>

        @foreach($catIndicators as $ind)
        @php $score = $ind->score; $raw = $score?->raw_score ?? 0; @endphp
        <div class="score-row" id="row-{{ $ind->id }}">

            {{-- Code --}}
            <div>
                <div class="score-code">{{ $ind->code }}</div>
                <div style="font-size:10px; color:#9ca3af; margin-top:2px;">wt {{ $ind->indicator_weight }}</div>
            </div>

            {{-- Indicator name + anchors --}}
            <div>
                <div class="score-indicator-name">{{ $ind->indicator_name }}</div>
                <div class="score-indicator-evidence">{{ $ind->evidence_examples }}</div>
                <div style="margin-top:6px; display:flex; gap:8px; flex-wrap:wrap;">
                    <div style="font-size:10px; background:#fee2e2; padding:2px 6px; border-radius:4px; color:#991b1b;">
                        <strong>0</strong> {{ $ind->anchor_0 }}
                    </div>
                    <div style="font-size:10px; background:#dbeafe; padding:2px 6px; border-radius:4px; color:#1e40af;">
                        <strong>3</strong> {{ $ind->anchor_3 }}
                    </div>
                    <div style="font-size:10px; background:#dcfce7; padding:2px 6px; border-radius:4px; color:#166534;">
                        <strong>5</strong> {{ $ind->anchor_5 }}
                    </div>
                </div>
            </div>

            {{-- Score buttons + evidence --}}
            <div class="score-input-area">
                <form action="{{ route('committee.readiness.score', $ind) }}" method="POST"
                      class="score-form" data-id="{{ $ind->id }}">
                    @csrf
                    <input type="hidden" name="raw_score" class="js-raw-score" value="{{ $raw }}">

                    <div class="score-buttons">
                        @for($i = 0; $i <= 5; $i++)
                        <button type="button"
                                class="score-btn {{ $raw === $i ? 'active-'.$i : '' }}"
                                onclick="setScore({{ $ind->id }}, {{ $i }})">
                            {{ $i }}
                        </button>
                        @endfor
                    </div>

                    <div class="score-evidence-mini">
                        <input type="text" name="evidence_ref" placeholder="Evidence ref"
                               value="{{ $score?->evidence_ref }}" maxlength="255">
                        <input type="date" name="evidence_date"
                               value="{{ $score?->evidence_date?->format('Y-m-d') }}">
                        <button type="submit">Save</button>
                    </div>

                    @if($score)
                    <div style="font-size:10px; color:{{ $score->isEvidenceCurrent() ? '#15803d' : '#dc2626' }};">
                        {{ $score->isEvidenceCurrent() ? '✓ Evidence current' : '⚠ Evidence stale or missing' }}
                    </div>
                    @endif
                </form>
            </div>

        </div>
        @endforeach
    </div>
</div>
@endforeach

@push('scripts')
<script>
function setScore(indicatorId, value) {
    const form = document.querySelector(`.score-form[data-id="${indicatorId}"]`);
    if (!form) return;

    // Update hidden input
    form.querySelector('.js-raw-score').value = value;

    // Update button states
    form.querySelectorAll('.score-btn').forEach((btn, i) => {
        btn.className = 'score-btn' + (i === value ? ` active-${i}` : '');
    });
}
</script>
@endpush

@endsection
