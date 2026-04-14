@extends('layouts.app')
@section('title', 'Net Log')
@section('content')
<div style="max-width:1000px;margin:0 auto;padding:2rem 1rem;font-family:inherit">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
        <div>
            <h1 style="font-size:1.35rem;font-weight:700;margin:0">Net Log</h1>
            <p style="color:#6b7280;font-size:.85rem;margin:.2rem 0 0">Radio net sessions &amp; check-ins</p>
        </div>
    </div>

    @if(session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:.7rem 1rem;border-radius:8px;margin-bottom:1rem;font-size:.875rem">
            {{ session('success') }}
        </div>
    @endif

    {{-- Open new session --}}
    @if(!$open)
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:1.25rem;margin-bottom:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,.05)">
        <h2 style="font-size:.95rem;font-weight:600;margin:0 0 1rem">Open a new net session</h2>
        <form action="{{ route('admin.netlog.sessions.open') }}" method="POST">
            @csrf
            <div style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:.75rem;margin-bottom:.75rem">
                <div>
                    <label style="font-size:.78rem;font-weight:500;color:#374151;display:block;margin-bottom:.25rem">Session name *</label>
                    <input type="text" name="name" required placeholder="e.g. Monday Evening Net"
                        style="width:100%;border:1px solid #d1d5db;border-radius:7px;padding:.45rem .7rem;font-size:.85rem;box-sizing:border-box">
                </div>
                <div>
                    <label style="font-size:.78rem;font-weight:500;color:#374151;display:block;margin-bottom:.25rem">Frequency</label>
                    <input type="text" name="frequency" placeholder="145.500 MHz"
                        style="width:100%;border:1px solid #d1d5db;border-radius:7px;padding:.45rem .7rem;font-size:.85rem;box-sizing:border-box">
                </div>
                <div>
                    <label style="font-size:.78rem;font-weight:500;color:#374151;display:block;margin-bottom:.25rem">Net Control</label>
                    <input type="text" name="net_control" placeholder="Callsign"
                        style="width:100%;border:1px solid #d1d5db;border-radius:7px;padding:.45rem .7rem;font-size:.85rem;box-sizing:border-box">
                </div>
            </div>
            <button type="submit"
                style="background:#2563eb;color:#fff;border:none;padding:.45rem 1.1rem;border-radius:7px;font-size:.85rem;font-weight:500;cursor:pointer">
                Open Session
            </button>
        </form>
    </div>
    @else
    {{-- Active session --}}
    <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:1.25rem;margin-bottom:1.5rem;box-shadow:0 1px 3px rgba(34,197,94,.1)">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1rem">
            <div>
                <div style="display:flex;align-items:center;gap:.5rem">
                    <span style="width:8px;height:8px;border-radius:50%;background:#22c55e;display:inline-block;box-shadow:0 0 6px rgba(34,197,94,.6)"></span>
                    <h2 style="font-size:1rem;font-weight:700;margin:0;color:#111827">{{ $open->name }}</h2>
                    <span style="background:#dcfce7;color:#15803d;font-size:.7rem;font-weight:600;padding:.15rem .5rem;border-radius:999px">LIVE</span>
                </div>
                <p style="color:#4b5563;font-size:.82rem;margin:.3rem 0 0">
                    {{ $open->frequency ?? 'No frequency set' }}
                    @if($open->net_control) &nbsp;·&nbsp; NCS: {{ $open->net_control }} @endif
                    &nbsp;·&nbsp; Opened {{ \Carbon\Carbon::parse($open->created_at)->diffForHumans() }}
                </p>
            </div>
            <div style="display:flex;gap:.5rem">
                <a href="{{ route('admin.netlog.sessions.export', $open->id) }}"
                    style="background:#fff;border:1px solid #d1d5db;color:#374151;padding:.35rem .8rem;border-radius:7px;font-size:.78rem;text-decoration:none;font-weight:500">
                    ↓ Export CSV
                </a>
                <form action="{{ route('admin.netlog.sessions.close', $open->id) }}" method="POST">
                    @csrf
                    <button style="background:#dc2626;color:#fff;border:none;padding:.35rem .8rem;border-radius:7px;font-size:.78rem;cursor:pointer;font-weight:500"
                        onclick="return confirm('Close this session?')">Close Session</button>
                </form>
            </div>
        </div>

        {{-- Check-in form --}}
        <form action="{{ route('admin.netlog.checkins.add', $open->id) }}" method="POST">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr auto;gap:.6rem;align-items:flex-end">
                <div>
                    <label style="font-size:.75rem;font-weight:500;color:#374151;display:block;margin-bottom:.2rem">Callsign *</label>
                    <input type="text" name="callsign" required placeholder="M0XXX" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:.4rem .6rem;font-size:.85rem;box-sizing:border-box;text-transform:uppercase">
                </div>
                <div>
                    <label style="font-size:.75rem;font-weight:500;color:#374151;display:block;margin-bottom:.2rem">Name</label>
                    <input type="text" name="name" placeholder="Optional" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:.4rem .6rem;font-size:.85rem;box-sizing:border-box">
                </div>
                <div>
                    <label style="font-size:.75rem;font-weight:500;color:#374151;display:block;margin-bottom:.2rem">Location</label>
                    <input type="text" name="location" placeholder="e.g. Liverpool" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:.4rem .6rem;font-size:.85rem;box-sizing:border-box">
                </div>
                <div>
                    <label style="font-size:.75rem;font-weight:500;color:#374151;display:block;margin-bottom:.2rem">Signal</label>
                    <input type="text" name="signal_report" placeholder="59" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:.4rem .6rem;font-size:.85rem;box-sizing:border-box">
                </div>
                <button type="submit" style="background:#16a34a;color:#fff;border:none;padding:.42rem .9rem;border-radius:6px;font-size:.82rem;cursor:pointer;font-weight:600;white-space:nowrap">+ Check In</button>
            </div>
        </form>

        {{-- Check-in list --}}
        @php $checkins = \Illuminate\Support\Facades\DB::table('net_checkins')->where('net_session_id', $open->id)->orderByDesc('created_at')->get(); @endphp
        @if($checkins->count())
        <div style="margin-top:1rem;background:#fff;border-radius:8px;overflow:hidden;border:1px solid #bbf7d0">
            <table style="width:100%;border-collapse:collapse;font-size:.82rem">
                <thead>
                    <tr style="background:#f0fdf4">
                        <th style="padding:.5rem .75rem;text-align:left;font-weight:600;color:#374151">#</th>
                        <th style="padding:.5rem .75rem;text-align:left;font-weight:600;color:#374151">Callsign</th>
                        <th style="padding:.5rem .75rem;text-align:left;font-weight:600;color:#374151">Name</th>
                        <th style="padding:.5rem .75rem;text-align:left;font-weight:600;color:#374151">Location</th>
                        <th style="padding:.5rem .75rem;text-align:left;font-weight:600;color:#374151">Signal</th>
                        <th style="padding:.5rem .75rem;text-align:left;font-weight:600;color:#374151">Time</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($checkins as $i => $c)
                    <tr style="border-top:1px solid #f0fdf4">
                        <td style="padding:.45rem .75rem;color:#9ca3af">{{ $checkins->count() - $i }}</td>
                        <td style="padding:.45rem .75rem;font-weight:600;font-family:monospace">{{ $c->callsign }}</td>
                        <td style="padding:.45rem .75rem;color:#374151">{{ $c->name ?: '—' }}</td>
                        <td style="padding:.45rem .75rem;color:#374151">{{ $c->location ?: '—' }}</td>
                        <td style="padding:.45rem .75rem;color:#374151">{{ $c->signal_report ?: '—' }}</td>
                        <td style="padding:.45rem .75rem;color:#6b7280">{{ \Carbon\Carbon::parse($c->created_at)->format('H:i:s') }}</td>
                        <td style="padding:.45rem .75rem">
                            <form action="{{ route('admin.netlog.checkins.delete', $c->id) }}" method="POST" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="submit" style="background:none;border:none;color:#dc2626;cursor:pointer;font-size:.75rem;padding:0"
                                    onclick="return confirm('Remove check-in?')">✕</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
            <p style="color:#6b7280;font-size:.82rem;margin:1rem 0 0;text-align:center">No check-ins yet — use the form above.</p>
        @endif
    </div>
    @endif

    {{-- Session history --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.05)">
        <div style="padding:.75rem 1.1rem;border-bottom:1px solid #f3f4f6;font-size:.875rem;font-weight:600;color:#374151">Session History</div>
        @if($sessions->count())
        <table style="width:100%;border-collapse:collapse;font-size:.83rem">
            <thead>
                <tr style="background:#f9fafb">
                    <th style="padding:.55rem .9rem;text-align:left;font-weight:600;color:#374151">Session</th>
                    <th style="padding:.55rem .9rem;text-align:left;font-weight:600;color:#374151">Frequency</th>
                    <th style="padding:.55rem .9rem;text-align:left;font-weight:600;color:#374151">NCS</th>
                    <th style="padding:.55rem .9rem;text-align:left;font-weight:600;color:#374151">Check-ins</th>
                    <th style="padding:.55rem .9rem;text-align:left;font-weight:600;color:#374151">Status</th>
                    <th style="padding:.55rem .9rem;text-align:left;font-weight:600;color:#374151">Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @foreach($sessions as $s)
                <tr style="border-top:1px solid #f3f4f6">
                    <td style="padding:.55rem .9rem;font-weight:500;color:#111827">{{ $s->name }}</td>
                    <td style="padding:.55rem .9rem;color:#374151;font-family:monospace;font-size:.8rem">{{ $s->frequency ?: '—' }}</td>
                    <td style="padding:.55rem .9rem;color:#374151">{{ $s->net_control ?: '—' }}</td>
                    <td style="padding:.55rem .9rem;color:#374151">
                        {{ \Illuminate\Support\Facades\DB::table('net_checkins')->where('net_session_id',$s->id)->count() }}
                    </td>
                    <td style="padding:.55rem .9rem">
                        @if($s->status === 'open')
                            <span style="background:#dcfce7;color:#15803d;font-size:.7rem;font-weight:600;padding:.15rem .5rem;border-radius:999px">Open</span>
                        @else
                            <span style="background:#f3f4f6;color:#6b7280;font-size:.7rem;font-weight:600;padding:.15rem .5rem;border-radius:999px">Closed</span>
                        @endif
                    </td>
                    <td style="padding:.55rem .9rem;color:#6b7280;font-size:.78rem">{{ \Carbon\Carbon::parse($s->created_at)->format('d/m/Y H:i') }}</td>
                    <td style="padding:.55rem .9rem;display:flex;gap:.5rem;align-items:center">
                        <a href="{{ route('admin.netlog.sessions.export', $s->id) }}"
                            style="color:#2563eb;font-size:.75rem;text-decoration:none">Export</a>
                        <form action="{{ route('admin.netlog.sessions.delete', $s->id) }}" method="POST" style="display:inline">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:none;border:none;color:#dc2626;cursor:pointer;font-size:.75rem;padding:0"
                                onclick="return confirm('Delete session and all check-ins?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="padding:.75rem 1rem">{{ $sessions->links() }}</div>
        @else
            <p style="text-align:center;color:#9ca3af;padding:2rem;font-size:.875rem">No sessions yet.</p>
        @endif
    </div>

</div>
@endsection
