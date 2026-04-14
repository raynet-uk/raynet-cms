@extends('layouts.app')
@section('title', 'Admin Login')
@section('content')

<style>
/* ── RAYNET BRAND COLOURS & TYPOGRAPHY ── */
:root {
    --bg:       #003366; /* Navy Blue Pantone 295C */
    --surface:  #FFFFFF; /* White */
    --surface2: #F2F2F2; /* Light Grey */
    --accent:   #C8102E; /* Emergency Red Pantone 186C */
    --accent2:  #C8102E;
    --text:     #003366; /* Navy text */
    --muted:    #64748b;
    --muted2:   #94a3b8;
    --mono:     'Courier New', monospace;
    --sans:     Arial, Helvetica, sans-serif;
}

/* ── GLOBAL RESET ── */
*, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }

body {
    background: var(--bg);
    color: var(--text);
    font-family: var(--sans);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ── LOGIN CARD ── */
.login-wrap {
    width: 100%;
    max-width: 420px;
    padding: 1.5rem;
}

.login-card {
    background: var(--surface);
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    border-top: 4px solid var(--accent);
}

/* ── HEADER ── */
.login-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.2rem 1.5rem;
    border-bottom: 1px solid var(--muted2);
}
.login-brand-badge {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--accent);
    color: #fff;
    font-weight: bold;
    font-size: 1.2rem;
}
.login-brand-name {
    font-family: var(--sans);
    font-weight: bold;
    font-size: 1rem;
    color: var(--text);
    text-transform: uppercase;
}
.login-brand-sub {
    font-family: var(--sans);
    font-size: 0.75rem;
    color: var(--muted);
    margin-top: 2px;
}

/* ── BODY ── */
.login-body {
    padding: 1.5rem;
}
.login-title {
    font-family: var(--sans);
    font-weight: bold;
    font-size: 1.3rem;
    margin-bottom: 0.3rem;
    color: var(--text);
}
.login-subtitle {
    font-family: var(--sans);
    font-size: 0.85rem;
    color: var(--muted);
    margin-bottom: 1.5rem;
}

/* ── ERROR ALERT ── */
.error-alert {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 0.8rem;
    border-radius: 0.5rem;
    background: rgba(200,16,46,0.1);
    border: 1px solid var(--accent);
    color: var(--accent);
    font-size: 0.85rem;
    margin-bottom: 1rem;
}

/* ── FORM ── */
.login-form { display:flex; flex-direction:column; gap:1rem; }

.field label {
    font-family: var(--sans);
    font-size: 0.75rem;
    color: var(--text);
    font-weight: bold;
    text-transform: uppercase;
    margin-bottom: 0.3rem;
}
.input-wrap { position: relative; }
.input-icon {
    position: absolute;
    left: 0.8rem; top: 50%;
    transform: translateY(-50%);
    color: var(--muted);
    font-size: 0.9rem;
    pointer-events: none;
}
.field input {
    width: 100%;
    padding: 0.55rem 0.75rem 0.55rem 2rem;
    border-radius: 0.5rem;
    border: 1px solid var(--muted2);
    font-size: 0.9rem;
}
.field input:focus {
    border-color: var(--accent);
    outline: none;
}

/* Password toggle */
.pwd-toggle {
    position: absolute;
    right: 0.75rem; top: 50%;
    transform: translateY(-50%);
    background:none; border:none;
    font-family: var(--sans);
    font-size: 0.8rem;
    color: var(--muted);
    cursor:pointer;
}
.pwd-toggle:hover { color: var(--accent); }

/* Submit button */
.btn-submit {
    margin-top: 0.5rem;
    padding: 0.65rem 1.5rem;
    border-radius: 999px;
    border:none;
    background: var(--accent);
    color: #fff;
    font-family: var(--sans);
    font-weight: bold;
    cursor:pointer;
    transition: background 0.2s;
}
.btn-submit:hover { background: #a00d25; }

/* ── FOOTER ── */
.login-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--muted2);
    font-family: var(--sans);
    font-size: 0.75rem;
    color: var(--muted);
}
</style>

<div class="login-wrap">
    <div class="login-card">

        {{-- Header --}}
        <div class="login-header">
            <div class="login-brand-badge">📡</div>
            <div>
                <div class="login-brand-name">Liverpool RAYNET</div>
                <div class="login-brand-sub">Admin Control Panel</div>
            </div>
        </div>

        {{-- Body --}}
        <div class="login-body">
            <div class="login-title">Sign in</div>
            <div class="login-subtitle">
                Restricted access for controllers &amp; administrators.<br>
                Use your email address or callsign.
            </div>

            @if ($errors->any())
                <div class="error-alert">
                    ⚠ {{ $errors->first() ?? 'Login problem — please check your details and try again.' }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}" class="login-form">
                @csrf
                <div class="field">
                    <label for="login">Email or callsign</label>
                    <div class="input-wrap">
                        <span class="input-icon">✉</span>
                        <input id="login" name="login" type="text"
                               value="{{ old('login') }}"
                               required autofocus autocomplete="username"
                               placeholder="e.g. G4ABC or name@raynet.org">
                    </div>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">🔒</span>
                        <input id="password" name="password" type="password"
                               required autocomplete="current-password"
                               placeholder="••••••••••••">
                        <button type="button" class="pwd-toggle" onclick="togglePwd()" id="pwdToggle">show</button>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Sign in →</button>
            </form>
        </div>

        {{-- Footer --}}
        <div class="login-footer">
            🔐 <strong>Authorised officers only.</strong> All activity is logged for governance and audit purposes.
        </div>

    </div>
</div>

<script>
function togglePwd() {
    const input = document.getElementById('password');
    const toggle = document.getElementById('pwdToggle');
    if (input.type === 'password') {
        input.type = 'text'; toggle.textContent = 'hide';
    } else {
        input.type = 'password'; toggle.textContent = 'show';
    }
}
</script>

@endsection