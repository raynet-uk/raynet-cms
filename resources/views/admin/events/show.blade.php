@extends('layouts.admin')

@section('title', $event->title)

@section('content')
    <p style="font-size:0.8rem; color:#9ca3af; margin-bottom:0.6rem;">
        <a href="{{ route('calendar') }}" style="color:#93c5fd; text-decoration:none;">
            ← Back to calendar
        </a>
    </p>

    <h1>{{ $event->title }}</h1>
    <p style="margin-top:0.4rem; color:#9ca3af; max-width:40rem;">
        Operational view of a {{ \App\Helpers\RaynetSetting::groupName() }} activity – timing, location and key details.
    </p>

    <div style="margin-top:1.2rem; padding:1rem 1.2rem; border-radius:0.8rem;
                border:1px solid rgba(148,163,184,0.4); background:#020617;">
        <div style="display:grid; gap:0.8rem;
                    grid-template-columns:repeat(auto-fit,minmax(190px,1fr));">

            <div>
                <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.08em;
                            color:#9ca3af; margin-bottom:0.2rem;">
                    When
                </div>
                <div style="font-size:1rem; color:#e5e7eb;">
                    {{ $event->displayDate() }}
                </div>
            </div>

            @if ($event->location)
                <div>
                    <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.08em;
                                color:#9ca3af; margin-bottom:0.2rem;">
                        Location
                    </div>
                    <div style="font-size:1rem; color:#e5e7eb;">
                        {{ $event->location }}
                    </div>
                </div>
            @endif

            @if ($event->type || $event->category)
                <div>
                    <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.08em;
                                color:#9ca3af; margin-bottom:0.2rem;">
                        Type
                    </div>
                    <div style="font-size:1rem; color:#e5e7eb;">
                        @if ($event->type)
                            {{ $event->type->name }}
                        @elseif ($event->category)
                            {{ $event->category }}
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if ($event->description)
        <div style="margin-top:1.2rem; max-width:44rem;">
            <h2 style="font-size:1rem; margin-bottom:0.4rem;">Event details</h2>
            <p style="color:#e5e7eb; line-height:1.6; white-space:pre-line;">
                {{ $event->description }}
            </p>
        </div>
    @endif

    <div style="margin-top:1.4rem; display:flex; flex-wrap:wrap; gap:0.7rem; align-items:center;">
        <a href="{{ route('events.ics', [
                    'year'  => $event->starts_at->format('Y'),
                    'month' => $event->starts_at->format('m'),
                    'slug'  => $event->slug,
                ]) }}"
           style="padding:0.45rem 1.1rem; border-radius:999px; font-size:0.85rem;
                  border:1px solid rgba(56,189,248,0.9); background:#020617; color:#e5e7eb;
                  text-decoration:none;">
            Add to calendar (.ics)
        </a>

        <a href="{{ route('request-support') }}"
           style="padding:0.45rem 1.1rem; border-radius:999px; font-size:0.85rem;
                  border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;
                  text-decoration:none;">
            Request RAYNET for a future event
        </a>
    </div>
@endsection