{{-- resources/views/committee/risks/index.blade.php --}}
@extends('committee.layout')

@section('committee-content')

<div class="committee-page-header">
    <h1>Risks & Issues</h1>
    <p>Live risk register. Likelihood × impact. Review and mitigate; do not simply accept.</p>
</div>

<div style="display:flex; gap:20px; margin-bottom:24px; align-items:flex-start; flex-wrap:wrap;">

    {{-- Metric counts --}}
    <div style="display:flex; gap:12px; flex-wrap:wrap;">
        @foreach(['red'=>['Critical','≥15'],'orange'=>['High','9–14'],'amber'=>['Medium','4–8'],'green'=>['Low','1–3']] as $colour => [$label, $range])
        @php
            $count = $risks->filter(fn($r) => $r->riskColour() === $colour)->count();
        @endphp
        <div class="metric-card metric-card--{{ $colour }}" style="flex:0 0 120px;">
            <div class="metric-card__label">{{ $label }}</div>
            <div class="metric-card__value">{{ $count }}</div>
            <div class="metric-card__sub">score {{ $range }}</div>
        </div>
        @endforeach
    </div>

    {{-- 5×5 risk matrix --}}
    <div style="background:#fff; border:1px solid #e5e7eb; border-radius:8px; padding:16px;">
        <div style="font-size:11px; font-weight:700; color:#9ca3af; text-transform:uppercase; margin-bottom:8px;">
            Risk Matrix (Likelihood → / Impact ↑)
        </div>
        <div style="display:grid; grid-template-columns: 24px repeat(5, 32px); gap:2px; align-items:center;">
            {{-- Impact labels (top-to-bottom = 5→1) --}}
            @foreach([5,4,3,2,1] as $imp)
            <div style="font-size:10px; color:#9ca3af; text-align:right; padding-right:4px;">{{ $imp }}</div>
            @foreach([1,2,3,4,5] as $lik)
            @php
                $score = $imp * $lik;
                $bg = match(true) { $score >= 15 => '#fee2e2', $score >= 9 => '#ffedd5', $score >= 4 => '#fef9c3', default => '#dcfce7' };
                $fc = match(true) { $score >= 15 => '#b91c1c', $score >= 9 => '#c2410c', $score >= 4 => '#92400e', default => '#15803d' };
                $count = $risks->filter(fn($r) => $r->likelihood === $lik && $r->impact === $imp)->count();
            @endphp
            <div style="width:32px; height:32px; background:{{ $bg }}; border-radius:4px;
                        display:flex; align-items:center; justify-content:center;
                        font-size:{{ $count ? '13px' : '11px' }}; font-weight:700; color:{{ $count ? $fc : '#d1d5db' }};">
                {{ $count ?: $score }}
            </div>
            @endforeach
            @endforeach
            {{-- Likelihood axis labels --}}
            <div></div>
            @foreach([1,2,3,4,5] as $l)
            <div style="font-size:10px; color:#9ca3af; text-align:center;">{{ $l }}</div>
            @endforeach
        </div>
        <div style="font-size:10px; color:#9ca3af; margin-top:6px; text-align:center;">
            Bold numbers = active risks at that cell
        </div>
    </div>

    <a href="{{ route('committee.risks.create') }}" class="btn btn--primary" style="margin-left:auto; align-self:flex-start;">+ Log risk</a>
</div>

{{-- Open risks table --}}
<div class="committee-panel" style="margin-bottom:20px;">
    <div class="committee-panel__header">
        <h3 class="committee-panel__title">Open Risk Register</h3>
    </div>
    <table class="committee-table">
        <thead>
            <tr>
                <th>Score</th>
                <th>Title</th>
                <th>Category</th>
                <th style="text-align:center;">L</th>
                <th style="text-align:center;">I</th>
                <th>Mitigation</th>
                <th>Status</th>
                <th>Owner</th>
                <th>Review</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($risks as $risk)
            <tr>
                <td>
                    <div style="width:36px; height:36px; border-radius:6px;
                                background:{{ match($risk->riskColour()) { 'red'=>'#fee2e2','orange'=>'#ffedd5','amber'=>'#fef9c3',default=>'#dcfce7' } }};
                                display:flex; align-items:center; justify-content:center;
                                font-size:16px; font-weight:700;
                                color:{{ match($risk->riskColour()) { 'red'=>'#b91c1c','orange'=>'#c2410c','amber'=>'#92400e',default=>'#15803d' } }};">
                        {{ $risk->riskScore() }}
                    </div>
                </td>
                <td>
                    <strong>{{ $risk->title }}</strong>
                    @if($risk->description)
                        <div style="font-size:11px; color:#6b7280; margin-top:2px;">{{ Str::limit($risk->description, 80) }}</div>
                    @endif
                </td>
                <td><span class="pill pill--grey">{{ $risk->category ?? '—' }}</span></td>
                <td style="text-align:center; font-weight:700;">{{ $risk->likelihood }}</td>
                <td style="text-align:center; font-weight:700;">{{ $risk->impact }}</td>
                <td style="font-size:12px; max-width:200px;">{{ Str::limit($risk->mitigation ?? '—', 80) }}</td>
                <td>
                    <span class="pill pill--{{ match($risk->status) {
                        'open'=>'red', 'mitigated'=>'amber', 'accepted'=>'blue', default=>'green'
                    } }}">{{ ucfirst($risk->status) }}</span>
                </td>
                <td style="font-size:12px;">{{ $risk->owner?->name ?? '—' }}</td>
                <td style="font-size:12px;">
                    @if($risk->review_date)
                        <span class="{{ $risk->review_date->isPast() ? 'pill pill--red' : '' }}">
                            {{ $risk->review_date->format('j M Y') }}
                        </span>
                    @else
                        <span style="color:#9ca3af;">—</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('committee.risks.edit', $risk) }}" class="btn btn--secondary btn--sm">Edit</a>
                    <form action="{{ route('committee.risks.destroy', $risk) }}" method="POST"
                          style="display:inline;" onsubmit="return confirm('Remove this risk?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn--danger btn--sm">Del</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="text-align:center; color:#9ca3af; padding:24px;">No open risks logged.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Closed risks --}}
@if($closed->isNotEmpty())
<div class="committee-panel">
    <div class="committee-panel__header">
        <h3 class="committee-panel__title">Recently Closed / Accepted</h3>
    </div>
    <table class="committee-table">
        <thead>
            <tr><th>Title</th><th>Score</th><th>Status</th><th>Category</th></tr>
        </thead>
        <tbody>
            @foreach($closed as $risk)
            <tr style="opacity:.7;">
                <td style="text-decoration:line-through; color:#9ca3af;">{{ $risk->title }}</td>
                <td><span style="font-weight:700; color:#9ca3af;">{{ $risk->riskScore() }}</span></td>
                <td><span class="pill pill--grey">{{ ucfirst($risk->status) }}</span></td>
                <td style="font-size:12px;">{{ $risk->category ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@endsection
