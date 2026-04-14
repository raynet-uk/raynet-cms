@extends('layouts.app')
@section('title', 'Training & LMS')
@section('content')
<style>
:root{--navy:#003366;--red:#C8102E;--teal:#0288d1;--green:#1a6b3c;--green-bg:#eef7f2;--amber:#8a5500;--amber-bg:#fdf8ec;--grey:#f2f2f2;--grey-mid:#dde2e8;--white:#fff;--text:#001f40;--text-mid:#2d4a6b;--muted:#6b7f96;--shadow-sm:0 1px 3px rgba(0,51,102,.09);--font:Arial,'Helvetica Neue',Helvetica,sans-serif;}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:var(--font);background:var(--grey);color:var(--text);}
.lms-header{background:var(--navy);border-bottom:4px solid var(--red);padding:0 1.5rem;}
.lms-header-inner{max-width:1200px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;height:58px;gap:1rem;}
.lms-brand{display:flex;align-items:center;gap:.75rem;}
.lms-logo{width:36px;height:36px;background:var(--red);display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:bold;color:#fff;text-align:center;line-height:1.2;text-transform:uppercase;letter-spacing:.05em;}
.lms-title{font-size:14px;font-weight:bold;color:#fff;text-transform:uppercase;letter-spacing:.06em;}
.lms-sub{font-size:10px;color:rgba(255,255,255,.45);text-transform:uppercase;letter-spacing:.08em;margin-top:1px;}
.btn{display:inline-flex;align-items:center;gap:.4rem;padding:.45rem 1.1rem;border:1px solid;font-family:var(--font);font-size:12px;font-weight:bold;cursor:pointer;text-decoration:none;text-transform:uppercase;letter-spacing:.05em;transition:all .12s;}
.btn-primary{background:var(--navy);border-color:var(--navy);color:#fff;}
.btn-primary:hover{background:#002244;}
.btn-red{background:rgba(200,16,46,.08);border-color:rgba(200,16,46,.3);color:var(--red);}
.btn-red:hover{background:rgba(200,16,46,.15);}
.btn-sm{padding:.28rem .75rem;font-size:11px;}
.btn-ghost{background:transparent;border-color:var(--grey-mid);color:var(--muted);}
.btn-ghost:hover{border-color:var(--navy);color:var(--navy);}
.wrap{max-width:1200px;margin:0 auto;padding:1.5rem 1.5rem 4rem;}
.page-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;gap:1rem;flex-wrap:wrap;}
.page-head h1{font-size:20px;font-weight:bold;color:var(--navy);}
.page-head-sub{font-size:12px;color:var(--muted);margin-top:2px;}
.alert{padding:.65rem 1rem;margin-bottom:1rem;font-size:13px;font-weight:bold;display:flex;align-items:center;gap:.6rem;}
.alert-success{background:var(--green-bg);color:var(--green);border:1px solid #b8ddc9;border-left:3px solid var(--green);}
.course-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:1rem;}
.course-card{background:var(--white);border:1px solid var(--grey-mid);box-shadow:var(--shadow-sm);overflow:hidden;transition:box-shadow .15s;}
.course-card:hover{box-shadow:0 4px 16px rgba(0,51,102,.12);}
.course-card-top{padding:1rem 1.1rem;border-bottom:1px solid var(--grey-mid);background:var(--grey);display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem;}
.course-card-title{font-size:14px;font-weight:bold;color:var(--navy);line-height:1.3;}
.course-card-meta{font-size:11px;color:var(--muted);margin-top:.3rem;display:flex;gap:.75rem;flex-wrap:wrap;}
.badge{display:inline-flex;align-items:center;padding:2px 8px;font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.05em;}
.badge-green{background:var(--green-bg);border:1px solid #b8ddc9;color:var(--green);}
.badge-amber{background:var(--amber-bg);border:1px solid #f5d87a;color:var(--amber);}
.badge-grey{background:var(--grey);border:1px solid var(--grey-mid);color:var(--muted);}
.badge-navy{background:#e8eef5;border:1px solid rgba(0,51,102,.2);color:var(--navy);}
.course-card-stats{display:grid;grid-template-columns:repeat(3,1fr);border-bottom:1px solid var(--grey-mid);}
.course-stat{padding:.6rem .85rem;text-align:center;border-right:1px solid var(--grey-mid);}
.course-stat:last-child{border-right:none;}
.course-stat-num{font-size:18px;font-weight:bold;color:var(--navy);}
.course-stat-label{font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-top:1px;}
.course-card-foot{padding:.7rem 1.1rem;display:flex;align-items:center;justify-content:space-between;gap:.5rem;flex-wrap:wrap;}
.empty-state{background:var(--white);border:1px solid var(--grey-mid);padding:4rem 2rem;text-align:center;}
.empty-icon{font-size:3rem;opacity:.2;margin-bottom:1rem;}
.empty-title{font-size:16px;font-weight:bold;color:var(--muted);margin-bottom:.5rem;}
.empty-sub{font-size:13px;color:var(--muted);}
</style>

<div class="lms-header">
    <div class="lms-header-inner">
        <div class="lms-brand">
            <div class="lms-logo">RAY<br>NET</div>
            <div>
                <div class="lms-title">Liverpool RAYNET</div>
                <div class="lms-sub">Training &amp; LMS</div>
            </div>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost btn-sm" style="color:rgba(255,255,255,.7);border-color:rgba(255,255,255,.2);">← Admin</a>
    </div>
</div>

<div class="wrap">
    @if(session('success'))
        <div class="alert alert-success">✓ {{ session('success') }}</div>
    @endif

    <div class="page-head">
        <div>
            <h1>🏫 Course Library</h1>
            <div class="page-head-sub">{{ $courses->count() }} {{ Str::plural('course', $courses->count()) }} · Build, publish and assign training to members.</div>
        </div>
        <a href="{{ route('admin.lms.create') }}" class="btn btn-primary">+ New Course</a>
    </div>

    @if($courses->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">🎓</div>
            <div class="empty-title">No courses yet</div>
            <div class="empty-sub">Create your first course to start building the RAYNET training programme.</div>
            <br>
            <a href="{{ route('admin.lms.create') }}" class="btn btn-primary" style="margin-top:.75rem;">+ Create First Course</a>
        </div>
    @else
        <div class="course-grid">
            @foreach($courses as $course)
            <div class="course-card">
                <div class="course-card-top">
                    <div>
                        <div class="course-card-title">{{ $course->title }}</div>
                        <div class="course-card-meta">
                            <span>{{ ucfirst($course->difficulty) }}</span>
                            @if($course->category)<span>{{ $course->category }}</span>@endif
                            @if($course->estimated_hours)<span>~{{ $course->estimated_hours }}h</span>@endif
                        </div>
                    </div>
                    <span class="badge {{ $course->is_published ? 'badge-green' : 'badge-amber' }}">
                        {{ $course->is_published ? '● Live' : '○ Draft' }}
                    </span>
                </div>
                <div class="course-card-stats">
                    <div class="course-stat">
                        <div class="course-stat-num">{{ $course->lessons_count }}</div>
                        <div class="course-stat-label">Lessons</div>
                    </div>
                    <div class="course-stat">
                        <div class="course-stat-num">{{ $course->enrollments_count }}</div>
                        <div class="course-stat-label">Enrolled</div>
                    </div>
                    <div class="course-stat">
                        <div class="course-stat-num">{{ $course->certificate_enabled ? '✓' : '—' }}</div>
                        <div class="course-stat-label">Certificate</div>
                    </div>
                </div>
                <div class="course-card-foot">
                    <div style="display:flex;gap:.4rem;flex-wrap:wrap;">
                        <a href="{{ route('admin.lms.edit', $course->id) }}" class="btn btn-ghost btn-sm">✎ Build</a>
                        <a href="{{ route('admin.lms.analytics', $course->id) }}" class="btn btn-ghost btn-sm">📊 Analytics</a>
                    </div>
                    <div style="display:flex;gap:.4rem;align-items:center;">
                        <form method="POST" action="{{ route('admin.lms.publish', $course->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $course->is_published ? 'badge-amber' : 'badge-green' }}" style="border-width:1px;">
                                {{ $course->is_published ? '○ Unpublish' : '● Publish' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.lms.destroy', $course->id) }}"
                              onsubmit="return confirm('Delete {{ addslashes($course->title) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-red btn-sm">✕</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection