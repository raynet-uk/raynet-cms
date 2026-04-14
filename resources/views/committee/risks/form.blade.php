{{-- resources/views/committee/risks/form.blade.php --}}
@extends('committee.layout')

@section('committee-content')

<div class="committee-page-header">
    <h1>{{ $risk->exists ? 'Edit Risk' : 'Log Risk' }}</h1>
    <p>Score honestly. A high score means it needs attention, not that the group has failed.</p>
</div>

<div class="committee-panel">
    <div class="committee-panel__body">
        <form action="{{ $risk->exists ? route('committee.risks.update', $risk) : route('committee.risks.store') }}"
              method="POST" class="committee-form">
            @csrf
            @if($risk->exists) @method('PUT') @endif

            <div class="form-group">
                <label>Title *</label>
                <input type="text" name="title" value="{{ old('title', $risk->title) }}" required
                       placeholder="Brief description of the risk.">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Category</label>
                    <select name="category">
                        <option value="">— Select —</option>
                        @foreach(['People','Equipment','Training','Networks','Logistics','External','Governance','Other'] as $cat)
                        <option value="{{ $cat }}" {{ old('category', $risk->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Status *</label>
                    <select name="status">
                        @foreach(['open'=>'Open','mitigated'=>'Mitigated','accepted'=>'Accepted (residual)','closed'=>'Closed'] as $val => $label)
                        <option value="{{ $val }}" {{ old('status', $risk->status ?? 'open') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3"
                          placeholder="What is the risk, what could go wrong, and under what circumstances.">{{ old('description', $risk->description) }}</textarea>
            </div>

            {{-- Likelihood / Impact sliders --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:16px;">
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--raynet-navy); margin-bottom:8px;">
                        Likelihood *
                        <span id="likelihood-label" style="font-weight:400; color:#6b7280; margin-left:8px;"></span>
                    </label>
                    <div style="display:flex; gap:6px;" id="likelihood-btns">
                        @foreach([1=>'Rare',2=>'Unlikely',3=>'Possible',4=>'Likely',5=>'Almost certain'] as $n => $lbl)
                        <button type="button" onclick="pickRating('likelihood', {{ $n }}, '{{ $lbl }}')"
                                id="l-btn-{{ $n }}"
                                class="score-btn {{ old('likelihood', $risk->likelihood ?? 1) == $n ? 'active-'.min($n,5) : '' }}"
                                style="width:40px; height:40px; font-size:14px;">{{ $n }}</button>
                        @endforeach
                    </div>
                    <input type="hidden" name="likelihood" id="likelihood-val" value="{{ old('likelihood', $risk->likelihood ?? 1) }}" required>
                    @foreach([1=>'Rare',2=>'Unlikely',3=>'Possible',4=>'Likely',5=>'Almost certain'] as $n => $lbl)
                    <div style="font-size:10px; color:#9ca3af; margin-top:4px;">{{ $n }} = {{ $lbl }}</div>
                    @endforeach
                </div>

                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--raynet-navy); margin-bottom:8px;">
                        Impact *
                        <span id="impact-label" style="font-weight:400; color:#6b7280; margin-left:8px;"></span>
                    </label>
                    <div style="display:flex; gap:6px;" id="impact-btns">
                        @foreach([1=>'Negligible',2=>'Minor',3=>'Moderate',4=>'Significant',5=>'Critical'] as $n => $lbl)
                        <button type="button" onclick="pickRating('impact', {{ $n }}, '{{ $lbl }}')"
                                id="i-btn-{{ $n }}"
                                class="score-btn {{ old('impact', $risk->impact ?? 1) == $n ? 'active-'.min($n,5) : '' }}"
                                style="width:40px; height:40px; font-size:14px;">{{ $n }}</button>
                        @endforeach
                    </div>
                    <input type="hidden" name="impact" id="impact-val" value="{{ old('impact', $risk->impact ?? 1) }}" required>
                    @foreach([1=>'Negligible',2=>'Minor',3=>'Moderate',4=>'Significant',5=>'Critical'] as $n => $lbl)
                    <div style="font-size:10px; color:#9ca3af; margin-top:4px;">{{ $n }} = {{ $lbl }}</div>
                    @endforeach
                </div>
            </div>

            {{-- Live score preview --}}
            <div id="risk-score-preview" style="padding:12px 16px; border-radius:6px; margin-bottom:16px;
                 background:#f3f4f6; border:1px solid #e5e7eb; display:flex; align-items:center; gap:12px;">
                <div style="font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase;">Risk score:</div>
                <div id="risk-score-value" style="font-size:28px; font-weight:700; color:var(--raynet-navy);">—</div>
                <div id="risk-score-label" style="font-size:13px; color:#6b7280;"></div>
            </div>

            <div class="form-group">
                <label>Mitigation measures</label>
                <textarea name="mitigation" rows="3"
                          placeholder="What is being done or can be done to reduce likelihood or impact.">{{ old('mitigation', $risk->mitigation) }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Owner</label>
                    <select name="owner_id">
                        <option value="">— Unassigned —</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('owner_id', $risk->owner_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Review date</label>
                    <input type="date" name="review_date" value="{{ old('review_date', $risk->review_date?->format('Y-m-d')) }}">
                </div>
            </div>

            <div style="display:flex; gap:10px; margin-top:8px;">
                <button type="submit" class="btn btn--primary">{{ $risk->exists ? 'Save changes' : 'Log risk' }}</button>
                <a href="{{ route('committee.risks.index') }}" class="btn btn--secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function pickRating(field, value, label) {
    document.getElementById(field + '-val').value = value;
    document.getElementById(field + '-label').textContent = '— ' + label;

    // Update buttons
    for (let i = 1; i <= 5; i++) {
        const btn = document.getElementById((field === 'likelihood' ? 'l' : 'i') + '-btn-' + i);
        btn.className = 'score-btn' + (i === value ? ' active-' + Math.min(i, 5) : '');
    }
    updateScorePreview();
}

function updateScorePreview() {
    const l = parseInt(document.getElementById('likelihood-val').value) || 0;
    const imp = parseInt(document.getElementById('impact-val').value) || 0;
    const score = l * imp;
    const el = document.getElementById('risk-score-value');
    const labelEl = document.getElementById('risk-score-label');
    const preview = document.getElementById('risk-score-preview');

    el.textContent = score || '—';

    let colour, label;
    if      (score >= 15) { colour = '#fee2e2'; label = 'Critical'; el.style.color = '#b91c1c'; }
    else if (score >= 9)  { colour = '#ffedd5'; label = 'High';     el.style.color = '#c2410c'; }
    else if (score >= 4)  { colour = '#fef9c3'; label = 'Medium';   el.style.color = '#92400e'; }
    else if (score >= 1)  { colour = '#dcfce7'; label = 'Low';      el.style.color = '#15803d'; }
    else                  { colour = '#f3f4f6'; label = '';          el.style.color = '#374151'; }

    preview.style.background = colour;
    labelEl.textContent = label;
}

// Initialise on load
updateScorePreview();
</script>
@endpush

@endsection
