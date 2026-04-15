@extends('layouts.app')

@section('title', 'Events')

@section('content')

<style>
:root {
    --navy:       #003366;
    --navy-mid:   #004080;
    --navy-faint: #e8eef5;
    --red:        #C8102E;
    --white:      #FFFFFF;
    --grey:       #F2F2F2;
    --grey-mid:   #dde2e8;
    --grey-dark:  #9aa3ae;
    --text:       #001f40;
    --text-mid:   #2d4a6b;
    --text-muted: #6b7f96;
    --font: Arial, 'Helvetica Neue', Helvetica, sans-serif;
    --shadow-sm: 0 1px 3px rgba(0,51,102,.09);
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { background: var(--grey); color: var(--text); font-family: var(--font); font-size: 14px; min-height: 100vh; }

/* ─── HEADER ─── */
.rn-header {
    background: var(--navy); border-bottom: 4px solid var(--red);
    position: sticky; top: 0; z-index: 100; box-shadow: 0 2px 10px rgba(0,0,0,.3);
}
.rn-header-inner {
    max-width: 900px; margin: 0 auto; padding: 0 1.5rem;
    display: flex; align-items: center; justify-content: space-between; gap: 1rem;
}
.rn-brand { display: flex; align-items: center; gap: .85rem; padding: .75rem 0; }
.rn-logo { background: var(--red); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.rn-logo span { font-size: 10px; font-weight: bold; color: var(--white); letter-spacing: .06em; text-align: center; line-height: 1.15; text-transform: uppercase; }
.rn-org { font-size: 14px; font-weight: bold; color: var(--white); letter-spacing: .04em; text-transform: uppercase; }
.rn-sub { font-size: 11px; color: rgba(255,255,255,.5); margin-top: 2px; text-transform: uppercase; letter-spacing: .04em; }
.rn-cal-link {
    font-size: 12px; font-weight: bold; color: rgba(255,255,255,.8);
    text-decoration: none; border: 1px solid rgba(255,255,255,.25); padding: .35rem .9rem;
    transition: all .15s; white-space: nowrap;
}
.rn-cal-link:hover { background: rgba(255,255,255,.1); color: var(--white); }

/* ─── PAGE BAND ─── */
.page-band { background: var(--white); border-bottom: 1px solid var(--grey-mid); box-shadow: var(--shadow-sm); }
.page-band-inner {
    max-width: 900px; margin: 0 auto; padding: 1.25rem 1.5rem;
    display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; flex-wrap: wrap;
}
.page-eyebrow { font-size: 10px; font-weight: bold; color: var(--red); text-transform: uppercase; letter-spacing: .18em; margin-bottom: .3rem; display: flex; align-items: center; gap: .45rem; }
.page-eyebrow::before { content: ''; width: 14px; height: 2px; background: var(--red); display: inline-block; }
.page-title { font-size: 22px; font-weight: bold; color: var(--navy); line-height: 1; }
.page-desc  { font-size: 13px; color: var(--text-muted); margin-top: .4rem; }

/* ─── WRAP ─── */
.wrap { max-width: 900px; margin: 0 auto; padding: 1.5rem 1.5rem 4rem; }

/* ─── EVENTS CARD ─── */
.events-card {
    background: var(--white); border: 1px solid var(--grey-mid);
    border-top: 3px solid var(--navy); box-shadow: var(--shadow-sm);
}

/* ─── EVENT ROW ─── */
.event-row {
    display: flex; justify-content: space-between; gap: 1rem; flex-wrap: wrap;
    padding: 1rem 1.2rem; border-bottom: 1px solid var(--grey-mid);
    transition: background .1s;
}
.event-row:last-child { border-bottom: none; }
.event-row:hover { background: var(--navy-faint); }

.event-main { flex: 1 1 260px; min-width: 0; }
.event-title {
    font-size: 15px; font-weight: bold; color: var(--navy);
    text-decoration: none; display: block; margin-bottom: .2rem;
    transition: opacity .12s;
}
.event-title:hover { opacity: .7; }
.event-meta { font-size: 12px; color: var(--text-muted); margin-bottom: .15rem; font-weight: bold; }
.event-loc  { font-size: 12px; color: var(--text-muted); margin-bottom: .15rem; }
.event-desc { font-size: 12px; color: var(--text-muted); margin-top: .25rem; line-height: 1.55; max-height: 3.3rem; overflow: hidden; }

.event-aside { display: flex; flex-direction: column; align-items: flex-end; gap: .45rem; padding-top: .1rem; }

/* Type pill */
.type-pill {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: 2px 9px; font-size: 11px; font-weight: bold;
    border: 1px solid; text-transform: uppercase; letter-spacing: .04em; white-space: nowrap;
}

.ics-link {
    font-size: 11px; font-weight: bold; color: var(--navy); text-decoration: none;
    text-transform: uppercase; letter-spacing: .04em; transition: opacity .12s;
    border-bottom: 1px solid rgba(0,51,102,.25); padding-bottom: 1px;
}
.ics-link:hover { opacity: .65; }

/* ─── EMPTY STATE ─── */
.empty-state { padding: 3rem 1.2rem; text-align: center; }
.empty-icon  { font-size: 2rem; opacity: .2; margin-bottom: .75rem; }
.empty-text  { font-size: 13px; color: var(--text-muted); }

/* ─── PAGINATION ─── */
.pagination-wrap {
    padding: .75rem 1.2rem; border-top: 1px solid var(--grey-mid);
    background: var(--grey); display: flex; align-items: center;
    justify-content: space-between; gap: 1rem; flex-wrap: wrap;
}
.pagination-info { font-size: 11px; color: var(--text-muted); font-weight: bold; }
.page-links { display: flex; align-items: center; gap: .3rem; }
.page-link {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 30px; height: 30px; padding: 0 .4rem;
    font-family: var(--font); font-size: 12px; font-weight: bold;
    text-decoration: none; border: 1px solid var(--grey-mid);
    background: var(--white); color: var(--text-muted); transition: all .15s;
}
.page-link:hover    { border-color: var(--navy); color: var(--navy); }
.page-link.active   { background: var(--navy); border-color: var(--navy); color: var(--white); }
.page-link.disabled { opacity: .35; pointer-events: none; }

/* ─── ANIMATIONS ─── */
@keyframes fadeUp { from { opacity:0; transform:translateY(5px); } to { opacity:1; transform:none; } }
.fade-in { animation: fadeUp .3s ease both; }

@media(max-width:520px) {
    .event-aside { align-items: flex-start; }
    .page-band-inner { flex-direction: column; align-items: flex-start; }
}
</style>

{{-- ─── HEADER ─── --}}
<header class="rn-header fade-in">
    <div class="rn-header-inner">
        <div class="rn-brand">
            <div class="rn-logo"><span>RAY<br>NET</span></div>
            <div>
                <div class="rn-org">{{ \App\Helpers\RaynetSetting::groupName() }}</div>
                <div class="rn-sub">Upcoming Events</div>
            </div>
        </div>
        <a href="{{ route('calendar') }}" class="rn-cal-link">Switch to calendar view →</a>
    </div>
</header>

{{-- ─── PAGE BAND ─── --}}
<div class="page-band fade-in">
    <div class="page-band-inner">
        <div>
            <div class="page-eyebrow">Public Schedule</div>
            <h1 class="page-title">Upcoming Events</h1>
            <p class="page-desc">Public {{ \App\Helpers\RaynetSetting::groupName() }} events, pulled from the same data as the calendar.</p>
        </div>
    </div>
</div>

<div class="wrap">

    @if ($events->isEmpty())
        <div class="events-card fade-in">
            <div class="empty-state">
                <div class="empty-icon">📅</div>
                <div class="empty-text">No upcoming events are currently published.</div>
            </div>
        </div>
    @else
        <div class="events-card fade-in">

            @foreach ($events as $event)
                @php
                    $type        = $event->type;
                    $badgeColour = $type?->colour ?? null;
                    $badgeLabel  = $type?->name ?? null;
                @endphp
                <article class="event-row">
                    <div class="event-main">
                        <a href="{{ $event->url() }}" class="event-title">{{ $event->title }}</a>
                        <div class="event-meta">
                            {{ $event->displayDate() }}
                            @if ($event->ends_at)
                                &rarr; {{ $event->ends_at->format('D j M Y, H:i') }}
                            @endif
                        </div>
                        @if ($event->location)
                            <div class="event-loc">📍 {{ $event->location }}</div>
                        @endif
                        @if ($event->description)
                            <p class="event-desc">{{ Str::limit($event->description, 180) }}</p>
                        @endif
                    </div>

                    <div class="event-aside">
                        @if ($type && $badgeLabel)
                            @if ($badgeColour)
                                <span class="type-pill"
                                      style="background:{{ $badgeColour }}1a;border-color:{{ $badgeColour }};color:{{ $badgeColour }};">
                                    {{ $badgeLabel }}
                                </span>
                            @else
                                <span class="type-pill"
                                      style="background:var(--navy-faint);border-color:rgba(0,51,102,.2);color:var(--navy);">
                                    {{ $badgeLabel }}
                                </span>
                            @endif
                        @endif

                        <a href="{{ route('events.ics', [
                                    'year'  => $event->starts_at->format('Y'),
                                    'month' => $event->starts_at->format('m'),
                                    'slug'  => $event->slug,
                                ]) }}"
                           class="ics-link">↓ Download .ics</a>
                    </div>
                </article>
            @endforeach

            {{-- ─── PAGINATION ─── --}}
            @if ($events->lastPage() > 1)
                @php
                    $current = $events->currentPage();
                    $last    = $events->lastPage();
                @endphp
                <div class="pagination-wrap">
                    <div class="pagination-info">
                        Page {{ $current }} of {{ $last }}
                    </div>
                    <div class="page-links">
                        <a href="{{ $events->url($current - 1) }}"
                           class="page-link {{ $current === 1 ? 'disabled' : '' }}">‹</a>

                        @for ($page = max(1, $current - 2); $page <= min($last, $current + 2); $page++)
                            <a href="{{ $events->url($page) }}"
                               class="page-link {{ $page === $current ? 'active' : '' }}">{{ $page }}</a>
                        @endfor

                        <a href="{{ $events->url($current + 1) }}"
                           class="page-link {{ $current === $last ? 'disabled' : '' }}">›</a>
                    </div>
                </div>
            @endif

        </div>
    @endif

</div>

@endsection