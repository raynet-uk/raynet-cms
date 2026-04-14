<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ZipArchive;

class ScormBuilderController extends Controller
{
    /** Show the builder UI */
    public function index()
    {
        return view('admin.lms.scorm-builder');
    }

    /** Accept course JSON, generate SCORM ZIP, return for download */
    public function export(Request $request)
    {
        $request->validate([
            'title'   => ['required', 'string', 'max:255'],
            'slides'  => ['required', 'string'],
        ]);

        $title     = trim($request->input('title'));
        $slidesRaw = $request->input('slides');
        $passMark  = (int) $request->input('pass_mark', 80);

        // Sanitise title for use as filename
        $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($title));
        $slug = trim($slug, '-') ?: 'scorm-course';

        // Decode slides
        $slides = json_decode($slidesRaw, true);
        if (!is_array($slides)) {
            return response()->json(['error' => 'Invalid slide data.'], 422);
        }

        // Build files
        $manifest     = $this->buildManifest($slug, $title);
        $scormApiPath = public_path('scorm/scorm_api.js');
        $scormApi     = file_exists($scormApiPath) ? file_get_contents($scormApiPath) : null;
        if (!$scormApi) {
            $scormApi = $this->inlineScormApi();
        }
        $lessonHtml = $this->buildLesson($title, $slides, $passMark);

        // Create ZIP in memory
        $tmpZip = tempnam(sys_get_temp_dir(), 'scorm_') . '.zip';
        $za = new ZipArchive();
        $za->open($tmpZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $za->addFromString('imsmanifest.xml', $manifest);
        $za->addFromString('scorm_api.js',    $scormApi);
        $za->addFromString('lesson.html',     $lessonHtml);
        $za->close();

        $response = response()->download($tmpZip, $slug . '.zip', [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);

        return $response;
    }

    // ── Private: imsmanifest.xml ──────────────────────────────────────────

    private function buildManifest(string $id, string $title): string
    {
        $safeTitle = htmlspecialchars($title, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $safeId    = preg_replace('/[^a-zA-Z0-9_]/', '_', $id);
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<manifest identifier="RAYNET_{$safeId}" version="1.0"
          xmlns="http://www.imsproject.org/xsd/imscp_rootv1p1p2"
          xmlns:adlcp="http://www.adlnet.org/xsd/adlcp_rootv1p2"
          xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
          xsi:schemaLocation="http://www.imsproject.org/xsd/imscp_rootv1p1p2 imscp_rootv1p1p2.xsd
                              http://www.adlnet.org/xsd/adlcp_rootv1p2 adlcp_rootv1p2.xsd">
  <metadata><schema>ADL SCORM</schema><schemaversion>1.2</schemaversion></metadata>
  <organizations default="ORG">
    <organization identifier="ORG">
      <title>{$safeTitle}</title>
      <item identifier="ITEM_01" identifierref="RES_01" isvisible="true">
        <title>{$safeTitle}</title>
        <adlcp:masteryscore>80</adlcp:masteryscore>
      </item>
    </organization>
  </organizations>
  <resources>
    <resource identifier="RES_01" type="webcontent" adlcp:scormtype="sco" href="lesson.html">
      <file href="lesson.html"/>
      <file href="scorm_api.js"/>
    </resource>
  </resources>
</manifest>
XML;
    }

    // ── Private: lesson HTML generator ───────────────────────────────────

    private function buildLesson(string $title, array $slides, int $passMark): string
    {
        $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $totalSlides = count($slides);
        $quizSlides = array_values(array_filter($slides, fn($s) => $s['type'] === 'quiz'));
        $totalQuestions = array_sum(array_map(fn($s) => count($s['questions'] ?? []), $quizSlides));
        $labelsJson = json_encode(array_map(fn($s) => $s['label'] ?? ucfirst($s['type']), $slides));
        $slidesJson = json_encode($slides);

        $slidesHtml = $this->renderSlides($slides);

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>{$safeTitle} — Liverpool RAYNET</title>
<script src="scorm_api.js"></script>
<style>
:root{--navy:#003366;--navy-d:#001f40;--navy-dd:#000d1a;--red:#C8102E;--red-g:rgba(200,16,46,.35);--grey:#F2F2F2;--grey-m:#dde2e8;--white:#fff;--text:#001f40;--muted:#6b7f96;--f:Arial,'Helvetica Neue',Helvetica,sans-serif;}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;}
body{font-family:var(--f);background:var(--navy-dd);color:var(--white);min-height:100vh;overflow-x:hidden;}
#bg{position:fixed;inset:0;z-index:0;pointer-events:none;background:linear-gradient(160deg,#000d1a 0%,#001428 60%,#000d1a 100%);}
.topbar{position:fixed;top:0;left:0;right:0;z-index:100;height:52px;display:flex;align-items:center;justify-content:space-between;padding:0 22px;background:rgba(0,10,20,.9);border-bottom:3px solid var(--red);backdrop-filter:blur(10px);}
.tb-brand{display:flex;align-items:center;gap:9px;}
.tb-logo{width:32px;height:32px;background:var(--red);clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);display:flex;align-items:center;justify-content:center;font-size:7px;font-weight:bold;color:#fff;text-align:center;line-height:1.2;text-transform:uppercase;}
.tb-title{font-size:12px;font-weight:bold;color:#fff;letter-spacing:.04em;text-transform:uppercase;}
.tb-sub{font-size:9px;color:rgba(255,255,255,.35);letter-spacing:.06em;text-transform:uppercase;}
.prog-wrap{display:flex;align-items:center;gap:8px;}
.prog-track{width:90px;height:4px;background:rgba(255,255,255,.1);border-radius:99px;overflow:hidden;}
.prog-fill{height:100%;background:var(--red);border-radius:99px;width:0%;transition:width .6s cubic-bezier(.4,0,.2,1);}
.prog-lbl{font-size:10px;font-weight:bold;color:rgba(255,255,255,.4);min-width:28px;}
.slide-wrap{position:relative;z-index:1;padding-top:52px;}
.slide{display:none;min-height:calc(100vh - 52px);flex-direction:column;}
.slide.active{display:flex;animation:sIn .4s cubic-bezier(.4,0,.2,1) both;}
@keyframes sIn{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:none;}}
.lesson{max-width:860px;width:100%;margin:0 auto;padding:44px 28px 108px;flex:1;}
.rv{opacity:0;transform:translateY(16px);transition:opacity .45s,transform .45s;}
.rv.v{opacity:1;transform:none;}
.rv.d1{transition-delay:.07s;}.rv.d2{transition-delay:.14s;}.rv.d3{transition-delay:.21s;}.rv.d4{transition-delay:.28s;}.rv.d5{transition-delay:.35s;}

/* Hero */
.hero{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:60px 32px 110px;position:relative;}
.hero-eyebrow{font-size:10px;font-weight:bold;color:var(--red);letter-spacing:.22em;text-transform:uppercase;margin-bottom:20px;opacity:0;animation:fadeUp .5s .2s both;}
.hero-h{font-size:clamp(32px,7vw,72px);font-weight:bold;color:#fff;text-transform:uppercase;line-height:.95;letter-spacing:-.01em;margin-bottom:12px;opacity:0;animation:fadeUp .5s .4s both;}
.hero-h .r{color:var(--red);}
.hero-sub{font-size:15px;color:rgba(255,255,255,.5);max-width:520px;line-height:1.7;margin:0 auto 36px;opacity:0;animation:fadeUp .5s .6s both;}
.hero-meta{display:flex;gap:10px;flex-wrap:wrap;justify-content:center;margin-bottom:44px;opacity:0;animation:fadeUp .5s .8s both;}
.m-pill{display:flex;align-items:center;gap:6px;padding:5px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:99px;font-size:11px;font-weight:bold;color:rgba(255,255,255,.55);}
.m-pill-dot{width:5px;height:5px;border-radius:50%;}
.m-red{background:var(--red);}.m-blue{background:#4a9eff;}.m-gold{background:#f5c842;}.m-green{background:#22c55e;}
.start-btn{display:inline-flex;align-items:center;gap:9px;padding:14px 40px;background:var(--red);border:none;border-radius:3px;font-family:var(--f);font-size:13px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;color:#fff;cursor:pointer;position:relative;overflow:hidden;transition:transform .15s,box-shadow .15s;opacity:0;animation:fadeUp .5s 1s both;}
.start-btn::before{content:'';position:absolute;top:0;left:-100%;width:60%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.18),transparent);animation:shimmer 2.5s ease infinite;}
@keyframes shimmer{0%{left:-100%;}60%,100%{left:150%;}}
.start-btn:hover{transform:translateY(-2px);box-shadow:0 6px 24px var(--red-g);}
.arr{font-size:16px;transition:transform .2s;}
.start-btn:hover .arr{transform:translateX(4px);}

/* Section header */
.s-tag{font-size:9px;font-weight:bold;color:var(--red);letter-spacing:.22em;text-transform:uppercase;margin-bottom:10px;display:flex;align-items:center;gap:8px;}
.s-tag::after{content:'';flex:1;height:1px;background:rgba(200,16,46,.3);}
.s-h{font-size:clamp(24px,5vw,48px);font-weight:bold;color:#fff;text-transform:uppercase;line-height:1;letter-spacing:-.01em;margin-bottom:8px;}
.s-h .r{color:var(--red);}
.s-lead{font-size:14px;color:rgba(255,255,255,.5);line-height:1.7;max-width:600px;margin-bottom:36px;}

/* Text block */
.text-block{font-size:14px;color:rgba(255,255,255,.7);line-height:1.75;margin-bottom:24px;}
.text-block h2,.text-block h3{color:#fff;font-weight:bold;margin:20px 0 8px;text-transform:uppercase;letter-spacing:.04em;}
.text-block p{margin-bottom:12px;}
.text-block ul,.text-block ol{margin:0 0 14px 20px;}
.text-block li{margin-bottom:5px;}
.text-block strong{color:#fff;}

/* Cards */
.card-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;margin-bottom:32px;}
.card{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);border-top:3px solid var(--red);border-radius:2px;padding:20px 18px;transition:transform .2s,background .2s,box-shadow .2s;}
.card:hover{transform:translateY(-3px);background:rgba(255,255,255,.07);box-shadow:0 8px 28px rgba(0,0,0,.4);}
.card-icon{font-size:26px;margin-bottom:10px;display:block;}
.card-title{font-size:13px;font-weight:bold;color:#fff;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px;}
.card-body{font-size:12px;color:rgba(255,255,255,.5);line-height:1.6;}

/* Callouts */
.callout{display:flex;gap:12px;padding:14px 16px;border-radius:3px;margin-bottom:20px;font-size:13px;line-height:1.65;}
.callout-icon{font-size:16px;flex-shrink:0;margin-top:1px;}
.callout-body{flex:1;}
.callout-title{font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;}
.co-info{background:rgba(0,51,102,.3);border:1px solid rgba(0,51,102,.6);border-left:3px solid #4a9eff;color:rgba(255,255,255,.7);}
.co-warn{background:rgba(200,16,46,.08);border:1px solid rgba(200,16,46,.2);border-left:3px solid var(--red);color:rgba(255,255,255,.7);}
.co-good{background:rgba(34,197,94,.06);border:1px solid rgba(34,197,94,.2);border-left:3px solid #22c55e;color:rgba(255,255,255,.7);}
.co-title-blue{color:#7eb3ff;}.co-title-red{color:#ff8fa3;}.co-title-grn{color:#86efac;}

/* Bullet list */
.blist{list-style:none;margin-bottom:28px;}
.blist li{display:flex;align-items:flex-start;gap:10px;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.05);font-size:13px;color:rgba(255,255,255,.7);line-height:1.55;}
.blist li::before{content:'';width:6px;height:6px;border-radius:50%;background:var(--red);flex-shrink:0;margin-top:5px;}

/* Table */
.data-table{width:100%;border-collapse:collapse;margin-bottom:28px;border-radius:3px;overflow:hidden;}
.data-table thead tr{background:rgba(0,51,102,.7);}
.data-table th{padding:10px 13px;font-size:9px;font-weight:bold;text-transform:uppercase;letter-spacing:.13em;color:rgba(255,255,255,.55);text-align:left;border-bottom:2px solid var(--red);}
.data-table tbody tr{border-bottom:1px solid rgba(255,255,255,.05);transition:background .12s;}
.data-table tbody tr:hover{background:rgba(0,51,102,.2);}
.data-table td{padding:11px 13px;font-size:12px;color:rgba(255,255,255,.6);}

/* Video */
.video-wrap{position:relative;padding-bottom:56.25%;height:0;overflow:hidden;margin-bottom:28px;border:1px solid rgba(255,255,255,.08);}
.video-wrap iframe{position:absolute;top:0;left:0;width:100%;height:100%;border:none;}

/* Divider */
.divider{display:flex;align-items:center;gap:14px;margin:32px 0;font-size:9px;font-weight:bold;color:rgba(255,255,255,.2);letter-spacing:.16em;text-transform:uppercase;}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:rgba(255,255,255,.08);}

/* Quiz */
.quiz-card{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);border-radius:3px;overflow:hidden;margin-bottom:12px;}
.quiz-q-h{display:flex;align-items:center;gap:9px;padding:13px 16px;background:rgba(0,0,0,.2);border-bottom:1px solid rgba(255,255,255,.06);}
.quiz-qn{font-size:9px;font-weight:bold;color:var(--red);letter-spacing:.12em;text-transform:uppercase;background:rgba(200,16,46,.12);border:1px solid rgba(200,16,46,.25);border-radius:2px;padding:2px 6px;flex-shrink:0;}
.quiz-qt{font-size:14px;font-weight:bold;color:#fff;flex:1;}
.quiz-opts{display:flex;flex-direction:column;gap:5px;padding:12px 16px;}
.quiz-opt{display:flex;align-items:center;gap:9px;padding:10px 12px;border:1px solid rgba(255,255,255,.08);border-radius:2px;background:transparent;font-family:var(--f);font-size:12px;color:rgba(255,255,255,.7);cursor:pointer;text-align:left;width:100%;transition:border-color .12s,background .12s,transform .1s;}
.quiz-opt:hover:not(:disabled){border-color:rgba(200,16,46,.4);background:rgba(200,16,46,.06);transform:translateX(2px);}
.quiz-radio{width:13px;height:13px;border-radius:50%;border:2px solid rgba(255,255,255,.2);flex-shrink:0;position:relative;transition:.12s;}
.quiz-opt.sel .quiz-radio{border-color:var(--red);}
.quiz-opt.sel .quiz-radio::after{content:'';position:absolute;inset:2px;border-radius:50%;background:var(--red);}
.quiz-opt.ok-a{border-color:#22c55e!important;background:rgba(34,197,94,.08)!important;animation:okF .35s ease;}
.quiz-opt.bad-a{border-color:var(--red)!important;background:rgba(200,16,46,.1)!important;animation:badS .3s ease;}
@keyframes okF{0%{background:rgba(34,197,94,.3)!important;}100%{background:rgba(34,197,94,.08)!important;}}
@keyframes badS{0%,100%{transform:translateX(0);}25%{transform:translateX(-5px);}75%{transform:translateX(5px);}}
.quiz-fb{padding:9px 16px;font-size:11px;font-weight:bold;border-top:1px solid rgba(255,255,255,.06);display:none;}
.quiz-fb.show{display:block;}
.quiz-fb.ok{color:#86efac;}.quiz-fb.bad{color:#ff8fa3;}

/* Score */
.score-screen{text-align:center;padding:44px 24px;display:none;}
.score-screen.show{display:block;}
.sr-wrap{position:relative;width:150px;height:150px;margin:0 auto 22px;}
.sr-svg{width:100%;height:100%;transform:rotate(-90deg);}
.sr-track{fill:none;stroke:rgba(255,255,255,.06);stroke-width:8;}
.sr-arc{fill:none;stroke-width:8;stroke-linecap:round;stroke-dasharray:408;stroke-dashoffset:408;transition:stroke-dashoffset 1.2s cubic-bezier(.4,0,.2,1),stroke .3s;}
.sr-inner{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;}
.sr-pct{font-size:36px;font-weight:bold;color:#fff;line-height:1;}
.sr-lbl{font-size:9px;font-weight:bold;color:rgba(255,255,255,.4);letter-spacing:.12em;text-transform:uppercase;margin-top:2px;}
.sr-verdict{font-size:24px;font-weight:bold;text-transform:uppercase;letter-spacing:.06em;margin-bottom:7px;}
.sr-detail{font-size:12px;color:rgba(255,255,255,.45);margin-bottom:28px;}
.sr-btn{display:none;display:none;align-items:center;gap:7px;padding:11px 28px;background:var(--red);border:none;border-radius:3px;font-family:var(--f);font-size:12px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;color:#fff;cursor:pointer;margin:0 auto;transition:box-shadow .2s;}
.sr-btn:hover{box-shadow:0 0 18px var(--red-g);}

/* Footer nav */
.foot{position:fixed;bottom:0;left:0;right:0;z-index:100;padding:11px 22px;display:flex;align-items:center;justify-content:space-between;gap:14px;background:rgba(0,8,18,.92);border-top:1px solid rgba(255,255,255,.07);backdrop-filter:blur(10px);}
.f-dots{display:flex;gap:5px;align-items:center;}
.fdot{width:6px;height:6px;border-radius:99px;background:rgba(255,255,255,.12);transition:all .3s;cursor:pointer;}
.fdot.active{background:var(--red);width:16px;box-shadow:0 0 5px var(--red-g);}
.fdot.done{background:rgba(255,255,255,.28);}
.f-center{display:flex;flex-direction:column;align-items:center;gap:3px;}
.f-lbl{font-size:9px;font-weight:bold;color:rgba(255,255,255,.3);letter-spacing:.1em;text-transform:uppercase;}
.fbtn{display:flex;align-items:center;gap:6px;padding:8px 20px;border:1px solid;border-radius:3px;font-family:var(--f);font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:.07em;cursor:pointer;transition:all .14s;}
.f-prev{background:transparent;border-color:rgba(255,255,255,.1);color:rgba(255,255,255,.35);}
.f-prev:hover:not(:disabled){border-color:rgba(255,255,255,.22);color:rgba(255,255,255,.65);}
.f-next{background:var(--navy);border-color:rgba(0,51,102,.8);color:rgba(255,255,255,.75);}
.f-next:hover:not(:disabled){background:rgba(0,51,102,.9);}
.f-done{background:var(--red);border-color:var(--red);color:#fff;}
.f-done:hover:not(:disabled){box-shadow:0 0 16px var(--red-g);}
.fbtn:disabled{opacity:.2;cursor:not-allowed;}

/* Complete overlay */
.c-overlay{display:none;position:fixed;inset:0;z-index:200;background:rgba(0,8,18,.96);align-items:center;justify-content:center;flex-direction:column;text-align:center;padding:40px;backdrop-filter:blur(16px);}
.c-overlay.show{display:flex;animation:fadeIn .4s ease both;}
@keyframes fadeIn{from{opacity:0;}to{opacity:1;}}
.c-badge{width:84px;height:84px;clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);background:var(--red);display:flex;align-items:center;justify-content:center;font-size:34px;margin:0 auto 26px;animation:bIn .6s .2s both;box-shadow:0 0 44px var(--red-g);}
@keyframes bIn{from{opacity:0;transform:scale(.4) rotate(-30deg);}to{opacity:1;transform:scale(1) rotate(0deg);}}
.c-title{font-size:34px;font-weight:bold;text-transform:uppercase;letter-spacing:.05em;color:#fff;margin-bottom:7px;animation:fadeUp .5s .4s both;}
.c-sub{font-size:13px;color:rgba(255,255,255,.45);margin-bottom:30px;animation:fadeUp .5s .6s both;}
.c-close{display:inline-flex;align-items:center;gap:7px;padding:11px 26px;background:var(--red);border:none;border-radius:3px;font-family:var(--f);font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;color:#fff;cursor:pointer;animation:fadeUp .5s .8s both;transition:box-shadow .18s;}
.c-close:hover{box-shadow:0 0 20px var(--red-g);}
@keyframes fadeUp{from{opacity:0;transform:translateY(12px);}to{opacity:1;transform:none;}}
@media(max-width:600px){.lesson{padding:28px 14px 96px;}.card-grid{grid-template-columns:1fr;}}
</style>
</head>
<body>
<div id="bg"></div>
<nav class="topbar">
  <div class="tb-brand">
    <div class="tb-logo">RAY<br>NET</div>
    <div><div class="tb-title">Liverpool RAYNET</div><div class="tb-sub">{$safeTitle}</div></div>
  </div>
  <div class="prog-wrap">
    <div class="prog-track"><div class="prog-fill" id="pFill"></div></div>
    <span class="prog-lbl" id="pLbl">0%</span>
  </div>
</nav>

<div class="slide-wrap" id="slideWrap">{$slidesHtml}</div>

<div class="foot">
  <button class="fbtn f-prev" id="bPrev" onclick="go(cur-1)" disabled>← Back</button>
  <div class="f-center">
    <div class="f-dots" id="fDots"></div>
    <div class="f-lbl" id="fLbl">Introduction</div>
  </div>
  <button class="fbtn f-next" id="bNext" onclick="go(cur+1)">Continue →</button>
</div>

<div class="c-overlay" id="cOverlay">
  <div class="c-badge">✓</div>
  <div class="c-title">Module Complete</div>
  <div class="c-sub">{$safeTitle} — Liverpool RAYNET<br>Your completion has been recorded.</div>
  <button class="c-close" onclick="document.getElementById('cOverlay').classList.remove('show')">Close ✕</button>
</div>

<script>
const LABELS = {$labelsJson};
const TOTAL  = {$totalSlides} - 1;
const PASS   = {$passMark};
let cur = 0;
const answers = {};
let quizDone = false;

function buildDots(){
  const w=document.getElementById('fDots');w.innerHTML='';
  for(let i=0;i<=TOTAL;i++){const d=document.createElement('div');d.className='fdot';d.onclick=()=>go(i);w.appendChild(d);}
}

function updateUI(){
  const dots=document.querySelectorAll('.fdot');
  dots.forEach((d,i)=>{d.className='fdot'+(i<cur?' done':'')+(i===cur?' active':'');});
  document.getElementById('bPrev').disabled=cur===0;
  document.getElementById('fLbl').textContent=LABELS[cur]||'';
  const pct=Math.round(cur/TOTAL*100);
  document.getElementById('pFill').style.width=pct+'%';
  document.getElementById('pLbl').textContent=pct+'%';
  const nb=document.getElementById('bNext');
  if(cur===TOTAL){nb.style.display='none';}
  else{nb.style.display='';nb.textContent=cur===0?'Begin →':'Continue →';nb.className='fbtn f-next';}
  ScormAPI.setValue('cmi.core.lesson_location',String(cur));
}

function go(n){
  if(n<0||n>TOTAL)return;
  const old=document.getElementById('s'+cur);
  const nxt=document.getElementById('s'+n);
  if(!old||!nxt)return;
  old.style.animation='sOut .28s cubic-bezier(.4,0,.2,1) both';
  setTimeout(()=>{
    old.classList.remove('active');old.style.animation='';
    nxt.classList.add('active');
    window.scrollTo(0,0);revAll();
  },260);
  cur=n;updateUI();ScormAPI.setStatus('incomplete');
}

const obs=new IntersectionObserver(es=>{es.forEach(e=>{if(e.isIntersecting){e.target.classList.add('v');obs.unobserve(e.target);}});},{threshold:.1});
function revAll(){setTimeout(()=>{document.querySelectorAll('.slide.active .rv').forEach(el=>obs.observe(el));},60);}

function ansQ(qi,oi,correct){
  if(answers[qi]!==undefined)return;
  answers[qi]=correct;
  const card=document.getElementById('qq'+qi);
  const opts=card.querySelectorAll('.quiz-opt');
  const fb=document.getElementById('qfb'+qi);
  opts.forEach(o=>o.disabled=true);
  opts[oi].classList.add('sel');
  if(correct){opts[oi].classList.add('ok-a');fb.textContent='✓ Correct!';fb.className='quiz-fb show ok';}
  else{
    opts[oi].classList.add('bad-a');fb.textContent='✗ Not quite — review the material.';fb.className='quiz-fb show bad';
    opts.forEach(o=>{if(o.onclick?.toString().includes(',true,'))o.classList.add('ok-a');});
  }
  const allQs=document.querySelectorAll('[id^="qq"]');
  const allAnswered=Array.from(allQs).every((_,i)=>answers[i]!==undefined);
  if(allAnswered)setTimeout(showScore,500);
}

function showScore(){
  const qs=document.querySelectorAll('[id^="qq"]');
  const right=Object.values(answers).filter(Boolean).length;
  const pct=Math.round(right/qs.length*100);
  const pass=pct>=PASS;
  document.getElementById('quizArea').style.display='none';
  const ss=document.getElementById('scoreScreen');ss.classList.add('show');
  setTimeout(()=>{
    document.getElementById('srPct').textContent=pct+'%';
    const arc=document.getElementById('srArc');
    const circ=2*Math.PI*65;
    arc.style.strokeDashoffset=circ-(circ*pct/100);
    arc.style.stroke=pass?'#22c55e':'#C8102E';
  },200);
  document.getElementById('srVerdict').textContent=pass?'✓ Passed':'✗ Not Passed';
  document.getElementById('srVerdict').style.color=pass?'#86efac':'#ff8fa3';
  document.getElementById('srDetail').textContent=right+' of '+qs.length+' correct — '+(pass?'well done!':'please review and retry.');
  ScormAPI.setScore(pct,0,100);
  if(pass){const b=document.getElementById('srBtn');b.style.display='inline-flex';ScormAPI.finish('passed');}
  else ScormAPI.finish('failed');
}

function completeMod(){document.getElementById('cOverlay').classList.add('show');}

buildDots();updateUI();revAll();
const saved=ScormAPI.getValue('cmi.core.lesson_location');
if(saved&&parseInt(saved)>0)go(Math.min(parseInt(saved),TOTAL));
</script>
</body>
</html>
HTML;
    }

    private function renderSlides(array $slides): string
    {
        $html = '';
        foreach ($slides as $i => $slide) {
            $active = $i === 0 ? ' active' : '';
            $type   = $slide['type'] ?? 'text';
            $html  .= "<div class=\"slide{$active}\" id=\"s{$i}\">" . $this->renderSlide($slide, $i) . "</div>\n";
        }
        return $html;
    }

    private function renderSlide(array $slide, int $idx): string
    {
        $type = $slide['type'] ?? 'text';

        return match ($type) {
            'hero'     => $this->renderHero($slide, $idx),
            'cards'    => $this->renderCards($slide, $idx),
            'callout'  => $this->renderCallout($slide, $idx),
            'bullets'  => $this->renderBullets($slide, $idx),
            'table'    => $this->renderTable($slide, $idx),
            'video'    => $this->renderVideo($slide, $idx),
            'divider'  => $this->renderDivider($slide, $idx),
            'quiz'     => $this->renderQuiz($slide, $idx),
            default    => $this->renderText($slide, $idx),
        };
    }

    private function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

    private function renderHero(array $s, int $idx): string
    {
        $title    = $this->h($s['title'] ?? 'Untitled Module');
        $subtitle = $this->h($s['subtitle'] ?? '');
        $desc     = $this->h($s['description'] ?? '');
        $eyebrow  = $this->h($s['eyebrow'] ?? 'Liverpool RAYNET · Training');
        return <<<HTML
<div class="hero">
  <div class="hero-eyebrow">{$eyebrow}</div>
  <h1 class="hero-h">{$title}</h1>
  <div class="hero-sub" style="font-size:clamp(16px,3vw,26px);font-weight:bold;color:rgba(255,255,255,.35);text-transform:uppercase;letter-spacing:.08em;margin-bottom:18px;opacity:0;animation:fadeUp .5s .55s both;">{$subtitle}</div>
  <p class="hero-sub">{$desc}</p>
  <div class="hero-meta">
    <span class="m-pill"><span class="m-pill-dot m-red"></span>Liverpool RAYNET</span>
    <span class="m-pill"><span class="m-pill-dot m-blue"></span>Pass mark 80%</span>
    <span class="m-pill"><span class="m-pill-dot m-green"></span>Certificate awarded</span>
  </div>
  <button class="start-btn" onclick="go(1)">Begin Module <span class="arr">→</span></button>
</div>
HTML;
    }

    private function renderText(array $s, int $idx): string
    {
        $tag   = $this->h($s['tag'] ?? '');
        $title = $this->h($s['title'] ?? '');
        $body  = $s['body'] ?? '';
        $safeBody = nl2br(htmlspecialchars($body, ENT_QUOTES, 'UTF-8'));
        return <<<HTML
<div class="lesson">
  <div class="s-tag rv">{$tag}</div>
  <h2 class="s-h rv">{$title}</h2>
  <div class="text-block rv d1">{$safeBody}</div>
</div>
HTML;
    }

    private function renderCards(array $s, int $idx): string
    {
        $tag   = $this->h($s['tag'] ?? '');
        $title = $this->h($s['title'] ?? '');
        $lead  = $this->h($s['lead'] ?? '');
        $cards = $s['cards'] ?? [];
        $cardsHtml = '';
        foreach ($cards as $ci => $c) {
            $icon  = $this->h($c['icon'] ?? '📄');
            $ct    = $this->h($c['title'] ?? '');
            $cb    = $this->h($c['body'] ?? '');
            $d     = 'd' . ($ci + 1);
            $cardsHtml .= "<div class=\"card rv {$d}\"><span class=\"card-icon\">{$icon}</span><div class=\"card-title\">{$ct}</div><div class=\"card-body\">{$cb}</div></div>\n";
        }
        return <<<HTML
<div class="lesson">
  <div class="s-tag rv">{$tag}</div>
  <h2 class="s-h rv">{$title}</h2>
  <p class="s-lead rv">{$lead}</p>
  <div class="card-grid">{$cardsHtml}</div>
</div>
HTML;
    }

    private function renderCallout(array $s, int $idx): string
    {
        $tag     = $this->h($s['tag'] ?? '');
        $title   = $this->h($s['title'] ?? '');
        $lead    = $this->h($s['lead'] ?? '');
        $type    = $s['callout_type'] ?? 'info';
        $icon    = $this->h($s['icon'] ?? 'ℹ');
        $ctitle  = $this->h($s['callout_title'] ?? '');
        $cbody   = $this->h($s['callout_body'] ?? '');
        $typeMap = ['info' => 'co-info', 'warn' => 'co-warn', 'good' => 'co-good'];
        $cls     = $typeMap[$type] ?? 'co-info';
        $tcMap   = ['info' => 'co-title-blue', 'warn' => 'co-title-red', 'good' => 'co-title-grn'];
        $tcls    = $tcMap[$type] ?? 'co-title-blue';
        return <<<HTML
<div class="lesson">
  <div class="s-tag rv">{$tag}</div>
  <h2 class="s-h rv">{$title}</h2>
  <p class="s-lead rv d1">{$lead}</p>
  <div class="callout {$cls} rv d2">
    <span class="callout-icon">{$icon}</span>
    <div class="callout-body"><div class="callout-title {$tcls}">{$ctitle}</div>{$cbody}</div>
  </div>
</div>
HTML;
    }

    private function renderBullets(array $s, int $idx): string
    {
        $tag   = $this->h($s['tag'] ?? '');
        $title = $this->h($s['title'] ?? '');
        $lead  = $this->h($s['lead'] ?? '');
        $items = $s['items'] ?? [];
        $liHtml = '';
        foreach ($items as $item) {
            $liHtml .= '<li>' . $this->h($item) . '</li>';
        }
        return <<<HTML
<div class="lesson">
  <div class="s-tag rv">{$tag}</div>
  <h2 class="s-h rv">{$title}</h2>
  <p class="s-lead rv d1">{$lead}</p>
  <ul class="blist rv d2">{$liHtml}</ul>
</div>
HTML;
    }

    private function renderTable(array $s, int $idx): string
    {
        $tag     = $this->h($s['tag'] ?? '');
        $title   = $this->h($s['title'] ?? '');
        $lead    = $this->h($s['lead'] ?? '');
        $headers = $s['headers'] ?? [];
        $rows    = $s['rows'] ?? [];
        $thHtml  = implode('', array_map(fn($h) => '<th>' . $this->h($h) . '</th>', $headers));
        $rowsHtml = '';
        foreach ($rows as $row) {
            $rowsHtml .= '<tr>' . implode('', array_map(fn($c) => '<td>' . $this->h($c) . '</td>', $row)) . '</tr>';
        }
        return <<<HTML
<div class="lesson">
  <div class="s-tag rv">{$tag}</div>
  <h2 class="s-h rv">{$title}</h2>
  <p class="s-lead rv d1">{$lead}</p>
  <table class="data-table rv d2"><thead><tr>{$thHtml}</tr></thead><tbody>{$rowsHtml}</tbody></table>
</div>
HTML;
    }

    private function renderVideo(array $s, int $idx): string
    {
        $tag   = $this->h($s['tag'] ?? '');
        $title = $this->h($s['title'] ?? '');
        $lead  = $this->h($s['lead'] ?? '');
        $url   = $s['url'] ?? '';
        // Convert YouTube URL to embed
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $m)) {
            $url = 'https://www.youtube.com/embed/' . $m[1];
        } elseif (preg_match('/vimeo\.com\/(\d+)/', $url, $m)) {
            $url = 'https://player.vimeo.com/video/' . $m[1];
        }
        $safeUrl = $this->h($url);
        return <<<HTML
<div class="lesson">
  <div class="s-tag rv">{$tag}</div>
  <h2 class="s-h rv">{$title}</h2>
  <p class="s-lead rv d1">{$lead}</p>
  <div class="video-wrap rv d2"><iframe src="{$safeUrl}" allowfullscreen allow="autoplay"></iframe></div>
</div>
HTML;
    }

    private function renderDivider(array $s, int $idx): string
    {
        $tag   = $this->h($s['tag'] ?? '');
        $title = $this->h($s['title'] ?? '');
        $body  = $this->h($s['body'] ?? '');
        return <<<HTML
<div class="lesson">
  <div class="s-tag rv">{$tag}</div>
  <h2 class="s-h rv">{$title}</h2>
  <div class="divider rv d1">Section Break</div>
  <div class="text-block rv d2">{$body}</div>
</div>
HTML;
    }

    private function renderQuiz(array $s, int $idx): string
    {
        $tag   = $this->h($s['tag'] ?? '');
        $title = $this->h($s['title'] ?? '');
        $lead  = $this->h($s['lead'] ?? '');
        $qs    = $s['questions'] ?? [];

        $qHtml = '';
        foreach ($qs as $qi => $q) {
            $qText  = $this->h($q['question'] ?? '');
            $optHtml = '';
            foreach (($q['options'] ?? []) as $oi => $opt) {
                $correct = ($q['correct'] ?? 0) === $oi ? 'true' : 'false';
                $optText = $this->h($opt);
                $optHtml .= "<button class=\"quiz-opt\" onclick=\"ansQ({$qi},{$oi},{$correct})\"><span class=\"quiz-radio\"></span>{$optText}</button>";
            }
            $qHtml .= <<<HTML
<div class="quiz-card rv d{$qi}" id="qq{$qi}">
  <div class="quiz-q-h"><span class="quiz-qn">Q{$this->h((string)($qi+1))}</span><span class="quiz-qt">{$qText}</span></div>
  <div class="quiz-opts">{$optHtml}</div>
  <div class="quiz-fb" id="qfb{$qi}"></div>
</div>
HTML;
        }

        return <<<HTML
<div class="lesson">
  <div class="s-tag rv">{$tag}</div>
  <h2 class="s-h rv">{$title}</h2>
  <p class="s-lead rv d1">{$lead}</p>
  <div id="quizArea">{$qHtml}</div>
  <div class="score-screen" id="scoreScreen">
    <div class="sr-wrap">
      <svg class="sr-svg" viewBox="0 0 140 140"><circle class="sr-track" cx="70" cy="70" r="65"/><circle class="sr-arc" id="srArc" cx="70" cy="70" r="65"/></svg>
      <div class="sr-inner"><div class="sr-pct" id="srPct">0%</div><div class="sr-lbl">Score</div></div>
    </div>
    <div class="sr-verdict" id="srVerdict">—</div>
    <div class="sr-detail" id="srDetail">—</div>
    <button class="sr-btn" id="srBtn" onclick="completeMod()">✓ Complete Module</button>
  </div>
</div>
HTML;
    }

    private function inlineScormApi(): string
    {
        return <<<'JS'
var ScormAPI=(function(){var _api=null,_init=false,_data={};
function _find(w){var t=0;while(!w.API&&w.parent&&w.parent!==w&&t<10){w=w.parent;t++;}return w.API||null;}
function _get(){if(_api)return _api;_api=_find(window);if(!_api&&window.opener)_api=_find(window.opener);return _api;}
function init(){var a=_get();if(!a){_init=true;return true;}var r=a.LMSInitialize('');_init=(r==='true'||r===true);return _init;}
function finish(s){var a=_get();if(!a||!_init)return;if(s)setValue('cmi.core.lesson_status',s);a.LMSCommit('');a.LMSFinish('');_init=false;}
function getValue(k){var a=_get();if(!a)return _data[k]||'';return a.LMSGetValue(k)||'';}
function setValue(k,v){_data[k]=v;var a=_get();if(!a)return;a.LMSSetValue(k,v);a.LMSCommit('');}
function setStatus(s){setValue('cmi.core.lesson_status',s);}
function setScore(r,mn,mx){setValue('cmi.core.score.raw',r);setValue('cmi.core.score.min',mn||0);setValue('cmi.core.score.max',mx||100);}
function complete(s){if(s!==undefined)setScore(s,0,100);finish(s>=80?'passed':'completed');}
window.addEventListener('load',function(){init();});
window.addEventListener('beforeunload',function(){if(getValue('cmi.core.lesson_status')==='not attempted')finish('incomplete');});
return{init,finish,getValue,setValue,setStatus,setScore,complete};})();
JS;
    }
}