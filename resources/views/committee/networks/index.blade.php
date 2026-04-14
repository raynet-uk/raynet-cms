{{-- resources/views/committee/networks/index.blade.php --}}
@extends('committee.layout')

@section('committee-content')

<div class="committee-page-header">
    <h1>Networks & Systems</h1>
    <p>Current status of all communications bearers and network assets.</p>
</div>

<div style="display:flex; gap:12px; margin-bottom:24px; align-items:center; flex-wrap:wrap;">
    @foreach(['operational'=>'green','degraded'=>'amber','offline'=>'red','unknown'=>'grey'] as $status => $colour)
    <div class="metric-card metric-card--{{ $colour }}" style="flex:0 0 130px;">
        <div class="metric-card__label">{{ ucfirst($status) }}</div>
        <div class="metric-card__value">{{ $statusCounts[$status] ?? 0 }}</div>
    </div>
    @endforeach
    <a href="{{ route('committee.networks.create') }}" class="btn btn--primary" style="margin-left:auto;">+ Add network</a>
</div>

@foreach(['VHF/UHF','DMR','YSF','VoIP','LoRa','APRS','HF','Other'] as $type)
@php $group = $networks->where('type', $type); @endphp
@if($group->isNotEmpty())
<div class="committee-panel" style="margin-bottom:16px;">
    <div class="committee-panel__header">
        <h3 class="committee-panel__title">{{ $type }}</h3>
        <span style="font-size:11px; color:#9ca3af;">{{ $group->count() }} {{ Str::plural('network', $group->count()) }}</span>
    </div>
    <table class="committee-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Status</th>
                <th>Freq / Channel</th>
                <th>Last tested</th>
                <th>Owner</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($group as $net)
            <tr>
                <td>
                    <strong>{{ $net->name }}</strong>
                    @if($net->description)
                        <div style="font-size:11px; color:#9ca3af;">{{ $net->description }}</div>
                    @endif
                </td>
                <td><span class="pill pill--{{ $net->statusColour() }}">{{ ucfirst($net->status) }}</span></td>
                <td style="font-family:monospace; font-size:12px;">{{ $net->frequency_channel ?? '—' }}</td>
                <td style="font-size:12px;">{{ $net->last_tested?->format('j M Y') ?? '—' }}</td>
                <td style="font-size:12px;">{{ $net->owner?->name ?? '—' }}</td>
                <td>
                    <a href="{{ route('committee.networks.edit', $net) }}" class="btn btn--secondary btn--sm">Edit</a>
                    <form action="{{ route('committee.networks.destroy', $net) }}" method="POST"
                          style="display:inline;" onsubmit="return confirm('Remove?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn--danger btn--sm">Del</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endforeach

@if($networks->isEmpty())
    <div class="committee-panel">
        <div class="committee-panel__body" style="text-align:center; color:#9ca3af;">No networks logged yet.</div>
    </div>
@endif

@endsection
