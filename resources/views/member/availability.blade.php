@extends('layouts.app')

@section('title', 'My Availability')

@section('content')
<style>
:root {
    --navy:#003366; --navy-faint:#e8eef8; --red:#C8102E; --red-faint:#fdf0f2;
    --green:#1a6b3c; --green-bg:#eaf4ee; --green-bdr:#a8d5b8;
    --amber:#8a5c00; --amber-bg:#fff8e6; --amber-bdr:#e8c96a;
    --grey:#f5f6f8; --grey-mid:#dde2e8; --grey-dark:#6b7f96;
    --text:#1a2332; --text-muted:#6b7f96; --white:#fff;
    --font:Arial,'Helvetica Neue',sans-serif;
}
* { box-sizing:border-box; }
body { font-family:var(--font); background:var(--grey); color:var(--text); }

.page-header { background:var(--navy); color:#fff; padding:1.1rem 1.5rem; }
.page-header h1 { font-size:18px; font-weight:bold; margin:0; }
.page-header p  { font-size:13px; opacity:.75; margin:.25rem 0 0; }

.content { max-width:700px; margin:0 auto; padding:1.25rem 1rem; }

.card { background:var(--white); border:1px solid var(--grey-mid); margin-bottom:1.25rem; }
.card-head { padding:.75rem 1.1rem; border-bottom:1px solid var(--grey-mid); display:flex; align-items:center; gap:.5rem; }
.card-title { font-size:13px; font-weight:bold; text-transform:uppercase; letter-spacing:.08em; color:var(--navy); }
.card-body { padding:1.1rem; }

.field { margin-bottom:.85rem; }
.field label { display:block; font-size:10px; font-weight:bold; text-transform:uppercase; letter-spacing:.08em; color:var(--text-muted); margin-bottom:.3rem; }
.field input, .field textarea {
    width:100%; padding:.45rem .65rem; font-size:13px; border:1px solid var(--grey-mid);
    background:var(--white); color:var(--text); font-family:var(--font);
}
.field .error { font-size:11px; color:var(--red); margin-top:.25rem; }

.date-row { display:grid; grid-template-columns:1fr 1fr; gap:.75rem; }

.btn { display:inline-flex; align-items:center; gap:.35rem; padding:.5rem 1rem; font-size:12px; font-weight:bold; text-transform:uppercase; letter-spacing:.05em; border:1px solid; cursor:pointer; font-family:var(--font); }
.btn-navy { background:var(--navy); color:#fff; border-color:var(--navy); }
.btn-red  { background:var(--red-faint); color:var(--red); border-color:rgba(200,16,46,.3); font-size:11px; padding:.3rem .7rem; }
.btn-navy:hover { opacity:.88; }

.flash { padding:.65rem 1.1rem; font-size:13px; font-weight:bold; margin-bottom:1rem; }
.flash-success { background:var(--green-bg); border-left:3px solid var(--green); color:var(--green); }
.flash-error   { background:var(--red-faint); border-left:3px solid var(--red);   color:var(--red); }

.period-row { display:flex; align-items:center; gap:.75rem; padding:.65rem 1.1rem; border-bottom:1px solid var(--grey-mid); }
.period-row:last-child { border-bottom:none; }
.period-dates { flex:1; }
.period-dates .range { font-size:13px; font-weight:bold; color:var(--navy); }
.period-dates .reason { font-size:12px; color:var(--text-muted); margin-top:1px; }
.period-dates .days { font-size:11px; color:var(--text-muted); }
.period-badge { font-size:10px; font-weight:bold; text-transform:uppercase; letter-spacing:.05em; padding:2px 8px; border:1px solid; border-radius:2px; }
.badge-active { background:var(--amber-bg); border-color:var(--amber-bdr); color:var(--amber); }
.badge-future { background:var(--navy-faint); border-color:rgba(0,51,102,.2); color:var(--navy); }

.info-box { background:var(--navy-faint); border-left:3px solid var(--navy); padding:.75rem 1rem; font-size:13px; color:var(--navy); margin-bottom:1rem; line-height:1.5; }

.empty-state { padding:1.75rem; text-align:center; color:var(--text-muted); font-size:13px; }

.past-row { padding:.5rem 1.1rem; border-bottom:1px solid var(--grey-mid); display:flex; align-items:center; gap:.75rem; opacity:.6; }
.past-row:last-child { border-bottom:none; }
</style>

<div class="page-header">
    <h1>📅 My Availability</h1>
    <p>Let admins know when you're unavailable so they don't assign you to events you can't attend.</p>
</div>

<div class="content">

    @if (session('success'))
    <div class="flash flash-success">✓ {{ session('success') }}</div>
    @endif

    <div class="info-box">
        When you mark yourself unavailable, your name will be shown greyed-out in the event crew assignment panel for those dates. You won't be automatically removed from any existing assignments — the admin will see your unavailability and can decide how to handle it.
    </div>

    {{-- ── ADD FORM ──────────────────────────────────────────────────── --}}
    <div class="card">
        <div class="card-head">
            <span class="card-title">Add Unavailability Period</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('member.availability.store') }}">
                @csrf
                <div class="date-row">
                    <div class="field">
                        <label>From *</label>
                        <input type="date" name="from_date" required
                               min="{{ now()->toDateString() }}"
                               value="{{ old('from_date') }}">
                        @error('from_date') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div class="field">
                        <label>To *</label>
                        <input type="date" name="to_date" required
                               min="{{ now()->toDateString() }}"
                               value="{{ old('to_date') }}">
                        @error('to_date') <div class="error">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="field">
                    <label>Reason <span style="font-weight:normal;text-transform:none;">(optional — visible to admins)</span></label>
                    <input type="text" name="reason" maxlength="200"
                           placeholder="e.g. Holiday, Medical appointment, Work commitments…"
                           value="{{ old('reason') }}">
                </div>
                <button type="submit" class="btn btn-navy">Add Period</button>
            </form>
        </div>
    </div>

    {{-- ── CURRENT / UPCOMING ───────────────────────────────────────── --}}
    <div class="card">
        <div class="card-head">
            <span class="card-title">Current & Upcoming</span>
            <span style="font-size:11px;color:var(--text-muted);margin-left:auto;">{{ $periods->count() }} period{{ $periods->count() !== 1 ? 's' : '' }}</span>
        </div>

        @if ($periods->isEmpty())
        <div class="empty-state">No unavailability periods set. You're available for all upcoming events.</div>
        @else
        @foreach ($periods as $period)
        @php
            $isNow = $period->from_date->isPast() || $period->from_date->isToday();
        @endphp
        <div class="period-row">
            <div class="period-dates">
                <div class="range">{{ $period->date_range }}</div>
                @if ($period->reason)
                <div class="reason">{{ $period->reason }}</div>
                @endif
                <div class="days">{{ $period->daysCount() }} day{{ $period->daysCount() !== 1 ? 's' : '' }}</div>
            </div>
            <span class="period-badge {{ $isNow ? 'badge-active' : 'badge-future' }}">
                {{ $isNow ? 'Active' : 'Upcoming' }}
            </span>
            <form method="POST" action="{{ route('member.availability.destroy', $period) }}"
                  onsubmit="return confirm('Remove this unavailability period?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-red">✕ Remove</button>
            </form>
        </div>
        @endforeach
        @endif
    </div>

    {{-- ── PAST (last 10) ───────────────────────────────────────────── --}}
    @if ($past->isNotEmpty())
    <div class="card">
        <div class="card-head">
            <span class="card-title" style="opacity:.6;">Past Periods</span>
        </div>
        @foreach ($past as $period)
        <div class="past-row">
            <div style="flex:1;font-size:12px;">{{ $period->date_range }}</div>
            @if ($period->reason)
            <div style="font-size:11px;color:var(--text-muted);">{{ $period->reason }}</div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
