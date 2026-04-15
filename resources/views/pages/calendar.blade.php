
@extends('layouts.app')

@section('title', 'Calendar')

@section('content')
    <style>
        /* Show the hover card when the wrapper is hovered */
        .lr-calendar-event-pill:hover .hover-card {
            display: block;
        }
    </style>

    {{-- Header + month navigation --}}
    @php
        $prevUrl = route('calendar', [
            'year'  => $prevMonth->format('Y'),
            'month' => $prevMonth->format('m'),
        ]);
        $nextUrl = route('calendar', [
            'year'  => $nextMonth->format('Y'),
            'month' => $nextMonth->format('m'),
        ]);
    @endphp

    <div style="display:flex; justify-content:space-between; align-items:flex-end; gap:1rem; margin-bottom:1rem;">
        <div>
            <h1 style="margin:0; color:#e5e7eb;">
                {{ $currentMonth->format('F Y') }}
            </h1>
            <p style="margin:0.25rem 0 0; font-size:0.9rem; color:#9ca3af;">
                Public training, exercises and event support for {{ \App\Helpers\RaynetSetting::groupName() }}.
            </p>
        </div>

        <div style="display:flex; flex-wrap:wrap; gap:0.5rem; align-items:center;">
            <a href="{{ $prevUrl }}"
               style="padding:0.35rem 0.7rem; border-radius:999px;
                      border:1px solid rgba(148,163,184,0.7); color:#e5e7eb;
                      text-decoration:none; font-size:0.85rem;">
                ← {{ $prevMonth->format('M Y') }}
            </a>
            <a href="{{ $nextUrl }}"
               style="padding:0.35rem 0.7rem; border-radius:999px;
                      border:1px solid rgba(148,163,184,0.7); color:#e5e7eb;
                      text-decoration:none; font-size:0.85rem;">
                {{ $nextMonth->format('M Y') }} →
            </a>

            <a href="{{ $icsUrl }}"
               style="padding:0.4rem 0.9rem; border-radius:999px;
                      border:1px solid rgba(56,189,248,0.9); color:#e5e7eb;
                      text-decoration:none; font-size:0.85rem;">
                Export month (.ics)
            </a>
        </div>
    </div>

    {{-- Legend --}}
    <p style="font-size:0.8rem; color:#9ca3af; margin-bottom:0.7rem;">
        Hover over an event badge for details. Multi-day events appear on each day of their span.
    </p>

    {{-- Calendar grid --}}
    <div style="
        border-radius:0.9rem;
        border:1px solid rgba(148,163,184,0.5);
        background:#020617;
        padding:0.6rem 0.7rem 0.8rem;
        font-size:0.85rem;
        color:#e5e7eb;
    ">
        {{-- Weekday header --}}
        <div style="display:grid; grid-template-columns:repeat(7,1fr); gap:0.25rem; margin-bottom:0.35rem;">
            @foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $dayName)
                <div style="text-align:center; font-size:0.8rem; color:#9ca3af;">
                    {{ $dayName }}
                </div>
            @endforeach
        </div>

        {{-- Weeks --}}
        @foreach ($weeks as $week)
            <div style="display:grid; grid-template-columns:repeat(7,1fr); gap:0.25rem; margin-bottom:0.25rem;">
                @foreach ($week as $day)
                    @php
                        /** @var \Illuminate\Support\Carbon $date */
                        $date    = $day['date'];
                        $inMonth = $day['in_month'];
                        $isToday = $day['is_today'];
                        $dayEvents = $day['events'];
                    @endphp

                    <div style="
                        min-height:5.25rem;
                        border-radius:0.6rem;
                        padding:0.25rem 0.35rem 0.3rem;
                        border:1px solid
                            {{ $isToday ? 'rgba(56,189,248,0.9)' : 'rgba(31,41,55,0.9)' }};
                        background:
                            {{ $isToday ? 'linear-gradient(to bottom,rgba(15,23,42,0.9),rgba(15,23,42,0.98))' : 'rgba(15,23,42,0.95)' }};
                        opacity: {{ $inMonth ? '1' : '0.45' }};
                        position:relative;
                    ">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <span style="font-size:0.8rem;">
                                {{ $date->format('j') }}
                            </span>
                            @if ($isToday)
                                <span style="font-size:0.7rem; color:#93c5fd;">
                                    Today
                                </span>
                            @endif
                        </div>

                                                {{-- Events in this day --}}
                        @foreach ($dayEvents as $event)
                            @php
                                $type       = $event->type;
                                $baseColour = $type && $type->colour ? $type->colour : '#22c55e';
                            @endphp

                            <div class="lr-calendar-event-pill" style="position:relative; margin-top:0.22rem;">
                                <a href="{{ $event->url() }}"
                                   style="
                                       display:inline-flex;
                                       max-width:100%;
                                       align-items:center;
                                       padding:0.12rem 0.4rem;
                                       border-radius:999px;
                                       border:1px solid {{ $baseColour }};
                                       background: {{ $baseColour }}1a;
                                       color:#e5e7eb;
                                       text-decoration:none;
                                       font-size:0.74rem;
                                       white-space:nowrap;
                                       overflow:hidden;
                                       text-overflow:ellipsis;
                                   ">
                                    {{ $type?->name ?? 'Event' }}
                                </a>

                                {{-- Hover card --}}
                                <div class="hover-card"
                                     style="
                                         display:none;
                                         position:absolute;
                                         top:1.6rem;
                                         left:0;
                                         z-index:30;
                                         min-width:14rem;
                                         max-width:18rem;
                                         padding:0.5rem 0.6rem;
                                         border-radius:0.6rem;
                                         border:1px solid rgba(148,163,184,0.8);
                                         background:#020617;
                                         box-shadow:0 18px 40px rgba(0,0,0,0.8);
                                         font-size:0.78rem;
                                     ">
                                    <div style="font-weight:600; margin-bottom:0.15rem;">
                                        {{ $event->title }}
                                    </div>
                                    <div style="color:#9ca3af; margin-bottom:0.1rem;">
                                        {{ $event->displayDate() }}
                                        @if ($event->ends_at)
                                            <span> → {{ $event->ends_at->format('D j M Y, H:i') }}</span>
                                        @endif
                                    </div>
                                    @if ($event->location)
                                        <div style="color:#9ca3af; margin-bottom:0.1rem;">
                                            📍 {{ $event->location }}
                                        </div>
                                    @endif
                                    @if ($event->description)
                                        <div style="color:#6b7280; max-height:3.5rem; overflow:hidden; text-overflow:ellipsis;">
                                            {{ \Illuminate\Support\Str::limit($event->description, 120) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
@endsection