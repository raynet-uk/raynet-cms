@extends('layouts.app')
@section('title', $course->title)
@section('content')
<style>
:root{--navy:#003366;--red:#C8102E;--teal:#0288d1;--green:#1a6b3c;--green-bg:#eef7f2;--amber:#8a5500;--amber-bg:#fdf8ec;--grey:#f2f5f9;--grey-mid:#dde2e8;--white:#fff;--text:#001f40;--text-mid:#2d4a6b;--muted:#6b7f96;--shadow-sm:0 1px 3px rgba(0,51,102,.09);--font:Arial,'Helvetica Neue',Helvetica,sans-serif;}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:var(--font);background:var(--grey);color:var(--text);}
.course-hero{background:var(--navy);border-bottom:4px solid var(--red);padding:2rem 1.5rem;}
.course-hero-inner{max-width:1000px;margin:0 auto;}
.course-hero-cat{font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.14em;color:rgba(255,255,255,.5);margin-bottom:.4rem;}
.course-hero h1{font-size:22px;font-weight:bold;color:#fff;margin-bottom:.6rem;line-height:1.3;}
.course-hero-meta{display:flex;gap:1rem;flex-wrap:wrap;}
.course-hero-chip{font-size:11px;color:rgba(255,255,255,.6);display:flex;align-items:center;gap:.3rem;}
.wrap{max-width:1000px;margin:0 auto;padding:1.5rem 1.5rem 4rem;}
.course-layout{display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start;}
@media(max-width:800px){.course-layout{grid-template-columns:1fr;}}
.card{background:var(--white);border:1px solid var(--grey-mid);box-shadow:var(--shadow-sm);margin-bottom:1rem;overflow:hidden;}
.card-head{padding:.75rem 1.1rem;border-bottom:1px solid var(--grey-mid);background:var(--grey);font-size:12px;font-weight:bold;color:var(--navy);text-transform:uppercase;letter-spacing:.06em;}
.module-section{border-bottom:1px solid var(--grey-mid);}
.module-section:last-child{border-bottom:none;}
.module-title-row{padding:.7rem 1.1rem;background:var(--grey);display:flex;align-items:center;gap:.65rem;cursor:pointer;}
.module-title{font-size:12px;font-weight:bold;color:var(--navy);}
.lesson-list{padding:.35rem 0;}
.lesson-row{display:flex;align-items:center;gap:.75rem;padding:.55rem 1.1rem .55rem 2rem;text-decoration:none;color:var(--text-mid);font-size:13px;transition:background .1s;border-left:3px solid transparent;}
.lesson-row:hover{background:#f5f8ff;}
.lesson-row.completed{border-left-color:var(--green);}
.lesson-row.locked{opacity:.5;cursor:not-allowed;pointer-events:none;}
.lesson-type-icon{font-size:14px;flex-shrink:0;width:20px;text-align:center;}
.lesson-row-title{flex:1;}
.lesson-row-meta{font-size:10px;color:var(--muted);}
.lesson-check{font-size:14px;color:var(--green);flex-shrink:0;}
.sidebar-card{background:var(--white);border:1px solid var(--grey-mid);box-shadow:var(--shadow-sm);overflow:hidden;position:sticky;top:1rem;}
.sidebar-card-head{background:var(--navy);padding:1rem 1.1rem;}
.sidebar-progress-label{font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.5);margin-bottom:.4rem;}
.sidebar-progress-num{font-size:26px;font-weight:bold;color:#fff;line-height:1;}
.sidebar-progress-sub{font-size:11px;color:rgba(255,255,255,.5);margin-top:2px;}
.sidebar-progress-track{height:5px;background:rgba(255,255,255,.15);overflow:hidden;margin-top:.85rem;}
.sidebar-progress-fill{height:100%;background:var(--teal);transition:width .4s ease;}
.sidebar-meta{padding:.85rem 1.1rem;border-bottom:1px solid var(--grey-mid);}
.sidebar-meta-row{display:flex;justify-content:space-between;font-size:12px;padding:.3rem 0;border-bottom:1px solid var(--grey-mid);}
.sidebar-meta-row:last-child{border-bottom:none;}
.sidebar-meta-label{color:var(--muted);}
.sidebar-meta-val{font-weight:bold;color:var(--text);}
.sidebar-foot{padding:.85rem 1.1rem;}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:.4rem;padding:.55rem 1.1rem;border:1px solid;font-family:var(--font);font-size:12px;font-weight:bold;cursor:pointer;text-decoration:none;text-transform:uppercase;letter-spacing:.05em;transition:all .12s;width:100%;}
.btn-primary{background:var(--navy);border-color:var(--navy);color:#fff;}
.btn-primary:hover{background:#002244;}
.btn-teal{background:var(--teal);border-color:var(--teal);color:#fff;}
.btn-teal:hover{background:#0277bd;}
.btn-green{background:var(--green-bg);border-color:#b8ddc9;color:var(--green);}
.btn-green:hover{background:#d6ede3;}
.alert{padding:.65rem 1rem;margin-bottom:1rem;font-size:13px;font-weight:bold;display:flex;align-items:center;gap:.6rem;}
.alert-error{background:#fdf0f2;color:var(--red);border:1px solid rgba(200,16,46,.25);border-left:3px solid var(--red);}
</style>

@php
$progressPct = $enrollment ? $course->getProgressFor(auth()->id()) : 0;
$icons = ['text'=>'📄','video'=>'🎬','scorm'=>'📦','quiz'=>'❓'];
@endphp

<div class="course-hero">
    <div class="course-hero-inner">
        <div class="course-hero-cat">{{ $course->category ?? 'RAYNET Training' }}</div>
        <h1>{{ $course->title }}</h1>
        <div class="course-hero-meta">
            <span class="course-hero-chip">📊 {{ ucfirst($course->difficulty) }}</span>
            @if($course->estimated_hours)<span class="course-hero-chip">⏱ ~{{ $course->estimated_hours }} hours</span>@endif
            <span class="course-hero-chip">📋 {{ $course->lessons()->count() }} lessons</span>
            @if($course->certificate_enabled)<span class="course-hero-chip">🏅 Certificate on completion</span>@endif
        </div>
    </div>
</div>

<div class="wrap">
    @if(session('error'))<div class="alert alert-error">✕ {{ session('error') }}</div>@endif

    <div class="course-layout">
        <div>
            {{-- Description --}}
            @if($course->description)
            <div class="card" style="margin-bottom:1rem;">
                <div class="card-head">About this Course</div>
                <div style="padding:1rem 1.1rem;font-size:13px;color:var(--text-mid);line-height:1.7;">
                    {{ $course->description }}
                </div>
            </div>
            @endif

            {{-- Modules --}}
            <div class="card">
                <div class="card-head">Course Content</div>
                @forelse($course->modules as $mod)
                <div class="module-section">
                    <div class="module-title-row">
                        <span style="font-size:14px;">📁</span>
                        <span class="module-title">{{ $mod->title }}</span>
                        <span style="font-size:11px;color:var(--muted);margin-left:auto;">{{ $mod->lessons->count() }} lessons</span>
                    </div>
                    <div class="lesson-list">
                        @foreach($mod->lessons as $lesson)
                        @php
                            $done = isset($progress[$lesson->id]) && $progress[$lesson->id];
                            $canView = $enrollment && ($lesson->is_free_preview || $enrollment);
                        @endphp
                        <a href="{{ $canView ? route('lms.lesson', [$course->slug, $lesson->id]) : '#' }}"
                           class="lesson-row {{ $done ? 'completed' : '' }} {{ !$canView ? 'locked' : '' }}">
                            <span class="lesson-type-icon">{{ $icons[$lesson->type] ?? '📄' }}</span>
                            <span class="lesson-row-title">{{ $lesson->title }}</span>
                            @if($lesson->duration_minutes)
                                <span class="lesson-row-meta">{{ $lesson->duration_minutes }}m</span>
                            @endif
                            @if($lesson->drip_days > 0 && !$done)
                                <span class="lesson-row-meta">🔒 Day {{ $lesson->drip_days }}</span>
                            @endif
                            @if($done)
                                <span class="lesson-check">✓</span>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>
                @empty
                <div style="padding:2rem;text-align:center;font-size:13px;color:var(--muted);">No lessons yet.</div>
                @endforelse
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="sidebar-card">
            <div class="sidebar-card-head">
                <div class="sidebar-progress-label">Your Progress</div>
                <div class="sidebar-progress-num">{{ $progressPct }}%</div>
                <div class="sidebar-progress-sub">{{ $enrollment ? 'Enrolled' : 'Not enrolled' }}</div>
                <div class="sidebar-progress-track">
                    <div class="sidebar-progress-fill" style="width:{{ $progressPct }}%;"></div>
                </div>
            </div>
            <div class="sidebar-meta">
                <div class="sidebar-meta-row">
                    <span class="sidebar-meta-label">Difficulty</span>
                    <span class="sidebar-meta-val">{{ ucfirst($course->difficulty) }}</span>
                </div>
                @if($course->estimated_hours)
                <div class="sidebar-meta-row">
                    <span class="sidebar-meta-label">Est. Duration</span>
                    <span class="sidebar-meta-val">~{{ $course->estimated_hours }}h</span>
                </div>
                @endif
                <div class="sidebar-meta-row">
                    <span class="sidebar-meta-label">Certificate</span>
                    <span class="sidebar-meta-val">{{ $course->certificate_enabled ? 'Yes' : 'No' }}</span>
                </div>
                @if($enrollment?->due_date)
                <div class="sidebar-meta-row">
                    <span class="sidebar-meta-label">Due Date</span>
                    <span class="sidebar-meta-val" style="{{ $enrollment->due_date->isPast() && !$enrollment->completed_at ? 'color:var(--red);' : '' }}">
                        {{ $enrollment->due_date->format('d M Y') }}
                    </span>
                </div>
                @endif
            </div>
        {{-- Badges unlocked on completion --}}
        @if(!empty($course->unlocks_badge_ids) && count($course->unlocks_badge_ids) > 0)
        @php
        $badgeMap = [
            1   => ['label'=>'Operator',             'colour'=>'#003366', 'num'=>'T1'],
            2   => ['label'=>'Checkpoint Supervisor','colour'=>'#0277bd', 'num'=>'T2'],
            3   => ['label'=>'Net Controller',       'colour'=>'#1a7a3c', 'num'=>'T3'],
            4   => ['label'=>'Event Manager',        'colour'=>'#b45309', 'num'=>'T4'],
            5   => ['label'=>'Response Manager',     'colour'=>'#C8102E', 'num'=>'T5'],
            101 => ['label'=>'Power Systems',        'colour'=>'#5b21b6', 'num'=>'T1'],
            102 => ['label'=>'Digital Modes',        'colour'=>'#5b21b6', 'num'=>'T2'],
            111 => ['label'=>'Mapping',              'colour'=>'#0f766e', 'num'=>'O1'],
            112 => ['label'=>'Severe Weather',       'colour'=>'#0f766e', 'num'=>'O2'],
            113 => ['label'=>'First Aid Comms',      'colour'=>'#0f766e', 'num'=>'O3'],
            114 => ['label'=>'Marathon Ops',         'colour'=>'#0f766e', 'num'=>'O4'],
            115 => ['label'=>'Air Support',          'colour'=>'#0f766e', 'num'=>'O5'],
            116 => ['label'=>'Water Ops',            'colour'=>'#0f766e', 'num'=>'O6'],
            121 => ['label'=>'GDPR',                 'colour'=>'#be185d', 'num'=>'A1'],
            122 => ['label'=>'Media Liaison',        'colour'=>'#be185d', 'num'=>'A2'],
            123 => ['label'=>'Safeguarding',         'colour'=>'#be185d', 'num'=>'A3'],
            124 => ['label'=>'No Secret Codes',      'colour'=>'#be185d', 'num'=>'A4'],
            201 => ['label'=>'Antennas',             'colour'=>'#374151', 'num'=>'K1'],
            202 => ['label'=>'NVIS',                 'colour'=>'#374151', 'num'=>'K2'],
        ];
        $userBadgeIds = is_array(auth()->user()->completed_course_ids)
            ? auth()->user()->completed_course_ids
            : (json_decode(auth()->user()->completed_course_ids ?? '[]', true) ?? []);
        @endphp
        <div style="padding:.85rem 1.1rem;border-top:1px solid var(--grey-mid);">
            <div style="font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);margin-bottom:.65rem;">
                🏅 Badges Unlocked on Completion
            </div>
            <div style="display:flex;flex-direction:column;gap:.5rem;">
                @foreach($course->unlocks_badge_ids as $badgeId)
                @php
                    $badge   = $badgeMap[(int)$badgeId] ?? null;
                    $already = in_array((int)$badgeId, array_map('intval', $userBadgeIds));
                @endphp
                @if($badge)
                <div style="display:flex;align-items:center;gap:.65rem;padding:.45rem .65rem;border:1px solid {{ $already ? '#b8ddc9' : 'var(--grey-mid)' }};background:{{ $already ? 'var(--green-bg)' : 'var(--grey)' }};">
                    <svg width="28" height="28" viewBox="0 0 28 28" xmlns="http://www.w3.org/2000/svg" style="flex-shrink:0;">
                        <polygon points="14,1.5 25.5,7.75 25.5,20.25 14,26.5 2.5,20.25 2.5,7.75"
                                 fill="{{ $already ? $badge['colour'] : '#dde2e8' }}"
                                 stroke="{{ $already ? $badge['colour'] : '#c8d4e0' }}"
                                 stroke-width="1.5"/>
                        <text x="14" y="18" text-anchor="middle"
                              font-family="Arial,sans-serif" font-size="7" font-weight="bold"
                              fill="{{ $already ? '#fff' : 'rgba(0,0,0,.25)' }}">{{ $badge['num'] }}</text>
                    </svg>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:12px;font-weight:bold;color:{{ $already ? 'var(--green)' : 'var(--text)' }};">
                            {{ $badge['label'] }}
                        </div>
                        <div style="font-size:10px;color:var(--muted);">
                            {{ $already ? '✓ Already unlocked' : 'Unlocks when you complete this course' }}
                        </div>
                    </div>
                    @if($already)
                        <span style="font-size:9px;font-weight:bold;padding:2px 6px;background:var(--green-bg);border:1px solid #b8ddc9;color:var(--green);flex-shrink:0;">✓ Earned</span>
                    @else
                        <span style="font-size:9px;font-weight:bold;padding:2px 6px;background:#e8eef5;border:1px solid rgba(0,51,102,.2);color:var(--navy);flex-shrink:0;">🔒 Locked</span>
                    @endif
                </div>
                @endif
                @endforeach
            </div>
        </div>
        @endif
        <div class="sidebar-foot">
                @if(!$enrollment)
                    <div style="font-size:12px;color:var(--muted);text-align:center;padding:.5rem 0;">Contact your Group Controller to be enrolled on this course.</div>
                @elseif($certificate)
                    <a href="{{ route('lms.certificate', $course->id) }}" class="btn btn-green" style="margin-bottom:.5rem;">🏅 View Certificate</a>
                    @php $firstLesson = $course->lessons()->first(); @endphp
                    @if($firstLesson)
                    <a href="{{ route('lms.lesson', [$course->slug, $firstLesson->id]) }}" class="btn btn-teal">▶ Review Course</a>
                    @endif
                @else
                    @php
                        $nextLesson = $course->lessons()->get()->first(function($l) use ($progress) {
                            return !isset($progress[$l->id]) || !$progress[$l->id];
                        });
                    @endphp
                    @if($nextLesson)
                    <a href="{{ route('lms.lesson', [$course->slug, $nextLesson->id]) }}" class="btn btn-primary">
                        {{ $progressPct > 0 ? '▶ Continue' : '▶ Start Course' }}
                    </a>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection