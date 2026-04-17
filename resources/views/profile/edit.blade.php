@extends('layouts.app')
@section('title', 'My Profile')
@section('content')

@php
    $userName        = $user->name;
    $userEmail       = $user->email;
    $userCallsign    = $user->callsign;
    $userDmrId       = $user->dmr_id;
    $pendingCallsign = $user->pending_callsign;
    $userLicence     = $user->licence_class;
    $userRole        = $user->role;
    $userLevel       = $user->level;
    $userStatus      = $user->status;
    $userPhone       = $user->phone;
    $userJoined      = $user->joined_at;
    $userNotes       = $user->notes;
    $isOperator      = !empty($userRole);
    $levelLabels = [
        1 => 'Operator',
        2 => 'Advanced Operator',
        3 => 'Specialist',
        4 => 'Team Leader',
        5 => 'Instructor',
    ];
    $levelLabel = $userLevel !== null ? ($levelLabels[$userLevel] ?? 'Level ' . $userLevel) : null;
    $statusColours = [
        'Active'    => ['dot'=>'#22d47d','glow'=>'rgba(34,212,125,.5)','bg'=>'rgba(34,212,125,.08)','border'=>'rgba(34,212,125,.3)','text'=>'#086c3a'],
        'Standby'   => ['dot'=>'#fbbf24','glow'=>'rgba(251,191,36,.4)','bg'=>'rgba(251,191,36,.08)','border'=>'rgba(251,191,36,.3)','text'=>'#7a5000'],
        'Inactive'  => ['dot'=>'#64748b','glow'=>'none','bg'=>'rgba(100,116,139,.08)','border'=>'rgba(100,116,139,.3)','text'=>'#4a5568'],
        'Suspended' => ['dot'=>'#f87171','glow'=>'rgba(248,113,113,.4)','bg'=>'rgba(248,113,113,.08)','border'=>'rgba(248,113,113,.3)','text'=>'#c0392b'],
    ];
    $sc = $statusColours[$userStatus ?? ''] ?? null;
    $licenceConfig = [
        'Foundation'   => ['bg'=>'#fdf8ec','border'=>'#f5d87a','text'=>'#8a5500','label'=>'Foundation Licence','desc'=>'Entry-level amateur licence','dot'=>'#c49a00','icon'=>'📻','slug'=>'foundation'],
        'Intermediate' => ['bg'=>'#e8eef5','border'=>'rgba(0,51,102,.3)','text'=>'#003366','label'=>'Intermediate Licence','desc'=>'Intermediate amateur licence','dot'=>'#003366','icon'=>'🎛️','slug'=>'intermediate'],
        'Full'         => ['bg'=>'#eef7f2','border'=>'#b8ddc9','text'=>'#1a6b3c','label'=>'Full Licence','desc'=>'Full amateur licence holder','dot'=>'#1a6b3c','icon'=>'📡','slug'=>'full'],
    ];
    $lc = $userLicence ? ($licenceConfig[$userLicence] ?? null) : null;
    $initials = collect(explode(' ', $userName))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->implode('');
@endphp

<style>
:root {
    --navy:       #003366;
    --navy-mid:   #004080;
    --navy-faint: #e8eef5;
    --red:        #C8102E;
    --red-faint:  #fdf0f2;
    --teal:       #0288d1;
    --teal-light: #e1f5fe;
    --green:      #1a6b3c;
    --green-bg:   #eef7f2;
    --amber:      #8a5500;
    --amber-bg:   #fdf8ec;
    --purple:     #5b21b6;
    --purple-bg:  #f5f3ff;
    --grey:       #dde2e8;
    --light:      #f2f5f9;
    --white:      #fff;
    --text:       #001f40;
    --text-mid:   #2d4a6b;
    --muted:      #6b7f96;
    --shadow-sm:  0 2px 8px rgba(0,51,102,.07);
    --shadow-md:  0 6px 20px rgba(0,51,102,.12);
    --transition: all .2s ease;
    --font:       Arial,"Helvetica Neue",Helvetica,sans-serif;
}
*,*::before,*::after { box-sizing:border-box; margin:0; padding:0; }
html { scroll-behavior:smooth; }
body { background:var(--light); color:var(--text); font-family:var(--font); font-size:15px; line-height:1.55; min-height:100vh; }
.wrap { max-width:1180px; margin:0 auto; padding:0 1rem 3rem; }
.topbar { display:flex; align-items:center; justify-content:space-between; padding:1rem 0; border-bottom:2px solid var(--navy); margin-bottom:0; gap:1rem; flex-wrap:wrap; }
.brand { display:flex; align-items:center; gap:.8rem; }
.brand-badge { width:40px; height:40px; background:var(--navy); color:white; display:flex; align-items:center; justify-content:center; font-size:1.4rem; border-radius:8px; }
.brand-name { font-size:1.25rem; font-weight:bold; color:var(--navy); }
.brand-sub { font-size:.8rem; color:var(--muted); }
.back-btn { display:inline-flex; align-items:center; gap:.4rem; padding:.45rem 1rem; border:1px solid var(--grey); border-radius:8px; background:white; color:var(--muted); font-size:.88rem; text-decoration:none; transition:var(--transition); }
.back-btn:hover { border-color:var(--navy); color:var(--navy); }
.profile-hero { background:var(--navy); padding:2rem 2rem 4rem; position:relative; overflow:hidden; margin-bottom:0; }
.profile-hero::before { content:''; position:absolute; inset:0; background:repeating-linear-gradient(-45deg,transparent,transparent 20px,rgba(255,255,255,.02) 20px,rgba(255,255,255,.02) 21px); }
.profile-hero::after { content:''; position:absolute; bottom:0; left:0; right:0; height:3px; background:linear-gradient(90deg,var(--red) 0%,var(--red) 40%,rgba(200,16,46,0) 100%); }
.hero-inner { position:relative; z-index:1; }
.hero-top { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; }
.hero-brand { display:flex; align-items:center; gap:.6rem; }
.hero-brand-name { font-size:.8rem; font-weight:bold; color:rgba(255,255,255,.6); letter-spacing:.08em; text-transform:uppercase; }
.hero-badge { font-size:.7rem; color:rgba(255,255,255,.5); border:1px solid rgba(255,255,255,.18); border-radius:999px; padding:.2rem .7rem; letter-spacing:.05em; }
.hero-body { display:flex; align-items:flex-end; gap:1.4rem; flex-wrap:wrap; }
.hero-avatar { width:76px; height:76px; border-radius:50%; background:var(--red); border:3px solid rgba(255,255,255,.2); display:flex; align-items:center; justify-content:center; font-size:2rem; font-weight:bold; color:#fff; flex-shrink:0; }
.hero-name { font-size:1.6rem; font-weight:bold; color:#fff; line-height:1.2; margin-bottom:.45rem; }
.hero-chips { display:flex; align-items:center; gap:.5rem; flex-wrap:wrap; }
.chip-callsign { font-family:monospace; font-size:.8rem; font-weight:bold; color:#fff; background:rgba(2,136,209,.45); border:1px solid rgba(2,136,209,.55); border-radius:5px; padding:.15rem .55rem; letter-spacing:.08em; }
.chip-role { font-size:.72rem; font-weight:bold; color:#ffb3be; background:rgba(200,16,46,.28); border:1px solid rgba(200,16,46,.4); border-radius:4px; padding:.15rem .55rem; text-transform:uppercase; letter-spacing:.05em; }
.chip-level { font-size:.72rem; color:rgba(255,255,255,.7); background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.18); border-radius:4px; padding:.15rem .55rem; }
.hero-status { display:flex; align-items:center; gap:.4rem; font-size:.75rem; color:rgba(255,255,255,.55); }
.hero-sdot { width:7px; height:7px; border-radius:50%; }
.layout { display:grid; grid-template-columns:1fr; gap:1.5rem; }
@media(min-width:820px) { .layout { grid-template-columns:1fr 290px; } }
.card { background:white; border:1px solid var(--grey); border-radius:12px; overflow:hidden; box-shadow:var(--shadow-sm); margin-bottom:1.2rem; }
.card:first-child { border-radius:0 0 12px 12px; }
.card:last-of-type { margin-bottom:0; }
.card-head { display:flex; align-items:center; gap:.75rem; padding:.75rem 1.2rem; background:var(--light); border-bottom:1px solid var(--grey); }
.card-head-icon { width:32px; height:32px; border-radius:8px; background:var(--navy-faint); border:1px solid rgba(0,51,102,.15); display:flex; align-items:center; justify-content:center; font-size:.95rem; flex-shrink:0; }
.card-head h2 { font-size:.72rem; font-weight:bold; color:var(--navy); text-transform:uppercase; letter-spacing:.1em; }
.card-head p { font-size:.78rem; color:var(--muted); margin-top:.1rem; }
.card-body { padding:1.3rem; }
.toast-ok,.toast-err { display:flex; align-items:flex-start; gap:.8rem; padding:.75rem 1rem; margin-bottom:1.2rem; font-size:.88rem; font-weight:bold; border:1px solid; border-left:3px solid; border-radius:0 8px 8px 0; }
.toast-ok { background:var(--green-bg); border-color:#b8ddc9; border-left-color:var(--green); color:var(--green); }
.toast-err { background:var(--red-faint); border-color:rgba(200,16,46,.25); border-left-color:var(--red); color:var(--red); }
.toast-err ul { margin:.4rem 0 0 1.2rem; padding:0; list-style:disc; }
.toast-err li { margin:.2rem 0; font-weight:normal; }
.field { display:flex; flex-direction:column; gap:.4rem; margin-bottom:1.1rem; }
.field:last-of-type { margin-bottom:0; }
.field label { font-size:.7rem; font-weight:bold; color:var(--muted); text-transform:uppercase; letter-spacing:.1em; }
.field-note { font-size:.75rem; color:var(--muted); margin-top:.2rem; }
.field-note.warn { color:var(--amber); }
.input-wrap { position:relative; }
.input-icon { position:absolute; left:.8rem; top:50%; transform:translateY(-50%); font-size:.9rem; color:var(--muted); pointer-events:none; }
.field input { width:100%; padding:.6rem .8rem .6rem 2.1rem; border:1.5px solid var(--grey); border-radius:8px; font-size:.92rem; background:white; color:var(--text); transition:var(--transition); font-family:var(--font); }
.field input:focus { border-color:var(--teal); box-shadow:0 0 0 3px rgba(2,136,209,.1); outline:none; }
.field input:disabled { background:var(--light); color:var(--muted); cursor:not-allowed; }
#callsign { font-family:monospace; text-transform:uppercase; letter-spacing:.05em; }
#dmr_id { font-family:monospace; letter-spacing:.05em; }
.approved-tag { display:inline-flex; align-items:center; gap:.35rem; padding:.18rem .6rem; background:var(--green-bg); border:1px solid #b8ddc9; border-radius:5px; font-size:.8rem; font-weight:bold; color:var(--green); margin-bottom:.4rem; }
.approved-tag span { font-size:.75rem; color:var(--muted); font-weight:normal; }
.pending-banner { display:flex; align-items:flex-start; gap:.5rem; padding:.6rem .85rem; background:var(--amber-bg); border:1px solid #f5d87a; border-left:3px solid #c49a00; border-radius:0 6px 6px 0; margin-top:.4rem; font-size:.82rem; color:var(--amber); }
#callsign.cs-valid { border-color: #16a34a !important; box-shadow: 0 0 0 3px rgba(22,163,74,.1) !important; }
#callsign.cs-invalid { border-color: #C8102E !important; box-shadow: 0 0 0 3px rgba(200,16,46,.08) !important; }
.cs-feedback { display: none; align-items: center; gap: .4rem; font-size: .72rem; font-weight: bold; margin-top: .2rem; padding: .3rem .6rem; border: 1px solid; border-radius: 5px; }
.cs-feedback.show { display: flex; }
.cs-feedback.ok  { background: var(--green-bg); border-color: #b8ddc9; color: var(--green); }
.cs-feedback.err { background: var(--red-faint); border-color: rgba(200,16,46,.2); color: var(--red); }
.cs-help { font-size: .7rem; color: var(--muted); margin-top: .2rem; line-height: 1.55; }
.cs-help a { color: var(--navy); font-weight: bold; text-decoration: none; }
.cs-help a:hover { text-decoration: underline; }
@keyframes qrzSpin { to { transform: rotate(360deg); } }
.licence-block { display:flex; align-items:center; gap:.85rem; padding:.85rem 1rem; border:1px solid; border-left-width:3px; border-radius:8px; }
.lic-icon { width:34px; height:34px; display:flex; align-items:center; justify-content:center; font-size:1rem; flex-shrink:0; background:white; border:1px solid; border-radius:6px; }
.lic-info { flex:1; min-width:0; }
.lic-name { font-size:.88rem; font-weight:bold; }
.lic-desc { font-size:.75rem; opacity:.7; margin-top:1px; }
.lic-pill { font-size:.68rem; font-weight:bold; text-transform:uppercase; letter-spacing:.08em; padding:.18rem .55rem; border:1px solid; border-radius:4px; flex-shrink:0; }
.dmr-panel { margin-top:.9rem; background:var(--navy); border-radius:8px; overflow:hidden; }
.dmr-panel-top { display:flex; align-items:center; justify-content:space-between; padding:.85rem 1.1rem; }
.dmr-label { font-size:.65rem; font-weight:bold; text-transform:uppercase; letter-spacing:.1em; color:rgba(255,255,255,.4); margin-bottom:2px; }
.dmr-value { font-family:monospace; font-size:1.1rem; font-weight:bold; color:white; letter-spacing:.08em; }
.dmr-live-strip { border-top:1px solid rgba(255,255,255,.08); padding:.75rem 1.1rem; display:grid; grid-template-columns:1fr 1fr; gap:.75rem; }
.dmr-live-block { display:flex; flex-direction:column; gap:3px; }
.dmr-live-label { font-size:.6rem; font-weight:bold; text-transform:uppercase; letter-spacing:.12em; color:rgba(255,255,255,.3); }
.dmr-live-value { font-size:.88rem; font-weight:bold; color:white; display:flex; align-items:center; gap:.4rem; }
.dmr-live-tg { display:inline-flex; align-items:center; gap:.3rem; background:rgba(91,33,182,.4); border:1px solid rgba(91,33,182,.5); border-radius:4px; padding:.12rem .5rem; font-size:.78rem; font-weight:bold; color:#c4b5fd; font-family:monospace; letter-spacing:.04em; }
.dmr-heard-row { display:flex; align-items:center; gap:.35rem; }
.dmr-heard-dot { width:7px; height:7px; border-radius:50%; flex-shrink:0; }
.dmr-heard-dot.active { background:#22d47d; box-shadow:0 0 0 3px rgba(34,212,125,.25); animation:dmrPulse 2s ease infinite; }
.dmr-heard-dot.recent { background:#fbbf24; }
.dmr-heard-dot.stale  { background:rgba(255,255,255,.2); }
.dmr-heard-text { font-size:.82rem; font-weight:bold; color:rgba(255,255,255,.7); }
@keyframes dmrPulse { 0%,100% { box-shadow:0 0 0 3px rgba(34,212,125,.25); } 50% { box-shadow:0 0 0 6px rgba(34,212,125,.08); } }
.dmr-loading-shimmer { height:14px; background:rgba(255,255,255,.06); border-radius:4px; width:70%; animation:shimmer 1.5s ease infinite; }
@keyframes shimmer { 0%,100% { opacity:.4; } 50% { opacity:1; } }
.dmr-live-footer { padding:.5rem 1.1rem; border-top:1px solid rgba(255,255,255,.06); display:flex; align-items:center; justify-content:space-between; font-size:.65rem; color:rgba(255,255,255,.25); }
.dmr-live-footer a { color:rgba(255,255,255,.4); text-decoration:none; border-bottom:1px solid rgba(255,255,255,.15); transition:color .15s; }
.dmr-live-footer a:hover { color:rgba(255,255,255,.7); }
.info-note { padding:.55rem .85rem; font-size:.75rem; color:var(--navy); background:var(--navy-faint); border:1px solid rgba(0,51,102,.18); border-left:3px solid var(--navy); border-radius:0 6px 6px 0; margin-top:.9rem; }
.status-banner { display:flex; align-items:center; gap:.6rem; padding:.6rem .9rem; margin-bottom:1rem; font-size:.88rem; border:1px solid; border-left-width:3px; border-radius:0 8px 8px 0; }
.sbdot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
.op-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:.75rem; }
.op-tile { background:var(--light); border:1px solid var(--grey); border-radius:8px; padding:.75rem 1rem; }
.op-tile-label { font-size:.65rem; font-weight:bold; text-transform:uppercase; letter-spacing:.12em; color:var(--muted); margin-bottom:.2rem; }
.op-tile-value { font-size:.95rem; font-weight:bold; color:var(--navy); }
.op-tile-sub { font-size:.72rem; color:var(--muted); margin-top:2px; }
.level-bar-wrap { margin-top:.85rem; padding:.85rem 1rem; background:var(--light); border:1px solid var(--grey); border-radius:8px; }
.level-bar-header { display:flex; justify-content:space-between; align-items:baseline; margin-bottom:.5rem; }
.level-bar-title { font-size:.68rem; font-weight:bold; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); }
.level-bar-value { font-size:.82rem; font-weight:bold; color:var(--navy); }
.level-bar-track { height:5px; background:var(--grey); border-radius:999px; overflow:hidden; }
.level-bar-fill { height:100%; background:var(--navy); border-radius:999px; transition:width .6s ease; }
.notes-block { margin-top:.85rem; padding:.8rem 1rem; background:var(--light); border:1px solid var(--grey); border-left:3px solid var(--navy); border-radius:0 8px 8px 0; }
.notes-label { font-size:.65rem; font-weight:bold; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); margin-bottom:.3rem; }
.no-op-notice { padding:2rem; text-align:center; color:var(--muted); font-size:.92rem; }
.form-actions { display:flex; align-items:center; gap:1rem; flex-wrap:wrap; margin-top:1.2rem; padding-top:1rem; border-top:1px solid var(--grey); }
.btn-save { padding:.55rem 1.4rem; border:none; border-radius:8px; background:var(--navy); color:white; font-size:.9rem; font-weight:bold; cursor:pointer; font-family:var(--font); letter-spacing:.04em; transition:var(--transition); }
.btn-save:hover { background:var(--navy-mid); transform:translateY(-1px); box-shadow:0 4px 14px rgba(0,51,102,.2); }
.pwd-link { font-size:.88rem; color:var(--red); text-decoration:none; font-weight:bold; }
.pwd-link:hover { text-decoration:underline; }
.snap-card { background:white; border:1px solid var(--grey); border-radius:12px; overflow:hidden; box-shadow:var(--shadow-md); position:sticky; top:1rem; }
.snap-header { background:var(--navy); padding:1.5rem 1.1rem 1.2rem; display:flex; flex-direction:column; align-items:center; gap:.55rem; position:relative; overflow:hidden; }
.snap-header::before { content:''; position:absolute; inset:0; background:repeating-linear-gradient(-45deg,transparent,transparent 18px,rgba(255,255,255,.02) 18px,rgba(255,255,255,.02) 19px); }
.snap-header::after { content:''; position:absolute; bottom:0; left:0; right:0; height:2px; background:var(--red); }
.snap-avatar { width:60px; height:60px; background:var(--red); border-radius:50%; border:3px solid rgba(255,255,255,.2); display:flex; align-items:center; justify-content:center; font-size:1.5rem; font-weight:bold; color:#fff; position:relative; z-index:1; }
.snap-name { font-size:1rem; font-weight:bold; color:#fff; text-align:center; position:relative; z-index:1; }
.snap-callsign { font-family:monospace; font-size:.82rem; font-weight:bold; color:#fff; background:rgba(2,136,209,.4); border:1px solid rgba(2,136,209,.5); border-radius:4px; padding:.15rem .5rem; letter-spacing:.08em; position:relative; z-index:1; }
.snap-lic { font-size:.68rem; font-weight:bold; text-transform:uppercase; letter-spacing:.1em; padding:.18rem .6rem; border:1px solid; border-radius:4px; position:relative; z-index:1; }
.snap-lic-foundation   { background:var(--amber-bg); border-color:#f5d87a; color:var(--amber); }
.snap-lic-intermediate { background:rgba(255,255,255,.08); border-color:rgba(255,255,255,.25); color:rgba(255,255,255,.85); }
.snap-lic-full         { background:var(--green-bg); border-color:#b8ddc9; color:var(--green); }
.snap-role  { font-size:.68rem; font-weight:bold; text-transform:uppercase; letter-spacing:.06em; padding:.18rem .6rem; background:rgba(200,16,46,.28); border:1px solid rgba(200,16,46,.4); border-radius:4px; color:#ffb3be; position:relative; z-index:1; }
.snap-level { font-size:.68rem; color:rgba(255,255,255,.65); background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.18); border-radius:4px; padding:.18rem .6rem; position:relative; z-index:1; }
.snap-status-row { display:flex; align-items:center; gap:.4rem; font-size:.72rem; color:rgba(255,255,255,.5); position:relative; z-index:1; }
.snap-sdot { width:6px; height:6px; border-radius:50%; }
.snap-dmr-live { background:var(--navy); margin:.5rem .75rem .25rem; border-radius:8px; overflow:hidden; position:relative; z-index:1; }
.snap-dmr-live-inner { padding:.6rem .85rem; display:grid; grid-template-columns:1fr 1fr; gap:.5rem; }
.snap-dmr-live-label { font-size:.58rem; font-weight:bold; text-transform:uppercase; letter-spacing:.1em; color:rgba(255,255,255,.3); margin-bottom:2px; }
.snap-dmr-live-val { font-size:.8rem; font-weight:bold; color:white; display:flex; align-items:center; gap:.3rem; }
.snap-dmr-tg { background:rgba(91,33,182,.4); border:1px solid rgba(91,33,182,.5); border-radius:3px; padding:.1rem .4rem; font-size:.72rem; font-weight:bold; color:#c4b5fd; font-family:monospace; }
.snap-dmr-dot { width:6px; height:6px; border-radius:50%; flex-shrink:0; }
.snap-dmr-dot.active { background:#22d47d; box-shadow:0 0 0 2px rgba(34,212,125,.25); animation:dmrPulse 2s ease infinite; }
.snap-dmr-dot.recent { background:#fbbf24; }
.snap-dmr-dot.stale  { background:rgba(255,255,255,.2); }
.snap-dmr-loading { height:10px; background:rgba(255,255,255,.06); border-radius:3px; animation:shimmer 1.5s ease infinite; }
.snap-row { display:flex; justify-content:space-between; align-items:baseline; padding:.55rem 1rem; border-bottom:1px solid var(--grey); }
.snap-row:last-child { border-bottom:none; }
.snap-dt { font-size:.65rem; font-weight:bold; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); }
.snap-dd { font-size:.85rem; font-weight:bold; text-align:right; }
.snap-dd.mono  { font-family:monospace; letter-spacing:.05em; }
.snap-dd.muted { color:var(--muted); font-weight:normal; }
.snap-dd.amber { color:var(--amber); }
.snap-foot { padding:.8rem 1rem; background:var(--light); border-top:1px solid var(--grey); font-size:.75rem; color:var(--muted); text-align:center; line-height:1.5; }
.training-card { margin-bottom: 0; }
.training-section-label { font-size: .65rem; font-weight: bold; text-transform: uppercase; letter-spacing: .14em; color: var(--muted); display: flex; align-items: center; gap: .5rem; margin-bottom: .85rem; margin-top: 1.1rem; }
.training-section-label:first-of-type { margin-top: 0; }
.training-section-label::before { content: ''; width: 12px; height: 2px; background: var(--red); display: inline-block; flex-shrink: 0; }
.training-section-label::after { content: ''; flex: 1; height: 1px; background: var(--grey); display: inline-block; }
.hex-row { display: flex; flex-wrap: wrap; gap: .75rem; margin-bottom: .5rem; }
.hex-wrap { display: flex; flex-direction: column; align-items: center; gap: .4rem; width: 72px; position: relative; }
.hex { position: relative; width: 56px; height: 56px; cursor: default; }
.hex svg { width: 56px; height: 56px; display: block; }
.hex-num { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; font-weight: bold; z-index: 1; transition: var(--transition); }
.hex-label { font-size: .6rem; font-weight: bold; text-align: center; line-height: 1.3; color: var(--muted); max-width: 72px; text-transform: uppercase; letter-spacing: .04em; transition: var(--transition); }
.hex-wrap.locked   .hex-num { color: rgba(0,0,0,.18); }
.hex-wrap.locked   .hex-label { color: var(--grey); }
.hex-wrap.unlocked .hex-num { color: #fff; }
.hex-wrap.unlocked .hex-label { color: var(--text-mid); font-weight: bold; }
.hex-wrap.unlocked .hex { filter: drop-shadow(0 3px 8px rgba(0,0,0,.18)); }
.hex-tooltip { display: none; position: absolute; bottom: calc(100% + 8px); left: 50%; transform: translateX(-50%); background: var(--navy); color: #fff; font-size: .7rem; font-weight: 600; padding: .35rem .65rem; white-space: nowrap; z-index: 20; box-shadow: var(--shadow-md); pointer-events: none; line-height: 1.4; text-align: center; }
.hex-tooltip::after { content: ''; position: absolute; top: 100%; left: 50%; transform: translateX(-50%); border: 5px solid transparent; border-top-color: var(--navy); }
.hex-wrap:hover .hex-tooltip { display: block; }
.training-progress-strip { display: flex; align-items: center; gap: .75rem; padding: .65rem .85rem; background: var(--light); border: 1px solid var(--grey); margin-bottom: 1rem; }
.tps-label { font-size: .65rem; font-weight: bold; text-transform: uppercase; letter-spacing: .1em; color: var(--muted); flex-shrink: 0; }
.tps-track { flex: 1; height: 5px; background: var(--grey); overflow: hidden; }
.tps-fill { height: 100%; background: var(--navy); transition: width .6s ease; }
.tps-count { font-size: .72rem; font-weight: bold; color: var(--navy); flex-shrink: 0; font-family: monospace; }
.hex-legend { display: flex; align-items: center; gap: 1.25rem; margin-top: .75rem; padding-top: .65rem; border-top: 1px solid var(--grey); flex-wrap: wrap; }
.hex-legend-item { display: flex; align-items: center; gap: .35rem; font-size: .7rem; color: var(--muted); }
.hex-legend-dot { width: 10px; height: 10px; border-radius: 2px; flex-shrink: 0; }
</style>

<div class="wrap">

    <nav class="topbar">
        <div class="brand">
            <div class="brand-badge">📻</div>
            <div>
                <div class="brand-name">{{ \App\Helpers\RaynetSetting::groupName() }}</div>
                <div class="brand-sub">members' portal</div>
            </div>
        </div>
        <a href="{{ route('members') }}" class="back-btn">← Back to hub</a>
    </nav>

    <div class="profile-hero">
        <div class="hero-inner">
            <div class="hero-top">
                <div class="hero-brand">
                    <span style="font-size:1.1rem;">📻</span>
                    <span class="hero-brand-name">{{ \App\Helpers\RaynetSetting::groupName() }}</span>
                </div>
                <span class="hero-badge">Member Record</span>
            </div>
            <div class="hero-body">
                @if($user->avatar)
                    <div class="hero-avatar" style="padding:0;overflow:hidden;background:transparent;">
                        <img src="{{ Storage::url($user->avatar) }}" style="width:100%;height:100%;object-fit:cover;" alt="">
                    </div>
                @else
                    <div class="hero-avatar">{{ $initials ?: '?' }}</div>
                @endif
                <div>
                    <div class="hero-name">{{ $userName }}</div>
                    <div class="hero-chips">
                        @if ($userCallsign)
                            <span class="chip-callsign">{{ strtoupper($userCallsign) }}</span>
                        @endif
                        @if ($isOperator && $userRole)
                            <span class="chip-role">{{ $userRole }}</span>
                        @endif
                        @if ($userLevel !== null)
                            <span class="chip-level">Level {{ $userLevel }} · {{ $levelLabel }}</span>
                        @endif
                        @if ($userStatus && $sc)
                            <div class="hero-status">
                                <div class="hero-sdot" style="background:{{ $sc['dot'] }};box-shadow:0 0 6px {{ $sc['glow'] }};"></div>
                                {{ $userStatus }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="toast-ok" style="margin-top:1.5rem;">✓ {{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="toast-err" style="margin-top:1.5rem;">
            <div><strong>⚠ Please fix the following:</strong>
                <ul>@foreach ($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
            </div>
        </div>
    @endif

    <div class="layout" style="margin-top:1.5rem;">

        {{-- LEFT COLUMN --}}
        <div>

            {{-- PROFILE PHOTO --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-head-icon">📷</div>
                    <div>
                        <h2>Profile Photo</h2>
                        <p>Shown on your profile and across the portal. Max 5 MB — JPG, PNG, WebP or GIF.</p>
                    </div>
                </div>
                <div class="card-body">
                    <div style="display:flex;align-items:center;gap:1.25rem;flex-wrap:wrap;margin-bottom:1.1rem;">
                        @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}"
                                 style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid var(--grey);flex-shrink:0;" alt="">
                            <div>
                                <div style="font-size:.85rem;font-weight:bold;color:var(--text);margin-bottom:.35rem;">Current photo</div>
                                <form method="POST" action="{{ route('profile.avatar.destroy') }}"
                                      onsubmit="return confirm('Remove your profile photo?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-save" style="background:var(--red);font-size:.8rem;padding:.4rem 1rem;">Remove photo</button>
                                </form>
                            </div>
                        @else
                            <div style="width:72px;height:72px;border-radius:50%;background:var(--navy);display:flex;align-items:center;justify-content:center;font-size:1.6rem;font-weight:bold;color:#fff;flex-shrink:0;border:3px solid var(--grey);">
                                {{ $initials ?: '?' }}
                            </div>
                            <div style="font-size:.82rem;color:var(--muted);">No photo set — upload one below.</div>
                        @endif
                    </div>
                    <div class="field">
                        <label for="avatarRaw">Upload new photo</label>
                        <input id="avatarRaw" type="file" accept="image/*"
                               style="padding:.5rem .8rem;border:1.5px solid var(--grey);border-radius:8px;font-size:.88rem;width:100%;"
                               onchange="avatarOpenCropper(this)">
                        @error('avatar')<div class="field-note" style="color:var(--red);">{{ $message }}</div>@enderror
                    </div>
                    <div id="avatarCropperPanel" style="display:none;margin-top:1rem;border:1.5px solid var(--grey);border-radius:10px;overflow:hidden;background:var(--light);">
                        <div style="position:relative;width:100%;height:280px;background:#1a1a2e;overflow:hidden;cursor:grab;" id="cropDragArea">
                            <canvas id="cropCanvas" style="display:block;width:100%;height:100%;"></canvas>
                            <svg style="position:absolute;inset:0;width:100%;height:100%;pointer-events:none;" id="cropMaskSvg">
                                <defs>
                                    <mask id="circleMask">
                                        <rect width="100%" height="100%" fill="white"/>
                                        <circle id="maskCircle" fill="black"/>
                                    </mask>
                                </defs>
                                <rect width="100%" height="100%" fill="rgba(0,0,0,0.55)" mask="url(#circleMask)"/>
                                <circle id="guideCircle" fill="none" stroke="rgba(255,255,255,0.5)" stroke-width="2" stroke-dasharray="6,4"/>
                            </svg>
                        </div>
                        <div style="padding:.85rem 1rem;display:flex;flex-direction:column;gap:.75rem;">
                            <div style="display:flex;align-items:center;gap:.75rem;">
                                <span style="font-size:.7rem;font-weight:bold;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;min-width:42px;">Zoom</span>
                                <input type="range" id="cropZoom" min="50" max="300" value="100" step="1" style="flex:1;" oninput="avatarRender()">
                                <span id="cropZoomVal" style="font-size:.8rem;font-weight:bold;color:var(--navy);font-family:monospace;min-width:38px;">100%</span>
                            </div>
                            <div>
                                <div style="font-size:.7rem;font-weight:bold;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.45rem;">Filter preset</div>
                                <div style="display:flex;gap:.4rem;flex-wrap:wrap;">
                                    <button type="button" class="avf-btn avf-active" data-preset="none"  onclick="avatarPreset(this,'none')">None</button>
                                    <button type="button" class="avf-btn" data-preset="warm"  onclick="avatarPreset(this,'warm')">Warm</button>
                                    <button type="button" class="avf-btn" data-preset="cool"  onclick="avatarPreset(this,'cool')">Cool</button>
                                    <button type="button" class="avf-btn" data-preset="mono"  onclick="avatarPreset(this,'mono')">Mono</button>
                                    <button type="button" class="avf-btn" data-preset="vivid" onclick="avatarPreset(this,'vivid')">Vivid</button>
                                    <button type="button" class="avf-btn" data-preset="faded" onclick="avatarPreset(this,'faded')">Faded</button>
                                </div>
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.55rem .75rem;">
                                <div style="display:flex;align-items:center;gap:.5rem;">
                                    <span style="font-size:.7rem;font-weight:bold;color:var(--muted);min-width:60px;">Brightness</span>
                                    <input type="range" id="slBrightness" min="50" max="150" value="100" step="1" style="flex:1;" oninput="avatarRender()">
                                </div>
                                <div style="display:flex;align-items:center;gap:.5rem;">
                                    <span style="font-size:.7rem;font-weight:bold;color:var(--muted);min-width:56px;">Contrast</span>
                                    <input type="range" id="slContrast" min="50" max="150" value="100" step="1" style="flex:1;" oninput="avatarRender()">
                                </div>
                                <div style="display:flex;align-items:center;gap:.5rem;">
                                    <span style="font-size:.7rem;font-weight:bold;color:var(--muted);min-width:60px;">Saturation</span>
                                    <input type="range" id="slSaturation" min="0" max="200" value="100" step="1" style="flex:1;" oninput="avatarRender()">
                                </div>
                                <div style="display:flex;align-items:center;gap:.5rem;">
                                    <span style="font-size:.7rem;font-weight:bold;color:var(--muted);min-width:56px;">Sharpness</span>
                                    <input type="range" id="slSharpness" min="0" max="100" value="0" step="1" style="flex:1;" oninput="avatarRender()">
                                </div>
                            </div>
                            <div style="display:flex;gap:.5rem;padding-top:.25rem;border-top:1px solid var(--grey);">
                                <button type="button" onclick="avatarConfirmCrop()" class="btn-save" style="flex:1;">✓ Use this photo</button>
                                <button type="button" onclick="avatarCancelCrop()" class="btn-save" style="background:var(--muted);flex:0 0 auto;">Cancel</button>
                            </div>
                        </div>
                    </div>
                    <form id="avatarCropForm" method="POST" action="{{ route('profile.avatar.crop') }}" style="display:none;">
                        @csrf
                        <input type="hidden" name="avatar_data" id="avatarDataInput">
                    </form>
                </div>
            </div>

<style>
.avf-btn{padding:4px 12px;font-size:11px;font-weight:bold;font-family:var(--font);border:1px solid var(--grey);background:var(--white);color:var(--muted);cursor:pointer;border-radius:4px;transition:all .12s;letter-spacing:.04em;text-transform:uppercase;}
.avf-btn:hover{border-color:var(--navy);color:var(--navy);}
.avf-active{background:var(--navy);border-color:var(--navy);color:#fff !important;}
</style>

<script>
(function(){
    let img=new Image(),offsetX=0,offsetY=0,dragStartX=0,dragStartY=0,isDragging=false;
    const PRESETS={
        none: {brightness:100,contrast:100,saturation:100,sharpness:0},
        warm: {brightness:105,contrast:105,saturation:130,sharpness:10},
        cool: {brightness:100,contrast:100,saturation:85, sharpness:0},
        mono: {brightness:100,contrast:115,saturation:0,  sharpness:5},
        vivid:{brightness:108,contrast:120,saturation:160,sharpness:20},
        faded:{brightness:115,contrast:80, saturation:70, sharpness:0},
    };
    window.avatarOpenCropper=function(input){
        if(!input.files||!input.files[0])return;
        const reader=new FileReader();
        reader.onload=function(e){
            img=new Image();
            img.onload=function(){
                offsetX=0;offsetY=0;
                document.getElementById('cropZoom').value=100;
                document.getElementById('slBrightness').value=100;
                document.getElementById('slContrast').value=100;
                document.getElementById('slSaturation').value=100;
                document.getElementById('slSharpness').value=0;
                document.querySelectorAll('.avf-btn').forEach(b=>b.classList.remove('avf-active'));
                document.querySelector('[data-preset="none"]').classList.add('avf-active');
                document.getElementById('avatarCropperPanel').style.display='block';
                initCanvas();avatarRender();
            };
            img.src=e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    };
    function initCanvas(){
        const area=document.getElementById('cropDragArea');
        const canvas=document.getElementById('cropCanvas');
        const W=area.offsetWidth,H=area.offsetHeight;
        canvas.width=W*window.devicePixelRatio;canvas.height=H*window.devicePixelRatio;
        canvas.style.width=W+'px';canvas.style.height=H+'px';
        const r=Math.min(W,H)*0.42,cx=W/2,cy=H/2;
        const mc=document.getElementById('maskCircle'),gc=document.getElementById('guideCircle');
        mc.setAttribute('cx',cx);mc.setAttribute('cy',cy);mc.setAttribute('r',r);
        gc.setAttribute('cx',cx);gc.setAttribute('cy',cy);gc.setAttribute('r',r);
        area.onmousedown=function(e){isDragging=true;dragStartX=e.clientX-offsetX;dragStartY=e.clientY-offsetY;area.style.cursor='grabbing';};
        area.onmousemove=function(e){if(!isDragging)return;offsetX=e.clientX-dragStartX;offsetY=e.clientY-dragStartY;avatarRender();};
        area.onmouseup=area.onmouseleave=function(){isDragging=false;area.style.cursor='grab';};
        area.ontouchstart=function(e){const t=e.touches[0];isDragging=true;dragStartX=t.clientX-offsetX;dragStartY=t.clientY-offsetY;};
        area.ontouchmove=function(e){if(!isDragging)return;const t=e.touches[0];offsetX=t.clientX-dragStartX;offsetY=t.clientY-dragStartY;avatarRender();e.preventDefault();};
        area.ontouchend=function(){isDragging=false;};
        area.onwheel=function(e){const z=document.getElementById('cropZoom');z.value=Math.min(300,Math.max(50,parseInt(z.value)-Math.sign(e.deltaY)*5));avatarRender();e.preventDefault();};
    }
    window.avatarRender=function(){
        const canvas=document.getElementById('cropCanvas');
        const ctx=canvas.getContext('2d');
        const W=canvas.width,H=canvas.height;
        const dpr=window.devicePixelRatio||1;
        const zoom=parseInt(document.getElementById('cropZoom').value)/100;
        document.getElementById('cropZoomVal').textContent=Math.round(zoom*100)+'%';
        const b=document.getElementById('slBrightness').value;
        const c=document.getElementById('slContrast').value;
        const s=document.getElementById('slSaturation').value;
        ctx.clearRect(0,0,W,H);
        ctx.filter=`brightness(${b}%) contrast(${c}%) saturate(${s}%)`;
        const scaledW=img.width*zoom*dpr,scaledH=img.height*zoom*dpr;
        const drawX=W/2-scaledW/2+offsetX*dpr,drawY=H/2-scaledH/2+offsetY*dpr;
        ctx.drawImage(img,drawX,drawY,scaledW,scaledH);
        ctx.filter='none';
    };
    window.avatarPreset=function(btn,name){
        document.querySelectorAll('.avf-btn').forEach(b=>b.classList.remove('avf-active'));
        btn.classList.add('avf-active');
        const p=PRESETS[name];
        document.getElementById('slBrightness').value=p.brightness;
        document.getElementById('slContrast').value=p.contrast;
        document.getElementById('slSaturation').value=p.saturation;
        document.getElementById('slSharpness').value=p.sharpness;
        avatarRender();
    };
    window.avatarConfirmCrop=function(){
        const area=document.getElementById('cropDragArea');
        const canvas=document.getElementById('cropCanvas');
        const dpr=window.devicePixelRatio||1;
        const W=area.offsetWidth,H=area.offsetHeight;
        const r=Math.min(W,H)*0.42,cx=W/2,cy=H/2;
        const out=document.createElement('canvas');
        const size=Math.round(r*2*dpr);
        out.width=size;out.height=size;
        const octx=out.getContext('2d');
        octx.beginPath();octx.arc(size/2,size/2,size/2,0,Math.PI*2);octx.clip();
        octx.drawImage(canvas,(cx-r)*dpr,(cy-r)*dpr,size,size,0,0,size,size);
        document.getElementById('avatarDataInput').value=out.toDataURL('image/jpeg',0.92);
        document.getElementById('avatarCropForm').submit();
    };
    window.avatarCancelCrop=function(){
        document.getElementById('avatarCropperPanel').style.display='none';
        document.getElementById('avatarRaw').value='';
    };
})();
</script>

            {{-- PROFILE DETAILS --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-head-icon">👤</div>
                    <div>
                        <h2>Profile Details</h2>
                        <p>Used across the members' hub, event rosters and training systems.</p>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        <div class="field">
                            <label for="name">Full Name</label>
                            <div class="input-wrap">
                                <span class="input-icon">👤</span>
                                <input id="name" type="text" value="{{ old('name', $userName) }}" disabled placeholder="Your full name">
                                <input type="hidden" name="name" value="{{ old('name', $userName) }}">
                            </div>
                            <div class="field-note warn">Name is managed by an administrator</div>
                        </div>
                        <div class="field">
                            <label for="email">Email Address</label>
                            <div class="input-wrap">
                                <span class="input-icon">✉</span>
                                <input id="email" type="email" value="{{ $userEmail }}" disabled>
                            </div>
                            <div class="field-note warn">Login email is managed by an administrator.</div>
                        </div>
                        <div class="field">
                            <label for="callsign">Callsign</label>
                            @if ($userCallsign)
                                <div class="approved-tag">✓ {{ strtoupper($userCallsign) }} <span>approved</span></div>
                            @endif
                            <div class="input-wrap">
                                <span class="input-icon">📡</span>
                                <input id="callsign" name="callsign" type="text"
                                    value="{{ old('callsign', $pendingCallsign ?? $userCallsign) }}"
                                    placeholder="e.g. G4BDS" autocomplete="off" autocorrect="off"
                                    autocapitalize="characters" spellcheck="false" maxlength="10"
                                    oninput="validateCallsign(this)">
                            </div>
                            <div class="cs-feedback ok" id="cs-ok">✓ Valid callsign format</div>
                            <div class="cs-feedback err" id="cs-err">✕ <span id="cs-err-msg">Not a recognised amateur radio callsign</span></div>
                            @error('callsign')
                                <div class="cs-feedback err show">✕ {{ $message }}</div>
                            @enderror
                            @if ($pendingCallsign)
                                <div class="pending-banner">
                                    <div style="font-size:1.1rem;flex-shrink:0;">⏳</div>
                                    <div style="line-height:1.5;">
                                        <strong>Awaiting admin approval —</strong>
                                        <strong>{{ strtoupper($pendingCallsign) }}</strong>
                                        is pending review. Current approved callsign
                                        {{ $userCallsign ? '(' . strtoupper($userCallsign) . ')' : '(none)' }}
                                        remains active until approved.
                                    </div>
                                </div>
                            @else
                                <div class="field-note">Changes require admin approval before taking effect.</div>
                            @endif
                            <div class="cs-help">
                                Format: prefix + number + suffix — e.g. G4BDS, M0ABC, 2E0XYZ, GW4BDS.
                                UK: Foundation M7 · Intermediate 2E0/2M0 · Full G/M/GM/GW/GI.
                                <a href="https://www.ofcom.org.uk/manage-your-licence/radiocommunication-licences/amateur-radio" target="_blank" rel="noopener">Ofcom info ↗</a>
                            </div>
                            <div id="qrzCard" style="display:none;margin-top:.65rem;">
                                <div id="qrzLoading" style="display:none;align-items:center;gap:.5rem;padding:.55rem .8rem;background:var(--light);border:1px solid var(--grey);border-radius:8px;font-size:.78rem;color:var(--muted);">
                                    <span style="display:inline-block;width:14px;height:14px;border:2px solid var(--grey);border-top-color:var(--navy);border-radius:50%;animation:qrzSpin .7s linear infinite;flex-shrink:0;"></span>
                                    Looking up on QRZ.com…
                                </div>
                                <div id="qrzNotFound" style="display:none;padding:.5rem .8rem;background:#fff7ed;border:1px solid #fed7aa;border-left:3px solid #ea580c;border-radius:0 6px 6px 0;font-size:.78rem;color:#c2410c;font-weight:bold;">
                                    ⚠ Callsign not found on QRZ.com — double-check it's correct.
                                </div>
                                <div id="qrzResult" style="display:none;">
                                    <div style="display:flex;align-items:flex-start;gap:.75rem;padding:.75rem .9rem;background:var(--green-bg);border:1px solid #b8ddc9;border-left:3px solid var(--green);border-radius:0 8px 8px 0;">
                                        <div id="qrzAvatar" style="width:44px;height:44px;border-radius:50%;background:var(--navy);display:flex;align-items:center;justify-content:center;font-size:1.1rem;font-weight:bold;color:#fff;flex-shrink:0;overflow:hidden;border:2px solid rgba(0,51,102,.15);"></div>
                                        <div style="flex:1;min-width:0;">
                                            <div style="display:flex;align-items:center;gap:.45rem;flex-wrap:wrap;margin-bottom:.25rem;">
                                                <span style="font-size:.7rem;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;color:var(--green);">✓ QRZ Verified</span>
                                                <span id="qrzCallsignBadge" style="font-family:monospace;font-size:.8rem;font-weight:bold;background:var(--navy);color:#fff;padding:.1rem .45rem;border-radius:4px;letter-spacing:.06em;"></span>
                                                <span id="qrzLicenceBadge" style="font-size:.68rem;font-weight:bold;padding:.1rem .45rem;border:1px solid #b8ddc9;border-radius:4px;color:var(--green);background:#fff;display:none;"></span>
                                            </div>
                                            <div id="qrzName" style="font-size:.9rem;font-weight:bold;color:var(--text);"></div>
                                            <div id="qrzLocation" style="font-size:.75rem;color:var(--muted);margin-top:2px;"></div>
                                            <div id="qrzExtra" style="font-size:.72rem;color:var(--muted);margin-top:2px;"></div>
                                        </div>
                                        <a id="qrzLink" href="#" target="_blank" rel="noopener"
                                           style="font-size:.72rem;font-weight:bold;color:var(--navy);text-decoration:none;border-bottom:1px solid rgba(0,51,102,.25);white-space:nowrap;flex-shrink:0;margin-top:2px;">QRZ ↗</a>
                                    </div>
                                </div>
                                <div id="qrzError" style="display:none;padding:.5rem .8rem;background:var(--light);border:1px solid var(--grey);border-radius:8px;font-size:.75rem;color:var(--muted);">
                                    ⚠ QRZ lookup unavailable — <span id="qrzErrorDetail">could not reach the lookup service</span>.
                                </div>
                            </div>
                        </div>
                        <div class="field">
                            <label for="dmr_id">DMR ID</label>
                            <div class="input-wrap">
                                <span class="input-icon">🔢</span>
                                <input id="dmr_id" name="dmr_id" type="text" inputmode="numeric" value="{{ old('dmr_id', $userDmrId) }}" placeholder="e.g. 2346001">
                            </div>
                            <div class="field-note">Your DMR radio ID. Numbers only.</div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-save">Save Changes</button>
                            <a href="{{ route('password.change') }}" class="pwd-link">Change my password →</a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- LICENCE & DIGITAL IDs --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-head-icon">📜</div>
                    <div>
                        <h2>Licence &amp; Digital IDs</h2>
                        <p>Your Ofcom amateur licence class and network identifiers.</p>
                    </div>
                </div>
                <div class="card-body">
                    @if ($lc)
                        <div class="licence-block" style="background:{{ $lc['bg'] }};border-color:{{ $lc['border'] }};border-left-color:{{ $lc['dot'] }};">
                            <div class="lic-icon" style="border-color:{{ $lc['border'] }};">{{ $lc['icon'] }}</div>
                            <div class="lic-info">
                                <div class="lic-name" style="color:{{ $lc['text'] }};">{{ $lc['label'] }}</div>
                                <div class="lic-desc" style="color:{{ $lc['text'] }};">{{ $lc['desc'] }}</div>
                            </div>
                            <div class="lic-pill" style="background:{{ $lc['bg'] }};border-color:{{ $lc['border'] }};color:{{ $lc['text'] }};">{{ strtoupper($userLicence) }}</div>
                        </div>
                    @else
                        <div style="padding:.75rem 1rem;background:var(--light);border:1px solid var(--grey);border-radius:8px;font-size:.88rem;color:var(--muted);">
                            No licence class recorded — contact your Group Controller to update.
                        </div>
                    @endif
                    <div class="dmr-panel">
                        <div class="dmr-panel-top">
                            <div>
                                <div class="dmr-label">DMR ID</div>
                                @if ($userDmrId)
                                    <div class="dmr-value">{{ $userDmrId }}</div>
                                @else
                                    <span style="font-size:.88rem;color:rgba(255,255,255,.35);">Not set — update in form above</span>
                                @endif
                            </div>
                            @if ($userDmrId)
                                <div style="text-align:right;">
                                    <div style="font-size:.65rem;color:rgba(255,255,255,.35);text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px;">BrandMeister</div>
                                    <a href="https://brandmeister.network/?page=device&id={{ $userDmrId }}01" target="_blank"
                                       style="font-size:.78rem;font-weight:bold;color:rgba(255,255,255,.6);text-decoration:none;border-bottom:1px solid rgba(255,255,255,.2);">View profile ↗</a>
                                </div>
                            @endif
                        </div>
                        @if ($userCallsign && $userDmrId)
                        <div class="dmr-live-strip" id="dmrLiveStrip">
                            <div class="dmr-live-block">
                                <div class="dmr-live-label">Last talkgroup</div>
                                <div class="dmr-live-value" id="dmrTg"><div class="dmr-loading-shimmer" style="width:80px;"></div></div>
                            </div>
                            <div class="dmr-live-block">
                                <div class="dmr-live-label">Last heard</div>
                                <div class="dmr-live-value" id="dmrHeard"><div class="dmr-loading-shimmer" style="width:65px;"></div></div>
                            </div>
                        </div>
                        <div class="dmr-live-footer">
                            <span>RadioID.net live data · <span id="dmrCallsignLabel">{{ strtoupper($userCallsign) }}</span></span>
                            <a href="https://database.radioid.net/database/view#!entry?id={{ $userDmrId }}" target="_blank">Full record ↗</a>
                        </div>
                        @endif
                    </div>
                    <div class="info-note">ℹ Licence class is set by your Group Controller. DMR ID can be updated in the Profile Details form above.</div>
                </div>
            </div>

            {{-- OPERATOR PROFILE --}}
            <div class="card" style="margin-bottom:0;">
                <div class="card-head">
                    <div class="card-head-icon">📡</div>
                    <div>
                        <h2>Operator Profile</h2>
                        <p>Your RAYNET role, level and deployment status. Set by your Group Controller.</p>
                    </div>
                </div>
                <div class="card-body">
                    @if ($isOperator)
                        @if ($userStatus && $sc)
                            <div class="status-banner" style="background:{{ $sc['bg'] }};border-color:{{ $sc['border'] }};border-left-color:{{ $sc['dot'] }};">
                                <div class="sbdot" style="background:{{ $sc['dot'] }};box-shadow:0 0 6px {{ $sc['glow'] }};"></div>
                                <div>
                                    <div style="font-weight:bold;color:{{ $sc['text'] }};">{{ $userStatus }}</div>
                                    <div style="font-size:.72rem;color:var(--muted);">Operator status as recorded by the Group Controller</div>
                                </div>
                            </div>
                        @endif
                        <div class="op-grid">
                            <div class="op-tile">
                                <div class="op-tile-label">Role</div>
                                <div class="op-tile-value">{{ $userRole }}</div>
                            </div>
                            <div class="op-tile">
                                <div class="op-tile-label">Level</div>
                                @if ($userLevel !== null)
                                    <div class="op-tile-value">Level {{ $userLevel }}</div>
                                    <div class="op-tile-sub">{{ $levelLabel }}</div>
                                @else
                                    <div class="op-tile-value" style="color:var(--muted);font-weight:normal;">Not assigned</div>
                                @endif
                            </div>
                            @if ($userPhone)
                                <div class="op-tile">
                                    <div class="op-tile-label">Contact</div>
                                    <div class="op-tile-value">{{ $userPhone }}</div>
                                </div>
                            @endif
                            @if ($userJoined)
                                <div class="op-tile">
                                    <div class="op-tile-label">Joined RAYNET</div>
                                    <div class="op-tile-value">{{ \Carbon\Carbon::parse($userJoined)->format('d M Y') }}</div>
                                </div>
                            @endif
                        </div>
                        @if ($userLevel !== null)
                            <div class="level-bar-wrap">
                                <div class="level-bar-header">
                                    <span class="level-bar-title">Operator Level</span>
                                    <span class="level-bar-value">{{ $userLevel }} / 5 — {{ $levelLabel }}</span>
                                </div>
                                <div class="level-bar-track">
                                    <div class="level-bar-fill" style="width:{{ ($userLevel / 5) * 100 }}%;"></div>
                                </div>
                            </div>
                        @endif
                        @if ($userNotes)
                            <div class="notes-block">
                                <div class="notes-label">Notes from Group Controller</div>
                                <div style="font-size:.88rem;color:var(--text-mid);">{{ $userNotes }}</div>
                            </div>
                        @endif
                        @if ($userLicence === 'Full' && $userLevel !== null && $userLevel >= 3)
                            <div class="info-note" style="margin-top:.85rem;">
                                ⚡ Full Licence &amp; Level {{ $userLevel }} — eligible for net control duties and inter-group liaison roles.
                            </div>
                        @endif
                    @else
                        <div class="no-op-notice">
                            <div style="font-size:2rem;opacity:.35;margin-bottom:.8rem;">📡</div>
                            <div style="font-weight:bold;margin-bottom:.4rem;">Not yet registered as a RAYNET operator</div>
                            <div style="font-size:.88rem;">Once your Group Controller assigns you a role and level, your operator profile will appear here.</div>
                        </div>
                    @endif
                </div>
            </div>

        </div>{{-- /left --}}

        {{-- RIGHT COLUMN --}}
        <div>

            {{-- TRAINING PROGRESSION --}}
            <div class="card training-card" style="margin-top:0;">
                <div class="card-head">
                    <div class="card-head-icon">🏅</div>
                    <div>
                        <h2>Training Progression</h2>
                        <p>Tier badges unlock with course completion. Specialisms are independent.</p>
                    </div>
                </div>
                <div class="card-body">
                    @php
                    $completedCourseIds = collect($completedCourseIds ?? []);
                    $tiers = [
                        ['id'=>1,  'num'=>1, 'label'=>'Operator',      'colour'=>'#003366','border'=>'#001f40','desc'=>'RAYNET Basics: mission, Ofcom regs, message precedence'],
                        ['id'=>2,  'num'=>2, 'label'=>'Adv. Operator', 'colour'=>'#0277bd','border'=>'#01579b','desc'=>'Prereq: Operator. Station running & structured net traffic.'],
                        ['id'=>3,  'num'=>3, 'label'=>'Specialist',    'colour'=>'#1a7a3c','border'=>'#145c2e','desc'=>'Prereq: Adv. Operator. Technical or operational specialism.'],
                        ['id'=>4,  'num'=>4, 'label'=>'Team Leader',   'colour'=>'#b45309','border'=>'#92400e','desc'=>'Prereq: Specialist. Incident coordination & deployment lead.'],
                        ['id'=>5,  'num'=>5, 'label'=>'Instructor',    'colour'=>'#C8102E','border'=>'#9a0e22','desc'=>'Prereq: Team Leader. Deliver courses, assess & mentor.'],
                    ];
                    $specTech = [
                        ['id'=>101,'num'=>'T1','label'=>'Power Systems','colour'=>'#5b21b6','border'=>'#4c1d95','desc'=>'Battery, generator & solar power for ops.'],
                        ['id'=>102,'num'=>'T2','label'=>'Digital Modes','colour'=>'#5b21b6','border'=>'#4c1d95','desc'=>'DMR, D-STAR, Fusion & APRS. Ofcom rules.'],
                    ];
                    $specOps = [
                        ['id'=>111,'num'=>'O1','label'=>'Mapping',        'colour'=>'#0f766e','border'=>'#0d5e57','desc'=>'OS grid references, what3words, GIS basics.'],
                        ['id'=>112,'num'=>'O2','label'=>'Severe Weather', 'colour'=>'#0f766e','border'=>'#0d5e57','desc'=>'Storm ops, flood deployment, welfare.'],
                        ['id'=>113,'num'=>'O3','label'=>'First Aid Comms','colour'=>'#0f766e','border'=>'#0d5e57','desc'=>'Coordinating comms with medical teams.'],
                        ['id'=>114,'num'=>'O4','label'=>'Marathon Ops',   'colour'=>'#0f766e','border'=>'#0d5e57','desc'=>'Large event management & checkpoint liaison.'],
                        ['id'=>115,'num'=>'O5','label'=>'Air Support',    'colour'=>'#0f766e','border'=>'#0d5e57','desc'=>'Comms in support of air operations.'],
                        ['id'=>116,'num'=>'O6','label'=>'Water Ops',      'colour'=>'#0f766e','border'=>'#0d5e57','desc'=>'Flood, canal & coastal operations.'],
                    ];
                    $specAdmin = [
                        ['id'=>121,'num'=>'A1','label'=>'GDPR',           'colour'=>'#be185d','border'=>'#9d174d','desc'=>'Data protection for RAYNET volunteers.'],
                        ['id'=>122,'num'=>'A2','label'=>'Media Liaison',  'colour'=>'#be185d','border'=>'#9d174d','desc'=>'Press, social media & public communications.'],
                        ['id'=>123,'num'=>'A3','label'=>'Safeguarding',   'colour'=>'#be185d','border'=>'#9d174d','desc'=>'Protecting vulnerable persons during ops.'],
                        ['id'=>124,'num'=>'A4','label'=>'No Secret Codes','colour'=>'#be185d','border'=>'#9d174d','desc'=>'Ofcom rules: plain language only on amateur bands.'],
                    ];
                    $addl = [
                        ['id'=>201,'num'=>'K1','label'=>'Antennas','colour'=>'#374151','border'=>'#1f2937','desc'=>'Practical antenna theory & field erection.'],
                        ['id'=>202,'num'=>'K2','label'=>'NVIS',     'colour'=>'#374151','border'=>'#1f2937','desc'=>'Near Vertical Incidence Skywave. Ofcom notes.'],
                    ];
                    $allCourses = count($tiers)+count($specTech)+count($specOps)+count($specAdmin)+count($addl);
                    $allIds = collect(array_merge($tiers,$specTech,$specOps,$specAdmin,$addl))->pluck('id');
                    $completedCount = $allIds->intersect($completedCourseIds)->count();
                    $pct = $allCourses > 0 ? round(($completedCount/$allCourses)*100) : 0;
                    @endphp

                    <div class="training-progress-strip">
                        <span class="tps-label">Overall</span>
                        <div class="tps-track"><div class="tps-fill" style="width:{{ $pct }}%;"></div></div>
                        <span class="tps-count">{{ $completedCount }}/{{ $allCourses }}</span>
                    </div>

                    <div class="training-section-label">Tier Progression</div>
                    <div class="hex-row">
                        @foreach ($tiers as $i => $course)
                        @php
                            $prereqsMet = true;
                            for ($p = 0; $p < $i; $p++) { if (!$completedCourseIds->contains($tiers[$p]['id'])) { $prereqsMet = false; break; } }
                            $done = $completedCourseIds->contains($course['id']);
                            $state = $done ? 'unlocked' : 'locked';
                            $fill  = $done ? $course['colour'] : '#e2e8f0';
                            $stroke= $done ? $course['border'] : '#c8d4e0';
                        @endphp
                        <div class="hex-wrap {{ $state }}">
                            <div class="hex-tooltip">
                                <strong>{{ $course['label'] }}</strong><br>
                                <span style="font-weight:normal;opacity:.8;">{{ $course['desc'] }}</span>
                                @if (!$done && !$prereqsMet)<br><span style="color:#fca5a5;">🔒 Prerequisites needed</span>@endif
                                @if (!$done && $prereqsMet)<br><span style="color:#fde68a;">Ready to unlock</span>@endif
                                @if ($done)<br><span style="color:#86efac;">✓ Completed</span>@endif
                            </div>
                            <div class="hex">
                                <svg viewBox="0 0 56 56" xmlns="http://www.w3.org/2000/svg">
                                    <polygon points="28,3 51,15.5 51,40.5 28,53 5,40.5 5,15.5" fill="{{ $fill }}" stroke="{{ $stroke }}" stroke-width="2"/>
                                    @if ($done)<polygon points="28,4 50,16 42,4" fill="rgba(255,255,255,.12)" stroke="none"/>@endif
                                </svg>
                                <div class="hex-num" style="color:{{ $done ? '#fff' : 'rgba(0,0,0,.18)' }};">{{ $course['num'] }}</div>
                            </div>
                            <div class="hex-label">{{ $course['label'] }}</div>
                        </div>
                        @endforeach
                    </div>

                    @foreach ([['Specialisms — Technical',$specTech],['Specialisms — Operational',$specOps],['Specialisms — Administrative',$specAdmin],['Additional Knowledge',$addl]] as [$sectionLabel,$sectionCourses])
                    <div class="training-section-label">{{ $sectionLabel }}</div>
                    <div class="hex-row">
                        @foreach ($sectionCourses as $course)
                        @php $done=$completedCourseIds->contains($course['id']);$state=$done?'unlocked':'locked';$fill=$done?$course['colour']:'#e2e8f0';$stroke=$done?$course['border']:'#c8d4e0'; @endphp
                        <div class="hex-wrap {{ $state }}">
                            <div class="hex-tooltip"><strong>{{ $course['label'] }}</strong><br><span style="font-weight:normal;opacity:.8;">{{ $course['desc'] }}</span>@if($done)<br><span style="color:#86efac;">✓ Completed</span>@else<br><span style="color:#fde68a;">Available</span>@endif</div>
                            <div class="hex"><svg viewBox="0 0 56 56" xmlns="http://www.w3.org/2000/svg"><polygon points="28,3 51,15.5 51,40.5 28,53 5,40.5 5,15.5" fill="{{ $fill }}" stroke="{{ $stroke }}" stroke-width="2"/>@if($done)<polygon points="28,4 50,16 42,4" fill="rgba(255,255,255,.12)" stroke="none"/>@endif</svg><div class="hex-num" style="font-size:.8rem;color:{{ $done?'#fff':'rgba(0,0,0,.18)' }};">{{ $course['num'] }}</div></div>
                            <div class="hex-label">{{ $course['label'] }}</div>
                        </div>
                        @endforeach
                    </div>
                    @endforeach

                    <div class="hex-legend">
                        <div class="hex-legend-item"><div class="hex-legend-dot" style="background:var(--navy);"></div>Completed</div>
                        <div class="hex-legend-item"><div class="hex-legend-dot" style="background:#e2e8f0;border:1px solid #c8d4e0;"></div>Not yet completed</div>
                        <div class="hex-legend-item"><div class="hex-legend-dot" style="background:#5b21b6;"></div>Technical specialism</div>
                        <div class="hex-legend-item"><div class="hex-legend-dot" style="background:#0f766e;"></div>Operational specialism</div>
                        <div class="hex-legend-item"><div class="hex-legend-dot" style="background:#be185d;"></div>Administrative specialism</div>
                    </div>
                </div>
            </div>

            {{-- SNAPSHOT SIDEBAR --}}
            <div class="snap-card" style="margin-top:1.2rem;">
                <div class="snap-header">
                    @if($user->avatar)
                        <div class="snap-avatar" style="padding:0;overflow:hidden;background:transparent;">
                            <img src="{{ Storage::url($user->avatar) }}" style="width:100%;height:100%;object-fit:cover;" alt="">
                        </div>
                    @else
                        <div class="snap-avatar">{{ $initials ?: '?' }}</div>
                    @endif
                    <div class="snap-name">{{ $userName }}</div>
                    @if ($userCallsign)
                        <div class="snap-callsign">{{ strtoupper($userCallsign) }}</div>
                    @endif
                    @if ($lc)
                        <div class="snap-lic snap-lic-{{ $lc['slug'] }}">{{ $userLicence }} Licence</div>
                    @endif
                    @if ($isOperator && $userRole)
                        <div class="snap-role">{{ $userRole }}</div>
                    @endif
                    @if ($userLevel !== null)
                        <div class="snap-level">Level {{ $userLevel }} · {{ $levelLabel }}</div>
                    @endif
                    <div class="snap-status-row">
                        @if ($isOperator && $userStatus && $sc)
                            <div class="snap-sdot" style="background:{{ $sc['dot'] }};box-shadow:0 0 0 3px {{ $sc['glow'] }};"></div>
                            {{ $userStatus }}
                        @else
                            <div class="snap-sdot" style="background:rgba(255,255,255,.25);"></div>
                            Active member
                        @endif
                    </div>
                    @if ($userCallsign && $userDmrId)
                    <div class="snap-dmr-live" style="width:100%;">
                        <div style="padding:.4rem .85rem .25rem;font-size:.6rem;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.3);">DMR Live Activity</div>
                        <div class="snap-dmr-live-inner">
                            <div>
                                <div class="snap-dmr-live-label">Last TG</div>
                                <div class="snap-dmr-live-val" id="snapDmrTg"><div class="snap-dmr-loading" style="width:55px;"></div></div>
                            </div>
                            <div>
                                <div class="snap-dmr-live-label">Last heard</div>
                                <div class="snap-dmr-live-val" id="snapDmrHeard"><div class="snap-dmr-loading" style="width:50px;"></div></div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <dl class="snap-dl">
                    <div class="snap-row"><dt class="snap-dt">Name</dt><dd class="snap-dd">{{ $userName }}</dd></div>
                    <div class="snap-row"><dt class="snap-dt">Email</dt><dd class="snap-dd" style="font-size:.78rem;word-break:break-all;">{{ $userEmail }}</dd></div>
                    <div class="snap-row"><dt class="snap-dt">Callsign</dt>
                        @if ($userCallsign)<dd class="snap-dd mono">{{ strtoupper($userCallsign) }}</dd>
                        @else<dd class="snap-dd muted">Not set</dd>@endif
                    </div>
                    <div class="snap-row"><dt class="snap-dt">Licence</dt>
                        @if ($userLicence && $lc)<dd class="snap-dd" style="color:{{ $lc['text'] }};">{{ $userLicence }}</dd>
                        @else<dd class="snap-dd muted">Not recorded</dd>@endif
                    </div>
                    <div class="snap-row"><dt class="snap-dt">DMR ID</dt>
                        @if ($userDmrId)<dd class="snap-dd mono">{{ $userDmrId }}</dd>
                        @else<dd class="snap-dd muted">Not set</dd>@endif
                    </div>
                    @if ($pendingCallsign)
                        <div class="snap-row"><dt class="snap-dt">Pending</dt><dd class="snap-dd amber">{{ strtoupper($pendingCallsign) }} ⏳</dd></div>
                    @endif
                    @if ($isOperator)
                        <div class="snap-row"><dt class="snap-dt">Role</dt><dd class="snap-dd" style="color:var(--navy);">{{ $userRole }}</dd></div>
                        @if ($userLevel !== null)
                            <div class="snap-row"><dt class="snap-dt">Level</dt><dd class="snap-dd mono">L{{ $userLevel }} — {{ $levelLabel }}</dd></div>
                        @endif
                        @if ($userStatus && $sc)
                            <div class="snap-row"><dt class="snap-dt">Status</dt><dd class="snap-dd" style="color:{{ $sc['text'] }};">{{ $userStatus }}</dd></div>
                        @endif
                        @if ($userJoined)
                            <div class="snap-row"><dt class="snap-dt">Joined</dt><dd class="snap-dd">{{ \Carbon\Carbon::parse($userJoined)->format('d M Y') }}</dd></div>
                        @endif
                    @endif
                    <div class="snap-row"><dt class="snap-dt">Member Since</dt><dd class="snap-dd">{{ optional($user->created_at)->format('d M Y') ?? 'Unknown' }}</dd></div>
                </dl>
                <div class="snap-foot">
                    @if ($isOperator)
                        Role and level are assigned by your Group Controller and cannot be self-edited.
                    @else
                        Operator details are assigned by the Group Controller once your training is recorded.
                    @endif
                </div>
            </div>

        </div>{{-- /right --}}

    </div>{{-- /layout --}}

</div>

<script>
function previewAvatar(input) {
    const wrap = document.getElementById('avatarPreviewWrap');
    const img  = document.getElementById('avatarPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; wrap.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<script>
(function () {
    const PATTERNS = [
        /^G[MWIDGJUC]?[0-9][A-Z]{2,3}$/,
        /^M[MWIDGJUC]?[0-9][A-Z]{2,3}$/,
        /^2[EWMID][0-9][A-Z]{2,3}$/,
        /^[0-9]?[A-Z]{1,2}[0-9]{1,2}[A-Z]{1,4}$/,
    ];
    window.validateCallsign = function (input) {
        const raw = input.value.trim();
        const upper = raw.toUpperCase();
        if (raw !== upper) { const pos = input.selectionStart; input.value = upper; try { input.setSelectionRange(pos, pos); } catch(e) {} }
        const okEl = document.getElementById('cs-ok');
        const errEl = document.getElementById('cs-err');
        const msgEl = document.getElementById('cs-err-msg');
        if (!upper) { input.classList.remove('cs-valid','cs-invalid'); okEl.classList.remove('show'); errEl.classList.remove('show'); return; }
        function fail(msg) { input.classList.remove('cs-valid'); input.classList.add('cs-invalid'); okEl.classList.remove('show'); msgEl.textContent = msg; errEl.classList.add('show'); }
        function pass() { input.classList.remove('cs-invalid'); input.classList.add('cs-valid'); okEl.classList.add('show'); errEl.classList.remove('show'); }
        if (upper.length < 3) return fail('Too short — callsigns are at least 3 characters');
        if (/[^A-Z0-9]/.test(upper)) return fail('Letters and numbers only — no spaces or symbols');
        if (!/[A-Z]/.test(upper)) return fail('Callsigns must contain letters');
        if (!/[0-9]/.test(upper)) return fail('Callsigns must contain a district number');
        if (!PATTERNS.some(re => re.test(upper))) return fail('Not a recognised format — e.g. G4BDS, M0ABC, 2E0XYZ, VK2AB');
        pass();
    };
    const field = document.getElementById('callsign');
    if (field && field.value.trim()) validateCallsign(field);
    const form = field?.closest('form');
    if (form && field) {
        form.addEventListener('submit', function (e) {
            const val = field.value.trim();
            if (val && field.classList.contains('cs-invalid')) {
                e.preventDefault(); validateCallsign(field); field.focus();
                field.style.transition = 'transform .07s ease';
                [1,2,3,4].forEach(i => setTimeout(() => { field.style.transform = i%2?'translateX(5px)':'translateX(-5px)'; }, i*70));
                setTimeout(() => { field.style.transform = ''; }, 350);
            }
        });
    }
    let qrzTimer = null, qrzLast = '', qrzActive = false;
    const qrzCard = document.getElementById('qrzCard');
    const qrzLoading = document.getElementById('qrzLoading');
    const qrzNotFound = document.getElementById('qrzNotFound');
    const qrzResult = document.getElementById('qrzResult');
    const qrzError = document.getElementById('qrzError');
    function qrzShow(el) { [qrzLoading,qrzNotFound,qrzResult,qrzError].forEach(e => { if(e) e.style.display='none'; }); if(el) el.style.display = el===qrzResult?'block':'flex'; }
    function qrzHide() { if(qrzCard) qrzCard.style.display='none'; }
    function qrzRender(data) {
        const badge = document.getElementById('qrzCallsignBadge'); if(badge) badge.textContent = data.callsign||'';
        const lic = document.getElementById('qrzLicenceBadge'); if(lic) { if(data.licence_class){lic.textContent=data.licence_class;lic.style.display='inline-block';}else{lic.style.display='none';} }
        const nameEl = document.getElementById('qrzName'); if(nameEl) nameEl.textContent = data.name||data.callsign||'';
        const locEl = document.getElementById('qrzLocation'); if(locEl) locEl.textContent = data.location||'';
        const extraEl = document.getElementById('qrzExtra'); if(extraEl) { const parts=[]; if(data.grid) parts.push('Grid: '+data.grid); if(data.p_call) parts.push('Prev: '+data.p_call); extraEl.textContent=parts.join(' · '); }
        const avatarEl = document.getElementById('qrzAvatar'); if(avatarEl) { if(data.image_url){avatarEl.innerHTML=`<img src="${data.image_url}" style="width:100%;height:100%;object-fit:cover;" onerror="this.parentElement.textContent='📡';">`;}else{const ini=(data.name||data.callsign||'?').split(' ').map(w=>w[0]||'').join('').slice(0,2).toUpperCase();avatarEl.textContent=ini||'📡';} }
        const linkEl = document.getElementById('qrzLink'); if(linkEl&&data.callsign) linkEl.href='https://www.qrz.com/db/'+encodeURIComponent(data.callsign);
        qrzShow(qrzResult);
    }
    async function qrzFetch(callsign) {
        if(!qrzCard||!callsign) return;
        if(callsign===qrzLast&&qrzResult&&qrzResult.style.display!=='none') return;
        qrzLast=callsign; qrzActive=true; qrzCard.style.display='block'; qrzShow(qrzLoading);
        const detailEl = document.getElementById('qrzErrorDetail');
        try {
            const res = await fetch('/profile/qrz-lookup/'+encodeURIComponent(callsign),{headers:{'X-Requested-With':'XMLHttpRequest'}});
            const contentType = res.headers.get('content-type')||'';
            if(!contentType.includes('application/json')) { if(detailEl) detailEl.textContent='server returned HTTP '+res.status; qrzShow(qrzError); qrzActive=false; return; }
            const json = await res.json();
            if(!qrzActive||field.value.trim().toUpperCase()!==callsign) return;
            if(res.status===422){qrzHide();}else if(json.found&&json.data){qrzRender(json.data);}else if(json.service_error){if(detailEl)detailEl.textContent=json.reason||'QRZ service error';qrzShow(qrzError);}else{qrzShow(qrzNotFound);}
        } catch(err) { if(detailEl) detailEl.textContent=err.message||'network error'; qrzShow(qrzError); } finally { qrzActive=false; }
    }
    const _orig = window.validateCallsign;
    window.validateCallsign = function(input) {
        _orig(input); clearTimeout(qrzTimer);
        const upper = input.value.trim().toUpperCase();
        if(!upper||input.classList.contains('cs-invalid')) { qrzHide(); qrzLast=''; return; }
        qrzTimer = setTimeout(() => qrzFetch(upper), 600);
    };
    if(field&&field.value.trim()&&field.classList.contains('cs-valid')) qrzFetch(field.value.trim().toUpperCase());
})();
</script>

@if ($userCallsign && $userDmrId)
<script>
(function () {
    const callsign = '{{ strtoupper($userCallsign) }}';
    function relativeTime(dateStr) {
        if(!dateStr) return {label:'Never',cls:'stale'};
        const diff = Math.floor((Date.now()-new Date(dateStr))/86400000);
        if(diff===0)  return {label:'Today',cls:'active'};
        if(diff===1)  return {label:'Yesterday',cls:'active'};
        if(diff<7)    return {label:diff+'d ago',cls:'active'};
        if(diff<30)   return {label:diff+'d ago',cls:'recent'};
        if(diff<365)  return {label:Math.floor(diff/30)+'mo ago',cls:'stale'};
        return {label:Math.floor(diff/365)+'y ago',cls:'stale'};
    }
    fetch('/members/radioid-lookup/'+encodeURIComponent(callsign))
        .then(r=>r.json())
        .then(data=>{
            const user=data?.results?.[0]??null;
            const lasttg=user?.lasttg??null;
            const heard=user?.lastheard??null;
            const {label,cls}=relativeTime(heard);
            const tgEl=document.getElementById('dmrTg');
            const heardEl=document.getElementById('dmrHeard');
            if(tgEl) tgEl.innerHTML=lasttg?`<span class="dmr-live-tg">TG ${lasttg}</span>`:`<span style="color:rgba(255,255,255,.3);font-size:.78rem;">No data</span>`;
            if(heardEl) heardEl.innerHTML=`<span class="dmr-heard-row"><span class="dmr-heard-dot ${cls}"></span><span class="dmr-heard-text">${label}</span></span>`;
            const snapTg=document.getElementById('snapDmrTg');
            const snapHeard=document.getElementById('snapDmrHeard');
            if(snapTg) snapTg.innerHTML=lasttg?`<span class="snap-dmr-tg">TG ${lasttg}</span>`:`<span style="color:rgba(255,255,255,.25);font-size:.72rem;">—</span>`;
            if(snapHeard) snapHeard.innerHTML=`<span class="snap-dmr-dot ${cls}"></span><span style="font-size:.78rem;color:rgba(255,255,255,.65);">${label}</span>`;
        })
        .catch(()=>{
            ['dmrTg','dmrHeard','snapDmrTg','snapDmrHeard'].forEach(id=>{
                const el=document.getElementById(id);
                if(el) el.innerHTML='<span style="color:rgba(255,255,255,.2);font-size:.72rem;">—</span>';
            });
        });
})();
</script>
@endif

@endsection
