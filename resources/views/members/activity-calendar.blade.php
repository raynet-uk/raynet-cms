@extends('layouts.app')
@section('title', 'My Activity – ' . $current->format('F Y'))
@section('content')

<style>
:root {
    --navy: #003366;
    --teal: #0288d1;
    --red: #C8102E;
    --green: #2E7D32;
    --amber: #c47f00;
    --purple: #6b21a8;
    --bg: #f8f9fc;
    --surface: #ffffff;
    --surface-alt: #f0f4f8;
    --border: #d1dbe8;
    --text: #0f172a;
    --text-light: #334155;
    --muted: #64748b;
    --shadow-sm: 0 1px 3px rgba(0,0,0,0.08);
    --shadow-md: 0 4px 12px rgba(0,0,0,0.1);
    --transition: all 0.18s ease;
}
*, *::before, *::after { box-sizing: border-box; margin:0; padding:0; }
html { scroll-behavior: smooth; }
body {
    background: var(--bg);
    color: var(--text);
    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
    font-size: 15px;
    line-height: 1.55;
    min-height: 100vh;
}
.wrap {
    max-width: 1180px;
    margin: 0 auto;
    padding: 0 1rem 3rem;
}
/* TOPBAR */
.topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.1rem 0;
    border-bottom: 2px solid var(--navy);
    margin-bottom: 1.8rem;
    gap: 1rem;
    flex-wrap: wrap;
}
.brand { display: flex; align-items: center; gap: 0.9rem; }
.brand-badge {
    width: 42px; height: 42px;
    background: var(--navy);
    color: white;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; font-weight: bold;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,51,102,0.15);
}
.brand-name { font-size: 1.3rem; font-weight: bold; color: var(--navy); }
.brand-sub { font-size: 0.82rem; color: var(--muted); font-family: monospace; }
.back-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 999px;
    border: 1px solid var(--border);
    background: white;
    color: var(--muted);
    font-size: 0.9rem;
    text-decoration: none;
    transition: var(--transition);
}
.back-btn:hover {
    border-color: var(--navy);
    color: var(--navy);
}
/* PAGE HEADER */
.page-header { margin-bottom: 1.8rem; text-align: center; }
.page-header-eyebrow {
    font-size: 0.85rem;
    font-weight: bold;
    color: var(--teal);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.6rem;
}
.page-header h1 {
    font-size: 1.8rem;
    font-weight: bold;
    color: var(--navy);
    margin-bottom: 0.6rem;
}
@media (min-width: 576px) { .page-header h1 { font-size: 2.1rem; } }
.page-header p {
    font-size: 0.95rem;
    color: var(--text-light);
    max-width: 620px;
    margin: 0 auto;
}
/* LAYOUT */
.layout {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.6rem;
    align-items: start;
}
@media (min-width: 900px) { .layout { grid-template-columns: 1fr 280px; } }
/* MONTH NAVIGATOR */
.month-nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.2rem;
    gap: 1rem;
}
.month-nav-title {
    font-size: 1.3rem;
    font-weight: bold;
    color: var(--navy);
}
.month-nav-sub {
    font-size: 0.85rem;
    color: var(--muted);
    font-family: monospace;
    margin-top: 0.2rem;
}
.nav-arrows { display: flex; gap: 0.5rem; }
.nav-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: 1px solid var(--border);
    background: white;
    color: var(--muted);
    font-size: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: var(--transition);
}
.nav-btn:hover {
    border-color: var(--teal);
    color: var(--teal);
}
/* MONTH STAT PILLS */
.month-stats {
    display: flex;
    gap: 0.8rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}
.month-stat {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 0.9rem;
    border-radius: 999px;
    font-size: 0.85rem;
    font-family: monospace;
    font-weight: bold;
}
.month-stat.green {
    background: rgba(46,125,50,0.08);
    border: 1px solid rgba(46,125,50,0.25);
    color: var(--green);
}
.month-stat.cyan {
    background: rgba(2,136,209,0.08);
    border: 1px solid rgba(2,136,209,0.25);
    color: var(--teal);
}
.month-stat.muted {
    background: var(--surface-alt);
    border: 1px solid var(--border);
    color: var(--muted);
}
/* CALENDAR GRID */
.cal-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 10px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}
.cal-dow-row {
    display: grid;
    grid-template-columns: repeat(7,1fr);
    background: var(--surface-alt);
    border-bottom: 1px solid var(--border);
}
.cal-dow {
    padding: 0.6rem 0.4rem;
    text-align: center;
    font-size: 0.75rem;
    font-family: monospace;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--muted);
}
.cal-grid {
    display: grid;
    grid-template-columns: repeat(7,1fr);
}
.cal-day {
    min-height: 90px;
    border-right: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
    padding: 0.6rem 0.6rem;
    position: relative;
    transition: background 0.18s;
}
.cal-day:nth-child(7n) { border-right: none; }
.cal-day.empty { background: var(--surface-alt); opacity: 0.6; }
.cal-day.today { background: rgba(2,136,209,0.05); }
.cal-day.has-hours { cursor: default; }
.cal-day:hover:not(.empty) { background: rgba(2,136,209,0.08); }
.cal-day-num {
    font-size: 0.9rem;
    font-family: monospace;
    color: var(--muted);
    font-weight: 500;
    margin-bottom: 0.4rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.cal-day.today .cal-day-num { color: var(--teal); font-weight: bold; }
/* Hours badge inside day */
.cal-hours-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.2rem 0.6rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-family: monospace;
    font-weight: bold;
    background: rgba(46,125,50,0.08);
    border: 1px solid rgba(46,125,50,0.25);
    color: var(--green);
    margin-bottom: 0.3rem;
}
/* Event entries inside day */
.cal-event {
    font-size: 0.78rem;
    font-family: monospace;
    color: var(--text-light);
    line-height: 1.4;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 100%;
}
/* EVENT LOG LIST below calendar */
.log-list { margin-top: 1.8rem; }
.log-list-head {
    font-size: 0.85rem;
    font-family: monospace;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--muted);
    margin-bottom: 0.8rem;
}
.log-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.8rem;
    padding: 0.7rem 1rem;
    background: var(--surface-alt);
    border: 1px solid var(--border);
    border-radius: 8px;
    margin-bottom: 0.5rem;
    transition: var(--transition);
}
.log-row:hover { background: white; border-color: var(--teal); }
.log-row-left { display: flex; flex-direction: column; gap: 0.2rem; }
.log-row-date { font-size: 0.8rem; font-family: monospace; color: var(--muted); }
.log-row-name { font-size: 0.95rem; font-weight: 600; }
.log-row-hours {
    font-size: 1rem;
    font-weight: bold;
    font-family: monospace;
    color: var(--teal);
    white-space: nowrap;
}
.log-empty {
    text-align: center;
    padding: 2.5rem 1rem;
    font-size: 0.95rem;
    color: var(--muted);
    font-family: monospace;
}
/* SIDEBAR */
.sidebar { display: flex; flex-direction: column; gap: 1.4rem; }
.sidebar-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 10px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}
.sidebar-head {
    padding: 0.9rem 1.2rem;
    border-bottom: 1px solid var(--border);
    background: var(--surface-alt);
    font-size: 0.95rem;
    font-weight: bold;
    color: var(--navy);
}
.sidebar-body { padding: 1rem 1.2rem; }
/* Year total tiles */
.year-tiles { display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem; margin-bottom: 1rem; }
.year-tile {
    background: var(--surface-alt);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 0.8rem 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}
.year-tile-label {
    font-size: 0.75rem;
    font-family: monospace;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--muted);
}
.year-tile-value {
    font-size: 1.4rem;
    font-weight: bold;
    font-family: monospace;
}
.year-tile.c-green .year-tile-value { color: var(--green); }
.year-tile.c-cyan .year-tile-value { color: var(--teal); }
/* Monthly breakdown list */
.month-breakdown { display: flex; flex-direction: column; gap: 0.4rem; }
.mb-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem 0.8rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-family: monospace;
    transition: var(--transition);
    text-decoration: none;
    color: var(--text);
}
.mb-row:hover { background: var(--surface-alt); }
.mb-row.current-month { background: rgba(2,136,209,0.08); border: 1px solid rgba(2,136,209,0.25); }
.mb-month { color: var(--text-light); }
.mb-row.current-month .mb-month { color: var(--teal); font-weight: bold; }
.mb-hours {
    font-weight: bold;
    color: var(--teal);
    background: rgba(2,136,209,0.08);
    border: 1px solid rgba(2,136,209,0.25);
    padding: 0.15rem 0.6rem;
    border-radius: 6px;
    font-size: 0.75rem;
}
.mb-events { color: var(--muted); font-size: 0.75rem; }
.mb-none { color: var(--muted); font-style: italic; font-size: 0.85rem; }
/* ANIMATIONS */
.fade-in { animation: fadeIn 0.4s ease both; }
@keyframes fadeIn { from { opacity:0; transform:translateY(5px); } to { opacity:1; transform:none; } }
@media(max-width:600px) {
    .cal-day { min-height:60px; padding:0.4rem 0.4rem; }
    .cal-event { display:none; }
}
</style>

@php
    use Carbon\Carbon;
    $today = Carbon::today();
    $yearHours = collect($yearLogs)->sum(fn($g) => $g->sum('hours'));
    $yearTotal = collect($yearLogs)->sum(fn($g) => $g->count());
    // Build list of all months in RAYNET year for sidebar
    $sidebarMonths = [];
    $cursor = $yearStart->copy();
    while ($cursor->lte($yearEnd)) {
        $key = $cursor->format('Y-m');
        $sidebarMonths[] = [
            'key' => $key,
            'label' => $cursor->format('M Y'),
            'year' => $cursor->year,
            'month' => $cursor->month,
            'hours' => isset($yearLogs[$key]) ? round($yearLogs[$key]->sum('hours'), 1) : 0,
            'events' => isset($yearLogs[$key]) ? $yearLogs[$key]->count() : 0,
        ];
        $cursor->addMonth();
    }
@endphp

<div class="wrap">
    <nav class="topbar fade-in">
        <div class="brand">
            <div class="brand-badge">📅</div>
            <div>
                <div class="brand-name">{{ \App\Helpers\RaynetSetting::groupName() }}</div>
                <div class="brand-sub">my activity calendar</div>
            </div>
        </div>
        <a href="{{ route('members') }}" class="back-btn">← Back to hub</a>
    </nav>

    <div class="page-header fade-in">
        <div class="page-header-eyebrow">// my volunteering record</div>
        <h1>Activity Calendar</h1>
        <p>A month-by-month view of your logged hours and events. Managed by your group controller.</p>
    </div>

    <div class="layout">
        {{-- MAIN CALENDAR COLUMN --}}
        <div>
            {{-- Month navigator --}}
            <div class="month-nav fade-in">
                <div>
                    <div class="month-nav-title">{{ $current->format('F Y') }}</div>
                    <div class="month-nav-sub">{{ $yearLabel }}</div>
                </div>
                <div class="nav-arrows">
                    <a href="{{ route('members.activity', [$prevMonth->year, $prevMonth->month]) }}"
                       class="nav-btn" title="{{ $prevMonth->format('M Y') }}">←</a>
                    <a href="{{ route('members.activity', [now()->year, now()->month]) }}"
                       class="nav-btn" title="Today" style="font-size:.75rem;font-family:monospace;">now</a>
                    <a href="{{ route('members.activity', [$nextMonth->year, $nextMonth->month]) }}"
                       class="nav-btn" title="{{ $nextMonth->format('M Y') }}">→</a>
                </div>
            </div>

            {{-- Month stats --}}
            <div class="month-stats fade-in">
                <div class="month-stat {{ $monthEvents > 0 ? 'green' : 'muted' }}">
                    ● {{ $monthEvents }} {{ Str::plural('event', $monthEvents) }}
                </div>
                <div class="month-stat {{ $monthHours > 0 ? 'cyan' : 'muted' }}">
                    ◷ {{ number_format($monthHours, 1) }}h logged
                </div>
                @if ($monthEvents > 0)
                <div class="month-stat muted">
                    avg {{ number_format($monthHours / $monthEvents, 1) }}h / event
                </div>
                @endif
            </div>

            {{-- Calendar grid --}}
            <div class="cal-card fade-in">
                <div class="cal-dow-row">
                    @foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $d)
                        <div class="cal-dow">{{ $d }}</div>
                    @endforeach
                </div>
                <div class="cal-grid">
                    {{-- Leading empty cells --}}
                    @for ($e = 0; $e < $startDow; $e++)
                        <div class="cal-day empty"></div>
                    @endfor
                    {{-- Day cells --}}
                    @for ($d = 1; $d <= $daysInMonth; $d++)
                        @php
                            $dayLogs = $byDay->get($d, collect());
                            $dayHours = round($dayLogs->sum('hours'), 1);
                            $isToday = $today->year == $current->year
                                       && $today->month == $current->month
                                       && $today->day == $d;
                            $hasHours = $dayLogs->isNotEmpty();
                        @endphp
                        <div class="cal-day {{ $isToday ? 'today' : '' }} {{ $hasHours ? 'has-hours' : '' }}">
                            <div class="cal-day-num">
                                <span>{{ $d }}</span>
                                @if ($isToday)
                                    <span style="font-size:.65rem;color:var(--teal);letter-spacing:.05em;">TODAY</span>
                                @endif
                            </div>
                            @if ($hasHours)
                                <div class="cal-hours-badge">{{ number_format($dayHours, 1) }}h</div>
                                @foreach ($dayLogs as $log)
                                    <div class="cal-event" title="{{ $log->event_name ?? 'Event' }} — {{ $log->hours }}h">
                                        {{ $log->event_name ?? 'Event' }}
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endfor
                    {{-- Trailing empty cells to complete the grid --}}
                    @php
                        $totalCells = $startDow + $daysInMonth;
                        $trailingCells = (7 - ($totalCells % 7)) % 7;
                    @endphp
                    @for ($t = 0; $t < $trailingCells; $t++)
                        <div class="cal-day empty"></div>
                    @endfor
                </div>
            </div>

            {{-- Event log list --}}
            <div class="log-list fade-in">
                <div class="log-list-head">{{ $current->format('F Y') }} — event log</div>
                @forelse ($logs as $log)
                    <div class="log-row">
                        <div class="log-row-left">
                            <div class="log-row-date">{{ $log->event_date->format('D d M Y') }}</div>
                            <div class="log-row-name">{{ $log->event_name ?? 'Unnamed event' }}</div>
                        </div>
                        <div class="log-row-hours">{{ number_format($log->hours, 1) }}h</div>
                    </div>
                @empty
                    <div class="log-empty">No events logged in {{ $current->format('F Y') }}.</div>
                @endforelse
            </div>
        </div>

        {{-- SIDEBAR --}}
        <div class="sidebar fade-in">
            {{-- Year totals --}}
            <div class="sidebar-card">
                <div class="sidebar-head">{{ $yearLabel }}</div>
                <div class="sidebar-body">
                    <div class="year-tiles">
                        <div class="year-tile c-cyan">
                            <div class="year-tile-label">Hours</div>
                            <div class="year-tile-value">{{ number_format($yearHours, 1) }}</div>
                        </div>
                        <div class="year-tile c-green">
                            <div class="year-tile-label">Events</div>
                            <div class="year-tile-value">{{ $yearTotal }}</div>
                        </div>
                    </div>
                    <div style="font-size:.75rem;font-family:monospace;color:var(--muted);line-height:1.6;margin-top:0.8rem;">
                        Year runs 1 Sep → 31 Aug. Figures reset automatically each year.
                    </div>
                </div>
            </div>

            {{-- Monthly breakdown --}}
            <div class="sidebar-card">
                <div class="sidebar-head">Monthly breakdown</div>
                <div class="sidebar-body" style="padding:.6rem .8rem;">
                    <div class="month-breakdown">
                        @foreach ($sidebarMonths as $sm)
                            @php $isCurrent = ($sm['year'] == $current->year && $sm['month'] == $current->month); @endphp
                            <a href="{{ route('members.activity', [$sm['year'], $sm['month']]) }}"
                               class="mb-row {{ $isCurrent ? 'current-month' : '' }}">
                                <span class="mb-month">{{ $sm['label'] }}</span>
                                <span style="display:flex;align-items:center;gap:.5rem;">
                                    @if ($sm['hours'] > 0)
                                        <span class="mb-hours">{{ number_format($sm['hours'], 1) }}h</span>
                                        <span class="mb-events">{{ $sm['events'] }}ev</span>
                                    @else
                                        <span class="mb-none">—</span>
                                    @endif
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection