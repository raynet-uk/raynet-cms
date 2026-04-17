@extends('layouts.admin')
@section('title', 'Page Editor')
@section('content')

<style>
.pe { font-family: inherit; }
.pe-titlerow { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem; margin-bottom:1.5rem; }
.pe-h1 { font-size:1.35rem; font-weight:bold; color:#003366; margin:0; display:flex; align-items:center; gap:.55rem; }
.pe-btn { display:inline-flex; align-items:center; gap:.4rem; padding:.48rem 1rem; border:1px solid; font-family:inherit; font-size:12px; font-weight:bold; cursor:pointer; transition:all .13s; text-transform:uppercase; letter-spacing:.05em; text-decoration:none; white-space:nowrap; }
.pe-btn-primary { background:#003366; border-color:#003366; color:#fff; }
.pe-btn-primary:hover { background:#002244; }

/* Search */
.pe-search-row { display:flex; align-items:center; gap:.65rem; margin-bottom:1rem; flex-wrap:wrap; }
.pe-search { flex:1; min-width:180px; max-width:340px; border:1px solid #dde2e8; padding:.45rem .8rem; font-size:13px; font-family:inherit; color:#111827; outline:none; }
.pe-search:focus { border-color:#003366; }
.pe-count { font-size:12px; color:#9aa3ae; font-weight:bold; }

/* Table */
.pe-table-wrap { background:#fff; border:1px solid #dde2e8; overflow:hidden; }
.pe-table { width:100%; border-collapse:collapse; font-size:13px; }
.pe-table thead { background:#003366; }
.pe-table th { padding:.55rem .9rem; text-align:left; font-size:10px; font-weight:bold; text-transform:uppercase; letter-spacing:.1em; color:rgba(255,255,255,.75); white-space:nowrap; }
.pe-table th:last-child { text-align:right; }
.pe-table tbody tr { border-top:1px solid #f3f4f6; transition:background .1s; }
.pe-table tbody tr:hover { background:#f8fafc; }
.pe-table td { padding:.75rem .9rem; vertical-align:middle; }
.pe-table td:last-child { text-align:right; white-space:nowrap; }

/* Page name cell */
.pe-name { font-weight:700; font-size:.92rem; color:#003366; display:flex; align-items:center; gap:.45rem; }
.pe-slug { font-family:ui-monospace,monospace; font-size:.72rem; color:#9aa3ae; margin-top:.15rem; }
.pe-url  { font-size:.75rem; color:#2563eb; text-decoration:none; margin-top:.1rem; display:inline-flex; align-items:center; gap:.2rem; }
.pe-url:hover { text-decoration:underline; }

/* Badges */
.pe-badge { font-size:.66rem; font-weight:700; padding:.15rem .5rem; border-radius:999px; text-transform:uppercase; letter-spacing:.05em; white-space:nowrap; }
.pe-badge-routed  { background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; }
.pe-badge-custom  { background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; }
.pe-badge-complex { background:#fef3c7; color:#92400e; border:1px solid #fde68a; }

/* Actions */
.pe-actions { display:flex; align-items:center; gap:.35rem; justify-content:flex-end; }
.pe-act { display:inline-flex; align-items:center; gap:.3rem; padding:.35rem .75rem; border:1px solid; font-family:inherit; font-size:11px; font-weight:bold; cursor:pointer; transition:all .12s; text-decoration:none; white-space:nowrap; }
.pe-act-edit { background:#003366; border-color:#003366; color:#fff; }
.pe-act-edit:hover { background:#002244; }
.pe-act-view { background:#fff; border-color:#dde2e8; color:#6b7f96; }
.pe-act-view:hover { border-color:#003366; color:#003366; }
.pe-backup-count { font-size:10px; color:#9aa3ae; margin-left:.2rem; }

/* Size / date */
.pe-meta { font-size:.78rem; color:#6b7f96; }
.pe-meta-size { font-family:ui-monospace,monospace; }

/* Empty */
.pe-empty { padding:3rem; text-align:center; color:#9aa3ae; font-size:.875rem; }

/* Legend */
.pe-legend { display:flex; gap:1rem; flex-wrap:wrap; margin-top:.75rem; font-size:.75rem; color:#6b7f96; }
.pe-legend span { display:flex; align-items:center; gap:.35rem; }
</style>

<div class="pe">
    <div class="pe-titlerow">
        <h1 class="pe-h1">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#003366" stroke-width="1.75"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            Page Editor
        </h1>
        <a href="{{ route('admin.pages.create') }}" class="pe-btn pe-btn-primary">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add New Page
        </a>
    </div>

    <div class="pe-search-row">
        <input type="text" class="pe-search" id="pageSearch" placeholder="Search pages…" oninput="filterPages()">
        <div class="pe-count" id="pageCount">{{ $files->count() }} pages</div>
    </div>

    <div class="pe-table-wrap">
        <table class="pe-table" id="pageTable">
            <thead>
                <tr>
                    <th>Page</th>
                    <th>URL</th>
                    <th>Size</th>
                    <th>Last Modified</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($files as $file)
            @php
                $slug     = $file['slug'];
                $hasRoute = isset($routeMap[$slug]);
                $isComplex = in_array($slug, $complexPages);
                $url      = $routeMap[$slug] ?? null;
                $backups  = $backupCounts[$slug] ?? 0;
                $title    = ucwords(str_replace('-', ' ', $slug));
            @endphp
            <tr class="pe-row" data-name="{{ strtolower($title) }} {{ $slug }}">
                <td>
                    <div class="pe-name">
                        {{ $title }}
                        @if($hasRoute)  <span class="pe-badge pe-badge-routed">Live</span>  @endif
                        @if($isComplex) <span class="pe-badge pe-badge-complex">Complex</span> @endif
                        @if(!$hasRoute) <span class="pe-badge pe-badge-custom">No route</span> @endif
                    </div>
                    <div class="pe-slug">{{ $slug }}.blade.php</div>
                </td>
                <td>
                    @if($url)
                        <a href="{{ $url }}" target="_blank" class="pe-url">
                            {{ $url }}
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                        </a>
                    @else
                        <span style="color:#dde2e8">—</span>
                    @endif
                </td>
                <td><span class="pe-meta pe-meta-size">{{ $file['size'] }}</span></td>
                <td>
                    <div class="pe-meta">{{ $file['modified']->format('d M Y') }}</div>
                    <div class="pe-meta" style="font-size:.7rem;opacity:.7">{{ $file['modified']->diffForHumans() }}</div>
                </td>
                <td>
                    <div class="pe-actions">
                        @if($url)
                        <a href="{{ $url }}" target="_blank" class="pe-act pe-act-view" title="View live page">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            View
                        </a>
                        @endif
                        @if(!$isComplex)
                        <a href="{{ route('admin.pages.builder', $slug) }}" class="pe-act" style="background:#7c3aed;border-color:#7c3aed;color:#fff">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                            Builder
                        </a>
                        @endif
                        <a href="{{ route('admin.pages.edit', $slug) }}" class="pe-act pe-act-edit">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Source
                            @if($backups > 0)<span class="pe-backup-count">({{ $backups }} {{ Str::plural('backup', $backups) }})</span>@endif
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="pe-empty">No pages found in <code>resources/views/pages/</code></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="pe-legend">
        <span><span class="pe-badge pe-badge-routed">Live</span> Has a registered route — accessible on the public site</span>
        <span><span class="pe-badge pe-badge-complex">Complex</span> Contains PHP logic — source edit only</span>
        <span><span class="pe-badge pe-badge-custom">No route</span> File exists but no public URL registered</span>
    </div>
</div>

<script>
function filterPages() {
    const q = document.getElementById('pageSearch').value.toLowerCase();
    const rows = document.querySelectorAll('.pe-row');
    let visible = 0;
    rows.forEach(r => {
        const match = r.dataset.name.includes(q);
        r.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('pageCount').textContent = visible + ' page' + (visible !== 1 ? 's' : '');
}
</script>

@endsection