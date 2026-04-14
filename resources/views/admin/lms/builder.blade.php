@extends('layouts.app')
@section('title', $course ? 'Build: ' . $course->title : 'New Course')
@section('content')
<style>
:root{
    --navy:#003366;--red:#C8102E;--teal:#0288d1;--green:#1a6b3c;
    --green-bg:#eef7f2;--grey:#f2f2f2;--grey-mid:#dde2e8;--white:#fff;
    --text:#001f40;--text-mid:#2d4a6b;--muted:#6b7f96;
    --shadow-sm:0 1px 3px rgba(0,51,102,.09);
    --font:Arial,'Helvetica Neue',Helvetica,sans-serif;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{-webkit-text-size-adjust:100%;}
body{font-family:var(--font);background:var(--grey);color:var(--text);min-height:100vh;}

/* ── HEADER ── */
.bh{background:var(--navy);border-bottom:4px solid var(--red);position:sticky;top:0;z-index:200;box-shadow:0 2px 12px rgba(0,0,0,.3);}
.bh-inner{max-width:1400px;margin:0 auto;padding:0 1rem;display:flex;align-items:center;justify-content:space-between;height:52px;gap:.75rem;}
.bh-brand{display:flex;align-items:center;gap:.6rem;min-width:0;}
.bh-logo{width:30px;height:30px;background:var(--red);display:flex;align-items:center;justify-content:center;font-size:7px;font-weight:bold;color:#fff;text-align:center;line-height:1.2;text-transform:uppercase;flex-shrink:0;}
.bh-title{font-size:12px;font-weight:bold;color:#fff;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:200px;}
.bh-sub{font-size:9px;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.08em;}
.bh-actions{display:flex;align-items:center;gap:.4rem;flex-shrink:0;}

/* ── BUTTONS ── */
.btn{display:inline-flex;align-items:center;justify-content:center;gap:.3rem;padding:.4rem .85rem;border:1px solid;font-family:var(--font);font-size:11px;font-weight:bold;cursor:pointer;text-decoration:none;text-transform:uppercase;letter-spacing:.05em;transition:all .12s;white-space:nowrap;-webkit-tap-highlight-color:transparent;}
.btn-primary{background:var(--navy);border-color:var(--navy);color:#fff;}
.btn-primary:hover,.btn-primary:active{background:#002244;}
.btn-teal{background:var(--teal);border-color:var(--teal);color:#fff;}
.btn-teal:hover{background:#0277bd;}
.btn-red{background:rgba(200,16,46,.08);border-color:rgba(200,16,46,.3);color:var(--red);}
.btn-red:hover,.btn-red:active{background:rgba(200,16,46,.18);}
.btn-ghost{background:transparent;border-color:rgba(255,255,255,.2);color:rgba(255,255,255,.7);}
.btn-ghost:hover{border-color:rgba(255,255,255,.5);color:#fff;}
.btn-ghost-dark{background:transparent;border-color:var(--grey-mid);color:var(--muted);}
.btn-ghost-dark:hover{border-color:var(--navy);color:var(--navy);}
.btn-sm{padding:.22rem .6rem;font-size:10px;}
.btn-xs{padding:.16rem .45rem;font-size:10px;}

/* ── TABS ── */
.tab-bar{background:var(--white);border-bottom:2px solid var(--grey-mid);display:flex;overflow-x:auto;-webkit-overflow-scrolling:touch;}
.tab-bar::-webkit-scrollbar{display:none;}
.tab-btn{flex-shrink:0;padding:.65rem 1.1rem;background:transparent;border:none;border-bottom:3px solid transparent;color:var(--muted);font-family:var(--font);font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:.06em;cursor:pointer;transition:all .15s;margin-bottom:-2px;-webkit-tap-highlight-color:transparent;}
.tab-btn.active{color:var(--navy);border-bottom-color:var(--red);}
.tab-pane{display:none;}
.tab-pane.active{display:block;}

/* ── WRAP ── */
.bwrap{max-width:1400px;margin:0 auto;padding:1rem 1rem 5rem;}

/* ── ALERTS ── */
.alert{padding:.65rem 1rem;margin-bottom:1rem;font-size:13px;font-weight:bold;display:flex;align-items:center;gap:.6rem;}
.alert-success{background:var(--green-bg);color:var(--green);border:1px solid #b8ddc9;border-left:3px solid var(--green);}
.alert-error{background:#fdf0f2;color:var(--red);border:1px solid rgba(200,16,46,.25);border-left:3px solid var(--red);}

/* ── MOBILE NAV BAR ── */
.mpnav{display:none;background:var(--navy);border-bottom:3px solid var(--red);}
.mpbtn{flex:1;padding:.7rem .5rem;background:transparent;border:none;border-bottom:3px solid transparent;color:rgba(255,255,255,.5);font-family:var(--font);font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:.06em;cursor:pointer;transition:all .15s;margin-bottom:-3px;-webkit-tap-highlight-color:transparent;}
.mpbtn.active{color:#fff;border-bottom-color:var(--teal);}

/* ── BUILDER LAYOUT ── */
.blayout{display:grid;grid-template-columns:300px 1fr;gap:1rem;align-items:start;}

/* ── SIDEBAR ── */
.sidebar{background:var(--white);border:1px solid var(--grey-mid);box-shadow:var(--shadow-sm);position:sticky;top:60px;max-height:calc(100vh - 80px);overflow-y:auto;}
.sb-head{padding:.65rem .9rem;border-bottom:1px solid var(--grey-mid);background:var(--grey);display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:5;}
.sb-head-title{font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;color:var(--navy);}
.sb-empty{padding:2rem 1rem;text-align:center;font-size:12px;color:var(--muted);}

/* ── MODULES ── */
.mod-item{border-bottom:1px solid var(--grey-mid);}
.mod-hdr{display:flex;align-items:center;gap:.45rem;padding:.55rem .75rem;background:var(--grey);user-select:none;transition:background .1s;}
.mod-hdr:hover{background:#e8eef5;}
.mod-drag{color:var(--muted);font-size:14px;cursor:grab;flex-shrink:0;padding:4px;min-width:28px;min-height:28px;display:flex;align-items:center;justify-content:center;}
.mod-drag:active{cursor:grabbing;}
.mod-name{flex:1;font-size:12px;font-weight:bold;color:var(--navy);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.mod-toggle{font-size:10px;color:var(--muted);transition:transform .2s;display:inline-block;flex-shrink:0;padding:4px;}
.mod-toggle.open{transform:rotate(180deg);}
.mod-lessons{padding:.2rem 0;}
.mod-lessons.collapsed{display:none;}

/* ── LESSONS ── */
.les-item{
    display:flex;align-items:center;gap:.45rem;
    padding:.6rem .75rem .6rem 1.2rem;
    cursor:pointer;
    border-left:3px solid transparent;
    transition:background .1s;
    min-height:44px;
    -webkit-tap-highlight-color:rgba(0,51,102,.12);
}
.les-item:hover,.les-item:active{background:#f0f5ff;}
.les-item.active{background:#e8eef5;border-left-color:var(--navy);}
.les-drag{color:var(--muted);font-size:12px;flex-shrink:0;padding:4px;min-width:24px;min-height:36px;display:flex;align-items:center;justify-content:center;}
.les-icon{font-size:13px;flex-shrink:0;width:20px;text-align:center;}
.les-name{flex:1;font-size:12px;color:var(--text-mid);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.les-arrow{font-size:11px;color:var(--muted);flex-shrink:0;}

.add-les-btn{
    display:flex;align-items:center;gap:.4rem;
    padding:.55rem .75rem .55rem 1.2rem;
    min-height:44px;
    font-size:11px;color:var(--teal);font-weight:bold;
    cursor:pointer;background:none;border:none;
    font-family:var(--font);text-align:left;width:100%;
    transition:background .1s;text-transform:uppercase;letter-spacing:.05em;
    -webkit-tap-highlight-color:transparent;
}
.add-les-btn:hover,.add-les-btn:active{background:#f0f8ff;}

.add-mod-btn{
    display:flex;align-items:center;gap:.5rem;
    padding:.7rem 1rem;min-height:48px;
    font-size:11px;color:var(--navy);font-weight:bold;
    cursor:pointer;background:var(--grey);border:none;
    border-top:2px solid var(--navy);
    font-family:var(--font);text-align:left;width:100%;
    transition:background .1s;text-transform:uppercase;letter-spacing:.06em;
    -webkit-tap-highlight-color:transparent;
}
.add-mod-btn:hover,.add-mod-btn:active{background:#e8eef5;}

/* ── EDITOR PANEL ── */
.ep{background:var(--white);border:1px solid var(--grey-mid);box-shadow:var(--shadow-sm);}
.ep-head{padding:.65rem 1rem;border-bottom:1px solid var(--grey-mid);background:var(--grey);display:flex;align-items:center;justify-content:space-between;gap:.5rem;min-height:44px;}
.ep-title{font-size:11px;font-weight:bold;color:var(--navy);text-transform:uppercase;letter-spacing:.06em;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.ep-empty{padding:3rem 1rem;text-align:center;}
.ep-empty-icon{font-size:3rem;opacity:.12;margin-bottom:.65rem;}
.ep-empty-text{font-size:13px;color:var(--muted);}
.ep-foot{padding:.75rem 1rem;border-top:1px solid var(--grey-mid);background:var(--grey);display:flex;align-items:center;justify-content:flex-end;gap:.5rem;flex-wrap:wrap;}
.ep-foot .btn{flex:1;max-width:160px;justify-content:center;}

/* ── FORM ── */
.ep-body{padding:1rem;}
.ff{display:flex;flex-direction:column;gap:.3rem;margin-bottom:.8rem;}
.ff:last-child{margin-bottom:0;}
.ff label{font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);}
.ff input,.ff select,.ff textarea{background:var(--white);border:1px solid var(--grey-mid);padding:.5rem .7rem;color:var(--text);font-family:var(--font);font-size:13px;outline:none;width:100%;resize:vertical;transition:border-color .15s;-webkit-appearance:none;border-radius:0;}
.ff input:focus,.ff select:focus,.ff textarea:focus{border-color:var(--navy);box-shadow:0 0 0 3px rgba(0,51,102,.07);}
.fg2{display:grid;grid-template-columns:1fr 1fr;gap:.7rem;}
.trow{display:flex;align-items:center;gap:.6rem;padding:.5rem .7rem;background:var(--grey);border:1px solid var(--grey-mid);cursor:pointer;margin-bottom:.8rem;min-height:44px;-webkit-tap-highlight-color:transparent;}
.trow input[type="checkbox"]{width:15px;height:15px;accent-color:var(--navy);flex-shrink:0;cursor:pointer;}
.trow-label{font-size:12px;font-weight:bold;}
.trow-sub{font-size:11px;color:var(--muted);}
.sec-lbl{font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.12em;color:var(--muted);margin-bottom:.45rem;padding-top:.45rem;border-top:1px solid var(--grey-mid);}

/* ── QUIZ BUILDER ── */
.qb{margin-top:.65rem;}
.qcard{background:var(--grey);border:1px solid var(--grey-mid);margin-bottom:.65rem;overflow:hidden;}
.qcard-head{padding:.5rem .75rem;border-bottom:1px solid var(--grey-mid);background:#fff;display:flex;align-items:center;gap:.45rem;flex-wrap:wrap;}
.qnum{font-size:10px;font-weight:bold;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;flex-shrink:0;}
.qcard-body{padding:.65rem .75rem;}
.ans-row{display:flex;align-items:center;gap:.45rem;margin-bottom:.35rem;}
.ans-row input[type="text"]{flex:1;padding:.35rem .55rem;border:1px solid var(--grey-mid);font-family:var(--font);font-size:12px;border-radius:0;-webkit-appearance:none;}
.ans-row input[type="checkbox"]{width:15px;height:15px;accent-color:var(--green);flex-shrink:0;}
.del-ans{background:none;border:none;color:var(--muted);cursor:pointer;font-size:14px;padding:0 3px;min-width:24px;min-height:24px;display:flex;align-items:center;justify-content:center;}
.del-ans:hover{color:var(--red);}
.add-ans-btn,.add-q-btn{font-size:11px;color:var(--teal);font-weight:bold;background:none;border:none;cursor:pointer;font-family:var(--font);padding:.2rem 0;text-transform:uppercase;letter-spacing:.05em;-webkit-tap-highlight-color:transparent;}

/* ── SETTINGS GRID ── */
.sg{display:grid;grid-template-columns:1fr 1fr;gap:.75rem;}
.card{background:var(--white);border:1px solid var(--grey-mid);margin-bottom:1rem;box-shadow:var(--shadow-sm);}
.card-head{padding:.65rem 1rem;border-bottom:1px solid var(--grey-mid);background:var(--grey);}
.card-title{font-size:12px;font-weight:bold;color:var(--navy);text-transform:uppercase;letter-spacing:.06em;}
.card-body{padding:1rem;}
.card-foot{padding:.65rem 1rem;border-top:1px solid var(--grey-mid);background:var(--grey);display:flex;justify-content:flex-end;gap:.5rem;}

/* ── BADGE SELECTOR ── */
.badge-sg{display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:.45rem;margin-top:.45rem;}
.badge-item{display:flex;align-items:center;gap:.55rem;padding:.5rem .65rem;border:1px solid var(--grey-mid);background:var(--grey);cursor:pointer;transition:border-color .15s,background .15s;user-select:none;-webkit-tap-highlight-color:transparent;}
.badge-item:hover,.badge-item:active{border-color:var(--navy);background:#e8eef5;}
.badge-item.selected{border-color:var(--navy);background:#e8eef5;}
.badge-item input[type="checkbox"]{width:14px;height:14px;accent-color:var(--navy);flex-shrink:0;cursor:pointer;}
.badge-hex{width:26px;height:26px;flex-shrink:0;}
.badge-hex svg{width:26px;height:26px;}
.badge-name{font-size:11px;font-weight:bold;color:var(--text);line-height:1.2;}
.badge-cat{font-size:10px;color:var(--muted);}
.badge-sh{font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.12em;color:var(--muted);margin-top:.75rem;margin-bottom:.3rem;padding-bottom:.2rem;border-bottom:1px solid var(--grey-mid);}

/* ── ENROLLMENT ── */
.enroll-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}
.member-list{border:1px solid var(--grey-mid);max-height:280px;overflow-y:auto;}
.member-row{display:flex;align-items:center;gap:.6rem;padding:.5rem .8rem;border-bottom:1px solid var(--grey-mid);transition:background .1s;cursor:pointer;min-height:44px;-webkit-tap-highlight-color:transparent;}
.member-row:last-child{border-bottom:none;}
.member-row:hover,.member-row:active{background:var(--grey);}
.member-row input[type="checkbox"]{width:14px;height:14px;accent-color:var(--navy);flex-shrink:0;cursor:pointer;}
.member-name{font-size:12px;font-weight:bold;color:var(--text);}
.member-cs{font-size:10px;color:var(--muted);font-family:monospace;}

/* ── TOAST ── */
.toast{position:fixed;bottom:1.25rem;right:1.25rem;z-index:9999;padding:.6rem 1.1rem;background:var(--navy);color:#fff;font-size:12px;font-weight:bold;box-shadow:0 4px 20px rgba(0,0,0,.35);display:none;border-left:4px solid var(--teal);}

/* ── MODAL ── */
.rn-overlay{display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,20,50,.8);align-items:center;justify-content:center;padding:1rem;}
.rn-overlay.open{display:flex;}
.rn-modal{background:var(--white);border:1px solid var(--grey-mid);width:100%;max-width:440px;box-shadow:0 20px 60px rgba(0,0,0,.4);overflow:hidden;}
.rn-modal-head{background:var(--navy);padding:.85rem 1.1rem;display:flex;align-items:center;justify-content:space-between;border-bottom:3px solid var(--red);}
.rn-modal-logo{width:26px;height:26px;background:var(--red);display:flex;align-items:center;justify-content:center;font-size:7px;font-weight:bold;color:#fff;text-align:center;line-height:1.2;text-transform:uppercase;flex-shrink:0;}
.rn-modal-title{font-size:12px;font-weight:bold;color:#fff;text-transform:uppercase;letter-spacing:.06em;margin-left:.65rem;flex:1;}
.rn-modal-close{background:transparent;border:1px solid rgba(255,255,255,.2);color:rgba(255,255,255,.7);cursor:pointer;font-size:13px;width:26px;height:26px;display:flex;align-items:center;justify-content:center;font-family:var(--font);-webkit-tap-highlight-color:transparent;}
.rn-modal-close:hover{border-color:var(--red);color:var(--red);}
.rn-modal-body{padding:1.1rem;}
.rn-modal-foot{padding:.75rem 1.1rem;background:var(--grey);border-top:1px solid var(--grey-mid);display:flex;align-items:center;justify-content:flex-end;gap:.5rem;}
.rn-field{display:flex;flex-direction:column;gap:.3rem;margin-bottom:.75rem;}
.rn-field:last-child{margin-bottom:0;}
.rn-field label{font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);}
.rn-field input,.rn-field select{background:var(--white);border:1px solid var(--grey-mid);padding:.55rem .8rem;color:var(--text);font-family:var(--font);font-size:13px;outline:none;width:100%;-webkit-appearance:none;border-radius:0;}
.rn-field input:focus,.rn-field select:focus{border-color:var(--navy);}

/* ── BACK BTN ── */
.back-btn{display:none;background:none;border:1px solid var(--grey-mid);color:var(--muted);padding:.25rem .55rem;font-size:11px;cursor:pointer;font-family:var(--font);flex-shrink:0;min-height:32px;-webkit-tap-highlight-color:transparent;}
.back-btn:hover,.back-btn:active{border-color:var(--navy);color:var(--navy);}

/* info note */
.inote{padding:.45rem .75rem;font-size:11px;color:var(--text-mid);background:#e8eef5;border-left:3px solid var(--navy);margin-bottom:.75rem;line-height:1.6;}

/* ── DESKTOP ── */
@media(min-width:901px){
    .mpnav{display:none!important;}
    .sidebar{display:block!important;}
    .ep{display:block!important;}
    .back-btn{display:none!important;}
}

/* ── MOBILE ── */
@media(max-width:900px){
    .mpnav{display:flex;}
    .blayout{display:block;}
    .sidebar{
        position:static;max-height:none;
        display:none;
    }
    .sidebar.mp-active{display:block;}
    .ep{display:none;}
    .ep.mp-active{display:block;}
    .back-btn{display:flex!important;align-items:center;}
    .bwrap{padding:.65rem .65rem 5rem;}
    .fg2{grid-template-columns:1fr;}
    .sg{grid-template-columns:1fr;}
    .enroll-grid{grid-template-columns:1fr;}
    .badge-sg{grid-template-columns:1fr 1fr;}
    .bh-title{max-width:140px;font-size:11px;}
    .ep-foot .btn{flex:1;max-width:none;}
}
@media(max-width:480px){
    .badge-sg{grid-template-columns:1fr;}
    .fg2{grid-template-columns:1fr;}
    .bh-title{max-width:110px;}
}
</style>

@php
$isNew      = !$course;
$courseId   = $course?->id;
$courseJson = $course ? json_encode($course->load('modules.lessons.quiz.questions.answers')) : 'null';
$csrf       = csrf_token();
@endphp

{{-- MODAL --}}
<div class="rn-overlay" id="rnOverlay" onclick="if(event.target===this)closeModal()">
    <div class="rn-modal">
        <div class="rn-modal-head">
            <div class="rn-modal-logo">RAY<br>NET</div>
            <div class="rn-modal-title" id="rnTitle">Add Module</div>
            <button class="rn-modal-close" onclick="closeModal()">✕</button>
        </div>
        <div class="rn-modal-body" id="rnBody"></div>
        <div class="rn-modal-foot">
            <button class="btn btn-ghost-dark" onclick="closeModal()">Cancel</button>
            <button class="btn btn-primary" id="rnConfirm">Confirm</button>
        </div>
    </div>
</div>

{{-- HEADER --}}
<div class="bh">
    <div class="bh-inner">
        <div class="bh-brand">
            <div class="bh-logo">RAY<br>NET</div>
            <div>
                <div class="bh-title" id="headerTitle">{{ $course?->title ?? 'New Course' }}</div>
                <div class="bh-sub">Course Builder</div>
            </div>
        </div>
        <div class="bh-actions">
            @if($course)
                <span id="pubBadge" style="font-size:10px;font-weight:bold;padding:2px 8px;border:1px solid;flex-shrink:0;{{ $course->is_published ? 'background:rgba(26,107,60,.2);border-color:rgba(26,107,60,.4);color:#1a7a3c;' : 'background:rgba(200,16,46,.12);border-color:rgba(200,16,46,.3);color:#ffb3be;' }}">
                    {{ $course->is_published ? '● Live' : '○ Draft' }}
                </span>
                <button class="btn btn-teal btn-sm" onclick="saveSettings()">💾 Save</button>
            @endif
            <a href="{{ route('admin.lms.index') }}" class="btn btn-ghost btn-sm">← Courses</a>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ NEW COURSE ══ --}}
@if($isNew)
<div style="background:#0d1526;min-height:calc(100vh - 52px);display:flex;align-items:center;justify-content:center;padding:36px 16px;">
    <div style="width:100%;max-width:620px;">
        <div style="background:var(--navy);border-bottom:4px solid var(--red);padding:28px 32px 24px;position:relative;overflow:hidden;">
            <div style="position:absolute;top:-50px;right:-50px;width:180px;height:180px;border-radius:50%;background:rgba(200,16,46,.07);pointer-events:none;"></div>
            <div style="font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.2em;color:rgba(255,255,255,.35);margin-bottom:14px;">Liverpool RAYNET · LMS · Course Builder</div>
            <div style="font-size:22px;font-weight:bold;color:#fff;text-transform:uppercase;letter-spacing:.05em;line-height:1.15;margin-bottom:8px;">Create New Course</div>
            <div style="font-size:12px;color:rgba(255,255,255,.4);line-height:1.65;">Build a structured training course with modules, quizzes, certificates and badge awards.</div>
        </div>

        @if(session('success'))
            <div style="background:#eef7f2;border:1px solid #b8ddc9;border-left:4px solid var(--green);padding:.7rem 1rem;font-size:13px;font-weight:bold;color:var(--green);">✓ {{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div style="background:#fdf0f2;border:1px solid rgba(200,16,46,.25);border-left:4px solid var(--red);padding:.7rem 1rem;font-size:13px;font-weight:bold;color:var(--red);">✕ Please fix the errors below.</div>
        @endif

        <div style="background:#fff;border:1px solid var(--grey-mid);border-top:none;padding:28px 32px 32px;">
            <form method="POST" action="{{ route('admin.lms.store') }}">
                @csrf
                <div style="margin-bottom:1.1rem;">
                    <label style="display:block;font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.14em;color:var(--muted);margin-bottom:.4rem;">Course Title <span style="color:var(--red);">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="e.g. RAYNET Basics: Operator Fundamentals"
                           style="width:100%;padding:.65rem .9rem;border:2px solid var(--grey-mid);font-family:var(--font);font-size:14px;color:var(--text);outline:none;-webkit-appearance:none;border-radius:0;"
                           onfocus="this.style.borderColor='var(--navy)'" onblur="this.style.borderColor='var(--grey-mid)'">
                    @error('title')<div style="font-size:11px;color:var(--red);margin-top:3px;font-weight:bold;">✕ {{ $message }}</div>@enderror
                </div>
                <div style="margin-bottom:1.1rem;">
                    <label style="display:block;font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.14em;color:var(--muted);margin-bottom:.4rem;">Description <span style="font-weight:normal;text-transform:none;letter-spacing:0;">(optional)</span></label>
                    <textarea name="description" rows="3" placeholder="What will members learn?"
                              style="width:100%;padding:.65rem .9rem;border:2px solid var(--grey-mid);font-family:var(--font);font-size:13px;color:var(--text);outline:none;resize:vertical;-webkit-appearance:none;border-radius:0;"
                              onfocus="this.style.borderColor='var(--navy)'" onblur="this.style.borderColor='var(--grey-mid)'">{{ old('description') }}</textarea>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:.85rem;margin-bottom:1.1rem;">
                    <div>
                        <label style="display:block;font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.14em;color:var(--muted);margin-bottom:.4rem;">Category</label>
                        <select name="category" style="width:100%;padding:.65rem .9rem;border:2px solid var(--grey-mid);font-family:var(--font);font-size:13px;color:var(--text);outline:none;background:#fff;-webkit-appearance:none;border-radius:0;" onfocus="this.style.borderColor='var(--navy)'" onblur="this.style.borderColor='var(--grey-mid)'">
                            <option value="">— Select —</option>
                            @foreach(['Tier Progression','Technical','Operational','Administrative','Additional Knowledge'] as $cat)
                                <option value="{{ $cat }}" {{ old('category')==$cat?'selected':'' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.14em;color:var(--muted);margin-bottom:.4rem;">Difficulty</label>
                        <select name="difficulty" style="width:100%;padding:.65rem .9rem;border:2px solid var(--grey-mid);font-family:var(--font);font-size:13px;color:var(--text);outline:none;background:#fff;-webkit-appearance:none;border-radius:0;" onfocus="this.style.borderColor='var(--navy)'" onblur="this.style.borderColor='var(--grey-mid)'">
                            <option value="beginner">🟢 Beginner</option>
                            <option value="intermediate">🟡 Intermediate</option>
                            <option value="advanced">🔴 Advanced</option>
                        </select>
                    </div>
                </div>
                <div style="background:var(--grey);border:1px solid var(--grey-mid);padding:14px 18px;margin-bottom:1.25rem;">
                    <div style="font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.14em;color:var(--muted);margin-bottom:10px;">Quick Options</div>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.55rem;">
                        <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;padding:.5rem .65rem;background:#fff;border:1px solid var(--grey-mid);min-height:44px;-webkit-tap-highlight-color:transparent;">
                            <input type="checkbox" name="certificate_enabled" value="1" checked style="width:14px;height:14px;accent-color:var(--navy);flex-shrink:0;">
                            <div><div style="font-size:11px;font-weight:bold;">Certificate</div><div style="font-size:10px;color:var(--muted);">On completion</div></div>
                        </label>
                        <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;padding:.5rem .65rem;background:#fff;border:1px solid var(--grey-mid);min-height:44px;-webkit-tap-highlight-color:transparent;">
                            <input type="checkbox" name="is_published" value="1" style="width:14px;height:14px;accent-color:var(--navy);flex-shrink:0;">
                            <div><div style="font-size:11px;font-weight:bold;">Publish Now</div><div style="font-size:10px;color:var(--muted);">Visible to members</div></div>
                        </label>
                        <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;padding:.5rem .65rem;background:#fff;border:1px solid var(--grey-mid);min-height:44px;-webkit-tap-highlight-color:transparent;">
                            <input type="checkbox" name="is_drip" value="1" style="width:14px;height:14px;accent-color:var(--navy);flex-shrink:0;">
                            <div><div style="font-size:11px;font-weight:bold;">Drip Content</div><div style="font-size:10px;color:var(--muted);">Release over time</div></div>
                        </label>
                    </div>
                </div>
                <div class="inote">ℹ Add modules, lessons and assign members after creating the course.</div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
                    <a href="{{ route('admin.lms.index') }}" style="font-size:11px;font-weight:bold;color:var(--muted);text-decoration:none;text-transform:uppercase;letter-spacing:.08em;">← Back</a>
                    <button type="submit" style="display:inline-flex;align-items:center;gap:.5rem;padding:.7rem 1.75rem;background:var(--navy);border:none;border-left:4px solid var(--red);color:#fff;font-family:var(--font);font-size:12px;font-weight:bold;text-transform:uppercase;letter-spacing:.08em;cursor:pointer;min-height:44px;">
                        Create &amp; Open Builder →
                    </button>
                </div>
            </form>
        </div>
        <div style="text-align:center;padding:14px 0;font-size:10px;color:rgba(255,255,255,.2);text-transform:uppercase;letter-spacing:.12em;">Liverpool RAYNET Training Portal</div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ BUILDER ══ --}}
@else
<div style="background:var(--white);border-bottom:2px solid var(--grey-mid);">
    <div style="max-width:1400px;margin:0 auto;">
        <div class="tab-bar">
            <button class="tab-btn active" data-tab="content">📋 Content</button>
            <button class="tab-btn" data-tab="settings">⚙ Settings</button>
            <button class="tab-btn" data-tab="enrollment">👥 Enrollment</button>
        </div>
    </div>
</div>

<div class="bwrap">
    @if(session('success'))<div class="alert alert-success">✓ {{ session('success') }}</div>@endif

    {{-- CONTENT TAB --}}
    <div class="tab-pane active" id="tab-content">

        {{-- Mobile switcher --}}
        <div class="mpnav" id="mpnav">
            <button class="mpbtn active" id="mpModBtn" onclick="mpSwitch('modules')">📋 Modules</button>
            <button class="mpbtn" id="mpEdBtn" onclick="mpSwitch('editor')">✏ Edit Lesson</button>
        </div>

        <div class="blayout">
            {{-- SIDEBAR --}}
            <div class="sidebar mp-active" id="builderSidebar">
                <div class="sb-head">
                    <span class="sb-head-title">Modules &amp; Lessons</span>
                    <span style="font-size:10px;color:var(--muted);">Drag to reorder</span>
                </div>
                <div id="modulesContainer">
                    <div class="sb-empty" id="emptyMods">No modules yet. Add one below.</div>
                </div>
                <button class="add-mod-btn" onclick="showAddModule()">+ Add Module</button>
            </div>

            {{-- EDITOR --}}
            <div class="ep" id="editorPanel">
                <div class="ep-head">
                    <div style="display:flex;align-items:center;gap:.5rem;min-width:0;flex:1;">
                        <button class="back-btn" id="backBtn" onclick="mpSwitch('modules')">← Back</button>
                        <span class="ep-title" id="epTitle">Lesson Editor</span>
                    </div>
                    <div id="epActions" style="display:none;flex-shrink:0;">
                        <button class="btn btn-red btn-sm" onclick="deleteLesson()">✕ Delete</button>
                    </div>
                </div>
                <div id="epBody">
                    <div class="ep-empty">
                        <div class="ep-empty-icon">✏</div>
                        <div class="ep-empty-text">Tap a lesson to edit it, or add one below.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SETTINGS TAB --}}
    <div class="tab-pane" id="tab-settings">
        <div class="card">
            <div class="card-head"><div class="card-title">Course Settings</div></div>
            <div class="card-body">
                <div class="sg">
                    <div class="ff"><label>Course Title</label><input type="text" id="s_title" value="{{ $course->title }}"></div>
                    <div class="ff"><label>Category</label>
                        <select id="s_category">
                            <option value="">— None —</option>
                            @foreach(['Tier Progression','Technical','Operational','Administrative','Additional Knowledge'] as $cat)
                                <option value="{{ $cat }}" {{ $course->category==$cat?'selected':'' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ff"><label>Difficulty</label>
                        <select id="s_difficulty">
                            <option value="beginner" {{ $course->difficulty=='beginner'?'selected':'' }}>Beginner</option>
                            <option value="intermediate" {{ $course->difficulty=='intermediate'?'selected':'' }}>Intermediate</option>
                            <option value="advanced" {{ $course->difficulty=='advanced'?'selected':'' }}>Advanced</option>
                        </select>
                    </div>
                    <div class="ff"><label>Estimated Hours</label><input type="number" id="s_hours" value="{{ $course->estimated_hours }}" min="0" step="0.5"></div>
                    <div class="ff"><label>Pass Mark (%)</label><input type="number" id="s_pass_mark" value="{{ $course->pass_mark }}" min="0" max="100"></div>
                    <div class="ff"><label>Drip Interval (days)</label><input type="number" id="s_drip_interval" value="{{ $course->drip_interval_days }}" min="1"></div>
                </div>
                <div class="ff" style="margin-top:.5rem;"><label>Description</label><textarea id="s_desc" rows="3">{{ $course->description }}</textarea></div>
                <div class="fg2" style="margin-top:.65rem;">
                    <label class="trow"><input type="checkbox" id="s_published" {{ $course->is_published?'checked':'' }}><div><div class="trow-label">Published</div><div class="trow-sub">Visible to members</div></div></label>
                    <label class="trow"><input type="checkbox" id="s_drip" {{ $course->is_drip?'checked':'' }}><div><div class="trow-label">Drip Content</div><div class="trow-sub">Release over time</div></div></label>
                    <label class="trow"><input type="checkbox" id="s_cert" {{ $course->certificate_enabled?'checked':'' }}><div><div class="trow-label">Certificate</div><div class="trow-sub">Issue on completion</div></div></label>
                </div>
                <div class="ff" style="margin-top:.65rem;"><label>Certificate Body Text</label><textarea id="s_cert_text" rows="2" placeholder="Has successfully completed…">{{ $course->certificate_text }}</textarea></div>

                {{-- BADGE SELECTOR --}}
                <div style="margin-top:.75rem;">
                    <div style="font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);margin-bottom:.3rem;">Unlocks Training Badges on Completion</div>
                    <div class="inote">ℹ Selected badges are automatically unlocked on the member's profile when they complete this course.</div>
                    @php
                    $curBadgeIds = array_map('intval', $course->unlocks_badge_ids ?? []);
                    $allBadges = [
                        'Tier Progression' => [
                            ['id'=>1,'num'=>'T1','label'=>'Operator','colour'=>'#003366'],
                            ['id'=>2,'num'=>'T2','label'=>'Checkpoint Supervisor','colour'=>'#0277bd'],
                            ['id'=>3,'num'=>'T3','label'=>'Net Controller','colour'=>'#1a7a3c'],
                            ['id'=>4,'num'=>'T4','label'=>'Event Manager','colour'=>'#b45309'],
                            ['id'=>5,'num'=>'T5','label'=>'Response Manager','colour'=>'#C8102E'],
                        ],
                        'Technical Specialisms' => [
                            ['id'=>101,'num'=>'T1','label'=>'Power Systems','colour'=>'#5b21b6'],
                            ['id'=>102,'num'=>'T2','label'=>'Digital Modes','colour'=>'#5b21b6'],
                        ],
                        'Operational Specialisms' => [
                            ['id'=>111,'num'=>'O1','label'=>'Mapping','colour'=>'#0f766e'],
                            ['id'=>112,'num'=>'O2','label'=>'Severe Weather','colour'=>'#0f766e'],
                            ['id'=>113,'num'=>'O3','label'=>'First Aid Comms','colour'=>'#0f766e'],
                            ['id'=>114,'num'=>'O4','label'=>'Marathon Ops','colour'=>'#0f766e'],
                            ['id'=>115,'num'=>'O5','label'=>'Air Support','colour'=>'#0f766e'],
                            ['id'=>116,'num'=>'O6','label'=>'Water Ops','colour'=>'#0f766e'],
                        ],
                        'Administrative Specialisms' => [
                            ['id'=>121,'num'=>'A1','label'=>'GDPR','colour'=>'#be185d'],
                            ['id'=>122,'num'=>'A2','label'=>'Media Liaison','colour'=>'#be185d'],
                            ['id'=>123,'num'=>'A3','label'=>'Safeguarding','colour'=>'#be185d'],
                            ['id'=>124,'num'=>'A4','label'=>'No Secret Codes','colour'=>'#be185d'],
                        ],
                        'Additional Knowledge' => [
                            ['id'=>201,'num'=>'K1','label'=>'Antennas','colour'=>'#374151'],
                            ['id'=>202,'num'=>'K2','label'=>'NVIS','colour'=>'#374151'],
                        ],
                    ];
                    @endphp
                    @foreach($allBadges as $catName => $badges)
                    <div class="badge-sh">{{ $catName }}</div>
                    <div class="badge-sg">
                        @foreach($badges as $b)
                        @php $chk = in_array($b['id'], $curBadgeIds); @endphp
                        <label class="badge-item {{ $chk?'selected':'' }}" id="bi-{{ $b['id'] }}">
                            <input type="checkbox" class="badge-cb" data-bid="{{ $b['id'] }}" {{ $chk?'checked':'' }} onchange="toggleBadge({{ $b['id'] }},this)">
                            <div class="badge-hex">
                                <svg viewBox="0 0 28 28" xmlns="http://www.w3.org/2000/svg">
                                    <polygon points="14,1.5 25.5,7.75 25.5,20.25 14,26.5 2.5,20.25 2.5,7.75" fill="{{ $b['colour'] }}" stroke="{{ $b['colour'] }}" stroke-width="1"/>
                                    <text x="14" y="18" text-anchor="middle" font-family="Arial,sans-serif" font-size="7" font-weight="bold" fill="#fff">{{ $b['num'] }}</text>
                                </svg>
                            </div>
                            <div>
                                <div class="badge-name">{{ $b['label'] }}</div>
                                <div class="badge-cat">ID {{ $b['id'] }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="card-foot">
                <button class="btn btn-primary" onclick="saveSettings()">💾 Save Settings</button>
            </div>
        </div>
    </div>

    {{-- ENROLLMENT TAB --}}
    <div class="tab-pane" id="tab-enrollment">
        <div class="card">
            <div class="card-head"><div class="card-title">Assign Course to Members</div></div>
            <div class="card-body">
                <div class="enroll-grid">
                    <div>
                        <div style="font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);margin-bottom:.5rem;">Available Members</div>
                        <input type="text" placeholder="Search members…" oninput="filterMembers(this.value)"
                               style="width:100%;padding:.45rem .7rem;border:1px solid var(--grey-mid);font-family:var(--font);font-size:12px;margin-bottom:.5rem;-webkit-appearance:none;border-radius:0;">
                        <div class="member-list" id="memberList">
                            @foreach($users as $u)
                            <label class="member-row" data-search="{{ strtolower($u->name.' '.$u->email.' '.$u->callsign) }}">
                                <input type="checkbox" name="enroll_users[]" value="{{ $u->id }}" {{ in_array($u->id,$enrolledIds)?'checked disabled':'' }}>
                                <div>
                                    <div class="member-name">{{ $u->name }}</div>
                                    <div class="member-cs">{{ $u->callsign ?? $u->email }}</div>
                                </div>
                                @if(in_array($u->id,$enrolledIds))
                                    <span style="font-size:9px;font-weight:bold;padding:1px 6px;background:var(--green-bg);border:1px solid #b8ddc9;color:var(--green);margin-left:auto;">Enrolled</span>
                                @endif
                            </label>
                            @endforeach
                        </div>
                        <div style="margin-top:.65rem;">
                            <label style="display:block;font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);margin-bottom:.3rem;">Due Date (optional)</label>
                            <input type="date" id="enrollDue" style="width:100%;padding:.4rem .6rem;border:1px solid var(--grey-mid);font-family:var(--font);font-size:12px;-webkit-appearance:none;border-radius:0;">
                        </div>
                        <button class="btn btn-primary" style="margin-top:.65rem;width:100%;min-height:44px;" onclick="enrollSelected()">+ Enroll Selected</button>
                    </div>
                    <div>
                        <div style="font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);margin-bottom:.5rem;">Currently Enrolled ({{ count($enrolledIds) }})</div>
                        <div class="member-list" id="enrolledList">
                            @php $enrolled = $users->whereIn('id',$enrolledIds); @endphp
                            @forelse($enrolled as $u)
                            <div class="member-row" id="er-{{ $u->id }}">
                                <div style="flex:1;">
                                    <div class="member-name">{{ $u->name }}</div>
                                    <div class="member-cs">{{ $u->callsign ?? $u->email }}</div>
                                </div>
                                <button class="btn btn-red btn-xs" onclick="unenroll({{ $u->id }})">✕</button>
                            </div>
                            @empty
                            <div style="padding:2rem 1rem;text-align:center;font-size:12px;color:var(--muted);">No members enrolled yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>{{-- /bwrap --}}
@endif

<div class="toast" id="toast"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
<script>
const CSRF      = '{{ $csrf }}';
const COURSE_ID = {{ $courseId ?? 'null' }};
const BASE      = '/admin/lms';

let courseData   = {!! $courseJson !!};
let activeLesson = null;

// ── Toast ──────────────────────────────────────────────────────────────────
function toast(msg, ok = true) {
    const t = document.getElementById('toast');
    t.textContent = (ok?'✓ ':'✕ ') + msg;
    t.style.background      = ok ? 'var(--navy)' : 'var(--red)';
    t.style.borderLeftColor = ok ? 'var(--teal)' : '#ff6b6b';
    t.style.display = 'block';
    clearTimeout(t._t);
    t._t = setTimeout(() => t.style.display = 'none', 3000);
}

// ── Modal ──────────────────────────────────────────────────────────────────
// ── Modal ──────────────────────────────────────────────────────────────────
let _res = null;

function openModal(title, html, btnLabel = 'Confirm') {
    return new Promise(resolve => {
        _res = resolve;

        document.getElementById('rnTitle').textContent   = title;
        document.getElementById('rnBody').innerHTML      = html;
        document.getElementById('rnConfirm').textContent = btnLabel;
        document.getElementById('rnOverlay').classList.add('open');
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            const f = document.querySelector('#rnBody input, #rnBody select');
            if (f) f.focus();
        }, 80);

        // Confirm: capture resolve, null it, hide, THEN resolve so
        // the awaiting function runs after the modal is already gone.
        document.getElementById('rnConfirm').onclick = () => {
            const r = _res;
            _res = null;
            _hideModal();
            if (r) r(true);
        };
    });
}

function _hideModal() {
    document.getElementById('rnOverlay').classList.remove('open');
    document.body.style.overflow = '';
}

function closeModal() {
    _hideModal();
    if (_res) {
        const r = _res;
        _res = null;
        r(false);   // resolve with false so await continues but !title guard stops execution
    }
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        if (document.getElementById('rnOverlay').classList.contains('open')) closeModal();
    }
    if (e.key === 'Enter' && document.getElementById('rnOverlay').classList.contains('open')) {
        const active = document.activeElement;
        // Don't trigger confirm if cursor is in a textarea
        if (active && active.tagName === 'TEXTAREA') return;
        e.preventDefault();
        document.getElementById('rnConfirm').click();
    }
});

// ── AJAX ───────────────────────────────────────────────────────────────────
async function api(url, method = 'GET', body = null) {
    const opts = { method, headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' } };
    if (body) opts.body = JSON.stringify(body);
    try {
        const r = await fetch(url, opts);
        if (!r.ok) { toast('Server error: ' + r.status, false); return {}; }
        return r.json();
    } catch(e) { toast('Request failed: ' + e.message, false); return {}; }
}

// ── Tabs ───────────────────────────────────────────────────────────────────
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
    });
});

// ── Mobile panel switch ────────────────────────────────────────────────────
function mpSwitch(panel) {
    const sb = document.getElementById('builderSidebar');
    const ep = document.getElementById('editorPanel');
    const mb = document.getElementById('mpModBtn');
    const eb = document.getElementById('mpEdBtn');
    const bb = document.getElementById('backBtn');

    if (!sb || !ep) return;

    if (panel === 'modules') {
        sb.classList.add('mp-active');
        ep.classList.remove('mp-active');
        mb && mb.classList.add('active');
        eb && eb.classList.remove('active');
        if (bb) bb.style.display = 'none';
    } else {
        sb.classList.remove('mp-active');
        ep.classList.add('mp-active');
        mb && mb.classList.remove('active');
        eb && eb.classList.add('active');
        if (bb) bb.style.display = '';
    }
}

// ── Render tree ────────────────────────────────────────────────────────────
function renderTree() {
    const container = document.getElementById('modulesContainer');
    if (!container) return;
    const empty = document.getElementById('emptyMods');

    [...container.querySelectorAll('.mod-item')].forEach(e => e.remove());

    if (!courseData?.modules?.length) {
        if (empty) { container.appendChild(empty); empty.style.display = ''; }
        return;
    }
    if (empty) empty.style.display = 'none';

    courseData.modules.forEach(mod => {
        const el = document.createElement('div');
        el.className  = 'mod-item';
        el.dataset.id = mod.id;
        const lessons = mod.lessons || [];

        el.innerHTML = `
        <div class="mod-hdr">
            <span class="mod-drag" title="Hold to drag">⠿</span>
            <span class="mod-name" ondblclick="editModTitle(${mod.id},this)">${esc(mod.title)}</span>
            <button class="btn btn-red btn-xs" onclick="deleteMod(${mod.id},event)" style="flex-shrink:0;">✕</button>
            <span class="mod-toggle open" onclick="toggleMod(this)">▼</span>
        </div>
        <div class="mod-lessons" id="ll-${mod.id}">
            ${lessons.map(l => lessonHtml(l)).join('')}
            <button class="add-les-btn" onclick="showAddLesson(${mod.id},${mod.course_id||COURSE_ID})">+ Add Lesson</button>
        </div>`;

        container.insertBefore(el, empty);

        // Sortable for lessons
        const llEl = el.querySelector('#ll-' + mod.id);
        if (llEl) {
            new Sortable(llEl, {
                animation: 150,
                handle: '.les-drag',
                filter: '.add-les-btn',
                delay: 200,
                delayOnTouchOnly: true,
                touchStartThreshold: 10,
                forceFallback: false,
                onEnd: async evt => {
                    const order = [...evt.to.querySelectorAll('.les-item')].map(e => e.dataset.id);
                    await api(`${BASE}/lessons/reorder`, 'POST', { order });
                }
            });
        }
    });

    // Sortable for modules
    new Sortable(container, {
        animation: 150,
        handle: '.mod-drag',
        filter: '.mod-lessons, .add-les-btn',
        delay: 200,
        delayOnTouchOnly: true,
        touchStartThreshold: 10,
        forceFallback: false,
        onEnd: async () => {
            const order = [...container.querySelectorAll('.mod-item')].map(e => e.dataset.id);
            await api(`${BASE}/modules/reorder`, 'POST', { order });
        }
    });

    // Restore active highlight
// Restore active highlight
    if (activeLesson) {
        document.querySelectorAll('.les-item').forEach(e =>
            e.classList.toggle('active', e.dataset.id == activeLesson));
    }

    // Event delegation for lesson taps (works on iOS Safari)
    const mc = document.getElementById('modulesContainer');
    mc._lesClick && mc.removeEventListener('click', mc._lesClick);
    mc._lesClick = function(e) {
        const item = e.target.closest('.les-item');
        if (item && !e.target.closest('.les-drag')) {
            openLesson(parseInt(item.dataset.id));
        }
    };
    mc.addEventListener('click', mc._lesClick);

    mc._lesTouch && mc.removeEventListener('touchend', mc._lesTouch);
    mc._lesTouch = function(e) {
        const item = e.target.closest('.les-item');
        if (item && !e.target.closest('.les-drag')) {
            e.preventDefault();
            openLesson(parseInt(item.dataset.id));
        }
    };
    mc.addEventListener('touchend', mc._lesTouch, {passive: false});
}

function lessonHtml(l) {
    const icons = {text:'📄',video:'🎬',scorm:'📦',quiz:'❓',audio:'🎧',document:'📋',presentation:'📊',external:'🔗',checklist:'✅'};
    return `<div class="les-item" data-id="${l.id}">
        <span class="les-drag" title="Hold to drag">⠿</span>
        <span class="les-icon">${icons[l.type]||'📄'}</span>
        <span class="les-name">${esc(l.title)}</span>
        <span class="les-arrow">›</span>
    </div>`;
}

function lessonFromId(id) {
    for (const m of courseData?.modules||[]) {
        const l = (m.lessons||[]).find(x => x.id == id);
        if (l) return l;
    }
    return null;
}

function toggleMod(chevron) {
    const ll = chevron.closest('.mod-item').querySelector('.mod-lessons');
    const open = !ll.classList.contains('collapsed');
    ll.classList.toggle('collapsed', open);
    chevron.classList.toggle('open', !open);
}

// ── Add Module ─────────────────────────────────────────────────────────────
async function showAddModule() {
    await openModal('Add Module',
        `<div class="rn-field"><label>Module Title</label><input type="text" id="mod_t" placeholder="e.g. Introduction to RAYNET" autocomplete="off"></div>
         <div class="rn-field"><label>Description (optional)</label><input type="text" id="mod_d" placeholder="Brief description…" autocomplete="off"></div>`,
        'Add Module');
    const title = document.getElementById('mod_t')?.value?.trim();
    if (!title) return;
    const data = await api(`${BASE}/modules`, 'POST', {
        course_id: COURSE_ID, title, description: document.getElementById('mod_d')?.value||''
    });
    if (data.success) {
        courseData.modules = courseData.modules||[];
        courseData.modules.push({...data.module, lessons:[]});
        renderTree();
        toast('Module added.');
    } else toast('Failed.', false);
}

// ── Add Lesson ─────────────────────────────────────────────────────────────
async function showAddLesson(moduleId, courseId) {
    await openModal('Add Lesson',
        `<div class="rn-field"><label>Lesson Title</label><input type="text" id="les_t" placeholder="e.g. RAYNET Mission & Purpose" autocomplete="off"></div>
         <div class="rn-field"><label>Lesson Type</label>
         <select id="les_ty">
             <option value="text">📄 Text / Reading</option>
             <option value="video">🎬 Video</option>
             <option value="audio">🎧 Audio</option>
             <option value="document">📋 Document / PDF</option>
             <option value="presentation">📊 Presentation</option>
             <option value="external">🔗 External Link</option>
             <option value="checklist">✅ Checklist</option>
             <option value="scorm">📦 SCORM</option>
             <option value="quiz">❓ Quiz</option>
         </select></div>`,
        'Add Lesson');
    const title = document.getElementById('les_t')?.value?.trim();
    if (!title) return;
    const type = document.getElementById('les_ty')?.value||'text';
    const data = await api(`${BASE}/lessons`, 'POST', {
        module_id: moduleId, course_id: courseId||COURSE_ID, title, type
    });
    if (data.success) {
        const mod = courseData.modules.find(m => m.id == moduleId);
        if (mod) { mod.lessons = mod.lessons||[]; mod.lessons.push(data.lesson); }
        renderTree();
        toast('Lesson added.');
        openLesson(data.lesson.id);
    } else toast('Failed.', false);
}

// ── Delete Module ──────────────────────────────────────────────────────────
async function deleteMod(id, e) {
    e.stopPropagation();
    if (!confirm('Delete this module and all its lessons?')) return;
    const data = await api(`${BASE}/modules/${id}`, 'DELETE');
    if (data.success) {
        courseData.modules = courseData.modules.filter(m => m.id != id);
        if (activeLesson && !lessonFromId(activeLesson)) { activeLesson = null; showEditorEmpty(); }
        renderTree();
        toast('Module deleted.');
    } else toast('Delete failed.', false);
}

// ── Edit Module Title ──────────────────────────────────────────────────────
function editModTitle(id, el) {
    const cur = el.textContent;
    const inp = document.createElement('input');
    inp.value = cur;
    inp.style.cssText = 'flex:1;padding:2px 4px;border:1px solid var(--navy);font-size:12px;font-weight:bold;font-family:var(--font);min-width:0;';
    el.replaceWith(inp);
    inp.focus(); inp.select();
    const done = async () => {
        const t = inp.value.trim()||cur;
        await api(`${BASE}/modules/${id}`, 'PUT', {title: t});
        const mod = courseData.modules.find(m => m.id == id);
        if (mod) mod.title = t;
        renderTree();
        toast('Module renamed.');
    };
    inp.addEventListener('blur', done);
    inp.addEventListener('keydown', e => { if(e.key==='Enter')done(); if(e.key==='Escape')renderTree(); });
}

// ── Delete Lesson ──────────────────────────────────────────────────────────
async function deleteLesson() {
    if (!activeLesson) return;
    if (!confirm('Delete this lesson?')) return;
    const data = await api(`${BASE}/lessons/${activeLesson}`, 'DELETE');
    if (data.success) {
        for (const m of courseData.modules) m.lessons = (m.lessons||[]).filter(l => l.id != activeLesson);
        activeLesson = null;
        renderTree();
        showEditorEmpty();
        toast('Lesson deleted.');
    } else toast('Delete failed.', false);
}

// ── Open Lesson ────────────────────────────────────────────────────────────
function openLesson(lessonId) {
    activeLesson = lessonId;
    document.querySelectorAll('.les-item').forEach(e => e.classList.toggle('active', e.dataset.id == lessonId));
    const lesson = lessonFromId(lessonId);
    if (!lesson) return;

    document.getElementById('epTitle').textContent    = lesson.title;
    document.getElementById('epActions').style.display = '';

    document.getElementById('epBody').innerHTML = `
    <div class="ep-body">
        <div class="ff"><label>Lesson Title</label><input type="text" id="l_title" value="${escA(lesson.title)}"></div>
        <div class="fg2">
            <div class="ff"><label>Type</label>
                <select id="l_type" onchange="onTypeChange(this.value)">
                    <option value="text"         ${lesson.type=='text'         ?'selected':''}>📄 Text / Reading</option>
                    <option value="video"        ${lesson.type=='video'        ?'selected':''}>🎬 Video</option>
                    <option value="audio"        ${lesson.type=='audio'        ?'selected':''}>🎧 Audio</option>
                    <option value="document"     ${lesson.type=='document'     ?'selected':''}>📋 Document / PDF</option>
                    <option value="presentation" ${lesson.type=='presentation' ?'selected':''}>📊 Presentation</option>
                    <option value="external"     ${lesson.type=='external'     ?'selected':''}>🔗 External Link</option>
                    <option value="checklist"    ${lesson.type=='checklist'    ?'selected':''}>✅ Checklist</option>
                    <option value="scorm"        ${lesson.type=='scorm'        ?'selected':''}>📦 SCORM</option>
                    <option value="quiz"         ${lesson.type=='quiz'         ?'selected':''}>❓ Quiz</option>
                </select>
            </div>
            <div class="ff"><label>Duration (mins)</label><input type="number" id="l_duration" value="${lesson.duration_minutes||''}" min="0"></div>
        </div>
        <div class="ff"><label>Drip Release (days after enrollment, 0 = immediate)</label><input type="number" id="l_drip" value="${lesson.drip_days||0}" min="0"></div>
        <label class="trow"><input type="checkbox" id="l_free" ${lesson.is_free_preview?'checked':''}><div><div class="trow-label">Free Preview</div><div class="trow-sub">Visible before enrollment</div></div></label>
        <div id="typeFields">${renderTypeFields(lesson)}</div>
        ${lesson.type==='quiz' && lesson.quiz ? renderQuizBuilder(lesson.quiz) : ''}
    </div>`;

    document.querySelector('.ep-foot')?.remove();
    const foot = document.createElement('div');
    foot.className = 'ep-foot';
    foot.innerHTML = `<button class="btn btn-ghost-dark btn-sm" onclick="showEditorEmpty()">Cancel</button>
                      <button class="btn btn-primary btn-sm" onclick="saveLesson(${lessonId})">💾 Save Lesson</button>`;
    document.getElementById('editorPanel').appendChild(foot);

    // Auto-switch to editor on mobile
    if (window.innerWidth <= 900) mpSwitch('editor');
}

function renderTypeFields(lesson) {
    const note = (t) => `<div style="padding:.4rem .65rem;background:#e8eef5;border-left:3px solid var(--navy);font-size:11px;color:var(--text-mid);margin-bottom:.75rem;">${t}</div>`;
    if (lesson.type === 'text')
        return `<div class="ff"><label>Content</label><textarea id="l_content" rows="10" style="font-size:13px;">${esc(lesson.content||'')}</textarea></div>`;
    if (lesson.type === 'video')
        return `<div class="ff"><label>Video URL</label><input type="text" id="l_video_url" value="${escA(lesson.video_url||'')}" placeholder="https://www.youtube.com/watch?v=XXXX or youtu.be/XXXX"></div>
                ${note('ℹ Paste any YouTube or Vimeo URL — auto-converted. Member must watch fully to complete.')}
                <div class="ff"><label>Notes / Transcript</label><textarea id="l_content" rows="4">${esc(lesson.content||'')}</textarea></div>`;
    if (lesson.type === 'audio')
        return `<div class="ff"><label>Audio File URL</label><input type="text" id="l_video_url" value="${escA(lesson.video_url||'')}" placeholder="https://example.com/audio/recording.mp3"></div>
                ${note('ℹ Supports MP3, OGG. Member must listen to the full recording to complete.')}
                <div class="ff"><label>Notes / Transcript (optional)</label><textarea id="l_content" rows="4">${esc(lesson.content||'')}</textarea></div>`;
    if (lesson.type === 'document')
        return `<div class="ff"><label>Document URL (PDF or Google Doc)</label><input type="text" id="l_video_url" value="${escA(lesson.video_url||'')}" placeholder="https://example.com/document.pdf"></div>
                ${note('ℹ PDFs shown in embedded viewer. Use a direct PDF URL or Google Docs /preview link.')}
                <div class="ff"><label>Description (optional)</label><textarea id="l_content" rows="3">${esc(lesson.content||'')}</textarea></div>`;
    if (lesson.type === 'presentation')
        return `<div class="ff"><label>Presentation Embed URL</label><input type="text" id="l_video_url" value="${escA(lesson.video_url||'')}" placeholder="https://docs.google.com/presentation/d/ID/embed"></div>
                ${note('ℹ Google Slides: File → Share → Publish to web → Embed → copy the src URL.')}
                <div class="ff"><label>Notes (optional)</label><textarea id="l_content" rows="3">${esc(lesson.content||'')}</textarea></div>`;
    if (lesson.type === 'external')
        return `<div class="ff"><label>External URL</label><input type="text" id="l_video_url" value="${escA(lesson.video_url||'')}" placeholder="https://ofcom.org.uk/..."></div>
                ${note('ℹ Opens in a new tab. Complete button unlocks when the member clicks the link.')}
                <div class="ff"><label>Description / Instructions</label><textarea id="l_content" rows="3" placeholder="What should the member do on this resource?">${esc(lesson.content||'')}</textarea></div>`;
    if (lesson.type === 'checklist')
        return `<div class="ff"><label>Checklist Items</label>
                ${note('ℹ Enter one item per line. All must be ticked before the lesson can be completed.')}
                <textarea id="l_content" rows="10" placeholder="Check radio is on correct frequency&#10;Confirm net is active&#10;Log callsign and signal report">${esc(lesson.content||'')}</textarea></div>`;
    if (lesson.type === 'scorm') {
        const hasPackage = lesson.video_url ? true : false;
        return `<div class="ff">
                <label>SCORM Package</label>
                ${hasPackage
                    ? `<div style="padding:.4rem .65rem;background:#eef7f2;border-left:3px solid var(--green);font-size:11px;color:var(--green);margin-bottom:.5rem;">
                        ✓ Package installed — <a href="${escA(lesson.video_url)}" target="_blank" style="color:var(--green);font-weight:bold;">preview launch file ↗</a>
                       </div>`
                    : `<div style="padding:.4rem .65rem;background:#e8eef5;border-left:3px solid var(--navy);font-size:11px;color:var(--text-mid);margin-bottom:.5rem;">
                        ℹ No package uploaded yet. Upload a SCORM 1.2 or 2004 ZIP below.
                       </div>`
                }
                <div style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap;">
                    <label class="btn btn-teal btn-sm" style="cursor:pointer;min-height:32px;">
                        📦 Upload SCORM ZIP
                        <input type="file" accept=".zip" style="display:none;" onchange="uploadScorm(this,${lesson.id})">
                    </label>
                    <span id="scormUploadStatus" style="font-size:11px;color:var(--muted);"></span>
                </div>
                <div class="ff" style="margin-top:.65rem;">
                    <label>Launch URL (auto-populated on upload)</label>
                    <input type="text" id="l_video_url" value="${escA(lesson.video_url||'')}" placeholder="/scorm/${lesson.id}/index.html" style="font-family:monospace;font-size:12px;">
                </div>
                <div style="padding:.4rem .65rem;background:#e8eef5;border-left:3px solid var(--navy);font-size:11px;color:var(--text-mid);margin-top:.35rem;">
                    ℹ Upload SCORM 1.2 or 2004 ZIP — extracted automatically, max 100 MB.
                    ${hasPackage ? ` <a href="/my-training/scorm/${lesson.id}" target="_blank" style="color:var(--navy);font-weight:bold;">▶ Preview as student ↗</a>` : ''}
                </div>
                </div>`;
    }
    if (lesson.type === 'quiz')
        return `<div class="ff"><label>Introduction Text (optional)</label><textarea id="l_content" rows="3">${esc(lesson.content||'')}</textarea></div>`;
    return '';
}

function renderQuizBuilder(quiz) {
    if (!quiz) return '';
    const qHtml = (quiz.questions||[]).map((q,qi) => renderQuestion(q,qi)).join('');
    return `<div class="sec-lbl" style="margin-top:.65rem;">Quiz Builder</div>
    <div class="fg2" style="margin-bottom:.65rem;">
        <div class="ff"><label>Quiz Title</label><input type="text" id="qz_title" value="${escA(quiz.title||'')}"></div>
        <div class="ff"><label>Pass Mark (%)</label><input type="number" id="qz_pass" value="${quiz.pass_mark||80}" min="0" max="100"></div>
        <div class="ff"><label>Attempts Allowed</label><input type="number" id="qz_attempts" value="${quiz.attempts_allowed||3}" min="1"></div>
        <div class="ff"><label>Time Limit (mins, blank = unlimited)</label><input type="number" id="qz_time" value="${quiz.time_limit_minutes||''}"></div>
    </div>
    <div class="qb" id="questionsContainer" data-qid="${quiz.id}">${qHtml}</div>
    <button class="add-q-btn" onclick="addQuestion()" style="margin-top:.35rem;">+ Add Question</button>
    <br><button class="btn btn-teal btn-sm" style="margin-top:.65rem;" onclick="saveQuiz(${quiz.id})">💾 Save Quiz</button>`;
}

function renderQuestion(q, idx) {
    const aHtml = (q.answers||[]).map((a,ai) => `
        <div class="ans-row">
            <span style="font-size:10px;color:var(--muted);min-width:16px;">${String.fromCharCode(65+ai)}.</span>
            <input type="text" placeholder="Answer option" value="${escA(a.answer_text||'')}" data-at>
            <input type="checkbox" ${a.is_correct?'checked':''} data-ac title="Correct?">
            <button class="del-ans" onclick="this.closest('.ans-row').remove()">✕</button>
        </div>`).join('');
    return `<div class="qcard" data-qid="${q.id||''}">
        <div class="qcard-head">
            <span class="qnum">Q${idx+1}</span>
            <select data-qt style="flex:1;padding:.25rem .45rem;border:1px solid var(--grey-mid);font-family:var(--font);font-size:11px;-webkit-appearance:none;border-radius:0;">
                <option value="multiple_choice" ${q.type=='multiple_choice'?'selected':''}>Multiple Choice</option>
                <option value="true_false" ${q.type=='true_false'?'selected':''}>True / False</option>
            </select>
            <input type="number" data-qp value="${q.points||1}" min="1" style="width:46px;padding:.25rem .35rem;border:1px solid var(--grey-mid);font-family:var(--font);font-size:11px;border-radius:0;" title="Points">
            <button class="btn btn-red btn-xs" onclick="this.closest('.qcard').remove()">✕</button>
        </div>
        <div class="qcard-body">
            <div class="ff" style="margin-bottom:.6rem;">
                <textarea data-qq rows="2" style="font-family:var(--font);font-size:13px;border:1px solid var(--grey-mid);padding:.4rem .55rem;width:100%;border-radius:0;-webkit-appearance:none;" placeholder="Enter question…">${esc(q.question||'')}</textarea>
            </div>
            <div data-ac-container>${aHtml}</div>
            <button class="add-ans-btn" onclick="addAnswer(this)">+ Answer</button>
        </div>
    </div>`;
}

function addQuestion() {
    const c = document.getElementById('questionsContainer');
    if (!c) return;
    const idx = c.querySelectorAll('.qcard').length;
    const tmp = document.createElement('div');
    tmp.innerHTML = renderQuestion({id:'',question:'',type:'multiple_choice',points:1,answers:[]}, idx);
    c.appendChild(tmp.firstElementChild);
}

function addAnswer(btn) {
    const c  = btn.previousElementSibling;
    const ai = c.querySelectorAll('.ans-row').length;
    const row = document.createElement('div');
    row.className = 'ans-row';
    row.innerHTML = `<span style="font-size:10px;color:var(--muted);min-width:16px;">${String.fromCharCode(65+ai)}.</span>
        <input type="text" placeholder="Answer option" data-at>
        <input type="checkbox" data-ac title="Correct?">
        <button class="del-ans" onclick="this.closest('.ans-row').remove()">✕</button>`;
    c.appendChild(row);
}

function onTypeChange(type) {
    const l = lessonFromId(activeLesson);
    if (l) l.type = type;
    document.getElementById('typeFields').innerHTML = renderTypeFields({type,content:'',video_url:'',scorm_package:''});
}

async function saveLesson(id) {
    const body = {
        title:            document.getElementById('l_title')?.value,
        type:             document.getElementById('l_type')?.value,
        content:          document.getElementById('l_content')?.value,
        video_url:        document.getElementById('l_video_url')?.value,
        duration_minutes: document.getElementById('l_duration')?.value,
        drip_days:        document.getElementById('l_drip')?.value,
        is_free_preview:  document.getElementById('l_free')?.checked ? 1 : 0,
    };
    const data = await api(`${BASE}/lessons/${id}`, 'PUT', body);
    if (data.success) {
        const l = lessonFromId(id);
        if (l) Object.assign(l, body);
        document.getElementById('epTitle').textContent = body.title;
        const el = document.querySelector(`.les-item[data-id="${id}"] .les-name`);
        if (el) el.textContent = body.title;
        toast('Lesson saved.');
    } else toast('Save failed.', false);
}

async function saveQuiz(quizId) {
    const c = document.getElementById('questionsContainer');
    const questions = [...c.querySelectorAll('.qcard')].map(qEl => ({
        id:       qEl.dataset.qid||'',
        question: qEl.querySelector('[data-qq]').value,
        type:     qEl.querySelector('[data-qt]').value,
        points:   qEl.querySelector('[data-qp]').value||1,
        answers: [...qEl.querySelectorAll('.ans-row')].map(aEl => ({
            id:          aEl.dataset.aid||'',
            answer_text: aEl.querySelector('[data-at]').value,
            is_correct:  aEl.querySelector('[data-ac]').checked,
        }))
    }));
    const body = {
        title:              document.getElementById('qz_title')?.value,
        pass_mark:          document.getElementById('qz_pass')?.value,
        attempts_allowed:   document.getElementById('qz_attempts')?.value,
        time_limit_minutes: document.getElementById('qz_time')?.value,
        questions,
    };
    const data = await api(`${BASE}/quizzes/${quizId}`, 'PUT', body);
    if (data.success) toast('Quiz saved.'); else toast('Save failed.', false);
}

function showEditorEmpty() {
    activeLesson = null;
    document.querySelectorAll('.les-item').forEach(e => e.classList.remove('active'));
    document.getElementById('epTitle').textContent     = 'Lesson Editor';
    document.getElementById('epActions').style.display = 'none';
    document.getElementById('epBody').innerHTML = `<div class="ep-empty">
        <div class="ep-empty-icon">✏</div>
        <div class="ep-empty-text">Tap a lesson to edit it.</div></div>`;
    document.querySelector('.ep-foot')?.remove();
    if (window.innerWidth <= 900) mpSwitch('modules');
}

async function saveSettings() {
    const badgeIds = [...document.querySelectorAll('.badge-cb:checked')].map(c => parseInt(c.dataset.bid));
    const body = {
        title:               document.getElementById('s_title')?.value,
        description:         document.getElementById('s_desc')?.value,
        category:            document.getElementById('s_category')?.value,
        difficulty:          document.getElementById('s_difficulty')?.value,
        estimated_hours:     document.getElementById('s_hours')?.value,
        pass_mark:           document.getElementById('s_pass_mark')?.value,
        drip_interval_days:  document.getElementById('s_drip_interval')?.value,
        is_published:        document.getElementById('s_published')?.checked ? 1 : 0,
        is_drip:             document.getElementById('s_drip')?.checked ? 1 : 0,
        certificate_enabled: document.getElementById('s_cert')?.checked ? 1 : 0,
        certificate_text:    document.getElementById('s_cert_text')?.value,
        unlocks_badge_ids:   badgeIds,
    };
    const data = await api(`${BASE}/${COURSE_ID}`, 'PUT', body);
    if (data.success) {
        document.getElementById('headerTitle').textContent = body.title;
        const pb = document.getElementById('pubBadge');
        if (pb) {
            pb.textContent = body.is_published ? '● Live' : '○ Draft';
            pb.style.cssText = body.is_published
                ? 'font-size:10px;font-weight:bold;padding:2px 8px;border:1px solid;flex-shrink:0;background:rgba(26,107,60,.2);border-color:rgba(26,107,60,.4);color:#1a7a3c;'
                : 'font-size:10px;font-weight:bold;padding:2px 8px;border:1px solid;flex-shrink:0;background:rgba(200,16,46,.12);border-color:rgba(200,16,46,.3);color:#ffb3be;';
        }
        toast('Settings saved.' + (badgeIds.length ? ` Unlocks ${badgeIds.length} badge${badgeIds.length>1?'s':''}.` : ''));
    } else toast('Save failed.', false);
}

function toggleBadge(id, cb) {
    document.getElementById('bi-' + id)?.classList.toggle('selected', cb.checked);
}

function filterMembers(q) {
    document.querySelectorAll('#memberList .member-row').forEach(row => {
        row.style.display = row.dataset.search?.includes(q.toLowerCase()) ? '' : 'none';
    });
}

async function enrollSelected() {
    const ids = [...document.querySelectorAll('#memberList input[type="checkbox"]:checked:not(:disabled)')].map(c => c.value);
    if (!ids.length) { toast('No members selected.', false); return; }
    const due  = document.getElementById('enrollDue')?.value||null;
    const data = await api(`${BASE}/enroll`, 'POST', {course_id: COURSE_ID, user_ids: ids, due_date: due});
    if (data.success) { toast(data.message); setTimeout(() => location.reload(), 800); }
    else toast('Enrollment failed.', false);
}

async function unenroll(userId) {
    if (!confirm('Remove this member from the course?')) return;
    const data = await api(`${BASE}/${COURSE_ID}/unenroll/${userId}`, 'DELETE');
    if (data.success) {
        document.getElementById(`er-${userId}`)?.remove();
        const cb = document.querySelector(`#memberList input[value="${userId}"]`);
        if (cb) { cb.disabled = false; cb.checked = false; }
        toast('Member unenrolled.');
    }
}

// ── Helpers ────────────────────────────────────────────────────────────────
function esc(s)  { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function escA(s) { return String(s||'').replace(/"/g,'&quot;'); }

// ── Init ───────────────────────────────────────────────────────────────────
async function uploadScorm(input, lessonId) {
    const file = input.files[0];
    if (!file) return;
 
    const status = document.getElementById('scormUploadStatus');
    if (status) { status.textContent = '⏳ Uploading…'; status.style.color = 'var(--muted)'; }
 
    const formData = new FormData();
    formData.append('scorm_zip', file);
    formData.append('_token', CSRF);
 
    try {
        const r = await fetch(`${BASE}/lessons/${lessonId}/scorm-upload`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: formData,
        });
        const data = await r.json();
 
        if (data.success) {
            const urlField = document.getElementById('l_video_url');
            if (urlField) urlField.value = data.launch_url;
 
            const l = lessonFromId(lessonId);
            if (l) l.video_url = data.launch_url;
 
            if (status) { status.textContent = '✓ Uploaded — click Save Lesson to confirm.'; status.style.color = 'var(--green)'; }
            toast('SCORM package uploaded. Click Save Lesson.');
        } else {
            if (status) { status.textContent = '✕ ' + (data.error || 'Upload failed.'); status.style.color = 'var(--red)'; }
            toast(data.error || 'Upload failed.', false);
        }
    } catch (e) {
        if (status) { status.textContent = '✕ Network error.'; status.style.color = 'var(--red)'; }
        toast('Upload failed: ' + e.message, false);
    }
 
    input.value = '';
}
 
if (COURSE_ID) renderTree();
</script>
@endsection