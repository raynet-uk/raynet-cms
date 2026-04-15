@extends('layouts.app')
@section('title', 'Verify Your Email')
@section('content')

<style>
:root {
    --navy:       #003366;
    --navy-mid:   #004080;
    --navy-faint: #e8eef5;
    --red:        #C8102E;
    --green:      #1a6b3c;
    --green-bg:   #eef7f2;
    --amber:      #8a5500;
    --amber-bg:   #fdf8ec;
    --grey:       #f2f5f9;
    --grey-mid:   #dde2e8;
    --grey-dark:  #9aa3ae;
    --white:      #fff;
    --text:       #001f40;
    --muted:      #6b7f96;
    --font:       Arial,"Helvetica Neue",Helvetica,sans-serif;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body { height: 100%; }
body { font-family: var(--font); font-size: 14px; color: var(--text); background: var(--navy); min-height: 100vh; }

.page { display: flex; min-height: 100vh; flex-direction: column; }
@media (min-width: 820px) { .page { flex-direction: row; } }

/* ── LEFT ── */
.left {
    background: var(--navy); padding: 2.5rem 2rem;
    display: flex; flex-direction: column;
    position: relative; overflow: hidden; flex-shrink: 0;
}
.left::before {
    content: ''; position: absolute; inset: 0;
    background: repeating-linear-gradient(-45deg,transparent,transparent 20px,rgba(255,255,255,.018) 20px,rgba(255,255,255,.018) 21px);
    pointer-events: none;
}
.left::after {
    content: ''; position: absolute; bottom: -20%; right: -15%;
    width: 60%; padding-top: 60%; border-radius: 50%;
    background: radial-gradient(circle,rgba(200,16,46,.14) 0%,transparent 65%);
    pointer-events: none;
}
@media (min-width: 820px) {
    .left { width: 42%; min-height: 100vh; padding: 3rem 3rem 3rem 2.5rem; position: sticky; top: 0; height: 100vh; }
}
.left-inner { position: relative; z-index: 1; display: flex; flex-direction: column; height: 100%; }

.brand { display: flex; align-items: center; gap: .85rem; }
.rn-logo { background: var(--red); width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.rn-logo span { font-size: 11px; font-weight: bold; color: var(--white); letter-spacing: .06em; text-align: center; line-height: 1.15; text-transform: uppercase; }
.brand-name { font-size: 15px; font-weight: bold; color: var(--white); letter-spacing: .04em; text-transform: uppercase; }
.brand-sub  { font-size: 11px; color: rgba(255,255,255,.4); margin-top: 2px; text-transform: uppercase; letter-spacing: .06em; }

.left-hero { margin-top: 3.5rem; flex: 1; }
.left-eyebrow { font-size: .68rem; font-weight: bold; text-transform: uppercase; letter-spacing: .16em; color: rgba(255,255,255,.35); margin-bottom: .6rem; }
.left-title { font-size: clamp(1.8rem,4vw,2.6rem); font-weight: bold; color: #fff; line-height: 1.12; margin-bottom: .85rem; }
.left-title span { color: #90caf9; }
.left-desc { font-size: .87rem; color: rgba(255,255,255,.5); line-height: 1.7; max-width: 340px; }

.left-steps { margin-top: 2.5rem; display: flex; flex-direction: column; gap: 0; }
.left-step { display: flex; gap: .85rem; position: relative; }
.left-step:not(:last-child)::before {
    content: ''; position: absolute; left: 13px; top: 28px;
    width: 2px; height: calc(100% - 8px);
    background: rgba(255,255,255,.1);
}
.left-step:not(:last-child).done::before { background: rgba(74,222,128,.25); }
.step-num {
    width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0;
    border: 2px solid rgba(255,255,255,.15);
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: bold; color: rgba(255,255,255,.5);
    position: relative; z-index: 1; background: var(--navy);
}
.left-step.done .step-num   { border-color: #4ade80; color: #4ade80; background: rgba(74,222,128,.08); }
.left-step.active .step-num { border-color: #90caf9; color: #90caf9; background: rgba(144,202,249,.08); }
.step-body { padding: .05rem 0 1.5rem; }
.step-title { font-size: .78rem; font-weight: bold; color: rgba(255,255,255,.65); margin-bottom: .15rem; }
.left-step.done .step-title   { color: rgba(255,255,255,.5); }
.left-step.active .step-title { color: #fff; }
.step-desc { font-size: .72rem; color: rgba(255,255,255,.35); line-height: 1.55; }

.left-footer { margin-top: auto; padding-top: 2rem; font-size: .7rem; color: rgba(255,255,255,.2); line-height: 1.6; }

/* ── RIGHT ── */
.right { background: var(--white); flex: 1; display: flex; flex-direction: column; border-left: 4px solid var(--red); }

.right-head { padding: 2.5rem 2.5rem 0; border-bottom: 1px solid var(--grey-mid); background: var(--grey); }
@media (min-width: 820px) { .right-head { padding: 3rem 3.5rem 0; } }

.right-head-title { display: flex; align-items: flex-start; justify-content: space-between; padding-bottom: 1.25rem; gap: 1rem; }
.right-eyebrow { font-size: .68rem; font-weight: bold; text-transform: uppercase; letter-spacing: .16em; color: var(--red); margin-bottom: .3rem; display: flex; align-items: center; gap: .35rem; }
.right-eyebrow::before { content: ''; width: 12px; height: 2px; background: var(--red); display: inline-block; }
.right-title { font-size: 1.2rem; font-weight: bold; color: var(--navy); }
.right-sub   { font-size: .78rem; color: var(--muted); margin-top: .2rem; line-height: 1.6; max-width: 380px; }
.secure-badge { display: inline-flex; align-items: center; gap: .3rem; padding: 3px 10px; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: .06em; background: var(--white); border: 1px solid var(--grey-mid); color: var(--muted); flex-shrink: 0; }

.right-body { flex: 1; padding: 0 2.5rem; }
@media (min-width: 820px) { .right-body { padding: 0 3.5rem; } }

.form-wrap { padding: 2rem 0 1rem; max-width: 480px; }

/* Notices */
.notice { display: flex; align-items: flex-start; gap: .75rem; padding: .85rem 1rem; margin-bottom: 1.25rem; animation: slideDown .3s ease; }
.notice-amber { background: var(--amber-bg); border: 1px solid #f5d87a; border-left: 3px solid #c49a00; }
.notice-green { background: var(--green-bg); border: 1px solid #b8ddc9; border-left: 3px solid var(--green); }
.notice-icon  { font-size: 1rem; flex-shrink: 0; margin-top: .1rem; }
.notice-title { font-size: .72rem; font-weight: bold; text-transform: uppercase; letter-spacing: .1em; margin-bottom: .2rem; }
.notice-amber .notice-title { color: var(--amber); }
.notice-green .notice-title { color: var(--green); }
.notice-body  { font-size: .78rem; color: var(--muted); line-height: 1.6; }
@keyframes slideDown { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: none; } }

/* Info strip */
.info-strip {
    display: flex; align-items: flex-start; gap: .65rem;
    padding: .75rem 1rem; margin-bottom: 1.5rem;
    background: var(--navy-faint); border-left: 3px solid var(--navy);
    font-size: .78rem; color: var(--muted); line-height: 1.6;
}
.info-strip strong { color: var(--navy); }

/* Email display chip */
.email-chip {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .6rem 1rem; margin-bottom: 1.5rem;
    background: var(--grey); border: 1px solid var(--grey-mid);
    border-left: 3px solid var(--navy);
    font-size: 13px; font-weight: bold; color: var(--navy);
    width: 100%;
}
.email-chip-label { font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: .1em; color: var(--muted); display: block; margin-bottom: 2px; }

/* Actions */
.form-actions { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
.btn-submit {
    padding: .52rem 1.3rem; background: var(--navy); color: var(--white);
    border: 1px solid var(--navy); font-family: var(--font);
    font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: .05em;
    cursor: pointer; transition: background .12s, box-shadow .12s;
    display: inline-flex; align-items: center; gap: .4rem;
}
.btn-submit:hover { background: var(--navy-mid); box-shadow: 0 4px 12px rgba(0,51,102,.18); }

.logout-link {
    font-size: 11px; font-weight: bold; color: var(--muted);
    text-decoration: none; text-transform: uppercase; letter-spacing: .05em;
    background: none; border: none; cursor: pointer; font-family: var(--font);
    transition: color .12s;
}
.logout-link:hover { color: var(--red); }

/* Right footer */
.right-footer {
    padding: 1rem 2.5rem; border-top: 1px solid var(--grey-mid); background: var(--grey);
    display: flex; align-items: center; justify-content: space-between;
    gap: 1rem; flex-wrap: wrap; font-size: 11px; color: var(--muted); font-weight: bold;
}
@media (min-width: 820px) { .right-footer { padding: 1rem 3.5rem; } }

@media (max-width: 480px) {
    .right-head { padding: 2rem 1.5rem 0; }
    .right-body { padding: 0 1.5rem; }
    .right-footer { padding: 1rem 1.5rem; }
}
</style>

<div class="page">

    {{-- ── LEFT ── --}}
    <div class="left">
        <div class="left-inner">

            <div class="brand">
                <div class="rn-logo"><span>RAY<br>NET</span></div>
                <div>
                    <div class="brand-name">{{ \App\Helpers\RaynetSetting::groupName() }}</div>
                    <div class="brand-sub">Members' Portal</div>
                </div>
            </div>

            <div class="left-hero">
                <div class="left-eyebrow">Account setup</div>
                <div class="left-title">Verify your <span>email</span></div>
                <div class="left-desc">
                    One quick step before you can access the portal — confirm your email address by clicking the link we sent you.
                </div>

                <div class="left-steps" style="margin-top:2.5rem;">
                    <div class="left-step done">
                        <div class="step-num">✓</div>
                        <div class="step-body">
                            <div class="step-title">Account created</div>
                            <div class="step-desc">Your details have been registered successfully.</div>
                        </div>
                    </div>
                    <div class="left-step active">
                        <div class="step-num">2</div>
                        <div class="step-body">
                            <div class="step-title">Verify your email</div>
                            <div class="step-desc">Click the link in the email we sent you to confirm your address.</div>
                        </div>
                    </div>
                    <div class="left-step">
                        <div class="step-num">3</div>
                        <div class="step-body">
                            <div class="step-title">Access granted</div>
                            <div class="step-desc">Once verified, you can sign in and access the members' hub.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="left-footer">
                Radio Amateurs' Emergency Network<br>
                Access is restricted to registered members only.
            </div>

        </div>
    </div>

    {{-- ── RIGHT ── --}}
    <div class="right">

        <div class="right-head">
            <div class="right-head-title">
                <div>
                    <div class="right-eyebrow">Email verification</div>
                    <div class="right-title">Check your inbox</div>
                    <div class="right-sub">We've sent a verification link to your registered email address. Click it to confirm your account.</div>
                </div>
                <div class="secure-badge">✉️ Pending</div>
            </div>
        </div>

        <div class="right-body">
            <div class="form-wrap">

                @if (session('status') == 'verification-link-sent')
                    <div class="notice notice-green">
                        <div class="notice-icon">✓</div>
                        <div>
                            <div class="notice-title">New link sent</div>
                            <div class="notice-body">A fresh verification link has been sent to your registered email address. Check your inbox and spam folder.</div>
                        </div>
                    </div>
                @endif

                <div class="notice notice-amber">
                    <div class="notice-icon">✉️</div>
                    <div>
                        <div class="notice-title">Action required</div>
                        <div class="notice-body">
                            Before you can access the portal, please verify your email address by clicking the link in the email we sent you.
                            If you can't find it, check your spam or junk folder — or request a new link below.
                        </div>
                    </div>
                </div>

                <div class="email-chip">
                    <div>
                        <div class="email-chip-label">Verification sent to</div>
                        {{ auth()->user()->email }}
                    </div>
                </div>

                <div class="form-actions">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn-submit">↺ Resend verification email</button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-link">← Sign out</button>
                    </form>
                </div>

            </div>
        </div>

        <div class="right-footer">
            <span>🔐 Members only · All activity is logged</span>
            <span>Link expires after 60 minutes — request a new one if needed</span>
        </div>

    </div>

</div>

@endsection