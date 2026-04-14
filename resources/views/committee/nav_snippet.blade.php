{{--
    ─────────────────────────────────────────────────────────────────────────
    ADD THIS TO YOUR MAIN NAVIGATION (resources/views/layouts/app.blade.php
    or wherever your nav links live).

    Paste it after the Admin nav link and before the Member-level items.
    ─────────────────────────────────────────────────────────────────────────
--}}

@if(in_array(auth()->user()->role ?? '', ['committee','admin','super_admin']))
<a href="{{ route('committee.dashboard') }}"
   class="nav-link {{ request()->routeIs('committee.*') ? 'nav-link--active' : '' }}"
   style="display:inline-flex; align-items:center; gap:6px;">
    <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
    </svg>
    Committee
    @php
        $overdueCount = \App\Models\CommitteeAction::where('due_date', '<', now())
            ->whereNotIn('status',['closed','cancelled'])->count();
    @endphp
    @if($overdueCount > 0)
        <span style="background:#C8102E; color:#fff; font-size:10px; font-weight:700;
                     padding:1px 5px; border-radius:999px; line-height:1.4;">
            {{ $overdueCount }}
        </span>
    @endif
</a>
@endif
