@extends('layouts.app')
@section('title', 'Set New Password')
@section('content')

<style>
:root {
    --navy:       #003366;
    --navy-mid:   #004080;
    --navy-faint: #e8eef5;
    --red:        #C8102E;
    --green:      #1a6b3c;
    --green-bg:   #eef7f2;
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

/* Password requirements */
.pwd-reqs {
    margin-top: 2rem;
    padding: .85rem 1rem;
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.08);
    border-left: 3px solid rgba(255,255,255,.12);
}
.pwd-reqs-title { font-size: .7rem; font-weight: bold; text-transform: uppercase; letter-spacing: .12em; color: rgba(255,255,255,.35); margin-bottom: .65rem; }
.pwd-req { display: flex; align-items: center; gap: .55rem; font-size: .75rem; color: rgba(255,255,255,.35); margin-bottom: .35rem; }
.pwd-req:last-child { margin-bottom: 0; }
.pwd-req-dot { width: 6px; height: 6px; border-radius: 50%; background: rgba(255,255,255,.2); flex-shrink: 0; transition: background .2s; }
.pwd-req.met { color: rgba(255,255,255,.6); }
.pwd-req.met .pwd-req-dot { background: #4ade80; }

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

/* Field */
.field { margin-bottom: 1rem; display: flex; flex-direction: column; gap: .3rem; }
.field label { font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: .1em; color: var(--muted); }
.field label small { text-transform: none; letter-spacing: 0; color: var(--grey-dark); font-weight: normal; font-size: 10px; }
.input-wrap { position: relative; }
.input-icon { position: absolute; left: .75rem; top: 50%; transform: translateY(-50%); font-size: .85rem; color: var(--muted); pointer-events: none; }
.field input[type="email"],
.field input[type="password"],
.field input[type="text"] {
    width: 100%; padding: .52rem .75rem .52rem 2.1rem;
    border: 1px solid var(--grey-mid); background: var(--white); color: var(--text);
    font-family: var(--font); font-size: 13px; outline: none;
    transition: border-color .15s, box-shadow .15s;
}
.field input:focus { border-color: var(--navy); box-shadow: 0 0 0 3px rgba(0,51,102,.08); }
.field input.input-error { border-color: var(--red); }
.field-error { font-size: 11px; color: var(--red); font-weight: bold; }

/* Password reveal */
.pwd-wrap input { padding-right: 3rem; }
.pwd-reveal {
    position: absolute; right: .75rem; top: 50%; transform: translateY(-50%);
    background: none; border: none; font-family: var(--font);
    font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: .08em;
    color: var(--muted); cursor: pointer; padding: 0; transition: color .12s;
}
.pwd-reveal:hover { color: var(--navy); }

/* Strength bar */
.pwd-strength-bar   { height: 3px; background: var(--grey-mid); margin-top: .4rem; width: 0; transition: all .2s; }
.pwd-strength-label { font-size: 11px; font-weight: bold; margin-top: .15rem; min-height: 1rem; color: var(--muted); }

/* Match hint */
.match-hint { font-size: 11px; font-weight: bold; margin-top: .2rem; min-height: 1rem; }

/* Error alert */
.error-alert { display: flex; align-items: center; gap: .6rem; padding: .65rem 1rem; margin-bottom: 1rem; background: #fdf0f2; border: 1px solid rgba(200,16,46,.25); border-left: 3px solid var(--red); color: var(--red); font-size: 12px; font-weight: bold; }

/* Actions */
.form-actions { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; padding-top: .25rem; }
.back-link { font-size: 11px; font-weight: bold; color: var(--navy); text-decoration: none; text-transform: uppercase; letter-spacing: .05em; }
.back-link:hover { text-decoration: underline; }
.btn-submit {
    padding: .52rem 1.3rem; background: var(--navy); color: var(--white);
    border: 1px solid var(--navy); font-family: var(--font);
    font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: .05em;
    cursor: pointer; transition: background .12s, box-shadow .12s;
    display: inline-flex; align-items: center; gap: .4rem;
}
.btn-submit:hover { background: var(--navy-mid); box-shadow: 0 4px 12px rgba(0,51,102,.18); }
.btn-submit:disabled { opacity: .45; cursor: not-allowed; }

/* Right footer */
.right-footer {
    padding: 1rem 2.5rem; border-top: 1px solid var(--grey-mid); background: var(--grey);
    display: flex; align-items: center; justify-content: space-between;
    gap: 1rem; flex-wrap: wrap; font-size: 11px; color: var(--muted); font-weight: bold;
}
@media (min-width: 820px) { .right-footer { padding: 1rem 3.5rem; } }
.right-footer a { color: var(--navy); text-decoration: none; }
.right-footer a:hover { text-decoration: underline; }

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
                <div class="left-eyebrow">Account recovery</div>
                <div class="left-title">Set your new <span>password</span></div>
                <div class="left-desc">
                    Choose a strong password to keep your account secure. You'll use it to sign in to the members' portal.
                </div>

                <div class="left-steps" style="margin-top:2.5rem;">
                    <div class="left-step done">
                        <div class="step-num">✓</div>
                        <div class="step-body">
                            <div class="step-title">Email submitted</div>
                            <div class="step-desc">Your reset link was sent to your inbox.</div>
                        </div>
                    </div>
                    <div class="left-step done">
                        <div class="step-num">✓</div>
                        <div class="step-body">
                            <div class="step-title">Link verified</div>
                            <div class="step-desc">You clicked your secure reset link.</div>
                        </div>
                    </div>
                    <div class="left-step active">
                        <div class="step-num">3</div>
                        <div class="step-body">
                            <div class="step-title">Set new password</div>
                            <div class="step-desc">Choose a strong password and confirm it below.</div>
                        </div>
                    </div>
                </div>

                <div class="pwd-reqs" id="pwdReqs">
                    <div class="pwd-reqs-title">Password requirements</div>
                    <div class="pwd-req" id="req-length"><div class="pwd-req-dot"></div>At least 8 characters</div>
                    <div class="pwd-req" id="req-upper"><div class="pwd-req-dot"></div>One uppercase letter</div>
                    <div class="pwd-req" id="req-number"><div class="pwd-req-dot"></div>One number</div>
                    <div class="pwd-req" id="req-special"><div class="pwd-req-dot"></div>One special character</div>
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
                    <div class="right-eyebrow">Password reset</div>
                    <div class="right-title">Set your new password</div>
                    <div class="right-sub">Choose a strong password for your account. Once saved, you can sign in with your new credentials.</div>
                </div>
                <div class="secure-badge">🔒 Secure</div>
            </div>
        </div>

        <div class="right-body">
            <div class="form-wrap">

                @if ($errors->any())
                    <div class="error-alert">✕ {{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('password.store') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                   {{-- Email or callsign --}}
<div class="field">
    <label for="email_or_callsign">Email or callsign <small>(used when your reset link was requested)</small></label>
    <div class="input-wrap">
        <span class="input-icon">✉</span>
        <input id="email_or_callsign" name="email_or_callsign" type="text"
               value="{{ old('email_or_callsign', $request->email) }}"
               required autofocus autocomplete="off"
               placeholder="e.g. M0XYZ or you@example.com"
               oninput="this.value = /^[a-zA-Z0-9]{1,4}[0-9][a-zA-Z]{1,4}$/.test(this.value.trim()) ? this.value.toUpperCase() : this.value"
               class="{{ $errors->has('email') ? 'input-error' : '' }}">
    </div>
    {{-- Hidden field that actually gets submitted --}}
    <input type="hidden" name="email" id="resolvedEmail" value="{{ old('email', $request->email) }}">
    @error('email')<div class="field-error">{{ $message }}</div>@enderror
    <div id="callsignResolveStatus" style="font-size:11px;font-weight:bold;margin-top:.25rem;min-height:1rem;"></div>
</div>

                    {{-- New password --}}
                    <div class="field">
                        <label for="password">New password</label>
                        <div class="input-wrap pwd-wrap">
                            <span class="input-icon">🔒</span>
                            <input id="password" name="password" type="password"
                                   required autocomplete="new-password"
                                   placeholder="Minimum 8 characters"
                                   oninput="checkStrength(this.value)"
                                   class="{{ $errors->has('password') ? 'input-error' : '' }}">
                            <button type="button" class="pwd-reveal"
                                    onclick="togglePwd('password', this)">show</button>
                        </div>
                        <div class="pwd-strength-bar" id="strengthBar"></div>
                        <div class="pwd-strength-label" id="strengthLabel"></div>
                        @error('password')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    {{-- Confirm password --}}
                    <div class="field">
                        <label for="password_confirmation">Confirm new password</label>
                        <div class="input-wrap pwd-wrap">
                            <span class="input-icon">🔒</span>
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                   required autocomplete="new-password"
                                   placeholder="Repeat your new password"
                                   oninput="checkMatch()"
                                   class="{{ $errors->has('password_confirmation') ? 'input-error' : '' }}">
                            <button type="button" class="pwd-reveal"
                                    onclick="togglePwd('password_confirmation', this)">show</button>
                        </div>
                        <div class="match-hint" id="matchHint"></div>
                        @error('password_confirmation')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('login') }}" class="back-link">← Back to sign in</a>
                        <button type="submit" class="btn-submit" id="btnSubmit">
                            Set new password →
                        </button>
                    </div>

                </form>

            </div>
        </div>

        <div class="right-footer">
            <span>🔐 Members only · All activity is logged</span>
            <a href="{{ route('login') }}">Back to sign in →</a>
        </div>

    </div>

</div>

<script>
function togglePwd(inputId, btn) {
    const inp = document.getElementById(inputId) || (typeof inputId === 'string' ? document.getElementById(inputId) : inputId);
    const el  = typeof btn === 'string' ? document.getElementById(btn) : btn;
    if (!inp || !el) return;
    inp.type       = inp.type === 'password' ? 'text' : 'password';
    el.textContent = inp.type === 'password' ? 'show' : 'hide';
}

function checkStrength(val) {
    const bar   = document.getElementById('strengthBar');
    const label = document.getElementById('strengthLabel');

    // Update requirement pills
    document.getElementById('req-length') .classList.toggle('met', val.length >= 8);
    document.getElementById('req-upper')  .classList.toggle('met', /[A-Z]/.test(val));
    document.getElementById('req-number') .classList.toggle('met', /[0-9]/.test(val));
    document.getElementById('req-special').classList.toggle('met', /[^A-Za-z0-9]/.test(val));

    if (!val) { bar.style.width = '0'; label.textContent = ''; checkMatch(); return; }

    let s = 0;
    if (val.length >= 8)           s++;
    if (val.length >= 12)          s++;
    if (/[A-Z]/.test(val))         s++;
    if (/[0-9]/.test(val))         s++;
    if (/[^A-Za-z0-9]/.test(val))  s++;

    const lvls = [
        { w:'20%', c:'#C8102E', t:'Very weak' },
        { w:'40%', c:'#f97316', t:'Weak' },
        { w:'60%', c:'#f59e0b', t:'Fair' },
        { w:'80%', c:'#84cc16', t:'Good' },
        { w:'100%',c:'#1a6b3c', t:'Strong' },
    ];
    const l = lvls[Math.min(s - 1, 4)] || lvls[0];
    bar.style.width      = l.w;
    bar.style.background = l.c;
    label.textContent    = l.t;
    label.style.color    = l.c;
    checkMatch();
}

function checkMatch() {
    const p = document.getElementById('password').value;
    const c = document.getElementById('password_confirmation').value;
    const h = document.getElementById('matchHint');
    if (!c) { h.textContent = ''; return; }
    if (p === c) {
        h.textContent = '✓ Passwords match';
        h.style.color = 'var(--green)';
    } else {
        h.textContent = '✗ Passwords do not match';
        h.style.color = 'var(--red)';
    }
}
</script>

@endsection