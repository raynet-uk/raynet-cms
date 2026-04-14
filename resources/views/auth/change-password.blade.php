@extends('layouts.app')
@section('title', 'Change Password')
@section('content')
<style>
:root {
    --navy:       #003366;
    --navy-mid:   #004080;
    --navy-faint: #e8eef5;
    --red:        #C8102E;
    --green:      #1a6b3c;
    --green-bg:   #eef7f2;
    --grey:       #dde2e8;
    --light:      #f2f5f9;
    --white:      #fff;
    --text:       #001f40;
    --text-mid:   #2d4a6b;
    --muted:      #6b7f96;
    --teal:       #0288d1;
    --shadow-sm:  0 2px 8px rgba(0,51,102,.07);
    --shadow-md:  0 6px 20px rgba(0,51,102,.12);
    --transition: all .2s ease;
    --font:       Arial,"Helvetica Neue",Helvetica,sans-serif;
}
*, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
html { scroll-behavior:smooth; }
body { background:var(--light); color:var(--text); font-family:var(--font); font-size:15px; line-height:1.55; min-height:100vh; }
.wrap { max-width:980px; margin:0 auto; padding:0 1rem 3rem; }

/* ── TOPBAR ── */
.topbar { display:flex; align-items:center; justify-content:space-between; padding:1rem 0; border-bottom:2px solid var(--navy); margin-bottom:0; gap:1rem; flex-wrap:wrap; }
.brand { display:flex; align-items:center; gap:.8rem; }
.brand-badge { width:40px; height:40px; background:var(--navy); color:white; display:flex; align-items:center; justify-content:center; font-size:1.3rem; border-radius:8px; }
.brand-name { font-size:1.2rem; font-weight:bold; color:var(--navy); }
.brand-sub { font-size:.78rem; color:var(--muted); }
.back-btn { display:inline-flex; align-items:center; gap:.4rem; padding:.4rem .9rem; border:1px solid var(--grey); border-radius:8px; background:white; color:var(--muted); font-size:.85rem; text-decoration:none; transition:var(--transition); }
.back-btn:hover { border-color:var(--navy); color:var(--navy); }

/* ── HERO ── */
.hero {
    background:var(--navy);
    padding:1.8rem 2rem 3.8rem;
    position:relative; overflow:hidden;
}
.hero::before {
    content:''; position:absolute; inset:0;
    background:repeating-linear-gradient(-45deg,transparent,transparent 20px,rgba(255,255,255,.02) 20px,rgba(255,255,255,.02) 21px);
}
.hero::after {
    content:''; position:absolute; bottom:0; left:0; right:0; height:3px;
    background:linear-gradient(90deg,var(--red) 0%,var(--red) 35%,rgba(200,16,46,0) 100%);
}
.hero-inner { position:relative; z-index:1; }
.hero-eyebrow { font-size:.7rem; font-weight:bold; text-transform:uppercase; letter-spacing:.12em; color:rgba(255,255,255,.45); margin-bottom:.5rem; }
.hero-title { font-size:1.8rem; font-weight:bold; color:#fff; line-height:1.2; margin-bottom:.4rem; }
.hero-title span { color:#90caf9; }
.hero-sub { font-size:.88rem; color:rgba(255,255,255,.55); max-width:500px; line-height:1.6; }
.hero-chip { display:inline-flex; align-items:center; gap:.4rem; margin-top:.9rem; padding:.25rem .75rem; border-radius:999px; background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.15); font-size:.72rem; color:rgba(255,255,255,.5); }

/* ── LAYOUT ── */
.layout { display:grid; grid-template-columns:1fr 290px; gap:1.5rem; margin-top:1.5rem; }
@media(max-width:820px) { .layout { grid-template-columns:1fr; } }

/* ── ALERTS ── */
.alert-success { display:flex; align-items:center; gap:.65rem; padding:.75rem 1rem; background:var(--green-bg); border:1px solid #b8ddc9; border-left:3px solid var(--green); border-radius:0 8px 8px 0; margin-top:1.2rem; font-size:.85rem; font-weight:bold; color:var(--green); }
.reset-banner { display:flex; align-items:flex-start; gap:.6rem; padding:.7rem 1rem; background:rgba(200,16,46,.07); border:1px solid rgba(200,16,46,.28); border-left:3px solid var(--red); border-radius:0 8px 8px 0; margin-bottom:1.2rem; font-size:.82rem; color:var(--red); }
.reset-banner strong { display:block; margin-bottom:.2rem; font-size:.85rem; }

/* ── CARDS ── */
.card { background:white; border:1px solid var(--grey); border-radius:12px; overflow:hidden; box-shadow:var(--shadow-sm); margin-bottom:1.2rem; }
.card:last-child { margin-bottom:0; }
.card-head { display:flex; align-items:center; gap:.75rem; padding:.75rem 1.2rem; background:var(--light); border-bottom:1px solid var(--grey); }
.card-head-icon { width:32px; height:32px; border-radius:8px; background:var(--navy-faint); border:1px solid rgba(0,51,102,.15); display:flex; align-items:center; justify-content:center; font-size:.95rem; flex-shrink:0; }
.card-head h2 { font-size:.72rem; font-weight:bold; color:var(--navy); text-transform:uppercase; letter-spacing:.1em; }
.card-head p { font-size:.75rem; color:var(--muted); margin-top:.1rem; }
.card-body { padding:1.3rem; }

/* ── FIELDS ── */
.field { margin-bottom:1.1rem; }
.field:last-of-type { margin-bottom:0; }
.field label { display:block; font-size:.7rem; font-weight:bold; color:var(--muted); text-transform:uppercase; letter-spacing:.1em; margin-bottom:.4rem; }
.field input {
    width:100%; padding:.6rem .85rem;
    border:1.5px solid var(--grey); border-radius:8px;
    font-size:.92rem; background:var(--light); color:var(--text);
    font-family:monospace; outline:none;
    transition:border-color .15s, box-shadow .15s, background .15s;
}
.field input:focus { border-color:var(--teal); background:#fff; box-shadow:0 0 0 3px rgba(2,136,209,.1); }
.field-error { margin-top:.4rem; font-size:.75rem; color:var(--red); }
.divider { height:1px; background:var(--grey); margin:1.1rem 0; }

/* ── SUBMIT ── */
.btn-save { width:100%; margin-top:1.3rem; padding:.65rem 1.2rem; border:none; border-radius:8px; background:var(--navy); color:#fff; font-size:.92rem; font-weight:bold; font-family:var(--font); cursor:pointer; letter-spacing:.03em; transition:var(--transition); }
.btn-save:hover { background:var(--navy-mid); transform:translateY(-1px); box-shadow:0 4px 14px rgba(0,51,102,.2); }

/* ── REQUIREMENTS LIST ── */
.req-list { display:flex; flex-direction:column; gap:.55rem; }
.req-item { display:flex; align-items:flex-start; gap:.7rem; padding:.65rem .85rem; background:var(--light); border:1px solid var(--grey); border-radius:8px; }
.req-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; margin-top:.28rem; }
.req-title { font-size:.82rem; font-weight:bold; color:var(--navy); }
.req-desc { font-size:.74rem; color:var(--muted); margin-top:2px; line-height:1.5; }
.info-text { font-size:.84rem; color:var(--text-mid); line-height:1.7; margin-bottom:.8rem; }
.info-text:last-child { margin-bottom:0; }
.info-note { padding:.55rem .85rem; font-size:.75rem; color:var(--navy); background:var(--navy-faint); border:1px solid rgba(0,51,102,.18); border-left:3px solid var(--navy); border-radius:0 6px 6px 0; }
</style>

<div class="wrap">

    {{-- TOPBAR --}}
    <nav class="topbar">
        <div class="brand">
            <div class="brand-badge">📻</div>
            <div>
                <div class="brand-name">Liverpool RAYNET</div>
                <div class="brand-sub">members' portal</div>
            </div>
        </div>
        <a href="{{ route('members') }}" class="back-btn">← Back to hub</a>
    </nav>

    {{-- HERO --}}
    <div class="hero">
        <div class="hero-inner">
            <div class="hero-eyebrow">Account Security</div>
            <div class="hero-title">Change your <span>password</span></div>
            <div class="hero-sub">Update your account credentials. Choose a strong, unique password to protect your RAYNET member account.</div>
            <div class="hero-chip">🔐 encrypted · members only</div>
        </div>
    </div>

    {{-- SUCCESS ALERT --}}
    @if (session('status'))
        <div class="alert-success">✓ {{ session('status') }}</div>
    @endif

    <div class="layout">

        {{-- LEFT: FORM --}}
        <div>
            <div class="card">
                <div class="card-head">
                    <div class="card-head-icon">🔐</div>
                    <div>
                        <h2>Update password</h2>
                        <p>All fields are required</p>
                    </div>
                </div>
                <div class="card-body">

                    @if (auth()->user()->force_password_reset)
                        <div class="reset-banner">
                            <div>⚠</div>
                            <div>
                                <strong>Password reset required</strong>
                                Your password has been reset by an administrator. Please choose a new password to continue accessing the portal.
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('PUT')

                        @if (! auth()->user()->force_password_reset)
                            <div class="field">
                                <label for="current_password">Current password</label>
                                <input id="current_password" name="current_password"
                                       type="password" autocomplete="current-password"
                                       required placeholder="••••••••" />
                                @error('current_password')
                                    <div class="field-error">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="divider"></div>
                        @endif

                        <div class="field">
                            <label for="password">New password</label>
                            <input id="password" name="password"
                                   type="password" autocomplete="new-password"
                                   required placeholder="••••••••" />
                            @error('password')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field">
                            <label for="password_confirmation">Confirm new password</label>
                            <input id="password_confirmation" name="password_confirmation"
                                   type="password" autocomplete="new-password"
                                   required placeholder="••••••••" />
                            @error('password_confirmation')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn-save">Save new password</button>
                    </form>

                </div>
            </div>
        </div>

        {{-- RIGHT: SIDE PANEL --}}
        <div>
            <div class="card">
                <div class="card-head">
                    <div class="card-head-icon">🛡️</div>
                    <div>
                        <h2>Password requirements</h2>
                        <p>keep your account safe</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="req-list">
                        <div class="req-item">
                            <div class="req-dot" style="background:var(--teal);"></div>
                            <div>
                                <div class="req-title">Minimum 8 characters</div>
                                <div class="req-desc">Aim for 12 or more for a stronger password.</div>
                            </div>
                        </div>
                        <div class="req-item">
                            <div class="req-dot" style="background:var(--navy);"></div>
                            <div>
                                <div class="req-title">Mix of character types</div>
                                <div class="req-desc">Uppercase, lowercase, numbers and symbols.</div>
                            </div>
                        </div>
                        <div class="req-item">
                            <div class="req-dot" style="background:var(--green);"></div>
                            <div>
                                <div class="req-title">Unique to this site</div>
                                <div class="req-desc">Don't reuse a password from another account.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-head">
                    <div class="card-head-icon">ℹ️</div>
                    <div><h2>Why this matters</h2></div>
                </div>
                <div class="card-body">
                    <p class="info-text">Your account controls access to RAYNET member data and activation records. A strong, unique password helps protect the group's operational information.</p>
                    <div class="info-note">If you believe your account has been compromised, contact the Group Controller immediately.</div>
                </div>
            </div>
        </div>

    </div>{{-- /layout --}}

</div>{{-- /wrap --}}

@endsection