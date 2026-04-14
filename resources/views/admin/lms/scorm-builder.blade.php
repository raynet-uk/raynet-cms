@extends('layouts.app')
@section('title','SCORM Course Builder')
@section('content')
<style>
:root{
  --navy:#003366;--navy-d:#001f40;--red:#C8102E;--teal:#0288d1;
  --green:#1a6b3c;--green-bg:#eef7f2;--grey:#f2f2f2;--grey-m:#dde2e8;
  --white:#fff;--text:#001f40;--muted:#6b7f96;
  --f:Arial,'Helvetica Neue',Helvetica,sans-serif;
  --shadow:0 1px 4px rgba(0,51,102,.1);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:var(--f);background:var(--grey);color:var(--text);}

/* ── TOP BAR ── */
.sb-bar{
  background:var(--navy);border-bottom:4px solid var(--red);
  height:52px;display:flex;align-items:center;justify-content:space-between;
  padding:0 16px;position:sticky;top:0;z-index:100;
  box-shadow:0 2px 12px rgba(0,0,0,.3);
}
.sb-brand{display:flex;align-items:center;gap:9px;}
.sb-logo{width:28px;height:28px;background:var(--red);clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);display:flex;align-items:center;justify-content:center;font-size:6.5px;font-weight:bold;color:#fff;text-align:center;line-height:1.2;text-transform:uppercase;}
.sb-title{font-size:12px;font-weight:bold;color:#fff;letter-spacing:.04em;text-transform:uppercase;}
.sb-sub{font-size:9px;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.06em;}
.sb-actions{display:flex;align-items:center;gap:6px;}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:4px;padding:.35rem .8rem;border:1px solid;font-family:var(--f);font-size:10px;font-weight:bold;cursor:pointer;text-decoration:none;text-transform:uppercase;letter-spacing:.05em;transition:all .12s;white-space:nowrap;}
.btn-primary{background:var(--navy);border-color:var(--navy);color:#fff;}
.btn-primary:hover{background:#002244;}
.btn-teal{background:var(--teal);border-color:var(--teal);color:#fff;}
.btn-teal:hover{background:#0277bd;}
.btn-ghost{background:transparent;border-color:rgba(255,255,255,.2);color:rgba(255,255,255,.7);}
.btn-ghost:hover{border-color:rgba(255,255,255,.45);color:#fff;}
.btn-red{background:rgba(200,16,46,.08);border-color:rgba(200,16,46,.3);color:var(--red);}
.btn-red:hover{background:rgba(200,16,46,.18);}
.btn-sm{padding:.2rem .55rem;font-size:9px;}
.btn-xs{padding:.14rem .4rem;font-size:9px;}
.btn-green{background:var(--green);border-color:var(--green);color:#fff;}
.btn-green:hover{background:#145c2e;}

/* ── LAYOUT ── */
.builder-layout{
  display:grid;
  grid-template-columns:230px 1fr 260px;
  height:calc(100vh - 52px);
  overflow:hidden;
}

/* ── LEFT PANEL ── */
.panel-left{
  background:var(--white);
  border-right:1px solid var(--grey-m);
  display:flex;flex-direction:column;
  overflow:hidden;
}
.panel-head{
  padding:10px 12px;
  background:var(--grey);
  border-bottom:1px solid var(--grey-m);
  font-size:9px;font-weight:bold;
  text-transform:uppercase;letter-spacing:.12em;
  color:var(--navy);flex-shrink:0;
}
.panel-body{flex:1;overflow-y:auto;padding:10px;}

/* Slide type palette */
.slide-type-grid{display:grid;grid-template-columns:1fr 1fr;gap:6px;}
.slide-type{
  display:flex;flex-direction:column;align-items:center;gap:5px;
  padding:10px 6px;
  background:var(--grey);
  border:1px solid var(--grey-m);
  border-radius:3px;
  cursor:grab;
  font-size:9px;font-weight:bold;
  text-transform:uppercase;letter-spacing:.06em;
  color:var(--navy);
  transition:background .12s,border-color .12s,transform .1s,box-shadow .1s;
  text-align:center;
  user-select:none;
}
.slide-type:hover{background:#e8eef5;border-color:var(--navy);}
.slide-type:active{cursor:grabbing;transform:scale(.97);}
.slide-type.dragging-source{opacity:.4;}
.slide-type-icon{font-size:22px;}

/* ── CENTRE PANEL ── */
.panel-centre{
  display:flex;flex-direction:column;
  background:#e0e6ef;
  overflow:hidden;
}

.canvas-toolbar{
  background:var(--white);
  border-bottom:1px solid var(--grey-m);
  padding:8px 14px;
  display:flex;align-items:center;gap:8px;
  flex-shrink:0;
}
.canvas-toolbar-label{font-size:10px;font-weight:bold;color:var(--muted);text-transform:uppercase;letter-spacing:.1em;margin-right:4px;}

/* Slide strip */
.slide-strip-wrap{
  background:var(--navy-d);
  border-bottom:1px solid rgba(0,0,0,.2);
  padding:10px 14px;
  flex-shrink:0;
  overflow-x:auto;
}
.slide-strip-wrap::-webkit-scrollbar{height:4px;}
.slide-strip-wrap::-webkit-scrollbar-track{background:rgba(255,255,255,.05);}
.slide-strip-wrap::-webkit-scrollbar-thumb{background:rgba(255,255,255,.2);border-radius:99px;}

.slide-strip{
  display:flex;gap:8px;align-items:center;
  min-height:72px;
  list-style:none;
}

.strip-item{
  position:relative;
  width:90px;height:60px;
  background:rgba(255,255,255,.07);
  border:2px solid rgba(255,255,255,.12);
  border-radius:3px;
  cursor:pointer;
  flex-shrink:0;
  display:flex;flex-direction:column;align-items:center;justify-content:center;gap:3px;
  transition:border-color .15s,background .15s,transform .1s;
  user-select:none;
}
.strip-item:hover{background:rgba(255,255,255,.12);border-color:rgba(255,255,255,.3);}
.strip-item.active{border-color:var(--red);background:rgba(200,16,46,.15);}
.strip-item.drag-over{border-color:var(--teal);border-style:dashed;background:rgba(2,136,209,.1);}
.strip-item.strip-placeholder{border-style:dashed;border-color:rgba(2,136,209,.5);background:rgba(2,136,209,.08);cursor:default;}

.strip-item-num{
  position:absolute;top:3px;left:5px;
  font-size:8px;font-weight:bold;
  color:rgba(255,255,255,.35);font-variant-numeric:tabular-nums;
}
.strip-item-icon{font-size:18px;}
.strip-item-label{font-size:8px;font-weight:bold;color:rgba(255,255,255,.45);text-transform:uppercase;letter-spacing:.04em;text-align:center;max-width:78px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.strip-item-del{
  position:absolute;top:2px;right:3px;
  width:14px;height:14px;
  background:rgba(200,16,46,.7);
  border-radius:50%;
  display:none;
  align-items:center;justify-content:center;
  font-size:8px;color:#fff;cursor:pointer;
  font-style:normal;line-height:1;
  border:none;font-family:var(--f);
}
.strip-item:hover .strip-item-del{display:flex;}

/* Add slide drop zone */
.strip-add{
  width:52px;height:60px;
  border:2px dashed rgba(255,255,255,.15);
  border-radius:3px;
  display:flex;align-items:center;justify-content:center;
  color:rgba(255,255,255,.25);
  font-size:22px;
  cursor:default;
  flex-shrink:0;
  transition:border-color .15s,background .15s;
}
.strip-add.drag-over{border-color:var(--teal);background:rgba(2,136,209,.1);color:var(--teal);}

/* Canvas area */
.canvas-area{
  flex:1;overflow-y:auto;
  padding:24px;
  display:flex;align-items:flex-start;justify-content:center;
}

.canvas-frame{
  width:100%;max-width:680px;
  background:var(--white);
  border-radius:4px;
  box-shadow:0 4px 24px rgba(0,0,0,.18);
  overflow:hidden;
  min-height:400px;
}

.canvas-empty{
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  padding:80px 32px;
  color:var(--muted);text-align:center;
  min-height:400px;
}
.canvas-empty-icon{font-size:48px;opacity:.2;margin-bottom:14px;}
.canvas-empty-text{font-size:13px;opacity:.6;}
.canvas-empty-sub{font-size:11px;opacity:.4;margin-top:4px;}

/* Preview areas inside canvas */
.cv-hero{
  background:linear-gradient(160deg,#000d1a,#001428);
  padding:48px 32px;
  text-align:center;
  min-height:320px;
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  gap:12px;
}
.cv-hero-badge{
  width:52px;height:52px;
  background:#C8102E;
  clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);
  display:flex;align-items:center;justify-content:center;
  font-size:9px;font-weight:bold;color:#fff;text-align:center;line-height:1.2;text-transform:uppercase;
  margin:0 auto;
}
.cv-hero h1{font-size:28px;font-weight:bold;color:#fff;text-transform:uppercase;letter-spacing:-.01em;line-height:1;}
.cv-hero .sub{font-size:13px;color:rgba(255,255,255,.45);}
.cv-hero .btn-start{display:inline-flex;align-items:center;gap:8px;padding:10px 28px;background:#C8102E;border:none;border-radius:3px;font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:.08em;color:#fff;margin-top:8px;}

.cv-lesson{padding:28px 28px 24px;}
.cv-tag{font-size:9px;font-weight:bold;color:#C8102E;letter-spacing:.18em;text-transform:uppercase;margin-bottom:8px;}
.cv-h{font-size:22px;font-weight:bold;color:#001f40;text-transform:uppercase;letter-spacing:-.01em;margin-bottom:6px;}
.cv-lead{font-size:12px;color:#6b7f96;line-height:1.65;margin-bottom:16px;}

.cv-card-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:8px;margin-bottom:16px;}
.cv-card{background:#f2f2f2;border-top:2px solid #C8102E;padding:12px;border-radius:2px;}
.cv-card-icon{font-size:18px;margin-bottom:6px;}
.cv-card-title{font-size:11px;font-weight:bold;color:#001f40;text-transform:uppercase;margin-bottom:4px;}
.cv-card-body{font-size:10px;color:#6b7f96;line-height:1.5;}

.cv-callout{display:flex;gap:10px;padding:10px 12px;border-radius:2px;margin-bottom:12px;font-size:11px;line-height:1.6;}
.cv-co-info{background:#e8eef5;border:1px solid #c4d3e8;border-left:3px solid #4a9eff;}
.cv-co-warn{background:#fdf0f2;border:1px solid rgba(200,16,46,.25);border-left:3px solid #C8102E;}
.cv-co-good{background:#eef7f2;border:1px solid #b8ddc9;border-left:3px solid #1a6b3c;}

.cv-blist{list-style:none;}
.cv-blist li{display:flex;align-items:flex-start;gap:8px;padding:6px 0;border-bottom:1px solid #f0f0f0;font-size:11px;color:#2d4a6b;}
.cv-blist li::before{content:'';width:5px;height:5px;border-radius:50%;background:#C8102E;flex-shrink:0;margin-top:4px;}

.cv-table{width:100%;border-collapse:collapse;margin-bottom:12px;font-size:10px;}
.cv-table th{background:#003366;color:#fff;padding:7px 9px;text-align:left;font-size:8px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;}
.cv-table td{padding:7px 9px;border-bottom:1px solid #f0f0f0;color:#2d4a6b;}
.cv-table tr:hover td{background:#f8fafc;}

.cv-video-ph{background:#001f40;display:flex;align-items:center;justify-content:center;min-height:160px;color:rgba(255,255,255,.3);font-size:13px;gap:8px;}

.cv-divider{display:flex;align-items:center;gap:12px;margin:12px 0;font-size:9px;font-weight:bold;color:#9ab;letter-spacing:.12em;text-transform:uppercase;}
.cv-divider::before,.cv-divider::after{content:'';flex:1;height:1px;background:#e8eef5;}

.cv-quiz-q{background:#f8fafc;border:1px solid #e2e8f0;border-radius:2px;padding:10px 12px;margin-bottom:8px;}
.cv-quiz-q-text{font-size:11px;font-weight:bold;color:#001f40;margin-bottom:8px;}
.cv-quiz-opt{font-size:10px;color:#6b7f96;padding:5px 8px;border:1px solid #e2e8f0;margin-bottom:4px;border-radius:2px;display:flex;align-items:center;gap:6px;}
.cv-quiz-opt::before{content:'';width:10px;height:10px;border-radius:50%;border:1.5px solid #dde2e8;flex-shrink:0;}

/* ── RIGHT PANEL ── */
.panel-right{
  background:var(--white);
  border-left:1px solid var(--grey-m);
  display:flex;flex-direction:column;
  overflow:hidden;
}
.props-area{flex:1;overflow-y:auto;}

.prop-section{border-bottom:1px solid var(--grey-m);}
.prop-section-head{
  padding:9px 12px;
  background:var(--grey);
  display:flex;align-items:center;justify-content:space-between;
  cursor:pointer;
  font-size:9px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;color:var(--navy);
  user-select:none;
}
.prop-section-body{padding:10px 12px;}
.prop-section-body.collapsed{display:none;}

.ff{display:flex;flex-direction:column;gap:3px;margin-bottom:8px;}
.ff:last-child{margin-bottom:0;}
.ff label{font-size:8px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);}
.ff input,.ff select,.ff textarea{
  background:var(--white);border:1px solid var(--grey-m);
  padding:.4rem .55rem;color:var(--text);
  font-family:var(--f);font-size:12px;outline:none;width:100%;
  resize:vertical;transition:border-color .12s;
  -webkit-appearance:none;border-radius:0;
}
.ff input:focus,.ff select:focus,.ff textarea:focus{border-color:var(--navy);box-shadow:0 0 0 2px rgba(0,51,102,.06);}

.fg2{display:grid;grid-template-columns:1fr 1fr;gap:6px;}

/* Dynamic list items (cards, bullets, etc.) */
.dlist-item{
  display:flex;align-items:flex-start;gap:5px;
  padding:6px 8px;
  background:var(--grey);
  border:1px solid var(--grey-m);
  margin-bottom:5px;
  position:relative;
}
.dlist-item-fields{flex:1;display:flex;flex-direction:column;gap:4px;}
.dlist-item-del{
  background:none;border:none;color:var(--muted);cursor:pointer;
  font-size:13px;padding:0 2px;line-height:1;flex-shrink:0;
  min-width:18px;min-height:18px;display:flex;align-items:center;justify-content:center;
}
.dlist-item-del:hover{color:var(--red);}
.add-item-btn{
  font-size:10px;font-weight:bold;color:var(--teal);background:none;border:none;
  cursor:pointer;font-family:var(--f);text-transform:uppercase;letter-spacing:.05em;
  padding:4px 0;display:flex;align-items:center;gap:4px;
}

/* Colour picker option */
.co-type-btns{display:flex;gap:4px;}
.co-type-btn{
  flex:1;padding:4px 6px;border:1px solid var(--grey-m);
  background:var(--grey);font-family:var(--f);font-size:9px;font-weight:bold;
  text-transform:uppercase;letter-spacing:.05em;cursor:pointer;color:var(--muted);
  transition:all .12s;
}
.co-type-btn.active{background:var(--navy);border-color:var(--navy);color:#fff;}

/* Quiz question builder */
.quiz-q-item{background:var(--grey);border:1px solid var(--grey-m);padding:8px;margin-bottom:8px;}
.quiz-q-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;}
.quiz-q-label{font-size:9px;font-weight:bold;color:var(--navy);text-transform:uppercase;letter-spacing:.08em;}
.quiz-opt-row{display:flex;align-items:center;gap:5px;margin-bottom:4px;}
.quiz-opt-row input[type="text"]{flex:1;padding:.3rem .45rem;border:1px solid var(--grey-m);font-family:var(--f);font-size:11px;background:#fff;border-radius:0;outline:none;}
.quiz-opt-row input[type="radio"]{accent-color:var(--navy);flex-shrink:0;cursor:pointer;}
.quiz-correct-label{font-size:9px;color:var(--muted);flex-shrink:0;}

/* Course settings */
.settings-section{padding:10px 12px;border-bottom:1px solid var(--grey-m);}

/* Toast */
.toast{
  position:fixed;bottom:20px;right:20px;z-index:9999;
  padding:10px 16px;background:var(--navy);color:#fff;
  font-size:11px;font-weight:bold;
  box-shadow:0 4px 18px rgba(0,0,0,.3);display:none;
  border-left:4px solid var(--teal);
  animation:toastIn .2s ease both;
}
.toast.err{background:var(--red);border-left-color:#ff6b6b;}
@keyframes toastIn{from{opacity:0;transform:translateY(8px);}to{opacity:1;transform:none;}}

/* No-select during drag */
.dragging *{user-select:none;}

/* Empty prop state */
.props-empty{padding:32px 16px;text-align:center;color:var(--muted);font-size:11px;}

/* Scrollbars */
.panel-body::-webkit-scrollbar,.props-area::-webkit-scrollbar,.canvas-area::-webkit-scrollbar{width:5px;}
.panel-body::-webkit-scrollbar-track,.props-area::-webkit-scrollbar-track,.canvas-area::-webkit-scrollbar-track{background:transparent;}
.panel-body::-webkit-scrollbar-thumb,.props-area::-webkit-scrollbar-thumb,.canvas-area::-webkit-scrollbar-thumb{background:#ccd3dc;border-radius:99px;}
</style>
<div id="mobile-block" class="mobile-block">
    <div class="mobile-message">
        <h2>This tool is not supported on mobile devices</h2>
        <p>Please use a desktop or laptop computer to build and edit SCORM courses.</p>
        <p>The drag-and-drop interface and property panels require a larger screen and mouse interaction.</p>
        <div style="margin-top: 2rem; font-size: 1.1rem; opacity: 0.8;">
            ← Return to <a href="{{ route('admin.lms.index') }}" style="color: var(--teal);">LMS Dashboard</a>
        </div>
    </div>
</div>

<style>
    .mobile-block {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.92);
        color: white;
        z-index: 9999;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 20px;
        font-family: var(--f);
    }

    .mobile-message {
        max-width: 480px;
    }

    .mobile-message h2 {
        font-size: 1.8rem;
        margin-bottom: 1.2rem;
        color: var(--red);
    }

    .mobile-message p {
        font-size: 1.1rem;
        line-height: 1.5;
        margin: 0.8rem 0;
    }

    @media (max-width: 1023px) {
        .mobile-block {
            display: flex;
        }

        /* Hide the actual builder on small screens */
        .sb-bar,
        .builder-layout {
            display: none !important;
        }
    }

    @media (max-width: 1023px) and (orientation: landscape) {
        /* Optional: allow very wide tablets in landscape if you want */
        /* .mobile-block { display: none; } */
        /* .sb-bar, .builder-layout { display: grid/flex !important; } */
    }
</style>
<!-- TOP BAR -->
<div class="sb-bar">
  <div class="sb-brand">
    <div class="sb-logo">RAY<br>NET</div>
    <div>
      <div class="sb-title">SCORM Builder</div>
      <div class="sb-sub">Liverpool RAYNET · Drag &amp; Drop Course Creator</div>
    </div>
  </div>
  <div class="sb-actions">
    <span id="slideCountBadge" style="font-size:9px;font-weight:bold;padding:2px 8px;border:1px solid rgba(255,255,255,.15);color:rgba(255,255,255,.5);">0 slides</span>
    <button class="btn btn-ghost btn-sm" onclick="previewCourse()">👁 Preview</button>
    <button class="btn btn-teal btn-sm" onclick="exportScorm()">📦 Export ZIP</button>
    <a href="{{ route('admin.lms.index') }}" class="btn btn-ghost btn-sm">← LMS</a>
  </div>
</div>

<div class="builder-layout">

  <!-- ═══ LEFT: SLIDE PALETTE ═══ -->
  <div class="panel-left">
    <div class="panel-head">Slide Types — Drag to add</div>
    <div class="panel-body">

      <div class="slide-type-grid">
        <div class="slide-type" draggable="true" data-type="hero">
          <span class="slide-type-icon">🏠</span>Hero
        </div>
        <div class="slide-type" draggable="true" data-type="text">
          <span class="slide-type-icon">📄</span>Text
        </div>
        <div class="slide-type" draggable="true" data-type="cards">
          <span class="slide-type-icon">🃏</span>Cards
        </div>
        <div class="slide-type" draggable="true" data-type="bullets">
          <span class="slide-type-icon">📋</span>Bullets
        </div>
        <div class="slide-type" draggable="true" data-type="callout">
          <span class="slide-type-icon">💡</span>Callout
        </div>
        <div class="slide-type" draggable="true" data-type="table">
          <span class="slide-type-icon">📊</span>Table
        </div>
        <div class="slide-type" draggable="true" data-type="video">
          <span class="slide-type-icon">🎬</span>Video
        </div>
        <div class="slide-type" draggable="true" data-type="divider">
          <span class="slide-type-icon">—</span>Divider
        </div>
        <div class="slide-type" draggable="true" data-type="quiz">
          <span class="slide-type-icon">❓</span>Quiz
        </div>
      </div>

      <div style="margin-top:20px;padding-top:14px;border-top:1px solid var(--grey-m);">
        <div style="font-size:9px;font-weight:bold;color:var(--muted);text-transform:uppercase;letter-spacing:.1em;margin-bottom:10px;">Course Settings</div>
        <div class="ff">
          <label>Course Title</label>
          <input type="text" id="courseTitle" placeholder="e.g. Introduction to DMR" value="My RAYNET Course">
        </div>
        <div class="ff">
          <label>Pass Mark (%)</label>
          <input type="number" id="passMark" value="80" min="0" max="100">
        </div>
        <div style="margin-top:6px;padding:8px;background:#e8eef5;border-left:3px solid var(--navy);font-size:10px;color:#2d4a6b;line-height:1.55;">
          ℹ Drag slide types onto the strip or canvas. Click a slide to edit its content on the right. Drag slides in the strip to reorder.
        </div>
      </div>

    </div>
  </div>

  <!-- ═══ CENTRE: CANVAS ═══ -->
  <div class="panel-centre">

    <div class="canvas-toolbar">
      <span class="canvas-toolbar-label">Slides</span>
      <button class="btn btn-primary btn-sm" onclick="addSlide('hero')" id="btnAddHero">+ Hero</button>
      <button class="btn btn-sm" style="background:var(--grey);border-color:var(--grey-m);color:var(--navy);" onclick="duplicateCurrent()">⧉ Duplicate</button>
      <button class="btn btn-red btn-sm" onclick="deleteCurrent()">✕ Delete</button>
      <div style="margin-left:auto;font-size:9px;color:var(--muted);" id="slideIndicator">No slides</div>
    </div>

    <!-- Slide strip -->
    <div class="slide-strip-wrap">
      <div class="slide-strip" id="slideStrip">
        <div class="strip-add" id="stripAdd" title="Drop slide types here">+</div>
      </div>
    </div>

    <!-- Canvas -->
    <div class="canvas-area" id="canvasArea">
      <div class="canvas-frame" id="canvasFrame">
        <div class="canvas-empty" id="canvasEmpty">
          <div class="canvas-empty-icon">📐</div>
          <div class="canvas-empty-text">Your course is empty</div>
          <div class="canvas-empty-sub">Drag a slide type from the left panel, or use the + buttons above</div>
        </div>
      </div>
    </div>

  </div>

  <!-- ═══ RIGHT: PROPERTIES ═══ -->
  <div class="panel-right">
    <div class="panel-head">Slide Properties</div>
    <div class="props-area" id="propsArea">
      <div class="props-empty">Select a slide to edit its content</div>
    </div>
  </div>

</div>

<div class="toast" id="toast"></div>

<script>
// ══════════════════════════════════════════════════════════════
// SCORM BUILDER — STATE
// ══════════════════════════════════════════════════════════════

let slides     = [];   // array of slide objects
let currentIdx = -1;   // currently selected slide index
let dragType   = null; // type being dragged from palette
let dragStripIdx = null; // index being dragged in strip

const SLIDE_ICONS = {
  hero:'🏠', text:'📄', cards:'🃏', bullets:'📋',
  callout:'💡', table:'📊', video:'🎬', divider:'—', quiz:'❓'
};

const DEFAULT_SLIDES = {
  hero:    { type:'hero',    label:'Hero',    eyebrow:'Liverpool RAYNET · Training', title:'Course Title', subtitle:'Subtitle Here', description:'A brief description of this module.' },
  text:    { type:'text',    label:'Text',    tag:'Section 01', title:'Section Title', body:'Enter your text content here.\n\nYou can use multiple paragraphs.' },
  cards:   { type:'cards',   label:'Cards',   tag:'Key Points', title:'Key Concepts', lead:'Overview of the main concepts covered.', cards:[
    {icon:'📡',title:'Point One',body:'Description of the first key point.'},
    {icon:'🔧',title:'Point Two',body:'Description of the second key point.'},
    {icon:'✅',title:'Point Three',body:'Description of the third key point.'},
  ]},
  bullets: { type:'bullets', label:'Bullets', tag:'Overview', title:'Key Points', lead:'The following points are important to understand.', items:['First key point to remember.','Second important item.','Third item on the list.'] },
  callout: { type:'callout', label:'Callout', tag:'Note', title:'Important Information', lead:'', callout_type:'info', icon:'ℹ', callout_title:'Did You Know?', callout_body:'Enter the callout message here.' },
  table:   { type:'table',   label:'Table',   tag:'Reference', title:'Reference Table', lead:'Use this table for quick reference.', headers:['Column 1','Column 2','Column 3'], rows:[['Row 1, Col 1','Row 1, Col 2','Row 1, Col 3'],['Row 2, Col 1','Row 2, Col 2','Row 2, Col 3']] },
  video:   { type:'video',   label:'Video',   tag:'Video',  title:'Watch This Video', lead:'Watch the following video and then proceed.', url:'' },
  divider: { type:'divider', label:'Divider', tag:'', title:'', body:'' },
  quiz:    { type:'quiz',    label:'Quiz',    tag:'Assessment', title:'Knowledge Check', lead:'Answer all questions to complete this module.', questions:[
    {question:'This is a sample question?', options:['Option A','Option B','Option C','Option D'], correct:0}
  ]},
};

// ══════════════════════════════════════════════════════════════
// TOAST
// ══════════════════════════════════════════════════════════════
function toast(msg, err=false) {
  const t = document.getElementById('toast');
  t.textContent = (err?'✕ ':'✓ ') + msg;
  t.className = 'toast' + (err?' err':'');
  t.style.display = 'block';
  clearTimeout(t._t);
  t._t = setTimeout(() => t.style.display='none', 2800);
}

// ══════════════════════════════════════════════════════════════
// SLIDE MANAGEMENT
// ══════════════════════════════════════════════════════════════
function addSlide(type, insertAfter=-1) {
  const slide = JSON.parse(JSON.stringify(DEFAULT_SLIDES[type] || DEFAULT_SLIDES.text));
  if (insertAfter >= 0 && insertAfter < slides.length) {
    slides.splice(insertAfter + 1, 0, slide);
    currentIdx = insertAfter + 1;
  } else {
    slides.push(slide);
    currentIdx = slides.length - 1;
  }
  renderStrip();
  renderCanvas();
  renderProps();
  toast('Slide added.');
}

function deleteSlide(idx) {
  if (idx < 0 || idx >= slides.length) return;
  slides.splice(idx, 1);
  if (currentIdx >= slides.length) currentIdx = slides.length - 1;
  renderStrip();
  renderCanvas();
  renderProps();
  toast('Slide deleted.');
}

function deleteCurrent() {
  if (currentIdx < 0) { toast('No slide selected.', true); return; }
  if (!confirm('Delete this slide?')) return;
  deleteSlide(currentIdx);
}

function duplicateCurrent() {
  if (currentIdx < 0) { toast('No slide selected.', true); return; }
  const clone = JSON.parse(JSON.stringify(slides[currentIdx]));
  slides.splice(currentIdx + 1, 0, clone);
  currentIdx = currentIdx + 1;
  renderStrip();
  renderCanvas();
  renderProps();
  toast('Slide duplicated.');
}

function selectSlide(idx) {
  currentIdx = idx;
  renderStrip();
  renderCanvas();
  renderProps();
}

function updateSlideCount() {
  const badge = document.getElementById('slideCountBadge');
  const ind   = document.getElementById('slideIndicator');
  badge.textContent = slides.length + ' slide' + (slides.length !== 1 ? 's' : '');
  ind.textContent   = slides.length > 0
    ? `Slide ${currentIdx + 1} of ${slides.length}`
    : 'No slides';
}

// ══════════════════════════════════════════════════════════════
// STRIP RENDERING
// ══════════════════════════════════════════════════════════════
function renderStrip() {
  updateSlideCount();
  const strip = document.getElementById('slideStrip');
  const add   = document.getElementById('stripAdd');

  // Remove all except the add zone
  [...strip.children].forEach(el => { if (el !== add) el.remove(); });

  slides.forEach((slide, i) => {
    const el = document.createElement('div');
    el.className  = 'strip-item' + (i === currentIdx ? ' active' : '');
    el.dataset.idx = i;
    el.draggable  = true;
    el.innerHTML  = `
      <span class="strip-item-num">${i + 1}</span>
      <span class="strip-item-icon">${SLIDE_ICONS[slide.type] || '📄'}</span>
      <span class="strip-item-label">${esc(slide.label || slide.type)}</span>
      <button class="strip-item-del" onclick="event.stopPropagation();deleteSlide(${i})" title="Delete">✕</button>
    `;
    el.onclick = () => selectSlide(i);

    // Strip drag-to-reorder
    el.addEventListener('dragstart', e => {
      dragStripIdx = i;
      el.style.opacity = '.4';
      e.dataTransfer.effectAllowed = 'move';
    });
    el.addEventListener('dragend', () => {
      el.style.opacity = '';
      dragStripIdx = null;
      document.querySelectorAll('.strip-item').forEach(e => e.classList.remove('drag-over'));
    });
    el.addEventListener('dragover', e => {
      e.preventDefault();
      if (dragStripIdx !== null && dragStripIdx !== i) {
        document.querySelectorAll('.strip-item').forEach(e => e.classList.remove('drag-over'));
        el.classList.add('drag-over');
      }
      if (dragType) el.classList.add('drag-over');
    });
    el.addEventListener('dragleave', () => el.classList.remove('drag-over'));
    el.addEventListener('drop', e => {
      e.preventDefault();
      el.classList.remove('drag-over');
      if (dragStripIdx !== null && dragStripIdx !== i) {
        // Reorder
        const moved = slides.splice(dragStripIdx, 1)[0];
        slides.splice(i, 0, moved);
        currentIdx = i;
        renderStrip(); renderCanvas(); renderProps();
        toast('Reordered.');
      } else if (dragType) {
        // Insert from palette
        addSlide(dragType, i);
        dragType = null;
      }
    });

    strip.insertBefore(el, add);
  });

  // Add zone events
  add.ondragover = e => { e.preventDefault(); if(dragType) add.classList.add('drag-over'); };
  add.ondragleave = () => add.classList.remove('drag-over');
  add.ondrop = e => {
    e.preventDefault();
    add.classList.remove('drag-over');
    if (dragType) { addSlide(dragType); dragType = null; }
  };
}

// ══════════════════════════════════════════════════════════════
// CANVAS RENDERING
// ══════════════════════════════════════════════════════════════
function renderCanvas() {
  const frame = document.getElementById('canvasFrame');
  const empty = document.getElementById('canvasEmpty');

  if (slides.length === 0 || currentIdx < 0) {
    frame.innerHTML = '';
    if (empty) { empty.style.display = ''; frame.appendChild(empty); }
    return;
  }

  // empty is removed from DOM once frame.innerHTML is first set — guard against null
  if (empty && empty.parentNode === frame) empty.style.display = 'none';

  const slide = slides[currentIdx];
  if (!slide) return;

  frame.innerHTML = previewSlide(slide);
}

function previewSlide(s) {
  switch(s.type) {
    case 'hero':    return previewHero(s);
    case 'cards':   return previewCards(s);
    case 'callout': return previewCallout(s);
    case 'bullets': return previewBullets(s);
    case 'table':   return previewTable(s);
    case 'video':   return previewVideo(s);
    case 'divider': return previewDivider(s);
    case 'quiz':    return previewQuiz(s);
    default:        return previewText(s);
  }
}

function previewHero(s) {
  return `<div class="cv-hero">
    <div class="cv-hero-badge">RAY<br>NET</div>
    <h1>${esc(s.title||'Untitled')}</h1>
    <div class="sub">${esc(s.subtitle||'')}</div>
    <div class="sub" style="font-size:11px;color:rgba(255,255,255,.35);max-width:380px;line-height:1.6;">${esc(s.description||'')}</div>
    <div class="btn-start">Begin Module →</div>
  </div>`;
}

function previewText(s) {
  const body = esc(s.body||'').replace(/\n/g,'<br>');
  return `<div class="cv-lesson">
    <div class="cv-tag">${esc(s.tag||'')}</div>
    <div class="cv-h">${esc(s.title||'')}</div>
    <div style="font-size:12px;color:#2d4a6b;line-height:1.7;">${body}</div>
  </div>`;
}

function previewCards(s) {
  const cards = (s.cards||[]).map(c => `
    <div class="cv-card">
      <div class="cv-card-icon">${esc(c.icon||'📄')}</div>
      <div class="cv-card-title">${esc(c.title||'')}</div>
      <div class="cv-card-body">${esc(c.body||'')}</div>
    </div>`).join('');
  return `<div class="cv-lesson">
    <div class="cv-tag">${esc(s.tag||'')}</div>
    <div class="cv-h">${esc(s.title||'')}</div>
    <div class="cv-lead">${esc(s.lead||'')}</div>
    <div class="cv-card-grid">${cards}</div>
  </div>`;
}

function previewCallout(s) {
  const typeMap = {info:'cv-co-info',warn:'cv-co-warn',good:'cv-co-good'};
  const cls     = typeMap[s.callout_type||'info']||'cv-co-info';
  return `<div class="cv-lesson">
    <div class="cv-tag">${esc(s.tag||'')}</div>
    <div class="cv-h">${esc(s.title||'')}</div>
    <div class="cv-lead">${esc(s.lead||'')}</div>
    <div class="callout ${cls}" style="font-size:11px;">
      <span style="font-size:14px;flex-shrink:0;">${esc(s.icon||'ℹ')}</span>
      <div><div style="font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px;">${esc(s.callout_title||'')}</div>${esc(s.callout_body||'')}</div>
    </div>
  </div>`;
}

function previewBullets(s) {
  const items = (s.items||[]).map(i => `<li>${esc(i)}</li>`).join('');
  return `<div class="cv-lesson">
    <div class="cv-tag">${esc(s.tag||'')}</div>
    <div class="cv-h">${esc(s.title||'')}</div>
    <div class="cv-lead">${esc(s.lead||'')}</div>
    <ul class="cv-blist">${items}</ul>
  </div>`;
}

function previewTable(s) {
  const ths  = (s.headers||[]).map(h=>`<th>${esc(h)}</th>`).join('');
  const rows = (s.rows||[]).map(r=>`<tr>${r.map(c=>`<td>${esc(c)}</td>`).join('')}</tr>`).join('');
  return `<div class="cv-lesson">
    <div class="cv-tag">${esc(s.tag||'')}</div>
    <div class="cv-h">${esc(s.title||'')}</div>
    <div class="cv-lead">${esc(s.lead||'')}</div>
    <table class="cv-table"><thead><tr>${ths}</tr></thead><tbody>${rows}</tbody></table>
  </div>`;
}

function previewVideo(s) {
  return `<div class="cv-lesson">
    <div class="cv-tag">${esc(s.tag||'')}</div>
    <div class="cv-h">${esc(s.title||'')}</div>
    <div class="cv-lead">${esc(s.lead||'')}</div>
    <div class="cv-video-ph">🎬 ${s.url ? esc(s.url) : 'No video URL set'}</div>
  </div>`;
}

function previewDivider(s) {
  return `<div class="cv-lesson">
    <div class="cv-tag">${esc(s.tag||'')}</div>
    ${s.title ? `<div class="cv-h">${esc(s.title)}</div>` : ''}
    <div class="cv-divider">Section Break</div>
    ${s.body ? `<div style="font-size:12px;color:#2d4a6b;line-height:1.7;">${esc(s.body)}</div>` : ''}
  </div>`;
}

function previewQuiz(s) {
  const qs = (s.questions||[]).map((q, qi) => {
    const opts = (q.options||[]).map((o,oi) => `
      <div class="cv-quiz-opt">${esc(o)}${oi===q.correct?' ✓':''}</div>`).join('');
    return `<div class="cv-quiz-q">
      <div class="cv-quiz-q-text">Q${qi+1}. ${esc(q.question||'')}</div>
      ${opts}
    </div>`;
  }).join('');
  return `<div class="cv-lesson">
    <div class="cv-tag">${esc(s.tag||'')}</div>
    <div class="cv-h">${esc(s.title||'')}</div>
    <div class="cv-lead">${esc(s.lead||'')}</div>
    ${qs}
  </div>`;
}

// ══════════════════════════════════════════════════════════════
// PROPERTIES PANEL
// ══════════════════════════════════════════════════════════════
function renderProps() {
  const area = document.getElementById('propsArea');
  if (currentIdx < 0 || !slides[currentIdx]) {
    area.innerHTML = '<div class="props-empty">Select a slide to edit its content</div>';
    return;
  }
  const s = slides[currentIdx];
  area.innerHTML = buildPropsHtml(s, currentIdx);
  bindPropsEvents(currentIdx);
}

function buildPropsHtml(s, idx) {
  const common = `
    <div class="prop-section">
      <div class="prop-section-head" onclick="togglePropSection(this)">Slide Info ▼</div>
      <div class="prop-section-body">
        <div class="ff"><label>Slide Label (shown in strip)</label><input type="text" data-prop="label" value="${escA(s.label||'')}"></div>
        <div class="ff"><label>Section Tag</label><input type="text" data-prop="tag" value="${escA(s.tag||'')}"></div>
        <div class="ff"><label>Title</label><input type="text" data-prop="title" value="${escA(s.title||'')}"></div>
      </div>
    </div>`;

  let specific = '';

  if (s.type === 'hero') {
    specific = `<div class="prop-section">
      <div class="prop-section-head" onclick="togglePropSection(this)">Hero Content ▼</div>
      <div class="prop-section-body">
        <div class="ff"><label>Eyebrow Text</label><input type="text" data-prop="eyebrow" value="${escA(s.eyebrow||'')}"></div>
        <div class="ff"><label>Subtitle</label><input type="text" data-prop="subtitle" value="${escA(s.subtitle||'')}"></div>
        <div class="ff"><label>Description</label><textarea data-prop="description" rows="3">${esc(s.description||'')}</textarea></div>
      </div>
    </div>`;
  }

  if (s.type === 'text' || s.type === 'divider') {
    specific = `<div class="prop-section">
      <div class="prop-section-head" onclick="togglePropSection(this)">Content ▼</div>
      <div class="prop-section-body">
        <div class="ff"><label>Body Text</label><textarea data-prop="body" rows="8" style="font-size:11px;">${esc(s.body||'')}</textarea></div>
      </div>
    </div>`;
  }

  if (s.type === 'cards') {
    let cardsHtml = (s.cards||[]).map((c, ci) => `
      <div class="dlist-item" data-ci="${ci}">
        <div class="dlist-item-fields">
          <input type="text" data-card-icon="${ci}" placeholder="Icon (emoji)" value="${escA(c.icon||'')}" style="padding:.25rem .4rem;border:1px solid var(--grey-m);font-family:var(--f);font-size:11px;">
          <input type="text" data-card-title="${ci}" placeholder="Card title" value="${escA(c.title||'')}" style="padding:.25rem .4rem;border:1px solid var(--grey-m);font-family:var(--f);font-size:11px;">
          <textarea data-card-body="${ci}" placeholder="Card body" rows="2" style="padding:.25rem .4rem;border:1px solid var(--grey-m);font-family:var(--f);font-size:11px;resize:vertical;">${esc(c.body||'')}</textarea>
        </div>
        <button class="dlist-item-del" onclick="removeCard(${idx},${ci})">✕</button>
      </div>`).join('');
    specific = `<div class="prop-section">
      <div class="prop-section-head" onclick="togglePropSection(this)">Cards ▼</div>
      <div class="prop-section-body">
        <div class="ff"><label>Lead Text</label><textarea data-prop="lead" rows="2">${esc(s.lead||'')}</textarea></div>
        <div id="cardsContainer">${cardsHtml}</div>
        <button class="add-item-btn" onclick="addCard(${idx})">+ Add Card</button>
      </div>
    </div>`;
  }

  if (s.type === 'bullets') {
    let itemsHtml = (s.items||[]).map((item, ii) => `
      <div class="dlist-item">
        <div class="dlist-item-fields">
          <input type="text" data-bullet-idx="${ii}" placeholder="Bullet point text" value="${escA(item)}" style="padding:.25rem .4rem;border:1px solid var(--grey-m);font-family:var(--f);font-size:11px;">
        </div>
        <button class="dlist-item-del" onclick="removeBullet(${idx},${ii})">✕</button>
      </div>`).join('');
    specific = `<div class="prop-section">
      <div class="prop-section-head" onclick="togglePropSection(this)">Bullet Items ▼</div>
      <div class="prop-section-body">
        <div class="ff"><label>Lead Text</label><textarea data-prop="lead" rows="2">${esc(s.lead||'')}</textarea></div>
        <div id="bulletsContainer">${itemsHtml}</div>
        <button class="add-item-btn" onclick="addBullet(${idx})">+ Add Bullet</button>
      </div>
    </div>`;
  }

  if (s.type === 'callout') {
    specific = `<div class="prop-section">
      <div class="prop-section-head" onclick="togglePropSection(this)">Callout Content ▼</div>
      <div class="prop-section-body">
        <div class="ff"><label>Lead Text</label><textarea data-prop="lead" rows="2">${esc(s.lead||'')}</textarea></div>
        <div class="ff"><label>Type</label>
          <div class="co-type-btns">
            <button class="co-type-btn${s.callout_type==='info'?' active':''}" onclick="setCalloutType(${idx},'info')">Info</button>
            <button class="co-type-btn${s.callout_type==='warn'?' active':''}" onclick="setCalloutType(${idx},'warn')">Warning</button>
            <button class="co-type-btn${s.callout_type==='good'?' active':''}" onclick="setCalloutType(${idx},'good')">Success</button>
          </div>
        </div>
        <div class="ff"><label>Icon</label><input type="text" data-prop="icon" value="${escA(s.icon||'ℹ')}" placeholder="Emoji or symbol"></div>
        <div class="ff"><label>Callout Title</label><input type="text" data-prop="callout_title" value="${escA(s.callout_title||'')}"></div>
        <div class="ff"><label>Callout Body</label><textarea data-prop="callout_body" rows="4">${esc(s.callout_body||'')}</textarea></div>
      </div>
    </div>`;
  }

  if (s.type === 'table') {
    const headersVal = (s.headers||[]).join('\n');
    let rowsHtml = (s.rows||[]).map((row, ri) => `
      <div class="dlist-item">
        <div class="dlist-item-fields">
          <input type="text" data-row-idx="${ri}" placeholder="Comma-separated values" value="${escA(row.join(','))}" style="padding:.25rem .4rem;border:1px solid var(--grey-m);font-family:var(--f);font-size:11px;">
        </div>
        <button class="dlist-item-del" onclick="removeRow(${idx},${ri})">✕</button>
      </div>`).join('');
    specific = `<div class="prop-section">
      <div class="prop-section-head" onclick="togglePropSection(this)">Table Data ▼</div>
      <div class="prop-section-body">
        <div class="ff"><label>Lead Text</label><textarea data-prop="lead" rows="2">${esc(s.lead||'')}</textarea></div>
        <div class="ff"><label>Column Headers (one per line)</label><textarea id="tableHeaders" rows="3" style="font-size:11px;">${esc(headersVal)}</textarea></div>
        <div style="font-size:9px;font-weight:bold;color:var(--muted);text-transform:uppercase;letter-spacing:.1em;margin:8px 0 5px;">Rows (comma-separated cells)</div>
        <div id="rowsContainer">${rowsHtml}</div>
        <button class="add-item-btn" onclick="addRow(${idx})">+ Add Row</button>
      </div>
    </div>`;
  }

  if (s.type === 'video') {
    specific = `<div class="prop-section">
      <div class="prop-section-head" onclick="togglePropSection(this)">Video ▼</div>
      <div class="prop-section-body">
        <div class="ff"><label>Lead Text</label><textarea data-prop="lead" rows="2">${esc(s.lead||'')}</textarea></div>
        <div class="ff"><label>YouTube / Vimeo URL</label><input type="text" data-prop="url" value="${escA(s.url||'')}" placeholder="https://youtube.com/watch?v=..."></div>
      </div>
    </div>`;
  }

  if (s.type === 'quiz') {
    let qsHtml = (s.questions||[]).map((q, qi) => {
      const optsHtml = (q.options||['','','','']).map((opt, oi) => `
        <div class="quiz-opt-row">
          <input type="radio" name="correct_${qi}" value="${oi}" ${q.correct===oi?'checked':''} onchange="setCorrect(${idx},${qi},${oi})">
          <input type="text" data-q-opt="${qi}_${oi}" placeholder="Option ${String.fromCharCode(65+oi)}" value="${escA(opt)}" style="flex:1;">
          <span class="quiz-correct-label">✓?</span>
        </div>`).join('');
      return `<div class="quiz-q-item" id="qq${qi}">
        <div class="quiz-q-head">
          <span class="quiz-q-label">Q${qi+1}</span>
          <button class="btn btn-red btn-xs" onclick="removeQuestion(${idx},${qi})">✕</button>
        </div>
        <div class="ff"><textarea data-q-text="${qi}" rows="2" placeholder="Question text…" style="font-size:11px;">${esc(q.question||'')}</textarea></div>
        ${optsHtml}
      </div>`;
    }).join('');
    specific = `<div class="prop-section">
      <div class="prop-section-head" onclick="togglePropSection(this)">Questions ▼</div>
      <div class="prop-section-body">
        <div class="ff"><label>Lead Text</label><textarea data-prop="lead" rows="2">${esc(s.lead||'')}</textarea></div>
        <div id="questionsContainer">${qsHtml}</div>
        <button class="add-item-btn" onclick="addQuestion(${idx})">+ Add Question</button>
      </div>
    </div>`;
  }

  return common + specific + `
    <div style="padding:10px 12px;display:flex;gap:6px;border-top:1px solid var(--grey-m);">
      <button class="btn btn-primary" style="flex:1;justify-content:center;" onclick="saveProps(${idx})">💾 Apply Changes</button>
    </div>`;
}

function togglePropSection(head) {
  const body = head.nextElementSibling;
  body.classList.toggle('collapsed');
  head.textContent = head.textContent.replace(/[▼▲]/g, body.classList.contains('collapsed') ? '▲' : '▼');
}

// ── Bind live events in props ────────────────────────────────
function bindPropsEvents(idx) {
  // Auto-save on change for common fields
  document.querySelectorAll('#propsArea [data-prop]').forEach(el => {
    el.addEventListener('input', () => {
      if (!slides[idx]) return;
      slides[idx][el.dataset.prop] = el.value;
      if (el.dataset.prop === 'label') renderStrip();
      renderCanvas();
    });
  });

  // Card fields
  document.querySelectorAll('[data-card-icon]').forEach(el => {
    const ci = parseInt(el.dataset.cardIcon);
    el.addEventListener('input', () => { if(slides[idx]?.cards?.[ci]) { slides[idx].cards[ci].icon = el.value; renderCanvas(); } });
  });
  document.querySelectorAll('[data-card-title]').forEach(el => {
    const ci = parseInt(el.dataset.cardTitle);
    el.addEventListener('input', () => { if(slides[idx]?.cards?.[ci]) { slides[idx].cards[ci].title = el.value; renderCanvas(); } });
  });
  document.querySelectorAll('[data-card-body]').forEach(el => {
    const ci = parseInt(el.dataset.cardBody);
    el.addEventListener('input', () => { if(slides[idx]?.cards?.[ci]) { slides[idx].cards[ci].body = el.value; renderCanvas(); } });
  });

  // Bullet items
  document.querySelectorAll('[data-bullet-idx]').forEach(el => {
    const bi = parseInt(el.dataset.bulletIdx);
    el.addEventListener('input', () => { if(slides[idx]?.items) { slides[idx].items[bi] = el.value; renderCanvas(); } });
  });

  // Table headers
  const thEl = document.getElementById('tableHeaders');
  if (thEl) thEl.addEventListener('input', () => {
    if(slides[idx]) { slides[idx].headers = thEl.value.split('\n').filter(h=>h.trim()); renderCanvas(); }
  });

  // Table rows
  document.querySelectorAll('[data-row-idx]').forEach(el => {
    const ri = parseInt(el.dataset.rowIdx);
    el.addEventListener('input', () => {
      if(slides[idx]?.rows) { slides[idx].rows[ri] = el.value.split(',').map(c=>c.trim()); renderCanvas(); }
    });
  });

  // Quiz question texts
  document.querySelectorAll('[data-q-text]').forEach(el => {
    const qi = parseInt(el.dataset.qText);
    el.addEventListener('input', () => { if(slides[idx]?.questions?.[qi]) { slides[idx].questions[qi].question = el.value; renderCanvas(); } });
  });

  // Quiz options
  document.querySelectorAll('[data-q-opt]').forEach(el => {
    const [qi, oi] = el.dataset.qOpt.split('_').map(Number);
    el.addEventListener('input', () => {
      if(slides[idx]?.questions?.[qi]?.options) { slides[idx].questions[qi].options[oi] = el.value; renderCanvas(); }
    });
  });
}

function saveProps(idx) {
  renderStrip();
  renderCanvas();
  toast('Changes applied.');
}

// ── Dynamic list operations ──────────────────────────────────
function removeCard(idx, ci) {
  slides[idx].cards.splice(ci, 1);
  renderCanvas(); renderProps();
}
function addCard(idx) {
  slides[idx].cards = slides[idx].cards || [];
  slides[idx].cards.push({icon:'📄',title:'New Card',body:'Card description.'});
  renderCanvas(); renderProps();
}
function removeBullet(idx, bi) {
  slides[idx].items.splice(bi, 1);
  renderCanvas(); renderProps();
}
function addBullet(idx) {
  slides[idx].items = slides[idx].items || [];
  slides[idx].items.push('New bullet point.');
  renderCanvas(); renderProps();
}
function removeRow(idx, ri) {
  slides[idx].rows.splice(ri, 1);
  renderCanvas(); renderProps();
}
function addRow(idx) {
  slides[idx].rows = slides[idx].rows || [];
  const cols = (slides[idx].headers||[]).length || 3;
  slides[idx].rows.push(Array(cols).fill(''));
  renderCanvas(); renderProps();
}
function removeQuestion(idx, qi) {
  slides[idx].questions.splice(qi, 1);
  renderCanvas(); renderProps();
}
function addQuestion(idx) {
  slides[idx].questions = slides[idx].questions || [];
  slides[idx].questions.push({question:'New question?',options:['Option A','Option B','Option C','Option D'],correct:0});
  renderCanvas(); renderProps();
}
function setCorrect(idx, qi, oi) {
  if(slides[idx]?.questions?.[qi]) slides[idx].questions[qi].correct = oi;
  renderCanvas();
}
function setCalloutType(idx, type) {
  slides[idx].callout_type = type;
  renderCanvas(); renderProps();
}

// ══════════════════════════════════════════════════════════════
// DRAG FROM PALETTE
// ══════════════════════════════════════════════════════════════
document.querySelectorAll('.slide-type').forEach(el => {
  el.addEventListener('dragstart', e => {
    dragType = el.dataset.type;
    el.classList.add('dragging-source');
    e.dataTransfer.effectAllowed = 'copy';
  });
  el.addEventListener('dragend', () => {
    dragType = null;
    el.classList.remove('dragging-source');
    document.querySelectorAll('.strip-item,.strip-add').forEach(e => e.classList.remove('drag-over'));
  });
});

// Allow dropping on canvas area
const canvasArea = document.getElementById('canvasArea');
canvasArea.addEventListener('dragover', e => { if(dragType) { e.preventDefault(); } });
canvasArea.addEventListener('drop', e => {
  e.preventDefault();
  if(dragType) { addSlide(dragType); dragType = null; }
});

// ══════════════════════════════════════════════════════════════
// EXPORT
// ══════════════════════════════════════════════════════════════
function exportScorm() {
  if (slides.length === 0) { toast('Add at least one slide first.', true); return; }
  const title    = document.getElementById('courseTitle').value.trim() || 'My RAYNET Course';
  const passMark = document.getElementById('passMark').value || '80';

  toast('Generating SCORM package…');

  const form = document.createElement('form');
  form.method = 'POST';
  form.action = '{{ route("admin.lms.scorm-builder.export") }}';
  form.style.display = 'none';

  const fields = {
    _token:    '{{ csrf_token() }}',
    title:     title,
    pass_mark: passMark,
    slides:    JSON.stringify(slides),
  };

  Object.entries(fields).forEach(([k, v]) => {
    const inp = document.createElement('input');
    inp.type = 'hidden'; inp.name = k; inp.value = v;
    form.appendChild(inp);
  });

  document.body.appendChild(form);
  form.submit();
  setTimeout(() => { document.body.removeChild(form); toast('ZIP download started!'); }, 800);
}

function previewCourse() {
  if (slides.length === 0) { toast('Add slides first.', true); return; }
  toast('Preview: open the exported ZIP to test in a SCORM player.');
}

// ══════════════════════════════════════════════════════════════
// HELPERS
// ══════════════════════════════════════════════════════════════
function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function escA(s){ return String(s||'').replace(/"/g,'&quot;'); }

// ══════════════════════════════════════════════════════════════
// INIT — start with a hero + text slide
// ══════════════════════════════════════════════════════════════
addSlide('hero');
addSlide('text');
currentIdx = 0;
renderStrip();
renderCanvas();
renderProps();
toast('Builder ready — drag slides to build your course!');
</script>
@endsection