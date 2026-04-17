@extends('layouts.app')

@section('title', 'DMR Network')

@section('content')

<style>
:root {
    --navy:       #003366;
    --navy-deep:  #001f44;
    --navy-mid:   #004080;
    --navy-faint: #e8eef5;
    --red:        #C8102E;
    --red-faint:  #fdf0f2;
    --white:      #FFFFFF;
    --grey:       #f0f4f8;
    --grey-mid:   #dde2e8;
    --grey-dark:  #9aa3ae;
    --text:       #001f40;
    --text-mid:   #2d4a6b;
    --text-muted: #6b7f96;
    --green:      #1a6b3c;
    --green-bright: #22c55e;
    --green-bg:   #eef7f2;
    --amber:      #8a5500;
    --amber-bg:   #fdf8ec;
    --font: Arial, 'Helvetica Neue', Helvetica, sans-serif;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
.dmr-shell { background: var(--grey); min-height: 100vh; font-family: var(--font); }

/* ── Command strip ── */
.dmr-command-strip { background: var(--navy-deep); border-bottom: 3px solid var(--red); padding: 0 24px; }
.dmr-command-inner { max-width: 1280px; margin: 0 auto; height: 64px; display: flex; align-items: center; justify-content: space-between; gap: 20px; }
.dmr-brand { display: flex; align-items: center; gap: 12px; }
.dmr-brand-badge { background: var(--red); color: white; font-size: 10px; font-weight: 700; padding: 4px 8px; letter-spacing: 1px; text-transform: uppercase; line-height: 1.3; text-align: center; flex-shrink: 0; }
.dmr-brand-name { font-size: 15px; font-weight: 700; color: white; text-transform: uppercase; letter-spacing: .06em; }
.dmr-brand-sub { font-size: 10px; color: rgba(255,255,255,.45); letter-spacing: .08em; text-transform: uppercase; margin-top: 2px; }
.dmr-command-right { display: flex; align-items: center; gap: 16px; }
.dmr-status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border: 1px solid rgba(255,255,255,.12); background: rgba(255,255,255,.05); font-size: 11px; font-weight: 700; color: rgba(255,255,255,.7); letter-spacing: .5px; text-transform: uppercase; }
.dmr-status-dot { width: 7px; height: 7px; border-radius: 50%; background: #4ade80; box-shadow: 0 0 6px rgba(74,222,128,.6); animation: pd 2s ease-in-out infinite; }
@keyframes pd { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(.8)} }
.dmr-refresh-pill { background: none; border: 1px solid rgba(255,255,255,.18); color: rgba(255,255,255,.7); padding: 5px 14px; font-family: var(--font); font-size: 11px; font-weight: 700; cursor: pointer; letter-spacing: .5px; text-transform: uppercase; transition: all .15s; }
.dmr-refresh-pill:hover { border-color: white; color: white; }
.dmr-refresh-pill.spinning { opacity: .4; pointer-events: none; }

/* ── Access banners ── */
.dmr-access-banner { background: linear-gradient(90deg, var(--navy-deep) 0%, #002255 100%); border-bottom: 1px solid rgba(255,255,255,.06); padding: 10px 24px; }
.dmr-access-inner { max-width: 1280px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.dmr-access-text { font-size: 12px; color: rgba(255,255,255,.5); display: flex; align-items: center; gap: 8px; }
.dmr-access-text strong { color: rgba(255,255,255,.8); }
.btn-open-dashboard { display: inline-flex; align-items: center; gap: 6px; background: var(--red); color: white; padding: 6px 16px; font-family: var(--font); font-size: 11px; font-weight: 700; text-decoration: none; letter-spacing: .5px; text-transform: uppercase; transition: background .15s; white-space: nowrap; }
.btn-open-dashboard:hover { background: #a00d24; color: white; }
.dmr-limited-banner { background: rgba(0,51,102,.06); border-bottom: 1px solid var(--grey-mid); padding: 9px 24px; font-size: 12px; color: var(--text-muted); display: flex; align-items: center; gap: 8px; }

/* ── Stats strip ── */
.dmr-stats { background: var(--navy-deep); padding: 0 24px 16px; border-bottom: 1px solid rgba(255,255,255,.05); }
.dmr-stats-grid { max-width: 1280px; margin: 0 auto; display: grid; grid-template-columns: repeat(4,1fr); gap: 10px; padding-top: 16px; }
.dmr-stat-tile { background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.08); padding: 12px 16px; position: relative; overflow: hidden; }
.dmr-stat-tile::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: var(--red); opacity: .5; }
.dmr-stat-tile.t-green::before { background: var(--green-bright); opacity: .8; }
.dmr-stat-tile.t-blue::before  { background: #60a5fa; opacity: .8; }
.dmr-stat-tile.t-amber::before { background: #f59e0b; opacity: .8; }
.dmr-stat-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .14em; color: rgba(255,255,255,.4); margin-bottom: 6px; }
.dmr-stat-value { font-size: 26px; font-weight: 700; color: white; line-height: 1; font-family: monospace; }
.dmr-stat-value.loading { font-size: 16px; color: rgba(255,255,255,.25); }
.dmr-stat-sub { font-size: 10px; color: rgba(255,255,255,.3); margin-top: 4px; }

/* ── Body ── */
.dmr-body { max-width: 1280px; margin: 0 auto; padding: 24px 24px 60px; }

/* ── Section head ── */
.dmr-sec-head { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 2px solid var(--navy); }
.dmr-sec-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .14em; color: var(--navy); display: flex; align-items: center; gap: 8px; }
.dmr-sec-title::before { content: ''; width: 4px; height: 16px; background: var(--red); display: inline-block; }
.dmr-sec-badge { font-size: 10px; font-weight: 700; padding: 1px 8px; background: var(--navy-faint); border: 1px solid rgba(0,51,102,.2); color: var(--navy); }
.dmr-sec-updated { margin-left: auto; font-size: 10px; color: var(--text-muted); font-weight: 700; }

/* ── Masters table ── */
.masters-wrap { background: var(--white); border: 1px solid var(--grey-mid); box-shadow: 0 2px 12px rgba(0,40,100,.07); overflow-x: auto; margin-bottom: 28px; }
.masters-table { width: 100%; border-collapse: collapse; font-size: 13px; min-width: 760px; }
.masters-table thead th { background: var(--navy); padding: 10px 14px; text-align: left; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: rgba(255,255,255,.7); border-right: 1px solid rgba(255,255,255,.06); border-bottom: 2px solid var(--red); white-space: nowrap; }
.masters-table thead th:last-child { border-right: none; }

/* Master group row */
.tr-master { background: linear-gradient(90deg, #001d3d 0%, var(--navy) 70%); border-top: 2px solid rgba(200,16,46,.25); }
.tr-master:first-child { border-top: none; }
.tr-master td { padding: 9px 14px; }
.master-name-cell { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.master-pulse { width: 10px; height: 10px; border-radius: 50%; background: var(--grey-dark); flex-shrink: 0; }
.master-pulse.online { background: var(--green-bright); box-shadow: 0 0 0 3px rgba(34,197,94,.2); animation: pd 2.5s ease-in-out infinite; }
.master-name-text { font-size: 13px; font-weight: 700; color: white; letter-spacing: .04em; }
.master-sub { font-size: 10px; color: rgba(255,255,255,.35); font-style: italic; }
.master-last { font-size: 10px; color: rgba(255,255,255,.3); font-weight: 400; margin-left: auto; }
.master-offline { font-size: 10px; font-weight: 700; color: rgba(255,100,100,.7); padding: 1px 8px; border: 1px solid rgba(255,100,100,.3); text-transform: uppercase; letter-spacing: .05em; }

/* Peer rows */
.tr-peer { border-bottom: 1px solid var(--grey-mid); transition: background .1s; }
.tr-peer:hover td { background: var(--navy-faint); }
.tr-peer td { padding: 8px 14px; vertical-align: middle; border-right: 1px solid var(--grey-mid); }
.tr-peer td:last-child { border-right: none; }

/* No peers */
.tr-no-peers td { padding: 10px 14px 10px 28px; color: var(--text-muted); font-size: 12px; font-style: italic; background: #fafbfd; border-bottom: 2px solid var(--grey-mid); }

/* Callsign */
.cs-link { font-weight: 700; color: var(--navy); text-decoration: none; font-family: monospace; font-size: 13px; }
.cs-link:hover { color: var(--red); }
.cs-id { font-family: monospace; font-size: 10px; color: var(--text-muted); display: block; margin-top: 2px; }
.cs-loc { font-size: 11px; color: #0288d1; display: block; margin-top: 1px; }
.time-val { font-size: 12px; font-weight: 700; color: var(--text-mid); white-space: nowrap; font-family: monospace; }
.slot-wrap { display: flex; gap: 4px; }
.slot-b { font-size: 10px; font-weight: 700; padding: 2px 7px; border: 1px solid; font-family: monospace; }
.slot-ts1 { color: var(--red); background: rgba(200,16,46,.06); border-color: rgba(200,16,46,.25); }
.slot-ts2 { color: var(--navy); background: var(--navy-faint); border-color: rgba(0,51,102,.2); }
.traffic-cell { font-size: 12px; color: var(--text-mid); }
.active-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 9px; font-weight: 700; color: var(--green); background: var(--green-bg); border: 1px solid #b8ddc9; padding: 1px 6px; margin-right: 4px; text-transform: uppercase; letter-spacing: .06em; }
.active-dot { width: 5px; height: 5px; border-radius: 50%; background: var(--green-bright); animation: pd 1.5s ease-in-out infinite; }

/* ── Last heard ── */
.lh-wrap { background: var(--white); border: 1px solid var(--grey-mid); box-shadow: 0 2px 12px rgba(0,40,100,.07); overflow-x: auto; margin-bottom: 28px; }
.lh-table { width: 100%; border-collapse: collapse; font-size: 13px; min-width: 640px; }
.lh-table thead th { background: var(--navy); color: rgba(255,255,255,.7); padding: 10px 14px; text-align: left; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; border-right: 1px solid rgba(255,255,255,.06); border-bottom: 2px solid var(--red); white-space: nowrap; }
.lh-table thead th:last-child { border-right: none; }
.lh-table tbody td { padding: 8px 14px; border-bottom: 1px solid var(--grey-mid); border-right: 1px solid var(--grey-mid); vertical-align: middle; color: var(--text-mid); }
.lh-table tbody td:last-child { border-right: none; }
.lh-table tbody tr:last-child td { border-bottom: none; }
.lh-table tbody tr:hover td { background: var(--navy-faint); }
.lh-cs { font-weight: 700; color: var(--navy); font-family: monospace; }
.lh-cs a { color: var(--navy); text-decoration: none; }
.lh-cs a:hover { color: var(--red); }
.lh-tg { font-weight: 700; color: var(--red); font-family: monospace; }
.lh-t  { font-size: 11px; color: var(--text-muted); font-family: monospace; }
.lh-dur { font-size: 12px; font-weight: 700; color: var(--text-mid); font-family: monospace; }
.event-chip { display: inline-block; font-size: 9px; font-weight: 700; padding: 1px 6px; text-transform: uppercase; letter-spacing: .06em; border: 1px solid; }
.chip-end   { color: var(--text-muted); border-color: var(--grey-mid); background: var(--grey); }
.chip-start { color: var(--green); border-color: #b8ddc9; background: var(--green-bg); }

/* Skeleton */
.sk { display: inline-block; background: linear-gradient(90deg,var(--grey) 25%,var(--grey-mid) 50%,var(--grey) 75%); background-size: 200% 100%; animation: sk 1.4s infinite; border-radius: 3px; }
@keyframes sk { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
.dmr-err { padding: 14px 16px; background: var(--amber-bg); border: 1px solid #f5d87a; border-left: 4px solid #c49a00; color: var(--amber); font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.btn-retry { background: var(--navy); color: white; border: none; padding: 5px 14px; font-family: var(--font); font-size: 12px; font-weight: 700; cursor: pointer; }

@media(max-width:900px){ .dmr-stats-grid { grid-template-columns: repeat(2,1fr); } }
@media(max-width:600px){ .dmr-command-strip{padding:0 14px;} .dmr-body{padding:16px 12px 40px;} .dmr-stats{padding:0 14px 14px;} .dmr-stats-grid{grid-template-columns:repeat(2,1fr);gap:8px;} }
</style>

<div class="dmr-shell">

    <div class="dmr-command-strip">
        <div class="dmr-command-inner">
            <div class="dmr-brand">
                <div class="dmr-brand-badge">RAY<br>NET</div>
                <div>
                    <div class="dmr-brand-name">{{ \App\Helpers\RaynetSetting::groupName() }}</div>
                    <div class="dmr-brand-sub">DMR Network Monitor</div>
                </div>
            </div>
            <div class="dmr-command-right">
                <div class="dmr-status-pill" id="status-pill" style="display:none;">
                    <span class="dmr-status-dot"></span> LIVE
                </div>
                <button class="dmr-refresh-pill" id="refresh-btn" onclick="loadAll(true)">↺ Refresh</button>
            </div>
        </div>
    </div>

    @if($hasDashboard)
    <div class="dmr-access-banner">
        <div class="dmr-access-inner">
            <div class="dmr-access-text">📡 <strong>Full Access</strong> — Live QSOs, peers, bridges and call log available.</div>
            <a href="{{ $dashboardUrl }}:8010" target="_blank" class="btn-open-dashboard"> Open HBMon Dashboard ↗
</a>
        </div>
    </div>
    @else
    <div class="dmr-limited-banner">ℹ Read-only access to master status. Contact an administrator for full access.</div>
    @endif

    <div class="dmr-stats">
        <div class="dmr-stats-grid">
            <div class="dmr-stat-tile t-green">
                <div class="dmr-stat-label">Masters Online</div>
                <div class="dmr-stat-value loading" id="stat-masters">—</div>
                <div class="dmr-stat-sub">HB Protocol systems</div>
            </div>
            <div class="dmr-stat-tile t-blue">
                <div class="dmr-stat-label">Connected Peers</div>
                <div class="dmr-stat-value loading" id="stat-peers">—</div>
                <div class="dmr-stat-sub">Hotspots &amp; nodes</div>
            </div>
            <div class="dmr-stat-tile">
                <div class="dmr-stat-label">Last QSO</div>
                <div class="dmr-stat-value loading" id="stat-lastqso" style="font-size:13px;padding-top:4px;">—</div>
                <div class="dmr-stat-sub">Most recent callsign</div>
            </div>
            <div class="dmr-stat-tile t-amber">
                <div class="dmr-stat-label">Log Entries</div>
                <div class="dmr-stat-value loading" id="stat-lhcount">—</div>
                <div class="dmr-stat-sub">Recent QSOs</div>
            </div>
        </div>
    </div>

    <div class="dmr-body">

        <div class="dmr-sec-head">
            <div class="dmr-sec-title">
                HB Protocol Master Systems
                <span class="dmr-sec-badge" id="masters-count" style="display:none;">0</span>
            </div>
            <span class="dmr-sec-updated" id="last-updated"></span>
        </div>

        <div class="masters-wrap">
            <table class="masters-table">
                <thead>
                    <tr>
                        <th style="width:200px;">HB Protocol Master</th>
                        <th style="width:190px;">Callsign (DMR Id)</th>
                        <th style="width:130px;">Time Connected</th>
                        <th style="width:95px;">Slot</th>
                        <th>Source</th>
                        <th>Destination</th>
                    </tr>
                </thead>
                <tbody id="masters-tbody">
                    <tr><td colspan="6" style="padding:24px;text-align:center;"><span class="sk" style="width:220px;height:14px;"></span></td></tr>
                </tbody>
            </table>
        </div>

        @if($hasDashboard)
        <div class="dmr-sec-head" style="margin-top:4px;">
            <div class="dmr-sec-title">
                Last Heard
                <span class="dmr-sec-badge" id="lh-count" style="display:none;">0</span>
            </div>
        </div>
        <div class="lh-wrap">
            <table class="lh-table">
                <thead>
                    <tr>
                        <th style="width:155px;">Date / Time</th>
                        <th style="width:120px;">Callsign</th>
                        <th>Name</th>
                        <th style="width:110px;">Master</th>
                        <th style="width:75px;">Slot</th>
                        <th style="width:110px;">Talkgroup</th>
                        <th style="width:80px;">Duration</th>
                        <th style="width:70px;">Event</th>
                    </tr>
                </thead>
                <tbody id="lh-tbody">
                    <tr><td colspan="8" style="padding:24px;text-align:center;"><span class="sk" style="width:220px;height:14px;"></span></td></tr>
                </tbody>
            </table>
        </div>
        @endif

    </div>
</div>

<script>

const MASTERS_URL   = @json($mastersUrl);
const LASTHEARD_URL = @json($lastHeardUrl);
const HAS_DASHBOARD = {{ $hasDashboard ? 'true' : 'false' }};

function esc(str) {
    return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function slotPair() {
    return `<div class="slot-wrap"><span class="slot-b slot-ts1">TS1</span><span class="slot-b slot-ts2">TS2</span></div>`;
}

function slotSingle(slot) {
    if (!slot) return '—';
    const ts1 = slot.toString().includes('1');
    return `<span class="slot-b ${ts1 ? 'slot-ts1' : 'slot-ts2'}">${esc(slot)}</span>`;
}

async function loadMasters() {
    const tbody   = document.getElementById('masters-tbody');
    const pill    = document.getElementById('status-pill');
    const counter = document.getElementById('masters-count');
    const statM   = document.getElementById('stat-masters');
    const statP   = document.getElementById('stat-peers');
    const upd     = document.getElementById('last-updated');

    try {
        const resp = await fetch(MASTERS_URL, { signal: AbortSignal.timeout(10000), cache: 'no-cache' });
        if (!resp.ok) throw new Error('HTTP ' + resp.status);
        const data = await resp.json();

        if (!data.length) {
            tbody.innerHTML = `<tr><td colspan="6" style="padding:24px;text-align:center;color:var(--text-muted);">No master systems found.</td></tr>`;
            return;
        }

        if (pill) pill.style.display = 'inline-flex';
        const totalPeers = data.reduce((s,m) => s + (m.peer_count || 0), 0);
        const online     = data.filter(m => m.connected).length;
        if (statM) { statM.textContent = online; statM.classList.remove('loading'); }
        if (statP) { statP.textContent = totalPeers; statP.classList.remove('loading'); }
        if (counter) { counter.textContent = data.length; counter.style.display = 'inline-block'; }
        if (upd) upd.textContent = 'Updated ' + new Date().toLocaleTimeString('en-GB');

        let rows = '';
        data.forEach(m => {
            const peers   = m.peers   || [];
            const traffic = m.traffic || [];
            const on      = m.connected;

            rows += `<tr class="tr-master">
                <td colspan="6">
                    <div class="master-name-cell">
                        <span class="master-pulse ${on ? 'online' : ''}"></span>
                        <span class="master-name-text">${esc(m.name)}</span>
                        <span class="master-sub">repeat</span>
                        ${!on ? `<span class="master-offline">Offline</span>` : ''}
                        ${m.last_heard ? `<span class="master-last">Last activity: ${esc(m.last_heard)}</span>` : ''}
                    </div>
                </td>
            </tr>`;

            if (!peers.length) {
                rows += `<tr class="tr-no-peers"><td colspan="6">No peers connected</td></tr>`;
            } else {
                peers.forEach(p => {
                    const hasTraf = traffic.length && (traffic[0].source || traffic[0].dest);
                    rows += `<tr class="tr-peer">
                        <td></td>
                        <td>
                            <a class="cs-link" href="https://www.qrz.com/db/${esc(p.callsign)}" target="_blank">${esc(p.callsign)}</a>
                            <span class="cs-id">Id: ${esc(p.dmr_id)}</span>
                            ${p.location ? `<span class="cs-loc">${esc(p.location)}</span>` : ''}
                        </td>
                        <td>${p.time_connected ? `<span class="time-val">${esc(p.time_connected)}</span>` : '<span style="color:var(--grey-dark);">—</span>'}</td>
                        <td>${slotPair()}</td>
                        <td class="traffic-cell">${hasTraf && traffic[0].source ? `<span class="active-badge"><span class="active-dot"></span>ACTIVE</span>${esc(traffic[0].source)}` : ''}</td>
                        <td class="traffic-cell">${hasTraf && traffic[0].dest ? esc(traffic[0].dest) : ''}</td>
                    </tr>`;
                });
            }
        });

        tbody.innerHTML = rows;

    } catch(e) {
        tbody.innerHTML = `<tr><td colspan="6"><div class="dmr-err">⚠ Could not reach DMR server (${esc(e.message)}). <button class="btn-retry" onclick="loadAll(true)">Retry</button></div></td></tr>`;
        if (pill) pill.style.display = 'none';
    }
}

async function loadLastHeard() {
    if (!HAS_DASHBOARD) return;
    const tbody   = document.getElementById('lh-tbody');
    const counter = document.getElementById('lh-count');
    const statLh  = document.getElementById('stat-lhcount');
    const statQso = document.getElementById('stat-lastqso');

    try {
        const resp = await fetch(LASTHEARD_URL, { signal: AbortSignal.timeout(10000), cache: 'no-cache' });
        if (!resp.ok) throw new Error('HTTP ' + resp.status);
        const data = await resp.json();

        if (counter) { counter.textContent = data.length; counter.style.display = 'inline-block'; }
        if (statLh)  { statLh.textContent = data.length; statLh.classList.remove('loading'); }
        if (statQso && data[0]) {
            statQso.innerHTML = `<span style="font-size:14px;font-weight:700;">${esc(data[0].callsign || '—')}</span><br><span style="font-size:10px;opacity:.5;">${esc(data[0].talkgroup || '')}</span>`;
            statQso.classList.remove('loading');
        }

        if (!data.length) {
            tbody.innerHTML = `<tr><td colspan="8" style="padding:24px;text-align:center;color:var(--text-muted);">No recent QSOs.</td></tr>`;
            return;
        }

        tbody.innerHTML = data.slice(0, 100).map(e => {
            const isStart = (e.event || '').toLowerCase().includes('start');
            return `<tr>
                <td class="lh-t">${esc(e.time)}</td>
                <td class="lh-cs"><a href="https://www.qrz.com/db/${esc(e.callsign)}" target="_blank">${esc(e.callsign)}</a></td>
                <td>${esc(e.name)}</td>
                <td style="font-size:11px;color:var(--text-muted);">${esc(e.master)}</td>
                <td>${slotSingle(e.slot)}</td>
                <td class="lh-tg">${esc(e.talkgroup)}</td>
                <td class="lh-dur">${e.duration ? esc(e.duration) + 's' : '—'}</td>
                <td><span class="event-chip ${isStart ? 'chip-start' : 'chip-end'}">${isStart ? 'START' : 'END'}</span></td>
            </tr>`;
        }).join('');

    } catch(e) {
        if (tbody) tbody.innerHTML = `<tr><td colspan="8"><div class="dmr-err">⚠ Could not load last heard data.</div></td></tr>`;
    }
}

async function loadAll(manual = false) {
    const btn = document.getElementById('refresh-btn');
    if (manual) { btn.classList.add('spinning'); btn.textContent = '↺ Loading…'; }
    await Promise.all([loadMasters(), loadLastHeard()]);
    if (manual) { btn.classList.remove('spinning'); btn.textContent = '↺ Refresh'; }
}

loadAll();
setInterval(() => loadAll(), 30000);
</script>

@endsection