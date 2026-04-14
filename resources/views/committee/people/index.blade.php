{{-- resources/views/committee/people/index.blade.php --}}
@extends('committee.layout')

@section('committee-content')

<div class="committee-page-header">
    <h1>People & Availability</h1>
    <p>Operational availability of active operators — not just membership on paper.</p>
</div>

<div style="display:flex; gap:14px; margin-bottom:24px;">
    <div class="metric-card metric-card--navy" style="flex:0 0 160px;">
        <div class="metric-card__label">Ops ≤60 min</div>
        <div class="metric-card__value">{{ $ops60 }}</div>
    </div>
    <div class="metric-card metric-card--navy" style="flex:0 0 160px;">
        <div class="metric-card__label">Ops ≤120 min</div>
        <div class="metric-card__value">{{ $ops120 }}</div>
    </div>
    <div class="metric-card" style="flex:0 0 160px;">
        <div class="metric-card__label">Team Leaders</div>
        <div class="metric-card__value">{{ $leaders }}</div>
    </div>
</div>

<div class="committee-panel">
    <div class="committee-panel__header">
        <h3 class="committee-panel__title">Operator Register</h3>
    </div>
    <table class="committee-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Callsign</th>
                <th>Active</th>
                <th>≤60 min</th>
                <th>≤120 min</th>
                <th>Team Leader</th>
                <th>Induction</th>
                <th>Msg Handling</th>
                <th>Digital</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($members as $member)
            @php $av = $member->availability; @endphp
            <tr>
                <td style="font-weight:600;">{{ $member->name }}</td>
                <td style="font-family:monospace; font-size:12px;">{{ $member->callsign ?? '—' }}</td>
                <td>@include('committee.people._bool', ['v' => $av?->is_active_operator])</td>
                <td>@include('committee.people._bool', ['v' => $av?->deployable_60min])</td>
                <td>@include('committee.people._bool', ['v' => $av?->deployable_120min])</td>
                <td>@include('committee.people._bool', ['v' => $av?->is_team_leader])</td>
                <td>
                    @if($av?->induction_current)
                        <span class="pill pill--green">Current</span>
                        @if($av->induction_date)
                            <div style="font-size:10px; color:#9ca3af;">{{ $av->induction_date->format('M Y') }}</div>
                        @endif
                    @else
                        <span class="pill pill--red">Not current</span>
                    @endif
                </td>
                <td>@include('committee.people._bool', ['v' => $av?->message_handling_current])</td>
                <td>@include('committee.people._bool', ['v' => $av?->digital_data_competent])</td>
                <td>
                    <a href="{{ route('committee.people.edit', $member) }}" class="btn btn--secondary btn--sm">Edit</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="10" style="text-align:center; color:#9ca3af; padding:24px;">No members found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
