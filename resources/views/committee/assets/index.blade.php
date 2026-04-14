{{-- resources/views/committee/assets/index.blade.php --}}
@extends('committee.layout')

@section('committee-content')

<div class="committee-page-header">
    <h1>Assets & Field Kits</h1>
    <p>Equipment register, serviceability, and test dates. Flag overdue tests immediately.</p>
</div>

<div style="display:flex; gap:14px; margin-bottom:24px; align-items:center;">
    <div class="metric-card" style="flex:0 0 180px;">
        <div class="metric-card__label">Serviceable</div>
        <div class="metric-card__value">{{ $serviceableQty }}/{{ $totalQty }}</div>
        <div class="metric-card__sub">items</div>
    </div>
    <div class="metric-card {{ $overdueTests > 0 ? 'metric-card--red' : 'metric-card--green' }}" style="flex:0 0 180px;">
        <div class="metric-card__label">Tests Overdue</div>
        <div class="metric-card__value">{{ $overdueTests }}</div>
        <div class="metric-card__sub">> 6 months</div>
    </div>
    <a href="{{ route('committee.assets.create') }}" class="btn btn--primary" style="margin-left:auto;">+ Add asset</a>
</div>

<div class="committee-panel">
    <table class="committee-table">
        <thead>
            <tr>
                <th>Type</th>
                <th>Description</th>
                <th>Serial</th>
                <th>Qty</th>
                <th>Serviceable</th>
                <th>Last tested</th>
                <th>Power (hrs)</th>
                <th>Location</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $asset)
            <tr>
                <td><span class="pill pill--blue">{{ $asset->asset_type }}</span></td>
                <td style="font-weight:600;">{{ $asset->description }}
                    @if($asset->notes)
                        <div style="font-size:11px; color:#9ca3af;">{{ $asset->notes }}</div>
                    @endif
                </td>
                <td style="font-family:monospace; font-size:11px;">{{ $asset->serial_number ?? '—' }}</td>
                <td>{{ $asset->quantity }}</td>
                <td>
                    <span class="pill pill--{{ $asset->serviceabilityPct() >= 80 ? 'green' : ($asset->serviceabilityPct() >= 50 ? 'amber' : 'red') }}">
                        {{ $asset->serviceable_qty }}/{{ $asset->quantity }}
                    </span>
                </td>
                <td>
                    @if($asset->last_test_date)
                        <span class="{{ $asset->isTestOverdue() ? 'pill pill--red' : '' }}" style="font-size:12px;">
                            {{ $asset->last_test_date->format('j M Y') }}
                            @if($asset->isTestOverdue()) ⚠ @endif
                        </span>
                    @else
                        <span class="pill pill--red">Never</span>
                    @endif
                </td>
                <td>{{ $asset->power_runtime_hours ?? '—' }}</td>
                <td style="font-size:12px;">{{ $asset->location ?? '—' }}</td>
                <td>
                    <a href="{{ route('committee.assets.edit', $asset) }}" class="btn btn--secondary btn--sm">Edit</a>
                    <form action="{{ route('committee.assets.destroy', $asset) }}" method="POST"
                          style="display:inline;" onsubmit="return confirm('Remove this asset?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn--danger btn--sm">Del</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="9" style="text-align:center; color:#9ca3af; padding:24px;">No assets logged.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
