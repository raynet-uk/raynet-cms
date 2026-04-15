@extends('layouts.app')
@section('title', 'Operational Map — {{ \App\Helpers\RaynetSetting::groupName() }}')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css">
<style>
:root{--navy:#003366;--red:#C8102E;--light:#F2F2F2;--border:#D0D0D0;--muted:#6b7f96;--panel-w:290px;}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
.ops-shell{display:flex;height:calc(100vh - 60px);position:relative;overflow:hidden;}
.ops-sidebar{width:var(--panel-w);flex-shrink:0;background:#fff;border-right:2px solid var(--navy);display:flex;flex-direction:column;overflow:hidden;z-index:500;box-shadow:2px 0 12px rgba(0,51,102,.1);}
.sb-head{background:var(--navy);padding:.85rem 1rem;display:flex;align-items:center;gap:.65rem;flex-shrink:0;}
.sb-head-title{font-size:.95rem;font-weight:bold;color:#fff;}
.sb-head-sub{font-size:.72rem;color:rgba(255,255,255,.5);margin-top:1px;}
.sb-body{flex:1;overflow-y:auto;padding:.4rem 0;}
.sb-footer{border-top:1px solid var(--border);padding:.75rem;flex-shrink:0;background:#fafafa;}
.lg-label{font-size:.67rem;font-weight:bold;text-transform:uppercase;letter-spacing:.12em;color:var(--muted);padding:.5rem 1rem .2rem;}
.lr{display:flex;align-items:center;gap:.6rem;padding:.45rem .9rem;cursor:pointer;transition:background .1s;}
.lr:hover{background:rgba(0,51,102,.05);}
.lr.on{background:rgba(0,51,102,.07);}
.tog{position:relative;width:34px;height:18px;flex-shrink:0;}
.tog input{opacity:0;width:0;height:0;position:absolute;}
.tog-t{position:absolute;inset:0;background:#ccc;border-radius:18px;cursor:pointer;transition:background .2s;}
.tog input:checked~.tog-t{background:var(--navy);}
.tog-t::after{content:'';position:absolute;left:2px;top:2px;width:14px;height:14px;background:#fff;border-radius:50%;transition:transform .2s;}
.tog input:checked~.tog-t::after{transform:translateX(16px);}
.l-dot{width:9px;height:9px;border-radius:50%;flex-shrink:0;}
.l-info{flex:1;min-width:0;}
.l-name{font-size:.83rem;font-weight:bold;color:var(--navy);}
.l-status{font-size:.68rem;color:var(--muted);margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.l-status.live{color:#2E7D32;}
.l-status.err{color:var(--red);}
.l-status.loading{color:#d97706;}
.btn-refresh{width:100%;padding:.5rem;background:var(--navy);border:none;color:#fff;font-size:.82rem;font-weight:bold;border-radius:4px;cursor:pointer;font-family:inherit;transition:background .15s;}
.btn-refresh:hover{background:#002244;}
.btn-refresh:disabled{opacity:.5;cursor:default;}
.last-ref{font-size:.68rem;color:var(--muted);text-align:center;margin-top:.4rem;}
#opsMap{flex:1;z-index:1;}

/* APRS Loading Overlay */
#aprsOverlay{
    position:absolute;top:0;left:0;right:0;bottom:0;z-index:449;
    background:rgba(0,8,20,.82);
    display:none;align-items:center;justify-content:center;
    pointer-events:all;
}
.ao-panel{
    background:linear-gradient(160deg,rgba(0,30,60,.97),rgba(0,15,30,.97));
    border:1px solid rgba(0,100,200,.35);
    border-top:2px solid #C8102E;
    padding:1.75rem 2.25rem;
    min-width:300px;max-width:380px;
    box-shadow:0 8px 40px rgba(0,0,0,.6),0 0 80px rgba(0,51,102,.3);
    position:relative;overflow:hidden;
}
/* Animated background grid */
.ao-panel::before{
    content:'';position:absolute;inset:0;
    background-image:linear-gradient(rgba(0,100,200,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(0,100,200,.04) 1px,transparent 1px);
    background-size:24px 24px;
    animation:ao-grid 8s linear infinite;
}
@keyframes ao-grid{to{background-position:0 24px;}}

.ao-header{display:flex;align-items:center;gap:.85rem;margin-bottom:1.25rem;position:relative;}
.ao-radar{position:relative;width:44px;height:44px;flex-shrink:0;}
.ao-radar-bg{
    width:44px;height:44px;border-radius:50%;
    border:2px solid rgba(0,100,200,.3);
    background:radial-gradient(circle,rgba(0,51,102,.6),rgba(0,10,30,.9));
    position:absolute;inset:0;
}
.ao-radar-ring{
    position:absolute;inset:4px;border-radius:50%;
    border:1px solid rgba(0,150,255,.2);
}
.ao-radar-ring2{
    position:absolute;inset:10px;border-radius:50%;
    border:1px solid rgba(0,150,255,.15);
}
.ao-radar-sweep{
    position:absolute;inset:0;border-radius:50%;overflow:hidden;
}
.ao-radar-sweep::after{
    content:'';position:absolute;top:50%;left:50%;
    width:50%;height:50%;
    background:linear-gradient(90deg,rgba(200,16,46,.0),rgba(200,16,46,.6));
    transform-origin:0% 100%;
    animation:ao-sweep 1.8s linear infinite;
}
@keyframes ao-sweep{from{transform:rotate(0deg);}to{transform:rotate(360deg);}}
.ao-center-dot{
    position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
    width:4px;height:4px;border-radius:50%;background:#C8102E;
    box-shadow:0 0 6px #C8102E;
}
.ao-title{font-size:.9rem;font-weight:bold;color:#fff;letter-spacing:.06em;}
.ao-subtitle{font-size:.7rem;color:rgba(0,180,255,.6);margin-top:1px;letter-spacing:.08em;text-transform:uppercase;}

.ao-steps{display:flex;flex-direction:column;gap:.5rem;position:relative;}
.ao-step{
    display:flex;align-items:center;gap:.7rem;
    padding:.4rem .6rem;border-radius:3px;
    border:1px solid transparent;
    transition:all .3s;
}
.ao-step.wait  {border-color:rgba(255,255,255,.04);background:rgba(255,255,255,.02);}
.ao-step.active{border-color:rgba(0,150,255,.25);background:rgba(0,80,150,.15);}
.ao-step.done  {border-color:rgba(46,125,50,.25);background:rgba(46,125,50,.1);}
.ao-step.done .ao-step-icon{color:#2E7D32;}
.ao-step.active .ao-step-icon{color:#60a5fa;animation:ao-pulse 1s ease-in-out infinite;}
.ao-step.wait .ao-step-icon{color:rgba(255,255,255,.15);}
@keyframes ao-pulse{0%,100%{opacity:1;}50%{opacity:.4;}}
.ao-step-icon{font-size:12px;width:16px;text-align:center;flex-shrink:0;}
.ao-step-text{flex:1;}
.ao-step-label{font-size:.75rem;font-weight:600;color:rgba(255,255,255,.8);}
.ao-step.wait .ao-step-label{color:rgba(255,255,255,.3);}
.ao-step.done .ao-step-label{color:rgba(255,255,255,.6);}
.ao-step-sub{font-size:.66rem;color:rgba(255,255,255,.35);margin-top:1px;}
.ao-step.active .ao-step-sub{color:rgba(100,180,255,.6);}
.ao-step.done .ao-step-sub{color:rgba(46,125,50,.7);}
.ao-step-time{font-size:.65rem;color:rgba(255,255,255,.2);font-family:monospace;flex-shrink:0;}

.ao-bar-wrap{margin-top:1.1rem;position:relative;}
.ao-bar-bg{height:3px;background:rgba(255,255,255,.06);border-radius:2px;overflow:hidden;}
.ao-bar-fill{height:100%;width:0%;background:linear-gradient(90deg,#003366,#C8102E);border-radius:2px;transition:width .6s ease;}
.ao-count{margin-top:.55rem;font-size:.7rem;color:rgba(100,180,255,.5);text-align:center;font-family:monospace;letter-spacing:.06em;min-height:1em;}
.ops-bar{position:absolute;bottom:0;left:var(--panel-w);right:0;background:rgba(0,51,102,.9);color:#fff;display:flex;align-items:center;gap:.75rem;padding:.35rem 1rem;font-size:.72rem;z-index:400;border-top:2px solid var(--red);flex-wrap:wrap;}
.ops-chip{display:flex;align-items:center;gap:.3rem;padding:2px 7px;border-radius:999px;background:rgba(255,255,255,.12);white-space:nowrap;}
.ops-coords{margin-left:auto;font-family:monospace;opacity:.55;font-size:.68rem;}
.sb-toggle{display:none;position:absolute;top:10px;left:10px;z-index:600;background:var(--navy);border:none;color:#fff;width:38px;height:38px;border-radius:6px;cursor:pointer;font-size:1.1rem;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.3);}
@media(max-width:768px){
    .ops-sidebar{position:absolute;left:0;top:0;bottom:0;transform:translateX(-100%);transition:transform .3s;z-index:550;}
    .ops-sidebar.open{transform:translateX(0);}
    .sb-toggle{display:flex;}
    .ops-bar{left:0;}
    /* Roster: full-width bottom sheet on mobile */
    #aprsRoster{
        top:auto !important;right:0 !important;left:0 !important;
        width:100% !important;
        height:220px;bottom:30px !important;
        border-left:none !important;border-top:2px solid #003366;
    }
    /* Overlay panel: smaller on mobile */
    .ao-panel{min-width:260px;max-width:90vw;padding:1.25rem 1.25rem;}
    .ao-title{font-size:.8rem;}
}
.rp{font-family:Arial,sans-serif;font-size:12px;min-width:170px;}
.rp strong{color:#003366;display:block;font-size:13px;margin-bottom:3px;}
.rp-row{display:flex;justify-content:space-between;gap:8px;margin-top:2px;font-size:11px;}
.rp-lbl{color:#9aa3ae;}
.rp-badge{display:inline-block;padding:1px 6px;border-radius:999px;font-size:10px;font-weight:bold;margin-top:4px;}
.rp-warn{font-size:10px;color:#d97706;margin-top:4px;}
</style>
@endpush

@section('content')
<style>.content-wrap{max-width:100% !important;padding:0 !important;}</style>

<div class="ops-shell">
<button class="sb-toggle" onclick="document.getElementById('sb').classList.toggle('open')">☰</button>

<div class="ops-sidebar" id="sb">
    <div class="sb-head">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.7)" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
        <div>
            <div class="sb-head-title">Operational Map</div>
            <div class="sb-head-sub">{{ \App\Helpers\RaynetSetting::groupName() }} · Live layers</div>
        </div>
    </div>

    <div class="sb-body">
        <div class="lg-label">Base map</div>
        <div class="lr on" id="b-osm" onclick="setBase('osm')"><div class="l-dot" style="background:#3b82f6;"></div><div class="l-info"><div class="l-name">Street map</div><div class="l-status">OpenStreetMap</div></div></div>
        <div class="lr" id="b-topo" onclick="setBase('topo')"><div class="l-dot" style="background:#8b5cf6;"></div><div class="l-info"><div class="l-name">Topographic</div><div class="l-status">OpenTopoMap</div></div></div>
        <div class="lr" id="b-sat" onclick="setBase('sat')"><div class="l-dot" style="background:#059669;"></div><div class="l-info"><div class="l-name">Satellite</div><div class="l-status">Esri World Imagery</div></div></div>

        <div class="lg-label">Communications</div>
        <div class="lr" id="lr-aprs"><label class="tog"><input type="checkbox" id="chk-aprs"><span class="tog-t"></span></label><div class="l-dot" style="background:#003366;"></div><div class="l-info"><div class="l-name">APRS stations</div><div class="l-status" id="st-aprs">Off</div></div></div>
        <div class="lr" id="lr-mesh"><label class="tog"><input type="checkbox" id="chk-mesh"><span class="tog-t"></span></label><div class="l-dot" style="background:#7c3aed;"></div><div class="l-info"><div class="l-name">Meshtastic nodes</div><div class="l-status" id="st-mesh">Off</div></div></div>
        <div class="lr" id="lr-cov"><label class="tog"><input type="checkbox" id="chk-cov"><span class="tog-t"></span></label><div class="l-dot" style="background:#C8102E;"></div><div class="l-info"><div class="l-name">RAYNET coverage</div><div class="l-status" id="st-cov">Off · MEARL prediction</div></div></div>

        <div class="lg-label">Environment</div>
        <div class="lr" id="lr-flood"><label class="tog"><input type="checkbox" id="chk-flood"><span class="tog-t"></span></label><div class="l-dot" style="background:#0ea5e9;"></div><div class="l-info"><div class="l-name">Flood alerts</div><div class="l-status" id="st-flood">Off · Environment Agency</div></div></div>
        <div class="lr" id="lr-wx"><label class="tog"><input type="checkbox" id="chk-wx"><span class="tog-t"></span></label><div class="l-dot" style="background:#64748b;"></div><div class="l-info"><div class="l-name">Weather radar</div><div class="l-status" id="st-wx">Off · RainViewer</div></div></div>
        <div class="lr" id="lr-wind"><label class="tog"><input type="checkbox" id="chk-wind"><span class="tog-t"></span></label><div class="l-dot" style="background:#059669;"></div><div class="l-info"><div class="l-name">Wind / gusts</div><div class="l-status" id="st-wind">Off · Open-Meteo</div></div></div>

        <div class="lg-label">Infrastructure</div>
        <div class="lr" id="lr-power"><label class="tog"><input type="checkbox" id="chk-power"><span class="tog-t"></span></label><div class="l-dot" style="background:#f59e0b;"></div><div class="l-info"><div class="l-name">Power outages</div><div class="l-status" id="st-power">Off · Electricity NW</div></div></div>
        <div class="lr" id="lr-roads"><label class="tog"><input type="checkbox" id="chk-roads"><span class="tog-t"></span></label><div class="l-dot" style="background:#dc2626;"></div><div class="l-info"><div class="l-name">Road closures</div><div class="l-status" id="st-roads">Off · Overpass API</div></div></div>
    </div>

    <div class="sb-footer">
        <button class="btn-refresh" id="btnRef" onclick="refreshActive()">↻ Refresh active layers</button>
        <div class="last-ref" id="lastRef">—</div>
    </div>
</div>

<div id="opsMap"></div>

<div id="aprsRoster" style="display:none;position:absolute;top:0;right:0;bottom:30px;width:220px;background:rgba(0,19,38,.88);backdrop-filter:blur(4px);border-left:2px solid #003366;z-index:400;flex-direction:column;overflow:hidden;">
    <div style="background:#003366;padding:.6rem .85rem;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
        <span style="font-size:.8rem;font-weight:bold;color:#fff;letter-spacing:.05em;">APRS ROSTER</span>
        <span id="rosterOnAirCount" style="font-size:.7rem;color:rgba(255,255,255,.5);"></span>
    </div>
    <div id="rosterList" style="flex:1;overflow-y:auto;padding:.3rem 0;"></div>
</div>
<div id="aprsOverlay">
    <div class="ao-panel">
        <!-- Header with radar -->
        <div class="ao-header">
            <div class="ao-radar">
                <div class="ao-radar-bg"></div>
                <div class="ao-radar-ring"></div>
                <div class="ao-radar-ring2"></div>
                <div class="ao-radar-sweep"></div>
                <div class="ao-center-dot"></div>
            </div>
            <div>
                <div class="ao-title">APRS STATIONS</div>
                <div class="ao-subtitle">{{ \App\Helpers\RaynetSetting::groupName() }} &bull; Live feed</div>
            </div>
        </div>

        <!-- Steps -->
        <div class="ao-steps">
            <div class="ao-step wait" id="ao-s1">
                <span class="ao-step-icon">&#9670;</span>
                <div class="ao-step-text">
                    <div class="ao-step-label">Build callsign list</div>
                    <div class="ao-step-sub" id="ao-s1-sub">Loading member callsigns from portal</div>
                </div>
                <span class="ao-step-time" id="ao-t1"></span>
            </div>
            <div class="ao-step wait" id="ao-s2">
                <span class="ao-step-icon">&#9670;</span>
                <div class="ao-step-text">
                    <div class="ao-step-label">Query APRS positions</div>
                    <div class="ao-step-sub" id="ao-s2-sub">Fetching last 6hr from aprs.fi</div>
                </div>
                <span class="ao-step-time" id="ao-t2"></span>
            </div>
            <div class="ao-step wait" id="ao-s3">
                <span class="ao-step-icon">&#9670;</span>
                <div class="ao-step-text">
                    <div class="ao-step-label">Query WX &amp; gateways</div>
                    <div class="ao-step-sub" id="ao-s3-sub">Weather stations &amp; DMR gateways</div>
                </div>
                <span class="ao-step-time" id="ao-t3"></span>
            </div>
            <div class="ao-step wait" id="ao-s4">
                <span class="ao-step-icon">&#9670;</span>
                <div class="ao-step-text">
                    <div class="ao-step-label">Render Markers</div>
                    <div class="ao-step-sub" id="ao-s4-sub">Building map layer</div>
                </div>
                <span class="ao-step-time" id="ao-t4"></span>
            </div>
        </div>

        <!-- Progress bar -->
        <div class="ao-bar-wrap">
            <div class="ao-bar-bg"><div class="ao-bar-fill" id="aoBar"></div></div>
            <div class="ao-count" id="aoCount">&nbsp;</div>
        </div>
    </div>
</div>
<div class="ops-bar">
    <span class="ops-chip">📍 {{ \App\Helpers\RaynetSetting::groupName() }}</span>
    <span class="ops-chip" id="actCount">0 layers active</span>
    <span class="ops-coords" id="coords">Hover map for coordinates</span>
</div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script>
const LIV=[53.4084,-2.9916];
const BASE={
    osm:L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'© OpenStreetMap',maxZoom:19}),
    topo:L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',{attribution:'© OpenTopoMap',maxZoom:17}),
    sat:L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',{attribution:'© Esri',maxZoom:19}),
};
const map=L.map('opsMap',{center:LIV,zoom:11,layers:[BASE.osm]});
map.on('mousemove',e=>document.getElementById('coords').textContent=e.latlng.lat.toFixed(5)+', '+e.latlng.lng.toFixed(5));
let curBase='osm';
function setBase(k){map.removeLayer(BASE[curBase]);map.addLayer(BASE[k]);curBase=k;document.querySelectorAll('[id^="b-"]').forEach(el=>el.classList.remove('on'));const el=document.getElementById('b-'+k);if(el)el.classList.add('on');}
const LAYERS={};const ON={};
function icon(col,r=9){return L.divIcon({html:`<div style="width:${r*2}px;height:${r*2}px;border-radius:50%;background:${col};border:2px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,.4);"></div>`,iconSize:[r*2,r*2],iconAnchor:[r,r],className:''});}
function st(id,cls,txt){const el=document.getElementById('st-'+id);if(!el)return;el.className='l-status '+(cls||'');el.textContent=txt;}
function updateCount(){const n=Object.values(ON).filter(Boolean).length;document.getElementById('actCount').textContent=n+' layer'+(n===1?'':'s')+' active';}
function removeLayer(id){
    if(LAYERS[id]){map.removeLayer(LAYERS[id]);delete LAYERS[id];}
    if(id==='aprs'){
        const r=document.getElementById('aprsRoster');
        if(r)r.style.display='none';
        AO.hide();
    }
}

/* ── APRS Loading Overlay ── */
const AO = {
    t0: 0,
    timer: null,
    step(n, state, sub='', extra='') {
        const el = document.getElementById('ao-s'+n);
        if (!el) return;
        el.className = 'ao-step ' + state;
        // Icon: diamond=waiting, animated=active, checkmark=done
        const icon = el.querySelector('.ao-step-icon');
        if (icon) icon.innerHTML = state==='done' ? '&#10003;' : '&#9670;';
        // Sub-text update (HTML uses ao-s{n}-sub)
        const subEl = document.getElementById('ao-s'+n+'-sub');
        if (sub && subEl) subEl.textContent = sub;
        // Time badge
        const tEl = document.getElementById('ao-t'+n);
        if (extra && tEl) tEl.textContent = extra;
    },
    bar(pct) {
        const b = document.getElementById('aoBar');
        if (b) b.style.width = pct + '%';
    },
    count(txt) {
        const c = document.getElementById('aoCount');
        if (c) c.textContent = txt;
    },
    show() {
        this.t0 = Date.now();
        const o = document.getElementById('aprsOverlay');
        if (o) o.style.display = 'flex';
        // Reset all steps to wait state
        [1,2,3,4].forEach(n => {
            const el = document.getElementById('ao-s'+n);
            if (el) el.className = 'ao-step wait';
            const icon = el && el.querySelector('.ao-step-icon');
            if (icon) icon.innerHTML = '&#9670;';
            const tEl = document.getElementById('ao-t'+n);
            if (tEl) tEl.textContent = '';
        });
        this.bar(0);
        this.count('Initialising...');
        this.timer = setInterval(() => {
            const s = ((Date.now()-this.t0)/1000).toFixed(1);
            this.count('Scanning APRS network... ' + s + 's');
        }, 100);
    },
    hide() {
        clearInterval(this.timer);
        const o = document.getElementById('aprsOverlay');
        if (o) o.style.display = 'none';
    },
    elapsed() {
        return ((Date.now() - this.t0) / 1000).toFixed(1) + 's';
    }
};

async function loadAprs(){
    st('aprs','loading','Loading…');removeLayer('aprs');
    AO.show();
    // Step 1 — connection
    AO.step(1,'active','Loading member callsigns...');
    AO.bar(10);
    try{
        // Fire fetch immediately — no fake delays before it
        const fetchStart = Date.now();
        const fetchPromise = fetch('{{ route("ops-map.aprs") }}',{headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}});

        // Animate steps while fetch is running (time-based, not blocking)
        await new Promise(r => setTimeout(r, 600));
        AO.step(1,'done','Callsign list ready','✓');
        AO.step(2,'active','Querying aprs.fi for position data (last 6hr)...');
        AO.bar(30);

        await new Promise(r => setTimeout(r, 3500));
        AO.step(2,'done','Position data retrieved','✓');
        AO.step(3,'active','Querying weather stations and DMR gateways...');
        AO.bar(60);

        // Wait for actual fetch to complete
        const r = await fetchPromise;
        AO.step(3,'done','All stations retrieved in '+((Date.now()-fetchStart)/1000).toFixed(1)+'s','✓');
        AO.step(4,'active','Rendering markers on map...');
        AO.bar(85);

        const d = await r.json();
        if(d.error) throw new Error(d.error);
        clearInterval(AO.timer);
        const cl=L.markerClusterGroup({maxClusterRadius:50});
        (d.features||[]).forEach(f=>{
            const p=f.properties;
            let col,sz,border,opacity=1,ring='';

            const isWx = p.type === 'wx';
            if(p.isMember){
                if(p.inRadius){
                    col=p.fresh==='green'?'#2E7D32':p.fresh==='amber'?'#d97706':'#9aa3ae';
                }else{
                    col='#ea580c';
                }
                sz=10;
                border='2px solid #fff';
                if(p.fresh==='green'&&p.inRadius){
                    ring=`<div style="position:absolute;top:-4px;left:-4px;width:${sz*2+8}px;height:${sz*2+8}px;border-radius:50%;border:2px solid #2E7D32;opacity:0.4;animation:pulse-ring 2s ease-out infinite;"></div>`;
                }
            }else{
                col='#64748b'; sz=6; border='1px solid rgba(255,255,255,.5)'; opacity=0.65;
            }

            // Weather stations: square icon instead of circle
            const shape = isWx
                ? `<div style="width:${sz*2}px;height:${sz*2}px;background:${col};border:${border};box-shadow:0 1px 5px rgba(0,0,0,.4);opacity:${opacity};border-radius:3px;display:flex;align-items:center;justify-content:center;font-size:${sz}px;line-height:1;">🌧</div>`
                : `<div style="width:${sz*2}px;height:${sz*2}px;border-radius:50%;background:${col};border:${border};box-shadow:0 1px 5px rgba(0,0,0,.4);opacity:${opacity};"></div>`;

            const ic=L.divIcon({
                html:`<div style="position:relative;width:${sz*2}px;height:${sz*2}px;">
                    ${ring}
                    ${shape}
                </div>`,
                iconSize:[sz*2,sz*2],iconAnchor:[sz,sz],className:'',
            });

            const memberBadge=p.isMember
                ? (p.inRadius
                    ? `<span class="rp-badge" style="background:#003366;color:#fff;">RAYNET Member${isWx?' · WX Station':''}</span>`
                    : `<span class="rp-badge" style="background:#ea580c;color:#fff;">RAYNET -- Outside area${isWx?' · WX':''}</span>`)
                : (isWx?'<span class="rp-badge" style="background:#0891b2;color:#fff;">WX Station</span>':'');

            L.marker([f.geometry.coordinates[1],f.geometry.coordinates[0]],{icon:ic})
             .bindPopup(`<div class="rp">
                <strong>${p.call}${p.name&&p.name!==p.call?' -- '+p.name:''}</strong>
                ${memberBadge}
                ${p.title?`<div class="rp-row"><span class="rp-lbl">Role</span><span>${p.title}</span></div>`:''}
                <div class="rp-row"><span class="rp-lbl">Last seen</span><span>${p.age}</span></div>
                <div class="rp-row"><span class="rp-lbl">Distance</span><span>${p.distKm} km from {{ \App\Helpers\RaynetSetting::groupName() }}</span></div>
                ${p.speed>0?`<div class="rp-row"><span class="rp-lbl">Speed</span><span>${Math.round(p.speed)} km/h</span></div>`:''}
                ${p.alt>0?`<div class="rp-row"><span class="rp-lbl">Altitude</span><span>${Math.round(p.alt)} m</span></div>`:''}
                ${p.comment?`<div style="margin-top:4px;font-size:11px;color:#666;">${p.comment}</div>`:''}
             </div>`).addTo(cl);
        });
        LAYERS.aprs=cl;map.addLayer(cl);
        AO.step(4,'done', (d.count||0)+' stations placed on map','✓');
        AO.bar(100);
        const n=d.count||0, m=d.members||0, ir=d.inRadius||0;
        const out=m-ir;
        let label=n+' station'+(n===1?'':'s');
        if(m>0) label+=' · '+ir+' RAYNET in area'+(out>0?' · '+out+' out of area':'');
        AO.count('Done — ' + n + ' stations, ' + m + ' RAYNET members · ' + AO.elapsed());
        st('aprs','live',label);
        buildRoster(d.roster||[]);
        const roster=document.getElementById('aprsRoster');
        if(roster)roster.style.display='flex';
        await new Promise(r=>setTimeout(r,900));
        AO.hide();
    }catch(e){
        AO.hide();
        st('aprs','err','Failed: '+e.message);
    }
}

function buildRoster(roster){
    const onAir=roster.filter(r=>r.onAir).length;
    const total=roster.length;
    document.getElementById('rosterOnAirCount').textContent=onAir+'/'+total+' on air';
    const cols={green:'#2E7D32',amber:'#d97706',grey:'#64748b',offline:'#374151'};
    const list=document.getElementById('rosterList');
    list.innerHTML=roster.map(r=>{
        const col=cols[r.fresh]||cols.offline;
        // Build one row per active station, or single offline row
        const stationRows=(r.stations||[]).map(s=>{
            const sc=cols[s.fresh]||'#64748b';
            const dist=s.inRadius
                ?`<span style="color:#6ee7b7;font-size:10px">${s.distKm}km</span>`
                :`<span style="color:#fb923c;font-size:10px">${s.distKm}km out</span>`;
            const spd=s.speed>0?`<span style="color:#94a3b8;font-size:10px">${Math.round(s.speed)}km/h</span>`:'';
            return `<div style="display:flex;align-items:center;gap:5px;margin-top:3px;padding:2px 4px;background:rgba(255,255,255,.05);border-radius:3px;cursor:pointer"
                onclick="event.stopPropagation();window.open('https://aprs.fi/#!call=${encodeURIComponent(s.callsign)}&timerange=3600','_blank')">
                <div style="width:6px;height:6px;border-radius:50%;background:${sc};flex-shrink:0;"></div>
                <span style="font-size:10px;font-weight:bold;color:#cbd5e1;font-family:monospace;">${s.callsign}</span>
                <span style="font-size:10px;color:${sc};">${s.age}</span>
                ${dist}${spd}
            </div>`;
        }).join('');

        return `<div style="padding:.4rem .75rem;border-bottom:1px solid rgba(255,255,255,.07);">
            <div style="display:flex;align-items:center;gap:.4rem;">
                <div style="width:8px;height:8px;border-radius:50%;flex-shrink:0;background:${col};${r.fresh==='green'?'box-shadow:0 0 0 2px rgba(46,125,50,.35);':''}"></div>
                <span style="font-size:.78rem;font-weight:bold;color:#e2e8f0;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${r.name}</span>
                ${!r.onAir?'<span style="font-size:9px;color:#4b5563;font-family:monospace;">'+r.callsign+'</span>':''}
            </div>
            ${stationRows||`<div style="margin-top:2px;margin-left:13px;font-size:10px;color:#374151;">Not on air</div>`}
        </div>`;
    }).join('');
}

async function loadMesh(){
    st('mesh','loading','Loading…');removeLayer('mesh');
    try{
        const r=await fetch('{{ route("ops-map.meshtastic") }}',{headers:{'Accept':'application/json'}});
        const d=await r.json();
        if(d.error){st('mesh','err',d.error.substring(0,50));return;}
        const cl=L.markerClusterGroup({maxClusterRadius:60});
        (d.features||[]).forEach(f=>{
            const p=f.properties;
            L.marker([f.geometry.coordinates[1],f.geometry.coordinates[0]],{icon:icon('#7c3aed')})
             .bindPopup(`<div class="rp"><strong>📻 ${p.name}</strong>
                ${p.short?`<div class="rp-row"><span class="rp-lbl">Short</span><span>${p.short}</span></div>`:''}
                ${p.hw?`<div class="rp-row"><span class="rp-lbl">Hardware</span><span>${p.hw}</span></div>`:''}
                <div class="rp-row"><span class="rp-lbl">Last seen</span><span>${p.lastSeen}</span></div>
             </div>`).addTo(cl);
        });
        LAYERS.mesh=cl;map.addLayer(cl);
        const n=d.count||0;
        st('mesh',n>0?'live':'err',n>0?n+' nodes (UK)':'No UK nodes found · src: '+(d.source||'?').split('/').pop());
    }catch(e){st('mesh','err','Failed: '+e.message);}
}

function loadCov(){
    st('cov','loading','Loading…');removeLayer('cov');
    fetch('{{ route("ops-map.coverage") }}',{headers:{'Accept':'application/json'}})
    .then(r=>r.json()).then(d=>{
        const g=L.layerGroup();
        (d.sites||[]).forEach(s=>{
            L.circle([s.lat,s.lng],{radius:s.radiusKm*1000,color:s.colour,fillColor:s.colour,fillOpacity:0.07,weight:2,dashArray:'6 4'})
             .bindPopup(`<div class="rp"><strong>📡 ${s.name}</strong>
                <div class="rp-row"><span class="rp-lbl">Frequency</span><span>${s.freq}</span></div>
                <div class="rp-row"><span class="rp-lbl">Range</span><span>~${s.radiusKm} km</span></div>
                <div class="rp-warn">⚠ Approximate — replace with MEARL data</div>
             </div>`).addTo(g);
            L.marker([s.lat,s.lng],{icon:icon(s.colour,7)}).addTo(g);
        });
        LAYERS.cov=g;map.addLayer(g);
        st('cov','err',(d.sites||[]).length+' sites · '+d.note);
    }).catch(e=>st('cov','err','Failed'));
}

async function loadFlood(){
    st('flood','loading','Loading…');removeLayer('flood');
    try{
        const r=await fetch('{{ route("ops-map.flood") }}',{headers:{'Accept':'application/json'}});
        const d=await r.json();
        if(d.error)throw new Error(d.error);
        const g=L.layerGroup();
        const sc={0:'#0ea5e9',1:'#64748b',2:'#f59e0b',3:'#ef4444',4:'#7c2d12'};
        (d.features||[]).forEach(f=>{
            const p=f.properties;
            L.marker([f.geometry.coordinates[1],f.geometry.coordinates[0]],{icon:icon(sc[p.severity||0]||'#0ea5e9',8)})
             .bindPopup(`<div class="rp"><strong>💧 ${p.label}</strong>
                ${p.river?`<div class="rp-row"><span class="rp-lbl">River</span><span>${p.river}</span></div>`:''}
                ${p.town?`<div class="rp-row"><span class="rp-lbl">Town</span><span>${p.town}</span></div>`:''}
                <span class="rp-badge" style="background:${sc[p.severity||0]};color:#fff;">${p.sevLabel}</span>
             </div>`).addTo(g);
        });
        LAYERS.flood=g;map.addLayer(g);
        st('flood','live',(d.stationCount||0)+' stations · '+(d.alertCount||0)+' alerts');
    }catch(e){st('flood','err','Failed: '+e.message);}
}

async function loadWx(){
    st('wx','loading','Loading…');removeLayer('wx');
    try{
        const r=await fetch('{{ route("ops-map.weather") }}',{headers:{'Accept':'application/json'}});
        const d=await r.json();
        if(d.error)throw new Error(d.error);
        const frames=d.radar?.past||[];
        if(!frames.length)throw new Error('No frames');
        const latest=frames[frames.length-1];
        const tl=L.tileLayer(`https://tilecache.rainviewer.com${latest.path}/256/{z}/{x}/{y}/2/1_1.png`,{opacity:0.55,attribution:'RainViewer',maxZoom:18,maxNativeZoom:6,minZoom:3});
        LAYERS.wx=tl;map.addLayer(tl);
        const t=new Date(latest.time*1000);
        st('wx','live','Radar · '+t.toLocaleTimeString('en-GB',{hour:'2-digit',minute:'2-digit'}));
    }catch(e){st('wx','err','Failed: '+e.message);}
}

async function loadWind(){
    st('wind','loading','Loading…');removeLayer('wind');
    try{
        const r=await fetch('{{ route("ops-map.wind") }}',{headers:{'Accept':'application/json'}});
        const d=await r.json();
        if(d.error)throw new Error(d.error);
        const c=d.current||{};
        const spd=Math.round(c.wind_speed_10m||0),gst=Math.round(c.wind_gusts_10m||0),dir=Math.round(c.wind_direction_10m||0),temp=Math.round(c.temperature_2m||0);
        const col=spd<15?'#059669':spd<30?'#d97706':'#dc2626';
        const ic=L.divIcon({html:`<div style="text-align:center;font-family:Arial,sans-serif;"><div style="transform:rotate(${dir}deg);font-size:24px;line-height:1;color:${col};">↑</div><div style="font-size:10px;font-weight:bold;color:${col};white-space:nowrap;margin-top:-2px;">${spd} mph</div><div style="font-size:9px;color:#666;">${temp}°C</div></div>`,iconAnchor:[20,16],className:''});
        const g=L.layerGroup();
        L.marker(LIV,{icon:ic}).bindPopup(`<div class="rp"><strong>🌬 Wind — Liverpool</strong><div class="rp-row"><span class="rp-lbl">Speed</span><span>${spd} mph</span></div><div class="rp-row"><span class="rp-lbl">Gusts</span><span>${gst} mph</span></div><div class="rp-row"><span class="rp-lbl">Direction</span><span>${dir}°</span></div><div class="rp-row"><span class="rp-lbl">Temp</span><span>${temp}°C</span></div></div>`).addTo(g);
        LAYERS.wind=g;map.addLayer(g);
        st('wind','live',`${spd} mph · gusts ${gst} mph · ${dir}°`);
    }catch(e){st('wind','err','Failed: '+e.message);}
}

async function loadPower(){
    st('power','loading','Loading…');removeLayer('power');
    try{
        const r=await fetch('{{ route("ops-map.power") }}',{headers:{'Accept':'application/json'}});
        const d=await r.json();
        const g=L.layerGroup();
        (d.features||[]).forEach(f=>{
            const p=f.properties;
            L.marker([f.geometry.coordinates[1],f.geometry.coordinates[0]],{icon:icon('#f59e0b')})
             .bindPopup(`<div class="rp"><strong>⚡ Power outage</strong><div class="rp-row"><span class="rp-lbl">Area</span><span>${p.area}</span></div><div class="rp-row"><span class="rp-lbl">Homes</span><span>${p.homes||'Unknown'}</span></div>${p.eta?`<div class="rp-row"><span class="rp-lbl">ETA</span><span>${p.eta}</span></div>`:''}</div>`).addTo(g);
        });
        LAYERS.power=g;map.addLayer(g);
        const n=d.count||0;
        st('power',n?'live':'err',n?n+' incidents':(d.note?'No public API':'No incidents'));
    }catch(e){st('power','err','Failed: '+e.message);}
}

async function loadRoads(){
    st('roads','loading','Loading…');removeLayer('roads');
    try{
        const q=`[out:json][timeout:20];(way["highway"]["construction"](53.2,-3.2,53.6,-2.6);way["highway"="construction"](53.2,-3.2,53.6,-2.6););out center;`;
        const r=await fetch('https://overpass-api.de/api/interpreter',{method:'POST',body:'data='+encodeURIComponent(q)});
        const text=await r.text();
        if(!r.ok||!text.trim().startsWith('{'))throw new Error('Overpass error — try again shortly');
        const d=JSON.parse(text);
        const g=L.layerGroup();
        (d.elements||[]).forEach(el=>{
            if(!el.center)return;
            L.marker([el.center.lat,el.center.lon],{icon:icon('#dc2626',8)})
             .bindPopup(`<div class="rp"><strong>🚧 Road works</strong><div class="rp-row"><span class="rp-lbl">Name</span><span>${el.tags?.name||'Unknown road'}</span></div><div class="rp-row"><span class="rp-lbl">Type</span><span>${el.tags?.highway||'Road'}</span></div></div>`).addTo(g);
        });
        LAYERS.roads=g;map.addLayer(g);
        const n=(d.elements||[]).length;
        st('roads','live',n?n+' road works (OSM)':'No works found');
    }catch(e){st('roads','err',e.message);}
}

const LOADERS={aprs:loadAprs,mesh:loadMesh,cov:loadCov,flood:loadFlood,wx:loadWx,wind:loadWind,power:loadPower,roads:loadRoads};

function setLayer(id,on){
    ON[id]=on;
    const chk=document.getElementById('chk-'+id);
    const row=document.getElementById('lr-'+id);
    if(chk)chk.checked=on;
    if(row)row.classList.toggle('on',on);
    if(on){LOADERS[id]&&LOADERS[id]();}else{removeLayer(id);st(id,'','Off');}
    updateCount();
}

Object.keys(LOADERS).forEach(id=>{
    const chk=document.getElementById('chk-'+id);
    const row=document.getElementById('lr-'+id);
    if(chk)chk.addEventListener('change',()=>setLayer(id,chk.checked));
    if(row)row.addEventListener('click',e=>{if(e.target.tagName==='INPUT')return;setLayer(id,!ON[id]);});
});

async function refreshActive(){
    const btn=document.getElementById('btnRef');
    btn.disabled=true;btn.textContent='↻ Refreshing…';
    for(const id of Object.keys(ON).filter(k=>ON[k])){if(LOADERS[id])await LOADERS[id]();}
    btn.disabled=false;btn.textContent='↻ Refresh active layers';
    document.getElementById('lastRef').textContent='Refreshed '+new Date().toLocaleTimeString('en-GB',{hour:'2-digit',minute:'2-digit'});
}
</script>
@endpush