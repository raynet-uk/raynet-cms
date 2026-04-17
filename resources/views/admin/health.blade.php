@extends('layouts.admin')
@section('title', 'Core Health')
@section('content')
<div style="max-width:900px;margin:0 auto;padding:2rem 1rem;font-family:inherit">

    <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem">
        <a href="{{ route('admin.modules.index') }}" style="color:#6b7280;font-size:.85rem;text-decoration:none">← Module Manager</a>
    </div>

    {{-- Header --}}
    <div style="background:#003366;padding:1.25rem 1.5rem;margin-bottom:1.25rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem">
        <div style="display:flex;align-items:center;gap:.85rem">
            <div style="width:38px;height:38px;background:#C8102E;display:flex;align-items:center;justify-content:center;font-size:8px;font-weight:bold;color:#fff;text-align:center;line-height:1.2;text-transform:uppercase;letter-spacing:.04em;flex-shrink:0">RAY<br>NET</div>
            <div>
                <div style="font-size:1rem;font-weight:bold;color:#fff;letter-spacing:-.01em">RAYNET Core</div>
                <div style="font-size:11px;color:rgba(255,255,255,.5);margin-top:.15rem">v{{ $manifest['version'] }} · System Health Report</div>
            </div>
        </div>
        <span style="background:#dcfce7;color:#15803d;font-size:11px;font-weight:bold;padding:.3rem .75rem;letter-spacing:.04em;text-transform:uppercase">🔒 Protected</span>
    </div>

    {{-- Health checks --}}
    <div style="background:#fff;border:1px solid #e5e7eb;margin-bottom:1.25rem;overflow:hidden">
        <div style="padding:.65rem 1rem;background:#f9fafb;border-bottom:1px solid #e5e7eb;font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:.08em;color:#374151">
            System Health Checks
        </div>
        <table style="width:100%;border-collapse:collapse;font-size:.875rem">
            <thead>
                <tr style="background:#f3f4f6">
                    <th style="padding:.55rem 1rem;text-align:left;font-size:11px;font-weight:600;color:#4b5563;text-transform:uppercase;letter-spacing:.06em">Check</th>
                    <th style="padding:.55rem 1rem;text-align:left;font-size:11px;font-weight:600;color:#4b5563;text-transform:uppercase;letter-spacing:.06em">Result</th>
                    <th style="padding:.55rem 1rem;text-align:left;font-size:11px;font-weight:600;color:#4b5563;text-transform:uppercase;letter-spacing:.06em">Detail</th>
                    <th style="padding:.55rem 1rem;text-align:center;font-size:11px;font-weight:600;color:#4b5563;text-transform:uppercase;letter-spacing:.06em">Status</th>
                </tr>
            </thead>
            <tbody>
            @foreach($checks as $check)
            <tr style="border-top:1px solid #f3f4f6">
                <td style="padding:.65rem 1rem;font-weight:600;color:#111827">{{ $check['label'] }}</td>
                <td style="padding:.65rem 1rem;font-family:monospace;font-size:.82rem;color:#374151">{{ $check['value'] }}</td>
                <td style="padding:.65rem 1rem;color:#6b7280;font-size:.82rem">{{ $check['detail'] }}</td>
                <td style="padding:.65rem 1rem;text-align:center">
                    @if($check['status'] === 'ok')
                        <span style="background:#dcfce7;color:#15803d;font-size:.72rem;font-weight:700;padding:.2rem .6rem;border-radius:999px">✓ OK</span>
                    @elseif($check['status'] === 'warn')
                        <span style="background:#fef3c7;color:#92400e;font-size:.72rem;font-weight:700;padding:.2rem .6rem;border-radius:999px">⚠ Warn</span>
                    @else
                        <span style="background:#fee2e2;color:#dc2626;font-size:.72rem;font-weight:700;padding:.2rem .6rem;border-radius:999px">✕ Error</span>
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- Components list --}}
    <div style="background:#fff;border:1px solid #e5e7eb;overflow:hidden">
        <div style="padding:.65rem 1rem;background:#f9fafb;border-bottom:1px solid #e5e7eb;font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:.08em;color:#374151">
            Core Components
        </div>
        <div style="padding:1rem;display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:.5rem">
            @foreach($manifest['components'] as $component)
            <div style="display:flex;align-items:center;gap:.5rem;padding:.45rem .65rem;background:#f0fdf4;border:1px solid #bbf7d0;font-size:.82rem;color:#15803d;font-weight:500">
                <span>✓</span> {{ $component }}
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
