{{-- resources/views/committee/actions/index.blade.php --}}
@extends('committee.layout')

@section('committee-content')

<div class="committee-page-header">
    <h1>Actions</h1>
    <p>All open corrective actions and improvement items, tracked to closure.</p>
</div>

<div style="display:flex; gap:14px; margin-bottom:24px; align-items:center;">
    <div class="metric-card {{ $overdue > 0 ? 'metric-card--red' : 'metric-card--green' }}" style="flex:0 0 160px;">
        <div class="metric-card__label">Overdue</div>
        <div class="metric-card__value">{{ $overdue }}</div>
    </div>
    <div class="metric-card metric-card--navy" style="flex:0 0 160px;">
        <div class="metric-card__label">Open</div>
        <div class="metric-card__value">{{ $open->count() }}</div>
    </div>
    <a href="{{ route('committee.actions.create') }}" class="btn btn--primary" style="margin-left:auto;">+ Log action</a>
</div>

{{-- Open actions --}}
<div class="committee-panel" style="margin-bottom:20px;">
    <div class="committee-panel__header">
        <h3 class="committee-panel__title">Open Actions</h3>
    </div>
    <table class="committee-table">
        <thead>
            <tr>
                <th>Priority</th>
                <th>Title</th>
                <th>Source</th>
                <th>Owner</th>
                <th>Due</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($open as $action)
            @php $overdue = $action->isOverdue(); @endphp
            <tr style="{{ $overdue ? 'background:#fff5f5;' : '' }}">
                <td>
                    <span class="pill pill--{{ $action->priorityColour() }}">
                        {{ ucfirst($action->priority) }}
                    </span>
                </td>
                <td>
                    <strong>{{ $action->title }}</strong>
                    @if($action->description)
                        <div style="font-size:11px; color:#6b7280; margin-top:2px;">{{ Str::limit($action->description, 80) }}</div>
                    @endif
                </td>
                <td>
                    <span style="font-size:12px; color:#6b7280;">{{ ucfirst($action->source) }}</span>
                    @if($action->source_ref)
                        <div style="font-size:11px; color:#9ca3af;">{{ $action->source_ref }}</div>
                    @endif
                </td>
                <td style="font-size:13px;">{{ $action->owner?->name ?? '—' }}</td>
                <td>
                    @if($action->due_date)
                        <span class="pill pill--{{ $overdue ? 'red' : 'amber' }}">
                            {{ $overdue ? '⚠ ' : '' }}{{ $action->due_date->format('j M Y') }}
                        </span>
                    @else
                        <span style="color:#9ca3af; font-size:12px;">No date</span>
                    @endif
                </td>
                <td>
                    <span class="pill pill--{{ match($action->status) {
                        'in_progress' => 'blue', 'open' => 'amber', default => 'grey'
                    } }}">{{ ucfirst(str_replace('_', ' ', $action->status)) }}</span>
                </td>
                <td>
                    <a href="{{ route('committee.actions.edit', $action) }}" class="btn btn--secondary btn--sm">Edit</a>
                    {{-- Quick close --}}
                    <button onclick="document.getElementById('close-{{ $action->id }}').style.display='block'"
                            class="btn btn--primary btn--sm">Close</button>
                </td>
            </tr>
            {{-- Inline close form --}}
            <tr id="close-{{ $action->id }}" style="display:none; background:#f0fdf4;">
                <td colspan="7" style="padding:12px 16px;">
                    <form action="{{ route('committee.actions.close', $action) }}" method="POST"
                          style="display:flex; gap:10px; align-items:center;">
                        @csrf
                        <input type="text" name="closure_notes" placeholder="Closure notes (optional)"
                               style="flex:1; padding:7px 10px; border:1px solid #d1d5db; border-radius:6px; font-size:13px; font-family:Arial,sans-serif;">
                        <button type="submit" class="btn btn--primary btn--sm">Confirm close</button>
                        <button type="button" onclick="document.getElementById('close-{{ $action->id }}').style.display='none'"
                                class="btn btn--secondary btn--sm">Cancel</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center; color:#9ca3af; padding:24px;">
                    No open actions. 
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Recently closed --}}
@if($closed->isNotEmpty())
<div class="committee-panel">
    <div class="committee-panel__header">
        <h3 class="committee-panel__title">Recently Closed</h3>
        <span style="font-size:11px; color:#9ca3af;">Last 20 closed or cancelled</span>
    </div>
    <table class="committee-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Closed</th>
                <th>Owner</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($closed as $action)
            <tr style="opacity:.75;">
                <td>
                    <span style="text-decoration:line-through; color:#9ca3af;">{{ $action->title }}</span>
                    <span class="pill pill--{{ $action->status === 'cancelled' ? 'grey' : 'green' }}" style="margin-left:6px;">
                        {{ ucfirst($action->status) }}
                    </span>
                </td>
                <td style="font-size:12px;">{{ $action->closed_date?->format('j M Y') ?? '—' }}</td>
                <td style="font-size:12px;">{{ $action->owner?->name ?? '—' }}</td>
                <td style="font-size:12px; color:#6b7280;">{{ $action->closure_notes ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@endsection
