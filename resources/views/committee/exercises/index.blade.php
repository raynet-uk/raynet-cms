{{-- resources/views/committee/exercises/index.blade.php --}}
@extends('committee.layout')

@section('committee-content')

<div class="committee-page-header">
    <h1>Exercises & Deployments</h1>
    <p>Training nights, exercises, real deployments, and the lessons they generated.</p>
</div>

<div style="display:flex; gap:14px; margin-bottom:24px; align-items:center;">
    <div class="metric-card metric-card--navy" style="flex:0 0 200px;">
        <div class="metric-card__label">Activities (last 12 months)</div>
        <div class="metric-card__value">{{ $last12months }}</div>
    </div>
    @if($upcoming)
    <div class="metric-card metric-card--blue" style="flex:0 0 220px;">
        <div class="metric-card__label">Next activity</div>
        <div class="metric-card__value" style="font-size:20px;">{{ $upcoming->date->format('j M') }}</div>
        <div class="metric-card__sub">{{ $upcoming->activity }}</div>
    </div>
    @endif
    <a href="{{ route('committee.exercises.create') }}" class="btn btn--primary" style="margin-left:auto;">+ Log activity</a>
</div>

<div class="committee-panel">
    <table class="committee-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Activity</th>
                <th>Type</th>
                <th>Capability tested</th>
                <th>Lead</th>
                <th>Outcome</th>
                <th>Actions due</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($exercises as $ex)
            <tr>
                <td style="white-space:nowrap; font-weight:600;">{{ $ex->date->format('j M Y') }}</td>
                <td>{{ $ex->activity }}</td>
                <td><span class="pill pill--blue">{{ $ex->typeLabel() }}</span></td>
                <td style="font-size:12px;">{{ $ex->capability_tested ?? '—' }}</td>
                <td style="font-size:12px;">{{ $ex->lead ?? '—' }}</td>
                <td style="font-size:12px; max-width:200px;">
                    @if($ex->outcome)
                        {{ Str::limit($ex->outcome, 80) }}
                    @else
                        <span style="color:#9ca3af;">—</span>
                    @endif
                    @if($ex->lessons_identified)
                        <div style="margin-top:3px; color:#d97706; font-size:11px;">
                            ⚡ Lessons: {{ Str::limit($ex->lessons_identified, 60) }}
                        </div>
                    @endif
                </td>
                <td>
                    @if($ex->due_date && !$ex->closed_date)
                        <span class="pill pill--{{ $ex->due_date->isPast() ? 'red' : 'amber' }}">
                            {{ $ex->due_date->format('j M') }}
                        </span>
                    @elseif($ex->closed_date)
                        <span class="pill pill--green">Closed</span>
                    @else
                        <span style="color:#9ca3af; font-size:12px;">—</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('committee.exercises.edit', $ex) }}" class="btn btn--secondary btn--sm">Edit</a>
                    <form action="{{ route('committee.exercises.destroy', $ex) }}" method="POST"
                          style="display:inline;" onsubmit="return confirm('Remove this record?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn--danger btn--sm">Del</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center; color:#9ca3af; padding:32px;">
                    No exercises or activities logged yet.
                    <a href="{{ route('committee.exercises.create') }}" style="color:var(--raynet-navy);">Log the first one</a>.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding:16px 20px;">
        {{ $exercises->links() }}
    </div>
</div>

@endsection
