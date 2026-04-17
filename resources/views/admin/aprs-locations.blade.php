@extends('layouts.admin')
@section('title', 'APRS Operator Locations')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
:root {
    --navy:#003366; --red:#C8102E; --light:#F2F2F2;
    --border:#D0D0D0; --muted:#6b7f96;
    --shadow-sm:0 2px 8px rgba(0,51,102,.06);
    --shadow-md:0 4px 16px rgba(0,51,102,.13);
}
.aprs-wrap { max-width:1200px; margin:0 auto; padding:0 1rem 3rem; }

/* ── header strip ── */
.aprs-header {
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:1rem;
    padding:1rem 0; border-bottom:2px solid var(--navy); margin-bottom:1.5rem;
}
.aprs-title { font-size:1.5rem; font-weight:bold; color:var(--navy); }
.aprs-sub { font-size:.85rem; color:var(--muted); }
.aprs-meta { display:flex; align-items:center; gap:.75rem; flex-wrap:wrap; }
.aprs-stat {
    display:inline-flex; align-items:center; gap:.4rem;
    padding:.35rem .85rem; border-radius:999px;
    border:1px solid var(--border); background:#fff;
    font-size:.8rem; font-weight:bold; color:var(--navy);
}
.aprs-stat-dot { width:8px; height:8px; border-radius:50%; background:#2E7D32; flex-shrink:0;
    box-shadow:0 0 0 2px rgba(46,125,50,.2); }
.btn-refresh {
    padding:.45rem 1.1rem; background:var(--navy); border:none;
    color:#fff; font-size:.85rem; font-weight:bold;
    border-radius:6px; cursor:pointer; font-family:inherit;
    display:inline-flex; align-items:center; gap:.4rem; transition:background .15s;
}
.btn-refresh:hover { background:#002244; }
.btn-refresh:disabled { opacity:.55; cursor:default; }

/* ── map ── */
#aprsMap {
    height:480px; border:1px solid var(--border); border-radius:8px;
    margin-bottom:1.5rem; box-shadow:var(--shadow-sm); background:#e8eef5;
}

/* ── table card ── */
.table-card {
    background:#fff; border:1px solid var(--border);
    border-radius:8px; overflow:hidden; box-shadow:var(--shadow-sm);
}
.table-card-head {
    display:flex; align-items:center; justify-content:space-between;
    padding:.75rem 1.2rem; background:var(--light); border-bottom:1px solid var(--border);
    gap:1rem; flex-wrap:wrap;
}
.table-card-title { font-size:1rem; font-weight:bold; color:var(--navy); }
.search-box {
    padding:.4rem .8rem; border:1px solid var(--border); border-radius:6px;
    font-size:.85rem; font-family:inherit; color:var(--navy); outline:none;
    width:220px; transition:border-color .15s;
}
.search-box:focus { border-color:var(--navy); }
table { width:100%; border-collapse:collapse; font-size:.88rem; }
thead tr { background:var(--light); }
th {
    padding:.65rem 1rem; text-align:left; font-size:.75rem; font-weight:bold;
    text-transform:uppercase; letter-spacing:.07em; color:var(--muted);
    border-bottom:1px solid var(--border); white-space:nowrap;
}
td { padding:.7rem 1rem; border-bottom:1px solid var(--border); color:#1a1a1a; vertical-align:middle; }
tr:last-child td { border-bottom:none; }
tr.located-row:hover { background:#f0f5ff; cursor:pointer; }
tr.unlocated-row { opacity:.55; }

/* ── callsign cell ── */
.cs-cell { display:flex; flex-direction:column; gap:1px; }
.cs-main { font-weight:bold; color:var(--navy); font-family:monospace; font-size:.95rem; }
.cs-ssid { font-size:.72rem; color:var(--muted); }

/* ── freshness dots ── */
.fresh-dot {
    display:inline-block; width:9px; height:9px;
    border-radius:50%; flex-shrink:0; margin-right:.35rem;
}
.fresh-green  { background:#2E7D32; box-shadow:0 0 0 2px rgba(46,125,50,.2); }
.fresh-amber  { background:#d97706; box-shadow:0 0 0 2px rgba(217,119,6,.2); }
.fresh-grey   { background:#9aa3ae; }
.fresh-old    { background:#dde2e8; }

.age-cell { display:flex; align-items:center; white-space:nowrap; }

/* ── coord badge ── */
.coord-badge {
    font-family:monospace; font-size:.78rem;
    padding:2px 7px; border-radius:4px;
    background:var(--light); border:1px solid var(--border);
    color:var(--navy); white-space:nowrap;
}

/* ── locate btn ── */
.btn-locate {
    padding:.3rem .75rem; font-size:.75rem; font-weight:bold;
    border:1px solid var(--navy); border-radius:4px;
    background:transparent; color:var(--navy); cursor:pointer;
    font-family:inherit; transition:all .12s;
}
.btn-locate:hover { background:var(--navy); color:#fff; }

/* ── no-location row ── */
.nil-badge {
    font-size:.75rem; font-weight:bold; padding:2px 8px;
    border-radius:999px; background:#f8f8f8; border:1px solid var(--border);
    color:var(--muted);
}

/* ── SSID extras ── */
.ssid-extras { display:flex; flex-wrap:wrap; gap:4px; margin-top:3px; }
.ssid-chip {
    font-size:.7rem; font-weight:bold; padding:1px 6px;
    border-radius:999px; background:#e8eef5;
    border:1px solid var(--navy); color:var(--navy);
    cursor:pointer; font-family:monospace;
}
.ssid-chip:hover { background:var(--navy); color:#fff; }

/* ── avatar ── */
.user-cell { display:flex; align-items:center; gap:.6rem; }
.u-avatar {
    width:30px; height:30px; border-radius:50%; flex-shrink:0;
    background:var(--navy); display:flex; align-items:center;
    justify-content:center; font-size:11px; font-weight:bold; color:#fff;
    text-transform:uppercase; overflow:hidden; border:1px solid rgba(0,51,102,.15);
}
.u-avatar img { width:100%; height:100%; object-fit:cover; }
.u-name { font-weight:bold; color:var(--navy); }
.u-title { font-size:.75rem; color:var(--muted); }

/* ── refresh indicator ── */
#refreshStatus { font-size:.78rem; color:var(--muted); }

/* ── Leaflet popup override ── */
.aprs-popup { font-family:Arial,sans-serif; font-size:12px; min-width:160px; }
.aprs-popup strong { color:#003366; display:block; font-size:13px; margin-bottom:3px; }
.aprs-popup .ap-row { display:flex; justify-content:space-between; gap:8px; margin-top:2px; }
.aprs-popup .ap-lbl { color:#9aa3ae; }
</style>
@endpush

@section('content')
<div class="aprs-wrap">

    <div class="aprs-header">
        <div>
            <div class="aprs-title">📡 APRS Operator Locations</div>
            <div class="aprs-sub">
                Live positions via APRS.fi · {{ $userRows->count() }} operators ·
                <strong style="color:#2E7D32;">{{ $located }}</strong> currently located
            </div>
        </div>
        <div class="aprs-meta">
            <span class="aprs-stat">
                <span class="aprs-stat-dot"></span>
                <span id="refreshStatus">Loaded {{ now()->format('H:i:s') }}</span>
            </span>
            <button class="btn-refresh" id="btnRefresh" onclick="doRefresh()">
                ↻ Refresh
            </button>
        </div>
    </div>

    {{-- MAP --}}
    <div id="aprsMap"></div>

    {{-- TABLE --}}
    <div class="table-card">
        <div class="table-card-head">
            <div class="table-card-title">All operators</div>
            <input type="text" class="search-box" id="tableSearch"
                   placeholder="Search callsign or name…" oninput="filterTable(this.value)">
        </div>
        <div style="overflow-x:auto;">
            <table id="aprsTable">
                <thead>
                    <tr>
                        <th>Operator</th>
                        <th>Callsign</th>
                        <th>Last seen</th>
                        <th>Position</th>
                        <th>Speed / Alt</th>
                        <th>Comment</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($userRows as $row)
                @php
                    $u     = $row['user'];
                    $e     = $row['entry'];
                    $init  = strtoupper(substr($u->name, 0, 1));
                    $base  = strtoupper($u->callsign ?? '');
                    $ssid  = trim($u->aprs_ssid ?? '');
                    $full  = ($ssid && $ssid !== '0') ? $base.'-'.ltrim($ssid,'-') : $base;
                    $extras = $row['extras'];
                @endphp
                <tr class="{{ $e ? 'located-row' : 'unlocated-row' }}"
                    data-search="{{ strtolower($u->name . ' ' . $base) }}"
                    @if($e) onclick="flyTo({{ $e['lat'] }}, {{ $e['lng'] }}, '{{ $full }}')" @endif>

                    {{-- Operator --}}
                    <td>
                        <div class="user-cell">
                            <div class="u-avatar">
                                @if($u->avatar)
                                    <img src="{{ Storage::url($u->avatar) }}" alt="">
                                @else
                                    {{ $init }}
                                @endif
                            </div>
                            <div>
                                <div class="u-name">{{ $u->name }}</div>
                                @if($u->operator_title)
                                    <div class="u-title">{{ $u->operator_title }}</div>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Callsign --}}
                    <td>
                        <div class="cs-cell">
                            <span class="cs-main">{{ $base }}</span>
                            @if($ssid && $ssid !== '0')
                                <span class="cs-ssid">SSID: -{{ ltrim($ssid,'-') }} → queried as {{ $full }}</span>
                            @endif
                            @if(!empty($extras))
                                <div class="ssid-extras">
                                    @foreach($extras as $ek => $ev)
                                        <span class="ssid-chip"
                                              onclick="event.stopPropagation(); flyTo({{ $ev['lat'] }}, {{ $ev['lng'] }}, '{{ $ek }}')">
                                            {{ $ek }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </td>

                    {{-- Last seen --}}
                    <td>
                        @if($e)
                            @php
                                $ts   = (int) ($e['lasttime'] ?? $e['time'] ?? 0);
                                $cls  = app(App\Services\AprsService::class)->freshnessClass($ts);
                                $age  = app(App\Services\AprsService::class)->formatAge($ts);
                            @endphp
                            <div class="age-cell">
                                <span class="fresh-dot {{ $cls }}"></span>
                                {{ $age }}
                            </div>
                        @else
                            <span class="nil-badge">Not found</span>
                        @endif
                    </td>

                    {{-- Position --}}
                    <td>
                        @if($e)
                            <span class="coord-badge">
                                {{ number_format((float)$e['lat'], 5) }},
                                {{ number_format((float)$e['lng'], 5) }}
                            </span>
                        @else
                            —
                        @endif
                    </td>

                    {{-- Speed / Alt --}}
                    <td>
                        @if($e)
                            @php
                                $spd = (float)($e['speed'] ?? 0);
                                $alt = (float)($e['altitude'] ?? 0);
                            @endphp
                            <span style="font-size:.8rem;color:var(--muted);">
                                @if($spd > 0) {{ round($spd) }} km/h @else Stationary @endif
                                @if($alt > 0) · {{ round($alt) }}m @endif
                            </span>
                        @else
                            —
                        @endif
                    </td>

                    {{-- Comment --}}
                    <td style="max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-size:.8rem; color:var(--muted);">
                        {{ $e ? ($e['comment'] ?? '') : '—' }}
                    </td>

                    {{-- Action --}}
                    <td>
                        @if($e)
                            <button class="btn-locate"
                                    onclick="event.stopPropagation(); flyTo({{ $e['lat'] }}, {{ $e['lng'] }}, '{{ $full }}')">
                                📍 Locate
                            </button>
                        @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@php
$markersData = $userRows->filter(fn($r) => $r['located'])->map(function($r) {
    $e    = $r['entry'];
    $u    = $r['user'];
    $ssid = trim($u->aprs_ssid ?? '');
    $base = strtoupper($u->callsign ?? '');
    $full = ($ssid && $ssid !== '0') ? $base.'-'.ltrim($ssid,'-') : $base;
    $ts   = (int)($e['lasttime'] ?? $e['time'] ?? 0);
    $diff = now()->timestamp - $ts;
    return [
        'call'    => $full,
        'name'    => $u->name,
        'title'   => $u->operator_title ?? '',
        'lat'     => (float)$e['lat'],
        'lng'     => (float)$e['lng'],
        'speed'   => (float)($e['speed'] ?? 0),
        'alt'     => (float)($e['altitude'] ?? 0),
        'comment' => $e['comment'] ?? '',
        'age'     => $diff < 60 ? 'Just now' : ($diff < 3600 ? floor($diff/60).' min ago' : ($diff < 86400 ? floor($diff/3600).' hr ago' : date('d M H:i', $ts).' UTC')),
        'fresh'   => $diff < 1800 ? 'green' : ($diff < 7200 ? 'amber' : 'grey'),
    ];
})->values()->toArray();

$extrasData = collect($locations)->map(function($e, $call) {
    return [
        'call'    => $call,
        'lat'     => (float)$e['lat'],
        'lng'     => (float)$e['lng'],
        'comment' => $e['comment'] ?? '',
        'speed'   => (float)($e['speed'] ?? 0),
    ];
})->values()->toArray();
@endphp
<script>
const MARKERS = @json($markersData);
const EXTRAS  = @json($extrasData);

/* ── Leaflet map ── */
const map = L.map('aprsMap').setView([53.4, -2.97], 9);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
    maxZoom: 18,
}).addTo(map);

const markerMap = {};

function markerColour(fresh) {
    return fresh === 'green' ? '#2E7D32' : fresh === 'amber' ? '#d97706' : '#9aa3ae';
}

function makeIcon(call, fresh) {
    const colour = markerColour(fresh);
    const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 44 44">
        <circle cx="22" cy="22" r="18" fill="${colour}" fill-opacity=".18" stroke="${colour}" stroke-width="1.5"/>
        <circle cx="22" cy="22" r="8" fill="${colour}"/>
        <circle cx="22" cy="22" r="4" fill="white"/>
    </svg>`;
    return L.divIcon({
        html: `<div style="position:relative;">
            ${svg}
            <div style="position:absolute;top:-18px;left:50%;transform:translateX(-50%);
                 background:${colour};color:#fff;font-size:10px;font-weight:bold;
                 padding:1px 5px;border-radius:3px;white-space:nowrap;font-family:monospace;">
                ${call}
            </div>
        </div>`,
        iconSize: [44, 44],
        iconAnchor: [22, 22],
        className: '',
    });
}

const bounds = [];

MARKERS.forEach(m => {
    const popup = `
        <div class="aprs-popup">
            <strong>${m.call} — ${m.name}</strong>
            ${m.title ? `<div style="color:#9aa3ae;font-size:11px;margin-bottom:4px;">${m.title}</div>` : ''}
            <div class="ap-row"><span class="ap-lbl">Last seen</span><span>${m.age}</span></div>
            <div class="ap-row"><span class="ap-lbl">Speed</span><span>${m.speed > 0 ? Math.round(m.speed)+' km/h' : 'Stationary'}</span></div>
            ${m.alt > 0 ? `<div class="ap-row"><span class="ap-lbl">Altitude</span><span>${Math.round(m.alt)}m</span></div>` : ''}
            ${m.comment ? `<div style="margin-top:5px;font-size:11px;color:#666;">${m.comment}</div>` : ''}
        </div>`;
    const marker = L.marker([m.lat, m.lng], { icon: makeIcon(m.call, m.fresh) })
        .addTo(map)
        .bindPopup(popup);
    markerMap[m.call] = marker;
    bounds.push([m.lat, m.lng]);
});

if (bounds.length > 0) {
    map.fitBounds(bounds, { padding: [40, 40], maxZoom: 13 });
}

/* SSID extras that aren't primary markers */
EXTRAS.forEach(e => {
    if (markerMap[e.call]) return;
    const popup = `<div class="aprs-popup"><strong>${e.call}</strong>
        <div class="ap-row"><span class="ap-lbl">Speed</span><span>${e.speed > 0 ? Math.round(e.speed)+' km/h' : 'Stationary'}</span></div>
        ${e.comment ? `<div style="margin-top:4px;font-size:11px;color:#666;">${e.comment}</div>` : ''}
    </div>`;
    const marker = L.marker([e.lat, e.lng], { icon: makeIcon(e.call, 'grey') })
        .addTo(map).bindPopup(popup);
    markerMap[e.call] = marker;
});

/* Fly to a callsign */
function flyTo(lat, lng, call) {
    map.flyTo([lat, lng], 14, { duration: 1.2 });
    if (markerMap[call]) markerMap[call].openPopup();
}

/* Table search */
function filterTable(q) {
    const term = q.toLowerCase().trim();
    document.querySelectorAll('#aprsTable tbody tr').forEach(tr => {
        const hay = tr.dataset.search || '';
        tr.style.display = (!term || hay.includes(term)) ? '' : 'none';
    });
}

/* Refresh (JSON endpoint) */
function doRefresh() {
    const btn = document.getElementById('btnRefresh');
    const sta = document.getElementById('refreshStatus');
    btn.disabled = true;
    btn.textContent = '↻ Refreshing…';
    sta.textContent = 'Refreshing…';

    fetch('{{ route("admin.aprs.refresh") }}', {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        sta.textContent = `Refreshed ${data.refreshed} · ${data.count} located`;
        btn.textContent = '↻ Refresh';
        btn.disabled = false;
        // Full reload to re-render map + table cleanly
        window.location.reload();
    })
    .catch(() => {
        sta.textContent = 'Refresh failed';
        btn.textContent = '↻ Refresh';
        btn.disabled = false;
    });
}
</script>
@endpush
