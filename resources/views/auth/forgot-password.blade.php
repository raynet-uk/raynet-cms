@extends('layouts.app')
@section('title', 'Reset Password')
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
.step-num {
    width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0;
    border: 2px solid rgba(255,255,255,.15);
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: bold; color: rgba(255,255,255,.5);
    position: relative; z-index: 1; background: var(--navy);
}
.left-step.active .step-num { border-color: #90caf9; color: #90caf9; background: rgba(144,202,249,.08); }
.step-body { padding: .05rem 0 1.5rem; }
.step-title { font-size: .78rem; font-weight: bold; color: rgba(255,255,255,.65); margin-bottom: .15rem; }
.left-step.active .step-title { color: #fff; }
.step-desc  { font-size: .72rem; color: rgba(255,255,255,.35); line-height: 1.55; }

.left-footer { margin-top: auto; padding-top: 2rem; font-size: .7rem; color: rgba(255,255,255,.2); line-height: 1.6; }

/* ── RIGHT — IS the white surface ── */
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

/* Status / success notice */
.notice { display: flex; align-items: flex-start; gap: .75rem; padding: .85rem 1rem; margin-bottom: 1.25rem; animation: slideDown .3s ease; }
.notice-green { background: var(--green-bg); border: 1px solid #b8ddc9; border-left: 3px solid var(--green); }
.notice-icon  { font-size: 1rem; flex-shrink: 0; margin-top: .1rem; }
.notice-title { font-size: .72rem; font-weight: bold; text-transform: uppercase; letter-spacing: .1em; color: var(--green); margin-bottom: .2rem; }
.notice-body  { font-size: .78rem; color: var(--muted); line-height: 1.6; }
@keyframes slideDown { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: none; } }

/* Info strip */
.info-strip {
    display: flex; align-items: flex-start; gap: .65rem;
    padding: .75rem 1rem; margin-bottom: 1.25rem;
    background: var(--navy-faint);
    border-left: 3px solid var(--navy);
    font-size: .78rem; color: var(--muted); line-height: 1.6;
}
.info-strip strong { color: var(--navy); }

/* Field */
.field { margin-bottom: 1rem; display: flex; flex-direction: column; gap: .3rem; }
.field label { font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: .1em; color: var(--muted); }
.input-wrap { position: relative; }
.input-icon { position: absolute; left: .75rem; top: 50%; transform: translateY(-50%); font-size: .85rem; color: var(--muted); pointer-events: none; }
.field input[type="email"],
.field input[type="text"] {
    width: 100%; padding: .52rem .75rem .52rem 2.1rem;
    border: 1px solid var(--grey-mid); background: var(--white); color: var(--text);
    font-family: var(--font); font-size: 13px; outline: none;
    transition: border-color .15s, box-shadow .15s;
}
.field input:focus { border-color: var(--navy); box-shadow: 0 0 0 3px rgba(0,51,102,.08); }
.field-error { font-size: 11px; color: var(--red); font-weight: bold; margin-top: 2px; }

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
                    <div class="brand-name">Liverpool RAYNET</div>
                    <div class="brand-sub">Members' Portal</div>
                </div>
            </div>

            <div class="left-hero">
                <div class="left-eyebrow">Account recovery</div>
                <div class="left-title">Reset your <span>password</span></div>
                <div class="left-desc">
    Enter your registered <strong style="color:rgba(255,255,255,.7);">email address or callsign</strong> and we'll send you a secure link to choose a new password.
</div>

                <div class="left-steps" style="margin-top:2.5rem;">
                    <div class="left-step active">
                        <div class="step-num">1</div>
                        <div class="step-body">
                            <div class="step-title">Enter your email or callsign</div>
<div class="step-desc">We'll look up your account and send a reset link to your registered email.</div>
                        </div>
                    </div>
                    <div class="left-step">
                        <div class="step-num">2</div>
                        <div class="step-body">
                            <div class="step-title">Check your inbox</div>
                            <div class="step-desc">We'll send a secure reset link — check your spam folder too.</div>
                        </div>
                    </div>
                    <div class="left-step">
                        <div class="step-num">3</div>
                        <div class="step-body">
                            <div class="step-title">Choose a new password</div>
                            <div class="step-desc">Click the link and set a new secure password for your account.</div>
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
                    <div class="right-eyebrow">Password reset</div>
                    <div class="right-title">Forgot your password?</div>
                    <div class="right-sub">Enter your registered email address and we'll send you a link to reset your password.</div>
                </div>
                <div class="secure-badge">🔒 Secure</div>
            </div>
        </div>

        <div class="right-body">
            <div class="form-wrap">

                @if (session('status'))
                    <div class="notice notice-green">
                        <div class="notice-icon">✓</div>
                        <div>
                            <div class="notice-title">Reset link sent</div>
                            <div class="notice-body">{{ session('status') }}</div>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="error-alert">✕ {{ $errors->first() }}</div>
                @endif

                <div class="info-strip">
    <span>ℹ</span>
    <div>Enter your <strong>email address or callsign</strong>. If you enter a callsign we'll look up your registered email automatically.</div>
</div>

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                   <div class="field">
    <label for="login_input">Email or callsign</label>
    <div class="input-wrap">
        <span class="input-icon">✉</span>
        <input id="login_input" name="login_input" type="text"
               value="{{ old('login_input', old('email')) }}"
               required autofocus autocomplete="username"
               placeholder="e.g. M0XYZ or you@example.com">
    </div>
    <input type="hidden" name="email" id="resolvedEmail" value="{{ old('email') }}">
    @error('email')<div class="field-error">{{ $message }}</div>@enderror
    <div id="resolveStatus" style="font-size:11px;font-weight:bold;margin-top:.25rem;min-height:1rem;"></div>
</div>

                    <div class="form-actions">
                        <a href="{{ route('login') }}" class="back-link">← Back to sign in</a>
                        <button type="submit" class="btn-submit">Send reset link →</button>
                    </div>

                </form>

            </div>
        </div>

        <div class="right-footer">
            <span>🔐 Members only · All activity is logged</span>
            <a href="{{ route('register') }}">Not a member? Request access →</a>
        </div>

    </div>

</div>
<script>
const isCallsign = val => val.trim() !== '' && !val.includes('@');

(function () {
    const input = document.getElementById('login_input');
    if (input.value && !isCallsign(input.value)) {
        document.getElementById('resolvedEmail').value = input.value;
    }
})();

document.getElementById('login_input').addEventListener('blur', async function () {
    const val    = this.value.trim();
    const status = document.getElementById('resolveStatus');
    const hidden = document.getElementById('resolvedEmail');

    if (!val) return;

    if (!isCallsign(val)) {
        hidden.value       = val;
        status.textContent = '';
        return;
    }

    status.textContent = 'Looking up callsign…';
    status.style.color = 'var(--muted)';

    try {
        const token = document.querySelector('input[name="_token"]').value;

        const res = await fetch('{{ route("password.resolve-callsign") }}', {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({ callsign: val }),
        });

        // Read as text first so non-JSON responses don't throw
        const text = await res.text();
        console.log('[callsign lookup] status:', res.status);
        console.log('[callsign lookup] body:', text);

        let data;
        try {
            data = JSON.parse(text);
        } catch {
            status.textContent = '✕ Server error (status ' + res.status + ') — check console for details';
            status.style.color = 'var(--red)';
            return;
        }

        if (res.ok && data.email) {
            hidden.value       = data.email;
            status.textContent = '✓ Callsign found — reset link will be sent to your registered email';
            status.style.color = 'var(--green)';
        } else {
            hidden.value       = '';
            status.textContent = '✕ Callsign not found — try your email address instead';
            status.style.color = 'var(--red)';
        }

    } catch (err) {
        console.error('[callsign lookup] fetch threw:', err);
        hidden.value       = '';
        status.textContent = '✕ Fetch failed: ' + err.message;
        status.style.color = 'var(--red)';
    }
});

document.querySelector('form').addEventListener('submit', function (e) {
    const input  = document.getElementById('login_input').value.trim();
    const hidden = document.getElementById('resolvedEmail').value.trim();
    const status = document.getElementById('resolveStatus');

    if (!input) return;

    if (!isCallsign(input)) {
        document.getElementById('resolvedEmail').value = input;
        return;
    }

    if (!hidden) {
        e.preventDefault();
        status.textContent = '✕ Callsign could not be resolved. Please use your email address.';
        status.style.color = 'var(--red)';
    }
});
</script>
@endsection