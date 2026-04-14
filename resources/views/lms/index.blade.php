@extends('layouts.app')
@section('title', 'Training Portal')
@section('content')
<style>
:root{--navy:#003366;--red:#C8102E;--teal:#0288d1;--green:#1a6b3c;--green-bg:#eef7f2;--amber:#8a5500;--amber-bg:#fdf8ec;--grey:#f2f5f9;--grey-mid:#dde2e8;--white:#fff;--text:#001f40;--text-mid:#2d4a6b;--muted:#6b7f96;--shadow-sm:0 1px 3px rgba(0,51,102,.09);--font:Arial,'Helvetica Neue',Helvetica,sans-serif;}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:var(--font);background:var(--grey);color:var(--text);}
.lms-hero{background:var(--navy);border-bottom:4px solid var(--red);padding:2rem 1.5rem;}
.lms-hero-inner{max-width:1100px;margin:0 auto;}
.lms-hero h1{font-size:22px;font-weight:bold;color:#fff;margin-bottom:.3rem;}
.lms-hero p{font-size:13px;color:rgba(255,255,255,.6);}
.wrap{max-width:1100px;margin:0 auto;padding:1.5rem 1.5rem 4rem;}
.section-head{font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:.14em;color:var(--muted);display:flex;align-items:center;gap:.5rem;margin-bottom:1rem;margin-top:1.5rem;}
.section-head::before{content:'';width:12px;height:2px;background:var(--red);display:inline-block;}
.section-head::after{content:'';flex:1;height:1px;background:var(--grey-mid);display:inline-block;}
.course-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1rem;}
.course-card{background:var(--white);border:1px solid var(--grey-mid);box-shadow:var(--shadow-sm);overflow:hidden;transition:box-shadow .15s;text-decoration:none;color:inherit;display:block;}
.course-card:hover{box-shadow:0 4px 16px rgba(0,51,102,.12);border-color:var(--teal);}
.course-card-body{padding:1rem 1.15rem;}
.course-card-cat{font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;color:var(--teal);margin-bottom:.3rem;}
.course-card-title{font-size:14px;font-weight:bold;color:var(--navy);margin-bottom:.4rem;line-height:1.3;}
.course-card-desc{font-size:12px;color:var(--text-mid);line-height:1.5;overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;}
.course-card-foot{padding:.6rem 1.15rem;border-top:1px solid var(--grey-mid);background:var(--grey);display:flex;align-items:center;justify-content:space-between;}
.course-card-meta{font-size:11px;color:var(--muted);display:flex;gap:.75rem;}
.progress-wrap{padding:.5rem 1.15rem;border-top:1px solid var(--grey-mid);}
.progress-label{display:flex;justify-content:space-between;font-size:10px;color:var(--muted);margin-bottom:.3rem;font-weight:bold;text-transform:uppercase;letter-spacing:.06em;}
.progress-track{height:5px;background:var(--grey-mid);overflow:hidden;}
.progress-fill{height:100%;background:var(--navy);transition:width .4s ease;}
.progress-fill.complete{background:var(--green);}
.badge{display:inline-flex;align-items:center;padding:2px 8px;font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.05em;}
.badge-green{background:var(--green-bg);border:1px solid #b8ddc9;color:var(--green);}
.badge-amber{background:var(--amber-bg);border:1px solid #f5d87a;color:var(--amber);}
.badge-navy{background:#e8eef5;border:1px solid rgba(0,51,102,.2);color:var(--navy);}
.empty-state{background:var(--white);border:1px solid var(--grey-mid);padding:3rem 2rem;text-align:center;}
.empty-icon{font-size:2.5rem;opacity:.2;margin-bottom:.75rem;}
.empty-text{font-size:13px;color:var(--muted);}
.alert{padding:.65rem 1rem;margin-bottom:1rem;font-size:13px;font-weight:bold;display:flex;align-items:center;gap:.6rem;}
.alert-error{background:#fdf0f2;color:var(--red);border:1px solid rgba(200,16,46,.25);border-left:3px solid var(--red);}
</style>

<div class="lms-hero">
    <div class="lms-hero-inner">
        <h1>🎓 RAYNET Training Portal</h1>
        <p>Your personal learning dashboard — complete courses, earn qualifications and unlock your operator badges.</p>
    </div>
</div>

<div class="wrap">
    @if(session('error'))<div class="alert alert-error">✕ {{ session('error') }}</div>@endif

    {{-- My courses --}}
    <div class="section-head">My Courses ({{ $myEnrollments->count() }})</div>
    @if($myEnrollments->isEmpty())
        <div class="empty-state" style="margin-bottom:1rem;">
            <div class="empty-icon">📚</div>
            <div class="empty-text">You haven't been enrolled on any courses yet.<br>Your Group Controller will assign courses to you.</div>
        </div>
    @else
        <div class="course-grid">
            @foreach($myEnrollments as $e)
            @php $c = $e->course; @endphp
            <a href="{{ route('lms.course', $c->slug) }}" class="course-card">
                <div class="course-card-body">
                    <div class="course-card-cat">{{ $c->category ?? 'Training' }}</div>
                    <div class="course-card-title">{{ $c->title }}</div>
                    <div class="course-card-desc">{{ $c->description }}</div>
                </div>
                <div class="progress-wrap">
                    <div class="progress-label">
                        <span>Progress</span>
                        <span>{{ $e->progress_pct }}%</span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-fill {{ $e->progress_pct==100 ? 'complete' : '' }}" style="width:{{ $e->progress_pct }}%;"></div>
                    </div>
                </div>
                <div class="course-card-foot">
                    <div class="course-card-meta">
                        <span>{{ ucfirst($c->difficulty) }}</span>
                        @if($c->estimated_hours)<span>~{{ $c->estimated_hours }}h</span>@endif
                    </div>
                    @if($e->completed_at)
                        <span class="badge badge-green">✓ Complete</span>
                    @elseif($e->due_date && $e->due_date->isPast())
                        <span class="badge badge-amber">⚠ Overdue</span>
                    @elseif($e->due_date)
                        <span class="badge badge-navy">Due {{ $e->due_date->format('d M') }}</span>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    @endif

    {{-- Available courses --}}
    @if($availableCourses->isNotEmpty())
    <div class="section-head">Available Courses ({{ $availableCourses->count() }})</div>
    <div class="course-grid">
        @foreach($availableCourses as $c)
        <a href="{{ route('lms.course', $c->slug) }}" class="course-card" style="opacity:.8;">
            <div class="course-card-body">
                <div class="course-card-cat">{{ $c->category ?? 'Training' }}</div>
                <div class="course-card-title">{{ $c->title }}</div>
                <div class="course-card-desc">{{ $c->description }}</div>
            </div>
            <div class="course-card-foot">
                <div class="course-card-meta">
                    <span>{{ ucfirst($c->difficulty) }}</span>
                    @if($c->estimated_hours)<span>~{{ $c->estimated_hours }}h</span>@endif
                </div>
                <span style="font-size:11px;color:var(--muted);">Browse →</span>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</div>
@endsection