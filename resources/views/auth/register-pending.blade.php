@extends('layouts.app')
@section('title', 'Account Pending Approval')
@section('content')

<style>
/* Break out of layouts.app content wrapper */
body { padding: 0 !important; }
main, .content-wrap, [class*="container"], [class*="page-content"] {
    max-width: none !important;
    padding: 0 !important;
    margin: 0 !important;
    width: 100% !important;
}

:root {
    --navy:     #003366;
    --navy-mid: #004080;
    --navy-faint: #e8eef5;
    --red:      #C8102E;
    --green:    #1a6b3c;
    --green-bg: #eef7f2;
    --teal:     #0288d1;
    --teal-bg:  #e1f5fe;
    --amber:    #92400e;
    --amber-bg: #fffbeb;
    --amber-brd:#fcd34d;
    --grey:     #f2f5f9;
    --grey-mid: #dde2e8;
    --white:    #fff;
    --text:     #001f40;
    --muted:    #6b7f96;
    --font:     Arial,"Helvetica Neue",Helvetica,sans-serif;
    --shadow-lg:0 16px 48px rgba(0,51,102,.18);
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

/* ── FULL SCREEN SPLIT ── */
.page {
    display: flex;
    min-height: calc(100vh - 60px); /* account for navbar height */
    flex-direction: column;
    width: 100%;
}
@media (min-width: 820px) { .page { flex-direction: row; } }

/* ── LEFT PANEL ── */
.left {
    background: var(--navy);
    padding: 2.5rem 2rem;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
    flex-shrink: 0;
}
.left::before {
    content: ''; position: absolute; inset: 0;
    background: repeating-linear-gradient(-45deg,transparent,transparent 20px,rgba(255,255,255,.02) 20px,rgba(255,255,255,.02) 21px);
    pointer-events: none;
}
.left::after {
    content: ''; position: absolute;
    bottom: -20%; right: -15%;
    width: 60%; padding-top: 60%;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(200,16,46,.15) 0%, transparent 65%);
    pointer-events: none;
}
@media (min-width: 820px) {
    .left {
        width: 42%;
        min-height: calc(100vh - 60px);
        padding: 3rem 3rem 3rem 2.5rem;
        position: sticky;
        top: 60px;
        height: calc(100vh - 60px);
    }
}
.left-inner {
    position: relative; z-index: 1;
    display: flex; flex-direction: column; height: 100%;
}

/* Brand row — RAYNET logo style from original */
.brand { display: flex; align-items: center; gap: .85rem; }
.rn-logo {
    background: var(--red);
    width: 48px; height: 48px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.rn-logo span {
    font-size: 11px; font-weight: bold; color: var(--white);
    letter-spacing: .06em; text-align: center; line-height: 1.15; text-transform: uppercase;
}
.brand-name { font-size: 15px; font-weight: bold; color: var(--white); letter-spacing: .04em; text-transform: uppercase; }
.brand-sub  { font-size: 11px; color: rgba(255,255,255,.45); margin-top: 2px; text-transform: uppercase; letter-spacing: .06em; }

.left-hero { margin-top: 3rem; flex: 1; }
.left-eyebrow {
    font-size: .7rem; font-weight: bold;
    text-transform: uppercase; letter-spacing: .14em;
    color: rgba(255,255,255,.4); margin-bottom: .6rem;
}
.left-title {
    font-size: clamp(1.7rem, 4vw, 2.4rem);
    font-weight: bold; color: #fff;
    line-height: 1.15; margin-bottom: .75rem;
}
.left-title span { color: #90caf9; }
.left-desc {
    font-size: .88rem; color: rgba(255,255,255,.55);
    line-height: 1.65; max-width: 340px;
}
.left-chips { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: 1.5rem; }
.chip {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .28rem .7rem; border-radius: 999px;
    border: 1px solid rgba(255,255,255,.15);
    font-size: .72rem; color: rgba(255,255,255,.55);
    background: rgba(255,255,255,.06);
}
.left-footer {
    margin-top: auto; padding-top: 2rem;
    font-size: .72rem; color: rgba(255,255,255,.22); line-height: 1.6;
}

/* ── RIGHT PANEL ── */
.right {
    background: var(--grey);
    padding: 2rem 1.5rem 3rem;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
@media (min-width: 820px) { .right { padding: 3rem 4rem; } }

/* Inner card — original RAYNET card style */
.inner-card {
    background: var(--white);
    border-top: 5px solid var(--red);
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    max-width: 520px;
    width: 100%;
}
@media (min-width: 820px) { .inner-card { margin: 0; } }

/* Card hero area */
.inner-body { padding: 1.75rem 1.75rem 1.25rem; text-align: center; }
.status-icon {
    width: 64px; height: 64px; background: var(--amber-bg);
    border: 2px solid var(--amber-brd); border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 28px; margin: 0 auto 1.25rem;
}
.card-title { font-size: 20px; font-weight: bold; color: var(--navy); margin-bottom: .4rem; }
.card-desc  { font-size: 13px; color: var(--muted); line-height: 1.65; max-width: 380px; margin: 0 auto; }

/* Email callout */
.email-callout {
    display: flex; align-items: flex-start; gap: .75rem;
    margin: 1.3rem 0 0; padding: .9rem 1rem;
    background: var(--teal-bg);
    border: 1px solid rgba(2,136,209,.25);
    border-left: 3px solid var(--teal);
    text-align: left;
}
.ec-icon  { font-size: 1.2rem; flex-shrink: 0; margin-top: .05rem; }
.ec-title { font-size: 11px; font-weight: bold; color: var(--teal); margin-bottom: .25rem; text-transform: uppercase; letter-spacing: .08em; }
.ec-body  { font-size: 12px; color: #1e4d6b; line-height: 1.6; }
.ec-pill  {
    display: inline-block; margin-top: .4rem;
    font-size: 11px; font-weight: bold; padding: .15rem .55rem;
    background: var(--amber-bg); border: 1px solid var(--amber-brd); color: var(--amber);
}

/* Stepper */
.steps { padding: 1.4rem 1.75rem 1.5rem; }
.steps-label {
    font-size: 10px; font-weight: bold; text-transform: uppercase;
    letter-spacing: .12em; color: var(--muted); margin-bottom: 1rem;
}
.step { display: flex; gap: .9rem; position: relative; }
.step:not(:last-child) .step-line::after {
    content: ''; position: absolute;
    left: 15px; top: 32px;
    width: 2px; height: calc(100% - 12px);
    background: var(--grey-mid);
}
.step.done:not(:last-child) .step-line::after { background: var(--green); }
.step-line { position: relative; flex-shrink: 0; width: 32px; }
.step-dot {
    width: 32px; height: 32px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem; font-weight: bold;
    position: relative; z-index: 1;
    border: 2px solid var(--grey-mid);
    background: var(--white); color: var(--muted);
}
.step.done .step-dot   { background: var(--green); border-color: var(--green); color: var(--white); }
.step.active .step-dot { background: var(--navy); border-color: var(--navy); color: var(--white); box-shadow: 0 0 0 3px rgba(0,51,102,.12); }
.step-body { padding: .3rem 0 1.3rem; flex: 1; }
.step:last-child .step-body { padding-bottom: 0; }
.step-tag {
    display: inline-block; font-size: 10px; font-weight: bold;
    text-transform: uppercase; letter-spacing: .1em;
    padding: .15rem .55rem; margin-bottom: .3rem;
}
.step.done .step-tag    { background: var(--green-bg); color: var(--green); }
.step.active .step-tag  { background: var(--navy-faint); color: var(--navy); }
.step.pending .step-tag { background: var(--grey-mid); color: var(--muted); }
.step-title { font-size: 13px; font-weight: bold; color: var(--text); margin-bottom: .2rem; }
.step.pending .step-title { color: var(--muted); }
.step-desc  { font-size: 12px; color: var(--muted); line-height: 1.6; }
.step.active .step-desc { color: #334155; }

/* Card footer */
.card-footer {
    padding: .85rem 1.75rem; border-top: 1px solid var(--grey-mid);
    background: var(--grey); text-align: center;
    font-size: 11px; color: var(--muted); line-height: 1.6; font-weight: bold;
}

.sign-in-link {
    margin-top: 1.25rem;
    font-size: 12px; color: var(--muted); text-align: left;
}
.sign-in-link a { color: var(--navy); font-weight: bold; text-decoration: none; }
.sign-in-link a:hover { text-decoration: underline; }
</style>

<div class="page">

    {{-- LEFT --}}
    <div class="left">
        <div class="left-inner">
            <div class="brand">
                <div class="rn-logo"><span>RAY<br>NET</span></div>
                <div>
                    <div class="brand-name">Liverpool RAYNET</div>
                    <div class="brand-sub">Members' Portal</div>
                </div>
            </div>

            <div class="left-hero">
                <div class="left-eyebrow" style="margin-top:3rem;">Registration status</div>
                <div class="left-title">Almost <span>there</span></div>
                <div class="left-desc">
                    Your account is in our system. There are two quick steps before you can access the portal — verify your email and wait for Group Controller approval.
                </div>
                <div class="left-chips">
                    <span class="chip">📻 Liverpool RAYNET</span>
                    <span class="chip">Zone 10 · Merseyside</span>
                    <span class="chip">Group 179</span>
                </div>
            </div>

            <div class="left-footer">
                Radio Amateurs' Emergency Network<br>
                Access is for registered members only.
            </div>
        </div>
    </div>

    {{-- RIGHT --}}
    <div class="right">

        <div class="inner-card">

            <div class="inner-body">
                <div class="status-icon">⏳</div>
                <div class="card-title">Account awaiting approval</div>
                <div class="card-desc">
                    Your account has been created and is now pending review by a Group Controller.
                    While you wait, please verify your email address using the link we've sent you.
                </div>
                <div class="email-callout">
                    <div class="ec-icon">✉️</div>
                    <div>
                        <div class="ec-title">Action required — check your inbox</div>
                        <div class="ec-body">
                            We've sent a verification link to your registered email. Click it now to confirm your address — you don't need to wait for approval first. Check your spam folder if it doesn't arrive within a few minutes.
                        </div>
                        <div class="ec-pill">⏳ Link expires in 60 minutes</div>
                    </div>
                </div>
            </div>

            <div class="steps">
                <div class="steps-label">What happens next</div>

                <div class="step done">
                    <div class="step-line"><div class="step-dot">✓</div></div>
                    <div class="step-body">
                        <div class="step-tag">Complete</div>
                        <div class="step-title">Account created</div>
                        <div class="step-desc">Your name, callsign and email have been registered on the portal.</div>
                    </div>
                </div>

                <div class="step active">
                    <div class="step-line"><div class="step-dot">2</div></div>
                    <div class="step-body">
                        <div class="step-tag">Action needed</div>
                        <div class="step-title">Verify your email address</div>
                        <div class="step-desc">Click the link in the email we sent you. You can do this right now while you wait for approval.</div>
                    </div>
                </div>

                <div class="step pending">
                    <div class="step-line"><div class="step-dot">3</div></div>
                    <div class="step-body">
                        <div class="step-tag">Pending</div>
                        <div class="step-title">Group Controller approval</div>
                        <div class="step-desc">A Group Controller will review your registration and verify your callsign against Ofcom records.</div>
                    </div>
                </div>

                <div class="step pending">
                    <div class="step-line"><div class="step-dot">4</div></div>
                    <div class="step-body">
                        <div class="step-tag">Pending</div>
                        <div class="step-title">Access granted</div>
                        <div class="step-desc">Once approved, your account is activated and you can sign in to the members' hub.</div>
                    </div>
                </div>

            </div>

            <div class="card-footer">
                🔒 Account access is controlled by the <strong style="color:var(--navy);">Group Controller</strong>.<br>
                Do not attempt to create a second account.
            </div>

        </div>

        <div class="sign-in-link">
            Already approved? <a href="{{ route('login') }}">Sign in →</a>
        </div>

    </div>

</div>

@endsection