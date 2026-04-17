{{-- resources/views/members/dmr-network.blade.php --}}
@extends('layouts.app')

@section('title', 'DMR Network — {{ \App\Helpers\RaynetSetting::groupName() }}')

@section('content')
<style>
:root {
    --navy:#003366;--navy-mid:#004080;--navy-dark:#001f40;--navy-faint:#e8eef5;
    --red:#C8102E;--red-faint:#fdf0f2;--white:#ffffff;--grey:#F2F2F2;
    --grey-mid:#dde2e8;--grey-dark:#9aa3ae;--green:#16a34a;--green-bg:#dcfce7;
    --amber:#d97706;--amber-bg:#fef3c7;--purple:#7c3aed;
    --font:Arial,'Helvetica Neue',Helvetica,sans-serif;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}

.dmr-hero{background:var(--navy-dark);border-bottom:4px solid var(--red);padding:1.75rem 0 1.5rem;position:relative;overflow:hidden;}
.dmr-hero::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(0,51,102,.8)0%,rgba(0,31,64,.95)100%);}
.dmr-hero-inner{max-width:1360px;margin:0 auto;padding:0 1.5rem;display:flex;align-items:center;justify-content:space-between;gap:1.25rem;flex-wrap:wrap;position:relative;z-index:1;}
.dmr-hero-left{display:flex;align-items:center;gap:1rem;}
.dmr-hero-icon{width:52px;height:52px;background:var(--red);display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0;box-shadow:0 0 0 3px rgba(200,16,46,.3);}
.dmr-hero-title{font-family:var(--font);font-size:24px;font-weight:700;color:#fff;line-height:1.1;}
.dmr-hero-sub{font-size:12px;color:rgba(255,255,255,.45);margin-top:3px;text-transform:uppercase;letter-spacing:.12em;font-family:var(--font);}
.dmr-hero-right{display:flex;align-items:center;gap:.6rem;flex-wrap:wrap;}
.ns-pill{display:inline-flex;align-items:center;gap:.4rem;padding:.35rem .9rem;font-size:11px;font-weight:700;font-family:var(--font);text-transform:uppercase;letter-spacing:.07em;border:1px solid rgba(255,255,255,.15);color:rgba(255,255,255,.5);background:rgba(255,255,255,.06);}
.ns-pill.live{border-color:rgba(22,163,74,.5);background:rgba(22,163,74,.12);color:#4ade80;}
.ns-pill.offline{border-color:rgba(239,68,68,.4);background:rgba(239,68,68,.1);color:#f87171;}
.ns-dot{width:6px;height:6px;border-radius:50%;background:currentColor;flex-shrink:0;}
.ns-pill.live .ns-dot{animation:pns 2s infinite;}
@keyframes pns{0%,100%{opacity:1;box-shadow:0 0 0 0 rgba(74,222,128,.5);}50%{box-shadow:0 0 0 4px rgba(74,222,128,0);}}
.dmr-ts{font-size:11px;color:rgba(255,255,255,.25);font-family:var(--font);}

.dmr-wrap{max-width:1360px;margin:0 auto;padding:1.4rem 1.5rem 4rem;}

.dmr-stats{display:grid;grid-template-columns:repeat(5,1fr);gap:.9rem;margin-bottom:1.4rem;}
@media(max-width:1000px){.dmr-stats{grid-template-columns:repeat(3,1fr);}}
@media(max-width:600px){.dmr-stats{grid-template-columns:1fr 1fr;}}
.dstat{background:var(--white);border:1px solid var(--grey-mid);border-top:3px solid var(--navy);padding:1rem 1.1rem .85rem;box-shadow:0 1px 4px rgba(0,51,102,.06);}
.dstat.red{border-top-color:var(--red);}
.dstat.green{border-top-color:var(--green);}
.dstat.amber{border-top-color:var(--amber);}
.dstat.purple{border-top-color:var(--purple);}
.dstat-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.14em;color:var(--grey-dark);margin-bottom:.35rem;font-family:var(--font);}
.dstat-value{font-size:32px;font-weight:700;color:var(--navy);line-height:1;font-family:var(--font);}
.dstat.red .dstat-value{color:var(--red);}
.dstat.green .dstat-value{color:var(--green);}
.dstat.amber .dstat-value{color:var(--amber);}
.dstat.purple .dstat-value{color:var(--purple);}
.dstat-sub{font-size:11px;color:var(--grey-dark);margin-top:.25rem;font-family:var(--font);}

.dmr-grid{display:grid;grid-template-columns:1fr 360px;gap:1.2rem;align-items:start;}
@media(max-width:1060px){.dmr-grid{grid-template-columns:1fr;}}

.dp{background:var(--white);border:1px solid var(--grey-mid);box-shadow:0 1px 4px rgba(0,51,102,.06);overflow:hidden;margin-bottom:1.2rem;}
.dp-head{background:var(--navy);border-bottom:2px solid var(--red);padding:.65rem 1.1rem;display:flex;align-items:center;justify-content:space-between;gap:.75rem;}
.dp-title{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:rgba(255,255,255,.9);font-family:var(--font);display:flex;align-items:center;gap:.5rem;}
.dp-badge{font-size:10px;font-weight:700;padding:1px 6px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);color:rgba(255,255,255,.5);font-family:var(--font);}
.dp-refresh{font-size:10px;color:rgba(255,255,255,.25);font-family:var(--font);}
.spin{animation:spin .7s linear infinite;display:inline-block;}
@keyframes spin{to{transform:rotate(360deg);}}

.dt{width:100%;border-collapse:collapse;font-size:12px;font-family:var(--font);}
.dt th{background:#001f40;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.1em;font-size:10px;font-weight:700;padding:.45rem .85rem;text-align:left;white-space:nowrap;}
.dt td{padding:.6rem .85rem;border-bottom:1px solid var(--grey-mid);vertical-align:middle;color:#1a2a3a;}
.dt tr:last-child td{border-bottom:none;}
.dt tr:hover td{background:var(--navy-faint);}
.cs{font-family:'Courier New',monospace;font-weight:700;font-size:13px;color:var(--navy);letter-spacing:.05em;}
.dmrid{font-size:10px;color:var(--grey-dark);margin-top:2px;}
.tg{display:inline-block;padding:2px 7px;background:var(--navy-faint);border:1px solid rgba(0,51,102,.2);color:var(--navy);font-weight:700;font-size:11px;}
.slot-ts{display:inline-block;padding:1px 5px;background:#001f40;color:rgba(255,255,255,.65);font-size:10px;font-weight:700;margin:1px;}
.dur{font-size:11px;color:var(--grey-dark);}
.dur.long{color:var(--amber);font-weight:700;}
.conn-dot{display:inline-block;width:7px;height:7px;border-radius:50%;background:var(--green);margin-right:5px;}
.conn-dot.dim{background:var(--amber);}
.loc{font-size:10px;color:var(--grey-dark);margin-top:2px;}
.dp-section-head{background:rgba(0,31,64,.05);border-bottom:1px solid var(--grey-mid);padding:.35rem .85rem;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:var(--grey-dark);font-family:var(--font);}

.log-shell{background:#080e1a;height:400px;overflow-y:auto;font-family:'Courier New',monospace;font-size:11px;padding:.4rem .5rem;scroll-behavior:smooth;}
.log-shell::-webkit-scrollbar{width:5px;}
.log-shell::-webkit-scrollbar-track{background:#080e1a;}
.log-shell::-webkit-scrollbar-thumb{background:rgba(0,51,102,.6);}
.ll{padding:2px 5px;line-height:1.65;border-radius:2px;margin-bottom:1px;animation:llf .25s ease;word-break:break-all;white-space:pre-wrap;}
@keyframes llf{from{background:rgba(0,64,128,.25);opacity:0;}to{opacity:1;}}
.ll-start{color:#4ade80;}.ll-end{color:#64748b;}.ll-conn{color:#60a5fa;}
.ll-err{color:#f87171;}.ll-sys{color:#fbbf24;}.ll-def{color:#94a3b8;}
.ll-cs{color:#93c5fd;font-weight:700;}.ll-tg{color:#c4b5fd;}.ll-time{color:#334155;margin-right:.3rem;}

.log-foot{background:#050b15;border-top:1px solid rgba(255,255,255,.04);padding:.45rem .85rem;display:flex;align-items:center;justify-content:space-between;gap:.5rem;}
.ws-status{display:flex;align-items:center;gap:.4rem;font-size:10px;font-weight:700;font-family:var(--font);text-transform:uppercase;letter-spacing:.07em;}
.ws-dot{width:6px;height:6px;border-radius:50%;flex-shrink:0;}
.wsc-live{color:#4ade80;}.wsc-live .ws-dot{background:#4ade80;animation:pns 2s infinite;}
.wsc-connecting{color:var(--amber);}.wsc-connecting .ws-dot{background:var(--amber);}
.wsc-dead{color:#f87171;}.wsc-dead .ws-dot{background:#f87171;}
.log-lc{font-size:10px;color:rgba(255,255,255,.15);font-family:var(--font);}
.log-btn{font-size:10px;font-weight:700;font-family:var(--font);text-transform:uppercase;letter-spacing:.05em;padding:2px 8px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.4);cursor:pointer;}
.log-btn:hover{background:rgba(255,255,255,.12);color:rgba(255,255,255,.7);}

.dp-empty{padding:2.5rem 1rem;text-align:center;color:var(--grey-dark);font-size:13px;font-family:var(--font);}
.dp-spinner{display:inline-block;width:20px;height:20px;border:2px solid var(--grey-mid);border-top-color:var(--navy);border-radius:50%;animation:spin .8s linear infinite;margin-bottom:.65rem;}
</style>

<div class="dmr-hero">
    <div class="dmr-hero-inner">
        <div class="dmr-hero-left">
            <div class="dmr-hero-icon">📡</div>
            <div>
                <div class="dmr-hero-title">{{ \App\Helpers\RaynetSetting::groupName() }} DMR Network</div>
                <div class="dmr-hero-sub">Live EmComm Network · Members Only · HBLink Server M0KKN</div>
            </div>
        </div>
        <div class="dmr-hero-right">
            <div class="ns-pill" id="wsStatusPill">
                <span class="ns-dot"></span>
                <span id="wsStatusLabel">Connecting…</span>
            </div>
            <div class="dmr-ts">Updated: <span id="lastUpdate">—</span></div>
        </div>
    </div>
</div>

<div class="dmr-wrap">

    <div class="dmr-stats">
        <div class="dstat green">
            <div class="dstat-label">Connected Peers</div>
            <div class="dstat-value" id="sc-peers">—</div>
            <div class="dstat-sub">Masters &amp; hotspots</div>
        </div>
        <div class="dstat red">
            <div class="dstat-label">Active Calls</div>
            <div class="dstat-value" id="sc-calls">0</div>
            <div class="dstat-sub">Right now</div>
        </div>
        <div class="dstat">
            <div class="dstat-label">Last Callsign</div>
            <div class="dstat-value" id="sc-lastcs" style="font-size:18px;padding-top:5px;">—</div>
            <div class="dstat-sub" id="sc-lasttime">—</div>
        </div>
        <div class="dstat amber">
            <div class="dstat-label">Log Messages</div>
            <div class="dstat-value" id="sc-logct">0</div>
            <div class="dstat-sub">This session</div>
        </div>
        <div class="dstat purple">
            <div class="dstat-label">Primary TG</div>
            <div class="dstat-value" style="font-size:18px;padding-top:5px;">5023531</div>
            <div class="dstat-sub">RayNET NATIONAL</div>
        </div>
    </div>

    <div class="dmr-grid">
        <div>
            <div class="dp" id="lhPanel">
                <div class="dp-head">
                    <div class="dp-title">🔊 Last Heard <span class="dp-badge" id="lhCount">—</span></div>
                    <div class="dp-refresh"><span id="lhSpin" style="display:none;" class="spin">⟳</span> Refresh 20s</div>
                </div>
                <div id="lhBody"><div class="dp-empty"><div class="dp-spinner"></div><br>Loading last heard…</div></div>
            </div>

            <div class="dp" id="peersPanel">
                <div class="dp-head">
                    <div class="dp-title">🔗 Network Connections <span class="dp-badge" id="peersCount">—</span></div>
                    <div class="dp-refresh"><span id="peersSpin" style="display:none;" class="spin">⟳</span> Refresh 20s</div>
                </div>
                <div id="peersBody"><div class="dp-empty"><div class="dp-spinner"></div><br>Loading connections…</div></div>
            </div>
        </div>

        <div>
            <div class="dp" style="margin-bottom:1.2rem;">
                <div class="dp-head">
                    <div class="dp-title">⚡ Live Activity Log</div>
                    <div style="display:flex;align-items:center;gap:.4rem;">
                        <button class="log-btn" onclick="clearLog()">Clear</button>
                        <label style="display:flex;align-items:center;gap:.3rem;cursor:pointer;font-size:10px;color:rgba(255,255,255,.3);font-family:Arial,sans-serif;">
                            <input type="checkbox" id="autoScroll" checked style="accent-color:var(--red);width:12px;height:12px;">
                            Scroll
                        </label>
                    </div>
                </div>
                <div class="log-shell" id="logShell"></div>
                <div class="log-foot">
                    <div class="ws-status wsc-connecting" id="wsSt">
                        <span class="ws-dot"></span>
                        <span id="wsStTxt">Connecting…</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:.5rem;">
                        <span class="log-lc"><span id="logLines">0</span> lines</span>
                        <button class="log-btn" id="reconnBtn" style="display:none;" onclick="startWS()">↺ Reconnect</button>
                    </div>
                </div>
            </div>

            <div class="dp" style="margin-bottom:1.2rem;">
                <div class="dp-head"><div class="dp-title">📋 Talkgroup Reference</div></div>
                <table class="dt">
                    <thead><tr><th>TGID</th><th>Name</th><th>Use</th></tr></thead>
                    <tbody>
                        <tr><td><span class="tg">5023531</span></td><td style="font-weight:700;color:var(--navy);">RayNET NATIONAL <span style="font-size:9px;background:var(--red);color:#fff;padding:1px 5px;margin-left:4px;">PRIMARY</span></td><td style="font-size:11px;color:var(--grey-dark);">National coordination</td></tr>
                        <tr><td><span class="tg">5017900–04</span></td><td style="font-weight:700;color:var(--navy);">RAYNET Local <span style="font-size:9px;background:var(--red);color:#fff;padding:1px 5px;margin-left:4px;">PRIMARY</span></td><td style="font-size:11px;color:var(--grey-dark);">Local event nets</td></tr>
                        <tr><td><span class="tg">9999</span></td><td style="font-weight:700;color:var(--navy);">Parrot / Echo</td><td style="font-size:11px;color:var(--grey-dark);">Test &amp; hotspot echo</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="dp">
                <div class="dp-head">
                    <div class="dp-title">🖥 Server Details</div>
                    <a href="{{ $baseUrl }}" target="_blank" rel="noopener"
                       style="font-size:10px;color:rgba(255,255,255,.35);font-family:Arial,sans-serif;text-decoration:none;border:1px solid rgba(255,255,255,.12);padding:2px 7px;">
                        ↗ Open dashboard
                    </a>
                </div>
                <table class="dt">
                    <tbody>
                        <tr><td style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--grey-dark);width:100px;white-space:nowrap;">SysOp</td><td style="font-size:12px;color:var(--navy);font-weight:600;">M0ADM / M0KKN</td></tr>
                        <tr><td style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--grey-dark);width:100px;white-space:nowrap;">Network ID</td><td style="font-size:12px;color:var(--navy);font-weight:600;">234015101</td></tr>
                        <tr><td style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--grey-dark);width:100px;white-space:nowrap;">Protocol</td><td style="font-size:12px;color:var(--navy);font-weight:600;">HBLink3 · Homebrew DMR</td></tr>
                        <tr><td style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--grey-dark);width:100px;white-space:nowrap;">Live Log</td><td style="font-size:12px;color:var(--navy);font-weight:600;">SSE proxy (HTTPS)</td></tr>
                        <tr><td style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--grey-dark);width:100px;white-space:nowrap;">Access</td><td style="font-size:12px;color:var(--navy);font-weight:600;">Members only</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// ── {{ \App\Helpers\RaynetSetting::groupName() }} DMR Network ──────────────────────────────────────────
// SSE stream: /members/dmr-network/stream
// Data polls: /members/dmr-network/lastheard + /peers  (every 20s)
// ─────────────────────────────────────────────────────────────────────────
var POLL_MS   = 20000;
var sse       = null;
var logCount  = 0;
var activeCalls = {};

function el(id) { return document.getElementById(id); }

function esc(s) {
    return String(s || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

function relTime(date, time) {
    try {
        var d   = new Date(date + 'T' + (time || '00:00') + ':00');
        var min = Math.floor((Date.now() - d) / 60000);
        if (isNaN(min) || min < 0) return time || '';
        if (min < 1)    return 'Just now';
        if (min < 60)   return min + 'm ago';
        if (min < 1440) return Math.floor(min / 60) + 'h ago';
        return Math.floor(min / 1440) + 'd ago';
    } catch (e) { return time || ''; }
}

// ── UI state ──────────────────────────────────────────────────────────────
function setWsUi(state) {
    var stEl  = el('wsSt');
    var txtEl = el('wsStTxt');
    var pill  = el('wsStatusPill');
    var btn   = el('reconnBtn');

    var cls  = {live:'wsc-live', connecting:'wsc-connecting', dead:'wsc-dead'};
    var labs = {live:'⚡ Live', connecting:'Connecting…', dead:'✗ Disconnected'};

    if (stEl)  stEl.className  = 'ws-status ' + (cls[state] || 'wsc-connecting');
    if (txtEl) txtEl.textContent = labs[state] || state;
    if (btn)   btn.style.display = (state === 'dead') ? 'inline-block' : 'none';

    if (pill) {
        pill.className = 'ns-pill' + (state === 'live' ? ' live' : state === 'dead' ? ' offline' : '');
        el('wsStatusLabel').textContent = state === 'live' ? 'Server Online'
            : state === 'dead' ? 'Offline' : 'Connecting…';
    }
}

// ── Log rendering ─────────────────────────────────────────────────────────
function addLine(text, cls) {
    var shell = el('logShell');
    if (!shell) return;

    var div = document.createElement('div');
    div.className = 'll ll-' + (cls || 'def');
    div.innerHTML = colourLine(text);
    shell.appendChild(div);

    logCount++;
    el('logLines').textContent = logCount;
    el('sc-logct').textContent  = logCount;

    while (shell.children.length > 600) {
        shell.removeChild(shell.firstChild);
    }
    if (el('autoScroll') && el('autoScroll').checked) {
        shell.scrollTop = shell.scrollHeight;
    }
}

function colourLine(raw) {
    var s = esc(raw);
    // Timestamp
    s = s.replace(/^(\d\d:\d\d:\d\d)/, '<span class="ll-time">$1</span>');
    // Callsigns e.g. G3ZHX M0ADM
    s = s.replace(/\b([A-Z]{1,2}[0-9][A-Z0-9]{0,3}[A-Z])\b/g, '<span class="ll-cs">$1</span>');
    // TGID numbers
    s = s.replace(/TGID[:\s]+(\d+)/gi, 'TGID <span class="ll-tg">$1</span>');
    return s;
}

function lineClass(text) {
    var u = text.toUpperCase();
    if (u.indexOf('VOICE START') >= 0) return 'start';
    if (u.indexOf('VOICE END')   >= 0) return 'end';
    if (u.indexOf('CONNECT')     >= 0) return 'conn';
    if (u.indexOf('ERROR')       >= 0 || u.indexOf('FAIL') >= 0) return 'err';
    if (u.indexOf('SYS:')        >= 0) return 'sys';
    return 'def';
}

function clearLog() {
    el('logShell').innerHTML = '';
    logCount = 0;
    activeCalls = {};
    el('logLines').textContent = 0;
    el('sc-logct').textContent  = 0;
    el('sc-calls').textContent  = 0;
}

// ── WebSocket via Cloudflare Worker (wss://) ────────────────────────────
var WS_URL    = '{{ $wsUrl }}';
var wsConn    = null;
var wsTimer   = null;

function startWS() {
    if (wsConn) { try { wsConn.close(); } catch(e) {} wsConn = null; }
    clearTimeout(wsTimer);
    setWsUi('connecting');
    addLine('Connecting to ' + WS_URL + '…', 'conn');

    try {
        wsConn = new WebSocket(WS_URL);
    } catch(e) {
        setWsUi('dead');
        addLine('WebSocket error: ' + e.message, 'err');
        wsTimer = setTimeout(startWS, 10000);
        return;
    }

    wsConn.onopen = function() {
        setWsUi('live');
        addLine('Connected — {{ \App\Helpers\RaynetSetting::groupName() }} HBLink', 'conn');
    };

    wsConn.onmessage = function(e) {
        var lines = String(e.data).split('\n');
        for (var i = 0; i < lines.length; i++) {
            var line = lines[i].trim();
            if (!line) continue;
            var cls = lineClass(line);
            addLine(line, cls);
            var lu = line.toUpperCase();
            if (lu.indexOf('VOICE START') >= 0) activeCalls[line.substring(0, 40)] = 1;
            if (lu.indexOf('VOICE END')   >= 0) delete activeCalls[line.substring(0, 40)];
            el('sc-calls').textContent = Math.max(0, Object.keys(activeCalls).length);
        }
    };

    wsConn.onerror = function() {
        setWsUi('dead');
    };

    wsConn.onclose = function() {
        setWsUi('dead');
        addLine('Disconnected — retry in 10s…', 'err');
        wsTimer = setTimeout(startWS, 10000);
    };
}

// ── Last Heard ────────────────────────────────────────────────────────────
function loadLastheard() {
    el('lhSpin').style.display = 'inline';

    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/members/dmr-network/lastheard', true);
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    xhr.onload = function() {
        el('lhSpin').style.display = 'none';
        try {
            var data = JSON.parse(xhr.responseText);
            el('lastUpdate').textContent = data.ts || '';
            el('lhCount').textContent    = (data.rows && data.rows.length) || 0;

            if (!data.rows || !data.rows.length) {
                el('lhBody').innerHTML = '<div class="dp-empty">No recent activity.</div>';
                return;
            }

            var first = data.rows[0];
            if (first && first.callsign) {
                el('sc-lastcs').textContent   = first.callsign;
                el('sc-lasttime').textContent = relTime(first.date, first.time);
            }

            var rows = '';
            for (var i = 0; i < data.rows.length; i++) {
                var r = data.rows[i];
                rows += '<tr>'
                    + '<td style="white-space:nowrap;"><div style="font-size:10px;color:var(--grey-dark);">' + esc(r.date) + '</div>'
                    + '<div style="font-weight:700;color:var(--navy);font-size:12px;">' + esc(r.time) + '</div></td>'
                    + '<td><div class="cs">' + esc(r.callsign || '—') + '</div>'
                    + (r.dmr_id ? '<div class="dmrid">' + esc(r.dmr_id) + '</div>' : '') + '</td>'
                    + '<td style="font-size:12px;color:#374151;">' + esc(r.name || '—') + '</td>'
                    + '<td><span class="tg">' + esc(r.tgid || '—') + '</span>'
                    + (r.tg_name ? '<div style="font-size:10px;color:var(--grey-dark);margin-top:2px;">' + esc(r.tg_name) + '</div>' : '') + '</td>'
                    + '<td><span class="dur' + (parseInt(r.duration || 0) > 30 ? ' long' : '') + '">' + esc(r.duration || '—') + 's</span></td>'
                    + '<td><span class="slot-ts">TS' + esc(r.slot || '?') + '</span></td>'
                    + '<td style="font-size:11px;color:var(--grey-dark);">' + esc(r.system || '—') + '</td>'
                    + '</tr>';
            }

            el('lhBody').innerHTML = '<table class="dt"><thead><tr>'
                + '<th>Date/Time</th><th>Callsign</th><th>Name</th>'
                + '<th>Talkgroup</th><th>TX</th><th>Slot</th><th>System</th>'
                + '</tr></thead><tbody>' + rows + '</tbody></table>';

        } catch(ex) {
            el('lhBody').innerHTML = '<div class="dp-empty" style="color:var(--red);">Could not load data.</div>';
        }
    };

    xhr.onerror = function() {
        el('lhSpin').style.display = 'none';
        el('lhBody').innerHTML = '<div class="dp-empty" style="color:var(--red);">Network error.</div>';
    };

    xhr.send();
}

// ── Peers ─────────────────────────────────────────────────────────────────
function loadPeers() {
    el('peersSpin').style.display = 'inline';

    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/members/dmr-network/peers', true);
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    xhr.onload = function() {
        el('peersSpin').style.display = 'none';
        try {
            var data = JSON.parse(xhr.responseText);
            var d    = data.data || {};
            var opb  = d.openbridge || [];
            var mas  = d.masters    || [];
            var prs  = d.peers      || [];
            var total = mas.length + prs.length;

            el('peersCount').textContent = total;
            el('sc-peers').textContent   = total;

            var html = '';

            if (opb.length) {
                html += '<div class="dp-section-head">🌐 OpenBridge (' + opb.length + ')</div>';
                html += '<table class="dt"><thead><tr><th>Name</th><th>Net ID</th><th>Active</th></tr></thead><tbody>';
                for (var i = 0; i < opb.length; i++) {
                    var o = opb[i];
                    html += '<tr><td style="font-weight:700;color:var(--navy);">' + esc(o.name) + '</td>'
                        + '<td><span class="tg">' + esc(o.net_id) + '</span></td>'
                        + '<td>' + (o.active ? '<span style="color:var(--red);font-weight:700;">' + esc(o.active) + '</span>' : '—') + '</td></tr>';
                }
                html += '</tbody></table>';
            }

            if (mas.length) {
                html += '<div class="dp-section-head">📡 DN Masters (' + mas.length + ')</div>';
                html += '<table class="dt"><thead><tr><th>Master</th><th>Callsign</th><th>Connected</th><th>TS1</th><th>TS2</th></tr></thead><tbody>';
                for (var i = 0; i < mas.length; i++) {
                    var m = mas[i];
                    html += '<tr>'
                        + '<td style="font-weight:700;font-size:12px;color:var(--navy);">' + esc(m.master) + '</td>'
                        + '<td><span class="cs">' + esc(m.callsign) + '</span>'
                        + (m.location ? '<div class="loc">' + esc(m.location) + '</div>' : '') + '</td>'
                        + '<td><span class="conn-dot"></span><span style="font-size:12px;color:var(--green);font-weight:700;">' + esc(m.connected) + '</span></td>'
                        + '<td style="font-size:11px;">' + (m.ts1_src ? '<span class="slot-ts">TS1</span> ' + esc(m.ts1_src) : '<span style="color:var(--grey-dark);">Idle</span>') + '</td>'
                        + '<td style="font-size:11px;">' + (m.ts2_src ? '<span class="slot-ts">TS2</span> ' + esc(m.ts2_src) : '<span style="color:var(--grey-dark);">Idle</span>') + '</td>'
                        + '</tr>';
                }
                html += '</tbody></table>';
            }

            if (prs.length) {
                html += '<div class="dp-section-head">🔌 Peers / Hotspots (' + prs.length + ')</div>';
                html += '<table class="dt"><thead><tr><th>Peer</th><th>Callsign</th><th>Status</th><th>RX/TX</th></tr></thead><tbody>';
                for (var i = 0; i < prs.length; i++) {
                    var p = prs[i];
                    html += '<tr>'
                        + '<td style="font-weight:700;font-size:12px;color:var(--navy);">' + esc(p.peer) + '</td>'
                        + '<td><span class="cs">' + esc(p.callsign) + '</span>'
                        + (p.location ? '<div class="loc">' + esc(p.location) + '</div>' : '') + '</td>'
                        + '<td><span class="conn-dot"></span><span style="font-size:12px;font-weight:700;color:var(--green);">' + esc(p.status) + '</span></td>'
                        + '<td style="font-size:11px;color:var(--grey-dark);font-family:monospace;">' + esc(p.rx_tx || '—') + '</td>'
                        + '</tr>';
                }
                html += '</tbody></table>';
            }

            if (!html) {
                html = '<div class="dp-empty">No connection data — check server.</div>';
            }

            el('peersBody').innerHTML = html;

        } catch(ex) {
            el('peersBody').innerHTML = '<div class="dp-section-head">Live Status — External View</div>'
                + '<div style="position:relative;height:420px;background:#080e1a;overflow:hidden;">'
                + '<iframe src="{{ $baseUrl }}/" style="width:100%;height:100%;border:none;filter:invert(1) hue-rotate(180deg) brightness(.85);" loading="lazy" sandbox="allow-scripts allow-same-origin"></iframe>'
                + '</div>';
        }
    };

    xhr.onerror = function() {
        el('peersSpin').style.display = 'none';
        el('peersBody').innerHTML = '<div class="dp-empty" style="color:var(--red);">Network error.</div>';
    };

    xhr.send();
}

// ── Init ──────────────────────────────────────────────────────────────────
function refresh() {
    loadLastheard();
    loadPeers();
}

startWS();
refresh();
setInterval(refresh, POLL_MS);
</script>
@endsection