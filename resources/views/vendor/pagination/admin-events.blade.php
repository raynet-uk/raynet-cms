{{-- resources/views/vendor/pagination/admin-events.blade.php --}}

@if ($paginator->total() > 0)
    <nav role="navigation" aria-label="Event pagination"
         style="margin-top:0.75rem; padding:0.5rem 0; display:flex; justify-content:space-between; align-items:center; gap:1rem; font-size:0.85rem;">

        {{-- Left: page controls --}}
        <div style="display:flex; align-items:center; gap:0.35rem;">

            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span style="opacity:0.4;">&lt;</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   style="text-decoration:none; color:#93c5fd;">&lt;</a>
            @endif

            {{-- Page numbers --}}
            @foreach ($paginator->elements() as $element)
                @if (is_string($element))
                    <span style="opacity:0.7;">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span style="font-weight:600; color:#e5e7eb; border-bottom:1px solid #e5e7eb;">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               style="text-decoration:none; color:#a855f7;">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   style="text-decoration:none; color:#93c5fd;">&gt;</a>
            @else
                <span style="opacity:0.4;">&gt;</span>
            @endif
        </div>

        {{-- Right: summary --}}
        <div style="color:#9ca3af;">
            Showing
            {{ $paginator->firstItem() ?? 0 }}
            to
            {{ $paginator->lastItem() ?? 0 }}
            of
            {{ $paginator->total() }}
            results
        </div>
    </nav>
@endif