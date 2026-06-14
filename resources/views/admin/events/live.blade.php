<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Live Ops — {{ $event->title }}</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<style>
:root {
    --navy:#003366;--red:#C8102E;--green:#1a6b3c;--amber:#8a5c00;
    --green-bg:#eef7f2;--green-bdr:#b8ddc9;--amber-bg:#fef9ec;--amber-bdr:#e8c96a;
    --red-bg:#fdf0f2;--red-bdr:#f5b8c1;--grey:#f2f5f9;--border:#dde2e8;
    --text:#001f40;--muted:#6b7f96;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html,body{height:100%;font-family:Arial,sans-serif;background:var(--grey);color:var(--text);font-size:14px;}
.top-bar{background:var(--navy);border-bottom:3px solid var(--red);padding:.6rem 1rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;position:sticky;top:0;z-index:300;box-shadow:0 2px 10px rgba(0,0,0,.3);}
.top-bar-title{color:#fff;font-weight:bold;font-size:15px;}
.top-bar-meta{color:rgba(255,255,255,.6);font-size:11px;}
.top-bar-right{display:flex;align-items:center;gap:.75rem;}
.live-badge{background:var(--red);color:#fff;font-size:10px;font-weight:bold;padding:3px 8px;border-radius:999px;animation:pulse 2s infinite;}
@keyframes pulse{0%,100%{opacity:1;}50%{opacity:.6;}}
.layout{display:grid;grid-template-columns:320px 1fr;height:calc(100vh - 52px);}
@media(max-width:900px){.layout{grid-template-columns:1fr;}}

/* Sidebar */
.sidebar{background:#fff;border-right:1px solid var(--border);display:flex;flex-direction:column;overflow:hidden;}
.sidebar-tabs{display:flex;border-bottom:1px solid var(--border);}
.sidebar-tab{flex:1;padding:.55rem .5rem;font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:.06em;text-align:center;cursor:pointer;border-bottom:2px solid transparent;color:var(--muted);}
.sidebar-tab.active{color:var(--navy);border-bottom-color:var(--navy);}
.sidebar-pane{display:none;flex:1;overflow-y:auto;padding:.75rem;}
.sidebar-pane.active{display:block;}

/* Operator cards */
.op-card{background:var(--grey);border:1px solid var(--border);border-radius:8px;padding:.65rem .75rem;margin-bottom:.5rem;cursor:pointer;transition:border-color .15s;}
.op-card:hover{border-color:var(--navy);}
.op-card.selected{border-color:var(--navy);background:#e8eef5;}
.op-card.sos{border-color:var(--red);background:var(--red-bg);animation:sosflash 1s infinite;}
@keyframes sosflash{0%,100%{background:var(--red-bg);}50%{background:#fecdd3;}}
.op-card.geofence{border-color:#f59e0b;background:var(--amber-bg);}
.op-card.welfare-overdue{border-color:#f59e0b;background:var(--amber-bg);}
.op-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:.3rem;}
.op-callsign{font-weight:bold;font-size:14px;color:var(--navy);}
.op-status-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;}
.op-meta{font-size:11px;color:var(--muted);display:flex;flex-wrap:wrap;gap:.3rem .75rem;}
.op-battery{font-size:10px;font-weight:bold;}
.op-lastseen{font-size:10px;color:var(--muted);}
.badge{font-size:9px;font-weight:bold;padding:1px 5px;border-radius:3px;text-transform:uppercase;}
.badge-red{background:var(--red-bg);color:var(--red);border:1px solid var(--red-bdr);}
.badge-amber{background:var(--amber-bg);color:var(--amber);border:1px solid var(--amber-bdr);}
.badge-green{background:var(--green-bg);color:var(--green);border:1px solid var(--green-bdr);}

/* Message panel */
.msg-form{background:#f8f9fb;border:1px solid var(--border);border-radius:8px;padding:.75rem;margin-bottom:.75rem;}
.msg-form select,.msg-form textarea,.msg-form input{width:100%;border:1px solid var(--border);border-radius:5px;padding:.4rem .5rem;font-size:12px;margin-bottom:.4rem;font-family:Arial,sans-serif;}
.msg-form textarea{height:60px;resize:vertical;}
.btn{padding:.4rem .85rem;border:none;border-radius:5px;font-size:12px;font-weight:bold;cursor:pointer;}
.btn-navy{background:var(--navy);color:#fff;}
.btn-red{background:var(--red);color:#fff;}
.btn-amber{background:#f59e0b;color:#fff;}
.btn-sm{padding:.25rem .6rem;font-size:11px;}
.msg-log{max-height:200px;overflow-y:auto;}
.msg-item{padding:.4rem .5rem;border-bottom:1px solid var(--border);font-size:11px;}
.msg-item:last-child{border-bottom:none;}
.msg-item.urgent{background:var(--red-bg);}
.msg-item.warning{background:var(--amber-bg);}

/* Welfare */
.welfare-form{background:#f8f9fb;border:1px solid var(--border);border-radius:8px;padding:.75rem;margin-bottom:.75rem;}
.welfare-status-list{font-size:12px;}
.welfare-row{display:flex;align-items:center;justify-content:space-between;padding:.3rem 0;border-bottom:1px solid var(--border);}
.welfare-row:last-child{border-bottom:none;}

/* SOS alerts */
.sos-alert{background:var(--red-bg);border:2px solid var(--red);border-radius:8px;padding:.75rem;margin-bottom:.5rem;}
.sos-alert-header{font-weight:bold;color:var(--red);font-size:13px;margin-bottom:.3rem;}
.sos-alert-meta{font-size:11px;color:var(--muted);margin-bottom:.5rem;}

/* Map */
#live-map{height:100%;width:100%;}
.map-wrap{position:relative;height:100%;}
.map-overlay{position:absolute;top:10px;right:10px;z-index:1000;display:flex;flex-direction:column;gap:.4rem;}
.map-btn{background:#fff;border:1px solid var(--border);border-radius:6px;padding:.4rem .7rem;font-size:11px;font-weight:bold;cursor:pointer;box-shadow:0 1px 4px rgba(0,0,0,.15);}
.map-btn.active{background:var(--navy);color:#fff;}
.replay-bar{position:absolute;bottom:0;left:0;right:0;z-index:1000;background:rgba(255,255,255,.95);border-top:1px solid var(--border);padding:.6rem 1rem;display:none;}
.replay-bar.show{display:flex;align-items:center;gap:.75rem;}
.replay-bar input[type=range]{flex:1;}
.replay-time{font-size:11px;font-weight:bold;color:var(--navy);min-width:80px;text-align:center;}
.stat-bar{position:absolute;top:10px;left:10px;z-index:1000;background:rgba(255,255,255,.92);border:1px solid var(--border);border-radius:8px;padding:.5rem .75rem;font-size:11px;min-width:160px;box-shadow:0 2px 8px rgba(0,0,0,.12);}
.stat-row{display:flex;justify-content:space-between;gap:1rem;padding:1px 0;}
.stat-val{font-weight:bold;color:var(--navy);}
</style>
</head>
<body>
<div class="top-bar">
    <div>
        <div class="top-bar-title">⚡ Live Ops — {{ $event->title }}</div>
        <div class="top-bar-meta">{{ $event->starts_at?->format('D j M Y') }} · <span id="server-time">--:--:--</span></div>
    </div>
    <div class="top-bar-right">
        <span class="live-badge">● LIVE</span>
        <span id="op-count" style="color:rgba(255,255,255,.7);font-size:11px;"></span>
        <a href="/admin/events/{{ $event->id }}/assignments" style="color:rgba(255,255,255,.7);font-size:11px;text-decoration:none;">← Assignments</a>
    </div>
</div>

<div class="layout">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-tabs">
            <div class="sidebar-tab active" onclick="showTab('operators')">👥 Operators</div>
            <div class="sidebar-tab" onclick="showTab('messages')">📨 Messages</div>
            <div class="sidebar-tab" onclick="showTab('welfare')">✅ Welfare</div>
            <div class="sidebar-tab" onclick="showTab('sos')">🆘 SOS</div>
        </div>

        <!-- Operators tab -->
        <div class="sidebar-pane active" id="tab-operators">
            <div id="op-list"></div>
        </div>

        <!-- Messages tab -->
        <div class="sidebar-pane" id="tab-messages">
            <div class="msg-form">
                <div style="font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:.4rem;">Send Message</div>
                <select id="msg-type">
                    <option value="info">ℹ Info</option>
                    <option value="warning">⚠ Warning</option>
                    <option value="urgent">🚨 Urgent</option>
                    <option value="frequency_change">📻 Frequency Change</option>
                </select>
                <select id="msg-target">
                    <option value="">📢 Broadcast to all</option>
                    @foreach($assignments as $a)
                    <option value="{{ $a->id }}">{{ $a->callsign ?: ($a->user->callsign ?? $a->user->name) }}</option>
                    @endforeach
                </select>
                <textarea id="msg-body" placeholder="Message text…"></textarea>
                <div id="freq-fields" style="display:none;">
                    <input type="text" id="msg-freq" placeholder="New frequency e.g. 145.500">
                    <input type="text" id="msg-mode" placeholder="Mode e.g. FM">
                    <input type="text" id="msg-ctcss" placeholder="CTCSS tone (optional)">
                </div>
                <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.4rem;">
                    <input type="checkbox" id="msg-ack" style="width:auto;">
                    <label for="msg-ack" style="font-size:11px;">Require acknowledgement</label>
                </div>
                <button class="btn btn-navy" onclick="sendMessage()" style="width:100%;">Send Message</button>
            </div>
            <div style="font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:.4rem;">Recent Messages</div>
            <div class="msg-log" id="msg-log"></div>
        </div>

        <!-- Welfare tab -->
        <div class="sidebar-pane" id="tab-welfare">
            <div class="welfare-form">
                <div style="font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:.4rem;">Welfare Check Interval</div>
                <div style="display:flex;gap:.5rem;align-items:center;margin-bottom:.5rem;">
                    <select id="welfare-interval" style="flex:1;border:1px solid var(--border);border-radius:5px;padding:.35rem .5rem;font-size:12px;">
                        <option value="10">Every 10 min</option>
                        <option value="15">Every 15 min</option>
                        <option value="20">Every 20 min</option>
                        <option value="30" selected>Every 30 min</option>
                        <option value="60">Every 60 min</option>
                    </select>
                    <button class="btn btn-navy btn-sm" onclick="sendWelfareCheck()">Send Now</button>
                </div>
                <div style="font-size:10px;color:var(--muted);">Operators will receive a "Tap to confirm OK" prompt on their brief page.</div>
            </div>
            <div class="welfare-status-list" id="welfare-list"></div>
        </div>

        <!-- SOS tab -->
        <div class="sidebar-pane" id="tab-sos">
            <div id="sos-list">
                <div style="text-align:center;color:var(--muted);font-size:12px;padding:2rem 0;">No active SOS alerts</div>
            </div>
        </div>
    </div>

    <!-- Map -->
    <div class="map-wrap">
        <div id="live-map"></div>
        <div class="stat-bar" id="stat-bar">
            <div class="stat-row"><span>Checked in</span><span class="stat-val" id="stat-checked-in">0</span></div>
            <div class="stat-row"><span>On break</span><span class="stat-val" id="stat-on-break">0</span></div>
            <div class="stat-row"><span>GPS active</span><span class="stat-val" id="stat-gps">0</span></div>
            <div class="stat-row"><span>Alerts</span><span class="stat-val" id="stat-alerts" style="color:var(--red);">0</span></div>
        </div>
        <div class="map-overlay">
            <button class="map-btn active" id="btn-trails" onclick="toggleTrails()">🔵 Trails</button>
            <button class="map-btn" id="btn-heatmap" onclick="toggleHeatmap()">🔥 Heatmap</button>
            <button class="map-btn" id="btn-replay" onclick="toggleReplay()">⏮ Replay</button>
            <button class="map-btn" id="btn-geofence" onclick="toggleGeofences()">⭕ Zones</button>
        </div>
        <div class="replay-bar" id="replay-bar">
            <button class="btn btn-navy btn-sm" id="replay-play" onclick="replayPlayPause()">▶</button>
            <input type="range" id="replay-scrubber" min="0" max="100" value="0" oninput="replayScrub(this.value)">
            <div class="replay-time" id="replay-time">--:--</div>
            <button class="btn btn-sm" style="background:var(--border);" onclick="toggleReplay()">✕ Close</button>
        </div>
    </div>
</div>

<script>
const EVENT_ID  = {{ $event->id }};
const CSRF      = document.querySelector('meta[name="csrf-token"]').content;
const ROUTE_DATA = {!! json_encode($event->event_route) !!};
const POLY_DATA  = {!! json_encode($event->event_polygon) !!};

// ── Map setup ────────────────────────────────────────────────────────────────
const map = L.map('live-map', { zoomControl: true, scrollWheelZoom: true });
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution:'© OpenStreetMap', maxZoom:19
}).addTo(map);

// Draw route
if (ROUTE_DATA) {
    const segs = Array.isArray(ROUTE_DATA) ? ROUTE_DATA : [ROUTE_DATA];
    segs.forEach(function(seg) {
        const geom = seg.geometry || seg;
        if (!geom || !geom.coordinates) return;
        L.geoJSON({type:'Feature',geometry:geom},{
            style:{color:'#7c3aed',weight:3,opacity:.5,dashArray:'6 3'}
        }).addTo(map);
    });
}

// Draw polygon
if (POLY_DATA && POLY_DATA.coordinates) {
    L.geoJSON({type:'Feature',geometry:POLY_DATA},{
        style:{color:'#003366',weight:2,opacity:.7,fillOpacity:.05,dashArray:'6 3'}
    }).addTo(map);
}

// Fit map to route/polygon or Liverpool fallback
try {
    if (ROUTE_DATA) {
        const segs = Array.isArray(ROUTE_DATA) ? ROUTE_DATA : [ROUTE_DATA];
        const coords = [];
        segs.forEach(s => { const g = s.geometry||s; if(g&&g.coordinates) g.coordinates.forEach(c=>coords.push([c[1],c[0]])); });
        if (coords.length) map.fitBounds(L.latLngBounds(coords),{padding:[30,30]});
        else map.setView([53.4084,-2.9916],13);
    } else {
        map.setView([53.4084,-2.9916],13);
    }
} catch(e) { map.setView([53.4084,-2.9916],13); }

// ── State ────────────────────────────────────────────────────────────────────
let operators = [];
let opMarkers = {};
let trailLayers = {};
let geofenceCircles = {};
let showTrails = true;
let showGeofences = false;
let heatmapMode = false;
let selectedOpId = null;
let replayData = null;
let replayPlaying = false;
let replayInterval = null;
let replayIdx = 0;
let replayMarkers = {};
const OP_COLOURS = ['#C8102E','#f59e0b','#059669','#7c3aed','#0ea5e9','#db2777','#ea580c','#003366'];
let opColourMap = {};
let colourIdx = 0;

function getOpColour(id) {
    if (!opColourMap[id]) opColourMap[id] = OP_COLOURS[colourIdx++ % OP_COLOURS.length];
    return opColourMap[id];
}

// ── Main poll loop ───────────────────────────────────────────────────────────
function poll() {
    fetch(`/admin/events/${EVENT_ID}/live/state`, {
        headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'}
    })
    .then(r => r.json())
    .then(data => {
        operators = data.operators || [];
        renderOperators();
        renderMap();
        renderSos(data.sos_alerts || []);
        updateStats();
        if (data.server_time) {
            document.getElementById('server-time').textContent =
                new Date(data.server_time).toLocaleTimeString('en-GB');
        }
    })
    .catch(e => console.warn('Poll error:', e));
}

poll();
setInterval(poll, 5000);

// ── Render operator sidebar list ─────────────────────────────────────────────
function renderOperators() {
    const list = document.getElementById('op-list');
    const welfare = document.getElementById('welfare-list');
    let html = '';
    let welfareHtml = '';
    let count = 0;

    operators.forEach(function(op) {
        const colour = getOpColour(op.id);
        const lastSeenStr = op.last_seen_s === null ? 'No GPS' :
            op.last_seen_s < 60 ? `${op.last_seen_s}s ago` :
            op.last_seen_s < 3600 ? `${Math.floor(op.last_seen_s/60)}m ago` : 'Stale';
        const hasFix = op.lat !== null && op.lng !== null;
        const isSos = false; // Handled separately
        const cls = op.geofence_breach ? 'geofence' : op.welfare_status==='overdue' ? 'welfare-overdue' : '';
        const statusDot = op.status==='checked_in' ? '#1a6b3c' : op.status==='on_break' ? '#8a5c00' : '#9aa3ae';
        const battStr = op.battery !== null ? `🔋${op.battery}%` : '';
        const battColour = op.battery !== null ? (op.battery < 20 ? 'color:var(--red)' : op.battery < 40 ? 'color:var(--amber)' : '') : '';
        const drBadge = op.is_dead_reckoned ? '<span class="badge badge-amber">DR</span>' : '';
        const gfBadge = op.geofence_breach ? '<span class="badge badge-red">GEOFENCE</span>' : '';
        const wfBadge = op.welfare_status==='overdue' ? '<span class="badge badge-amber">WELFARE</span>' : '';

        if (op.status === 'checked_in' || op.status === 'on_break') count++;

        html += `<div class="op-card ${cls} ${selectedOpId===op.id?'selected':''}" onclick="selectOp(${op.id})" id="opcard-${op.id}">
            <div class="op-header">
                <span class="op-callsign" style="color:${colour};">${op.callsign}</span>
                <div style="display:flex;align-items:center;gap:.3rem;">
                    ${drBadge}${gfBadge}${wfBadge}
                    <div class="op-status-dot" style="background:${statusDot};"></div>
                </div>
            </div>
            <div class="op-meta">
                <span>${op.location_name || op.role || '—'}</span>
                <span class="op-lastseen">${lastSeenStr}</span>
                ${battStr ? `<span class="op-battery" style="${battColour}">${battStr}</span>` : ''}
                ${op.heading !== null ? `<span>↗ ${op.heading}°</span>` : ''}
            </div>
        </div>`;

        const wfColour = op.welfare_status==='overdue' ? 'var(--red)' : op.welfare_status==='pending' ? 'var(--amber)' : 'var(--green)';
        const wfLabel = op.welfare_status==='overdue' ? '⚠ Overdue' : op.welfare_status==='pending' ? '⏳ Pending' : '✓ OK';
        welfareHtml += `<div class="welfare-row">
            <span style="font-weight:bold;color:${colour};">${op.callsign}</span>
            <span style="color:${wfColour};font-weight:bold;">${wfLabel}</span>
        </div>`;
    });

    list.innerHTML = html || '<div style="text-align:center;color:var(--muted);padding:2rem 0;font-size:12px;">No operators checked in</div>';
    welfare.innerHTML = welfareHtml || '<div style="text-align:center;color:var(--muted);padding:1rem 0;font-size:12px;">No operators checked in</div>';
    document.getElementById('op-count').textContent = `${count} active`;
}

// ── Render map markers & trails ──────────────────────────────────────────────
function renderMap() {
    if (heatmapMode || replayData) return;

    const activeIds = new Set(operators.map(o => o.id));

    // Remove stale markers
    Object.keys(opMarkers).forEach(function(id) {
        if (!activeIds.has(parseInt(id))) {
            map.removeLayer(opMarkers[id]); delete opMarkers[id];
            if (trailLayers[id]) { map.removeLayer(trailLayers[id]); delete trailLayers[id]; }
            if (geofenceCircles[id]) { map.removeLayer(geofenceCircles[id]); delete geofenceCircles[id]; }
        }
    });

    operators.forEach(function(op) {
        if (!op.lat || !op.lng) return;
        const colour = getOpColour(op.id);
        const isDr = op.is_dead_reckoned;

        // Heading arrow or dot
        const iconHtml = op.heading !== null
            ? `<div style="position:relative;width:24px;height:24px;">
                <div style="position:absolute;inset:0;background:${colour};border:2px solid #fff;border-radius:50%;box-shadow:0 1px 5px rgba(0,0,0,.4);opacity:${isDr?.6:1};"></div>
                <div style="position:absolute;top:-6px;left:50%;transform:translateX(-50%);width:0;height:0;border-left:5px solid transparent;border-right:5px solid transparent;border-bottom:10px solid ${colour};transform-origin:bottom center;transform:translateX(-50%) rotate(${op.heading}deg);"></div>
              </div>`
            : `<div style="width:20px;height:20px;background:${colour};border:2px solid #fff;border-radius:50%;box-shadow:0 1px 5px rgba(0,0,0,.4);opacity:${isDr?.6:1};display:flex;align-items:center;justify-content:center;">
                ${isDr ? '<span style="font-size:8px;color:#fff;font-weight:bold;">DR</span>' : ''}
               </div>`;

        const icon = L.divIcon({
            className: '',
            html: `<div style="display:flex;flex-direction:column;align-items:center;gap:1px;">
                ${iconHtml}
                <div style="background:${colour};color:#fff;font-size:9px;font-weight:bold;padding:1px 5px;border-radius:3px;white-space:nowrap;box-shadow:0 1px 3px rgba(0,0,0,.3);">${op.callsign}</div>
            </div>`,
            iconSize: [60,36], iconAnchor:[30,18],
        });

        const lastSeenStr = op.last_seen_s !== null
            ? (op.last_seen_s < 60 ? `${op.last_seen_s}s ago` : `${Math.floor(op.last_seen_s/60)}m ago`)
            : 'No GPS';

        const popupHtml = `<div style="font-family:Arial,sans-serif;min-width:160px;">
            <strong style="color:${colour};font-size:13px;">${op.callsign}</strong>
            <div style="font-size:11px;color:#6b7f96;margin:2px 0;">${op.name}</div>
            <div style="font-size:11px;margin:3px 0;">${op.location_name || op.role || ''}</div>
            <div style="font-size:10px;color:#9aa3ae;">Last ping: ${lastSeenStr}</div>
            ${op.battery !== null ? `<div style="font-size:10px;">🔋 ${op.battery}%</div>` : ''}
            ${op.geofence_breach ? '<div style="color:#C8102E;font-size:10px;font-weight:bold;">⚠ Outside coverage zone</div>' : ''}
            ${op.is_dead_reckoned ? '<div style="color:#8a5c00;font-size:10px;">📍 Dead reckoned position</div>' : ''}
        </div>`;

        if (opMarkers[op.id]) {
            opMarkers[op.id].setLatLng([op.lat, op.lng]);
            opMarkers[op.id].setIcon(icon);
        } else {
            opMarkers[op.id] = L.marker([op.lat, op.lng], {icon})
                .bindPopup(popupHtml).addTo(map);
        }

        // Trail
        if (showTrails && op.trail && op.trail.length > 1) {
            const trailCoords = op.trail.map(p => [p.lat, p.lng]);
            if (trailLayers[op.id]) map.removeLayer(trailLayers[op.id]);
            trailLayers[op.id] = L.polyline(trailCoords, {
                color: colour, weight: 2, opacity: .5, dashArray: '4 3'
            }).addTo(map);
        }

        // Geofence circle
        if (showGeofences && op.assigned_lat && op.assigned_lng && op.coverage_radius > 0) {
            if (geofenceCircles[op.id]) map.removeLayer(geofenceCircles[op.id]);
            geofenceCircles[op.id] = L.circle([op.assigned_lat, op.assigned_lng], {
                radius: op.coverage_radius,
                color: op.geofence_breach ? '#C8102E' : colour,
                weight: 1.5, opacity: .6, fillOpacity: .05,
                dashArray: '6 3',
            }).addTo(map);
        }
    });
}

// ── Render SOS ────────────────────────────────────────────────────────────────
function renderSos(alerts) {
    const list = document.getElementById('sos-list');
    const tab = document.querySelector('.sidebar-tab:nth-child(4)');
    if (alerts.length === 0) {
        list.innerHTML = '<div style="text-align:center;color:var(--muted);font-size:12px;padding:2rem 0;">No active SOS alerts</div>';
        tab.textContent = '🆘 SOS';
        return;
    }
    tab.textContent = `🆘 SOS (${alerts.length})`;
    tab.style.color = 'var(--red)';
    list.innerHTML = alerts.map(s => `
        <div class="sos-alert">
            <div class="sos-alert-header">🆘 ${s.callsign}</div>
            <div class="sos-alert-meta">${s.message || 'No message'} · ${new Date(s.at).toLocaleTimeString('en-GB')}</div>
            ${s.lat ? `<div style="font-size:11px;margin-bottom:.4rem;"><a href="https://maps.google.com/?q=${s.lat},${s.lng}" target="_blank" style="color:var(--red);">📍 View on Google Maps</a></div>` : ''}
            <button class="btn btn-sm" style="background:var(--green);color:#fff;" onclick="resolveSos(${s.id})">✓ Resolve</button>
        </div>
    `).join('');

    document.getElementById('stat-alerts').textContent = alerts.length;
}

// ── Stats bar ─────────────────────────────────────────────────────────────────
function updateStats() {
    const checkedIn = operators.filter(o => o.status === 'checked_in').length;
    const onBreak   = operators.filter(o => o.status === 'on_break').length;
    const hasGps    = operators.filter(o => o.lat !== null).length;
    const alerts    = operators.filter(o => o.geofence_breach || o.welfare_status === 'overdue').length;
    document.getElementById('stat-checked-in').textContent = checkedIn;
    document.getElementById('stat-on-break').textContent = onBreak;
    document.getElementById('stat-gps').textContent = hasGps;
    if (!document.getElementById('sos-list').innerHTML.includes('sos-alert')) {
        document.getElementById('stat-alerts').textContent = alerts;
        document.getElementById('stat-alerts').style.color = alerts > 0 ? 'var(--red)' : 'var(--text)';
    }
}

// ── Actions ───────────────────────────────────────────────────────────────────
function selectOp(id) {
    selectedOpId = id;
    const op = operators.find(o => o.id === id);
    if (op && op.lat && op.lng) map.setView([op.lat, op.lng], 16);
    renderOperators();
}

function showTab(name) {
    document.querySelectorAll('.sidebar-tab').forEach((t,i) => {
        const names = ['operators','messages','welfare','sos'];
        t.classList.toggle('active', names[i] === name);
    });
    document.querySelectorAll('.sidebar-pane').forEach(p => p.classList.remove('active'));
    document.getElementById('tab-'+name).classList.add('active');
}

function sendMessage() {
    const type   = document.getElementById('msg-type').value;
    const body   = document.getElementById('msg-body').value.trim();
    const target = document.getElementById('msg-target').value;
    const ack    = document.getElementById('msg-ack').checked;
    if (!body) return alert('Please enter a message.');

    const payload = {};
    if (type === 'frequency_change') {
        payload.frequency = document.getElementById('msg-freq').value;
        payload.mode      = document.getElementById('msg-mode').value;
        payload.ctcss     = document.getElementById('msg-ctcss').value;
    }

    fetch(`/admin/events/${EVENT_ID}/live/message`, {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
        body: JSON.stringify({type, body, assignment_id: target||null, requires_ack:ack, payload})
    })
    .then(r => r.json())
    .then(d => {
        if (d.ok) {
            document.getElementById('msg-body').value = '';
            addMessageToLog({type, body, target: target ? document.getElementById('msg-target').options[document.getElementById('msg-target').selectedIndex].text : 'All', at: new Date().toLocaleTimeString('en-GB')});
        }
    });
}

function addMessageToLog(msg) {
    const log = document.getElementById('msg-log');
    const item = document.createElement('div');
    item.className = `msg-item ${msg.type}`;
    item.innerHTML = `<strong>${msg.target}</strong> · ${msg.type} · ${msg.at}<br><span style="color:var(--text);">${msg.body}</span>`;
    log.prepend(item);
}

document.getElementById('msg-type').addEventListener('change', function() {
    document.getElementById('freq-fields').style.display = this.value === 'frequency_change' ? 'block' : 'none';
});

function resolveSos(id) {
    fetch(`/admin/events/${EVENT_ID}/live/sos/${id}/resolve`, {
        method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}
    }).then(() => poll());
}

function sendWelfareCheck() {
    const interval = document.getElementById('welfare-interval').value;
    fetch(`/admin/events/${EVENT_ID}/live/welfare`, {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
        body: JSON.stringify({interval_minutes: parseInt(interval), active: true})
    }).then(r => r.json()).then(d => {
        if (d.ok) alert(`Welfare check sent to all active operators.`);
    });
}

function toggleTrails() {
    showTrails = !showTrails;
    document.getElementById('btn-trails').classList.toggle('active', showTrails);
    if (!showTrails) {
        Object.values(trailLayers).forEach(l => map.removeLayer(l));
        trailLayers = {};
    }
    renderMap();
}

function toggleGeofences() {
    showGeofences = !showGeofences;
    document.getElementById('btn-geofence').classList.toggle('active', showGeofences);
    if (!showGeofences) {
        Object.values(geofenceCircles).forEach(l => map.removeLayer(l));
        geofenceCircles = {};
    }
    renderMap();
}

function toggleHeatmap() {
    heatmapMode = !heatmapMode;
    document.getElementById('btn-heatmap').classList.toggle('active', heatmapMode);
    if (heatmapMode) {
        fetch(`/admin/events/${EVENT_ID}/live/replay`, {
            headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}
        })
        .then(r => r.json())
        .then(data => renderHeatmap(data.pings));
    } else {
        map.eachLayer(l => { if (l._heatmap) map.removeLayer(l); });
        renderMap();
    }
}

function renderHeatmap(pings) {
    // Simple canvas heatmap overlay
    map.eachLayer(l => { if (l._heatmap) map.removeLayer(l); });
    if (!pings || !pings.length) return;

    const counts = {};
    pings.forEach(p => {
        const key = `${parseFloat(p.lat).toFixed(4)},${parseFloat(p.lng).toFixed(4)}`;
        counts[key] = (counts[key] || 0) + 1;
    });
    const maxCount = Math.max(...Object.values(counts));

    Object.entries(counts).forEach(([key, count]) => {
        const [lat, lng] = key.split(',').map(Number);
        const intensity = count / maxCount;
        const radius = 15 + intensity * 25;
        const circle = L.circleMarker([lat, lng], {
            radius, color:'transparent',
            fillColor: `hsl(${Math.round((1-intensity)*240)},80%,50%)`,
            fillOpacity: 0.3 + intensity * 0.4,
            interactive: false,
        });
        circle._heatmap = true;
        circle.addTo(map);
    });
}

// ── Replay ────────────────────────────────────────────────────────────────────
function toggleReplay() {
    const bar = document.getElementById('replay-bar');
    const isShowing = bar.classList.contains('show');
    if (isShowing) {
        bar.classList.remove('show');
        stopReplay();
        Object.values(replayMarkers).forEach(m => map.removeLayer(m));
        replayMarkers = {};
        replayData = null;
        renderMap();
    } else {
        bar.classList.add('show');
        fetch(`/admin/events/${EVENT_ID}/live/replay`, {
            headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}
        })
        .then(r => r.json())
        .then(data => {
            replayData = data.pings;
            if (!replayData || !replayData.length) {
                alert('No replay data available yet.');
                bar.classList.remove('show');
                return;
            }
            document.getElementById('replay-scrubber').max = replayData.length - 1;
            replayIdx = 0;
            renderReplayFrame(0);
        });
    }
}

function replayPlayPause() {
    replayPlaying = !replayPlaying;
    document.getElementById('replay-play').textContent = replayPlaying ? '⏸' : '▶';
    if (replayPlaying) {
        replayInterval = setInterval(() => {
            if (replayIdx >= replayData.length - 1) { stopReplay(); return; }
            replayIdx++;
            document.getElementById('replay-scrubber').value = replayIdx;
            renderReplayFrame(replayIdx);
        }, 200);
    } else {
        stopReplay(true);
    }
}

function stopReplay(keepIdx) {
    replayPlaying = false;
    clearInterval(replayInterval);
    document.getElementById('replay-play').textContent = '▶';
    if (!keepIdx) replayIdx = 0;
}

function replayScrub(val) {
    replayIdx = parseInt(val);
    renderReplayFrame(replayIdx);
}

function renderReplayFrame(idx) {
    if (!replayData) return;
    const ping = replayData[idx];
    const colour = getOpColour(ping.assignment_id);

    // Clear old markers
    Object.values(replayMarkers).forEach(m => map.removeLayer(m));
    replayMarkers = {};

    // Show positions up to this frame grouped by operator
    const latest = {};
    for (let i = 0; i <= idx; i++) {
        latest[replayData[i].assignment_id] = replayData[i];
    }

    Object.values(latest).forEach(p => {
        const c = getOpColour(p.assignment_id);
        const icon = L.divIcon({
            className:'',
            html:`<div style="background:${c};color:#fff;font-size:9px;font-weight:bold;padding:2px 5px;border-radius:3px;border:1.5px solid #fff;box-shadow:0 1px 3px rgba(0,0,0,.3);">${p.callsign}</div>`,
            iconAnchor:[20,10],
        });
        replayMarkers[p.assignment_id] = L.marker([p.lat,p.lng],{icon,interactive:false}).addTo(map);
    });

    const time = new Date(ping.at).toLocaleTimeString('en-GB',{hour:'2-digit',minute:'2-digit'});
    document.getElementById('replay-time').textContent = time;
}
</script>
</body>
</html>
