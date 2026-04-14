<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>RAYNET CMS — Installation</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{-webkit-font-smoothing:antialiased}
body{font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;background:#0a0f1a;min-height:100vh;color:#111827;overflow-x:hidden}

/* ── Animated background ── */
.iz-bg{position:fixed;inset:0;z-index:0;overflow:hidden}
.iz-bg-grid{position:absolute;inset:0;background-image:linear-gradient(rgba(0,51,102,.15) 1px,transparent 1px),linear-gradient(90deg,rgba(0,51,102,.15) 1px,transparent 1px);background-size:40px 40px;animation:gridMove 20s linear infinite}
@keyframes gridMove{0%{background-position:0 0}100%{background-position:40px 40px}}
.iz-bg-glow{position:absolute;width:600px;height:600px;border-radius:50%;background:radial-gradient(circle,rgba(0,51,102,.4) 0%,transparent 70%);top:-100px;left:-100px;animation:glowPulse 8s ease-in-out infinite}
.iz-bg-glow2{position:absolute;width:400px;height:400px;border-radius:50%;background:radial-gradient(circle,rgba(200,16,46,.2) 0%,transparent 70%);bottom:-50px;right:-50px;animation:glowPulse 6s ease-in-out infinite reverse}
@keyframes glowPulse{0%,100%{transform:scale(1);opacity:.6}50%{transform:scale(1.2);opacity:1}}

/* ── Layout ── */
.iz-wrap{position:relative;z-index:1;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:2rem 1rem}

/* ── Brand ── */
.iz-brand{display:flex;align-items:center;gap:.85rem;margin-bottom:2.5rem;animation:fadeDown .6s ease both}
@keyframes fadeDown{from{opacity:0;transform:translateY(-16px)}to{opacity:1;transform:none}}
.iz-logo{width:52px;height:52px;background:linear-gradient(135deg,#003366,#004d99);border:2px solid rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 0 0 1px rgba(0,51,102,.5),0 8px 24px rgba(0,51,102,.4)}
.iz-logo span{font-size:9px;font-weight:bold;color:#fff;text-align:center;line-height:1.3;text-transform:uppercase;letter-spacing:.05em}
.iz-brand-name{font-size:1.25rem;font-weight:bold;color:#fff;letter-spacing:-.01em}
.iz-brand-sub{font-size:.75rem;color:rgba(255,255,255,.45);margin-top:.1rem;letter-spacing:.05em;text-transform:uppercase}
.iz-brand-badge{display:inline-flex;align-items:center;gap:.3rem;font-size:.68rem;font-weight:bold;text-transform:uppercase;letter-spacing:.08em;padding:.2rem .6rem;background:rgba(200,16,46,.15);border:1px solid rgba(200,16,46,.3);color:#ff6b7a;margin-top:.25rem}
.iz-brand-badge::before{content:'';width:6px;height:6px;border-radius:50%;background:#C8102E;animation:blink 2s ease-in-out infinite}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.3}}

/* ── Progress steps ── */
.iz-progress{display:flex;align-items:center;margin-bottom:2rem;animation:fadeDown .6s .1s ease both}
.iz-step-item{display:flex;align-items:center;gap:.5rem}
.iz-step-circle{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:bold;flex-shrink:0;transition:all .3s}
.iz-step-circle.done{background:#1a6b3c;border:2px solid #2d9a5a;color:#fff;box-shadow:0 0 12px rgba(26,107,60,.4)}
.iz-step-circle.active{background:linear-gradient(135deg,#003366,#004d99);border:2px solid #4d94ff;color:#fff;box-shadow:0 0 16px rgba(0,51,102,.6)}
.iz-step-circle.pending{background:rgba(255,255,255,.05);border:2px solid rgba(255,255,255,.1);color:rgba(255,255,255,.3)}
.iz-step-label{font-size:.72rem;font-weight:bold;text-transform:uppercase;letter-spacing:.06em}
.iz-step-label.done{color:#2d9a5a}
.iz-step-label.active{color:#fff}
.iz-step-label.pending{color:rgba(255,255,255,.25)}
.iz-step-connector{width:40px;height:2px;margin:0 .5rem;flex-shrink:0}
.iz-step-connector.done{background:linear-gradient(90deg,#1a6b3c,#2d9a5a)}
.iz-step-connector.pending{background:rgba(255,255,255,.08)}

/* ── Card ── */
.iz-card{width:100%;max-width:580px;background:rgba(255,255,255,.97);backdrop-filter:blur(20px);box-shadow:0 0 0 1px rgba(255,255,255,.1),0 24px 80px rgba(0,0,0,.5),0 0 60px rgba(0,51,102,.2);animation:fadeUp .6s .15s ease both;overflow:hidden}
@keyframes fadeUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:none}}

/* Card top accent bar */
.iz-card-accent{height:4px;background:linear-gradient(90deg,#003366,#004d99 50%,#C8102E)}

/* Card head */
.iz-card-head{padding:1.5rem 1.75rem 1.25rem;border-bottom:1px solid #e5e7eb}
.iz-card-title{font-size:1.05rem;font-weight:bold;color:#003366;display:flex;align-items:center;gap:.55rem}
.iz-card-title-icon{width:32px;height:32px;background:#e8eef5;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0}
.iz-card-sub{font-size:.78rem;color:#6b7f96;margin-top:.35rem;line-height:1.55}

/* Card body */
.iz-card-body{padding:1.75rem}

/* Card footer */
.iz-card-foot{padding:1.1rem 1.75rem;background:#f8fafc;border-top:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;gap:.75rem}

/* ── Form fields ── */
.iz-field{margin-bottom:1.25rem}
.iz-field:last-child{margin-bottom:0}
.iz-label{display:block;font-size:.72rem;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;color:#374151;margin-bottom:.4rem}
.iz-label-opt{font-weight:normal;text-transform:none;letter-spacing:0;color:#9ca3af;font-size:.72rem}
.iz-input{width:100%;border:1.5px solid #d1d5db;padding:.6rem .9rem;font-size:13.5px;font-family:inherit;color:#111827;outline:none;transition:all .15s;background:#fff}
.iz-input:focus{border-color:#003366;box-shadow:0 0 0 3px rgba(0,51,102,.1)}
.iz-input::placeholder{color:#9ca3af}
.iz-input-mono{font-family:ui-monospace,monospace}
.iz-err{font-size:.72rem;color:#C8102E;font-weight:bold;margin-top:.3rem;display:flex;align-items:center;gap:.3rem}
.iz-err::before{content:'⚠'}
.iz-hint{font-size:.72rem;color:#9aa3ae;margin-top:.3rem;line-height:1.5}
.iz-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
.iz-divider{height:1px;background:linear-gradient(90deg,transparent,#e5e7eb,transparent);margin:1.25rem 0}

/* ── Info boxes ── */
.iz-info{padding:.85rem 1rem;font-size:.8rem;line-height:1.6;margin-bottom:1.25rem;display:flex;gap:.75rem;align-items:flex-start}
.iz-info-icon{font-size:1.1rem;flex-shrink:0;margin-top:.05rem}
.iz-info-warn{background:#fffbeb;border:1px solid #fde68a;border-left:3px solid #f59e0b;color:#78350f}
.iz-info-ok  {background:#eef7f2;border:1px solid #b8ddc9;border-left:3px solid #1a6b3c;color:#1a3a1f}
.iz-info-info{background:#eff6ff;border:1px solid #bfdbfe;border-left:3px solid #2563eb;color:#1e3a5f}

/* ── Buttons ── */
.iz-btn{display:inline-flex;align-items:center;gap:.4rem;padding:.6rem 1.4rem;border:1.5px solid;font-family:inherit;font-size:.8rem;font-weight:bold;cursor:pointer;transition:all .15s;text-transform:uppercase;letter-spacing:.06em;text-decoration:none;white-space:nowrap}
.iz-btn-primary{background:linear-gradient(135deg,#003366,#004d99);border-color:#003366;color:#fff;box-shadow:0 4px 12px rgba(0,51,102,.3)}
.iz-btn-primary:hover{background:linear-gradient(135deg,#002244,#003d80);box-shadow:0 6px 16px rgba(0,51,102,.4);transform:translateY(-1px)}
.iz-btn-primary:active{transform:translateY(0)}
.iz-btn-ghost{background:#fff;border-color:#d1d5db;color:#6b7f96}
.iz-btn-ghost:hover{border-color:#003366;color:#003366;background:#f0f5ff}
.iz-btn-success{background:linear-gradient(135deg,#1a6b3c,#2d9a5a);border-color:#1a6b3c;color:#fff;box-shadow:0 4px 12px rgba(26,107,60,.3)}
.iz-btn-success:hover{transform:translateY(-1px);box-shadow:0 6px 16px rgba(26,107,60,.4)}

/* ── Welcome screen ── */
.iz-welcome{text-align:center;padding:.5rem 0 1.25rem}
.iz-welcome-icon{font-size:3.5rem;margin-bottom:1rem;filter:drop-shadow(0 4px 8px rgba(0,0,0,.15))}
.iz-welcome-title{font-size:1.5rem;font-weight:bold;color:#003366;margin-bottom:.5rem;letter-spacing:-.02em}
.iz-welcome-sub{font-size:.88rem;color:#6b7f96;max-width:380px;margin:0 auto 1.5rem;line-height:1.65}
.iz-features{display:grid;grid-template-columns:1fr 1fr;gap:.6rem;margin:1.25rem 0;text-align:left}
.iz-feature{display:flex;align-items:flex-start;gap:.55rem;padding:.65rem .85rem;background:#f8fafc;border:1px solid #e5e7eb;font-size:.8rem;color:#374151;line-height:1.4}
.iz-feature-icon{font-size:.95rem;flex-shrink:0;margin-top:.05rem}

/* ── Complete screen ── */
.iz-complete{text-align:center;padding:1rem 0}
.iz-complete-icon{font-size:4rem;margin-bottom:.75rem;animation:pop .5s cubic-bezier(.34,1.56,.64,1) both}
@keyframes pop{from{transform:scale(0)}to{transform:scale(1)}}
.iz-complete-title{font-size:1.4rem;font-weight:bold;color:#003366;margin-bottom:.4rem}
.iz-complete-sub{font-size:.85rem;color:#6b7f96;margin-bottom:1.5rem}
.iz-checklist{display:flex;flex-direction:column;gap:.5rem;text-align:left;margin:1.25rem 0}
.iz-check-item{display:flex;align-items:center;gap:.65rem;padding:.6rem .85rem;background:#eef7f2;border:1px solid #b8ddc9;font-size:.82rem;font-weight:bold;color:#1a6b3c}
.iz-check-item::before{content:'✓';font-size:1rem;flex-shrink:0}
.iz-next-steps{background:#f8fafc;border:1px solid #e5e7eb;padding:.85rem 1rem;font-size:.8rem;color:#374151;line-height:1.7;margin-top:1rem}
.iz-next-steps strong{color:#003366}

/* ── Error banner ── */
.iz-err-banner{background:#fdf0f2;border:1px solid rgba(200,16,46,.2);border-left:3px solid #C8102E;padding:.75rem 1rem;margin-bottom:1.25rem;font-size:.8rem;color:#C8102E;font-weight:bold;display:flex;align-items:center;gap:.5rem}

/* ── Footer ── */
.iz-footer{margin-top:1.75rem;font-size:.7rem;color:rgba(255,255,255,.25);text-align:center;letter-spacing:.04em;animation:fadeUp .6s .3s ease both}
.iz-footer a{color:rgba(255,255,255,.4);text-decoration:none}
.iz-footer a:hover{color:rgba(255,255,255,.7)}

@media(max-width:560px){
    .iz-row{grid-template-columns:1fr}
    .iz-features{grid-template-columns:1fr}
    .iz-step-label{display:none}
    .iz-step-connector{width:24px}
}
</style>
</head>
<body>

<div class="iz-bg">
    <div class="iz-bg-grid"></div>
    <div class="iz-bg-glow"></div>
    <div class="iz-bg-glow2"></div>
</div>

<div class="iz-wrap">

    {{-- Brand --}}
    <div class="iz-brand">
        <div class="iz-logo"><span>RAY<br>NET</span></div>
        <div>
            <div class="iz-brand-name">RAYNET CMS</div>
            <div class="iz-brand-sub">Installation Wizard</div>
            <div class="iz-brand-badge">Setup mode active</div>
        </div>
    </div>

    {{-- Progress --}}
    @if($step !== 'index')
    <div class="iz-progress">
        <div class="iz-step-item">
            <div class="iz-step-circle {{ in_array($step, ['step2','step3']) ? 'done' : ($step === 'step1' ? 'active' : 'pending') }}">
                {{ in_array($step, ['step2','step3']) ? '✓' : '1' }}
            </div>
            <span class="iz-step-label {{ in_array($step, ['step2','step3']) ? 'done' : ($step === 'step1' ? 'active' : 'pending') }}">Group</span>
        </div>
        <div class="iz-step-connector {{ in_array($step, ['step2','step3']) ? 'done' : 'pending' }}"></div>
        <div class="iz-step-item">
            <div class="iz-step-circle {{ $step === 'step3' ? 'done' : ($step === 'step2' ? 'active' : 'pending') }}">
                {{ $step === 'step3' ? '✓' : '2' }}
            </div>
            <span class="iz-step-label {{ $step === 'step3' ? 'done' : ($step === 'step2' ? 'active' : 'pending') }}">Admin</span>
        </div>
        <div class="iz-step-connector {{ $step === 'step3' ? 'done' : 'pending' }}"></div>
        <div class="iz-step-item">
            <div class="iz-step-circle {{ $step === 'step3' ? 'active' : 'pending' }}">3</div>
            <span class="iz-step-label {{ $step === 'step3' ? 'active' : 'pending' }}">Complete</span>
        </div>
    </div>
    @endif

    {{-- ── WELCOME ── --}}
    @if($step === 'index')
    <div class="iz-card">
        <div class="iz-card-accent"></div>
        <div class="iz-card-body">
            <div class="iz-welcome">
                <div class="iz-welcome-icon">📻</div>
                <h1 class="iz-welcome-title">Welcome to RAYNET CMS</h1>
                <p class="iz-welcome-sub">The complete web platform for RAYNET UK groups. Let's get your site set up — it takes about 2 minutes.</p>
            </div>

            <div class="iz-features">
                <div class="iz-feature"><span class="iz-feature-icon">👥</span>Member management & roles</div>
                <div class="iz-feature"><span class="iz-feature-icon">📅</span>Event scheduling & RSVPs</div>
                <div class="iz-feature"><span class="iz-feature-icon">📄</span>Visual page builder</div>
                <div class="iz-feature"><span class="iz-feature-icon">🎓</span>Training portal & LMS</div>
                <div class="iz-feature"><span class="iz-feature-icon">📡</span>Ops map & alert status</div>
                <div class="iz-feature"><span class="iz-feature-icon">🔌</span>Module update system</div>
            </div>

            <div class="iz-info iz-info-warn">
                <span class="iz-info-icon">⚠️</span>
                <div><strong>Before you continue:</strong> Make sure you have run <code>php artisan migrate</code> to set up the database tables.</div>
            </div>
        </div>
        <div class="iz-card-foot">
            <span style="font-size:.72rem;color:#9ca3af">RAYNET CMS · Built for RAYNET UK</span>
            <a href="{{ route('install.step1') }}" class="iz-btn iz-btn-primary">
                Get Started
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        </div>
    </div>

    {{-- ── STEP 1: GROUP DETAILS ── --}}
    @elseif($step === 'step1')
    <div class="iz-card">
        <div class="iz-card-accent"></div>
        <div class="iz-card-head">
            <div class="iz-card-title">
                <div class="iz-card-title-icon">📻</div>
                Your Group Details
            </div>
            <div class="iz-card-sub">This information appears throughout your site. You can change everything later in Admin → Settings.</div>
        </div>
        <form method="POST" action="{{ route('install.step1.post') }}">
            @csrf
            <div class="iz-card-body">

                @if($errors->any())
                <div class="iz-err-banner">⚠ Please fix the highlighted errors below.</div>
                @endif

                <div class="iz-field">
                    <label class="iz-label" for="group_name">Group Name <span class="iz-label-opt">(required)</span></label>
                    <input type="text" id="group_name" name="group_name" class="iz-input"
                           value="{{ old('group_name') }}" placeholder="e.g. Liverpool RAYNET" required autofocus>
                    @error('group_name')<div class="iz-err">{{ $message }}</div>@enderror
                </div>

                <div class="iz-row">
                    <div class="iz-field">
                        <label class="iz-label" for="group_number">Group Number <span class="iz-label-opt">(optional)</span></label>
                        <input type="text" id="group_number" name="group_number" class="iz-input iz-input-mono"
                               value="{{ old('group_number') }}" placeholder="e.g. 10/ME/179">
                    </div>
                    <div class="iz-field">
                        <label class="iz-label" for="group_callsign">Group Callsign <span class="iz-label-opt">(optional)</span></label>
                        <input type="text" id="group_callsign" name="group_callsign" class="iz-input iz-input-mono"
                               value="{{ old('group_callsign') }}" placeholder="e.g. M0XYZ"
                               oninput="this.value=this.value.toUpperCase()">
                    </div>
                </div>

                <div class="iz-row">
                    <div class="iz-field">
                        <label class="iz-label" for="group_region">Region / Area <span class="iz-label-opt">(optional)</span></label>
                        <input type="text" id="group_region" name="group_region" class="iz-input"
                               value="{{ old('group_region') }}" placeholder="e.g. Merseyside">
                    </div>
                    <div class="iz-field">
                        <label class="iz-label" for="raynet_zone">RAYNET Zone <span class="iz-label-opt">(optional)</span></label>
                        <input type="text" id="raynet_zone" name="raynet_zone" class="iz-input"
                               value="{{ old('raynet_zone') }}" placeholder="e.g. Zone 10">
                    </div>
                </div>

                <div class="iz-divider"></div>

                <div class="iz-row">
                    <div class="iz-field">
                        <label class="iz-label" for="gc_name">Group Controller <span class="iz-label-opt">(required)</span></label>
                        <input type="text" id="gc_name" name="gc_name" class="iz-input"
                               value="{{ old('gc_name') }}" placeholder="e.g. John Smith" required>
                        @error('gc_name')<div class="iz-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="iz-field">
                        <label class="iz-label" for="gc_email">GC Email <span class="iz-label-opt">(required)</span></label>
                        <input type="email" id="gc_email" name="gc_email" class="iz-input"
                               value="{{ old('gc_email') }}" placeholder="gc@yourgroup.raynet-uk.net" required>
                        @error('gc_email')<div class="iz-err">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="iz-row">
                    <div class="iz-field">
                        <label class="iz-label" for="support_request_email">Support Email <span class="iz-label-opt">(required)</span></label>
                        <input type="email" id="support_request_email" name="support_request_email" class="iz-input"
                               value="{{ old('support_request_email') }}" placeholder="support@yourgroup.com" required>
                        <div class="iz-hint">Where event support requests are sent.</div>
                        @error('support_request_email')<div class="iz-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="iz-field">
                        <label class="iz-label" for="site_url">Site URL <span class="iz-label-opt">(required)</span></label>
                        <input type="url" id="site_url" name="site_url" class="iz-input"
                               value="{{ old('site_url', config('app.url')) }}" placeholder="https://yourgroup.net" required>
                        @error('site_url')<div class="iz-err">{{ $message }}</div>@enderror
                    </div>
                </div>

            </div>
            <div class="iz-card-foot">
                <a href="{{ route('install.index') }}" class="iz-btn iz-btn-ghost">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                    Back
                </a>
                <button type="submit" class="iz-btn iz-btn-primary">
                    Next: Admin Account
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>
        </form>
    </div>

    {{-- ── STEP 2: ADMIN ACCOUNT ── --}}
    @elseif($step === 'step2')
    <div class="iz-card">
        <div class="iz-card-accent"></div>
        <div class="iz-card-head">
            <div class="iz-card-title">
                <div class="iz-card-title-icon">🔐</div>
                Create Your Admin Account
            </div>
            <div class="iz-card-sub">This will be the first administrator. More admins can be added later from the admin panel.</div>
        </div>
        <form method="POST" action="{{ route('install.step2.post') }}">
            @csrf
            <div class="iz-card-body">

                @if($errors->any())
                <div class="iz-err-banner">⚠ Please fix the highlighted errors below.</div>
                @endif

                <div class="iz-row">
                    <div class="iz-field">
                        <label class="iz-label" for="name">Full Name <span class="iz-label-opt">(required)</span></label>
                        <input type="text" id="name" name="name" class="iz-input"
                               value="{{ old('name') }}" placeholder="e.g. John Smith" required autofocus>
                        @error('name')<div class="iz-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="iz-field">
                        <label class="iz-label" for="callsign">Callsign <span class="iz-label-opt">(required)</span></label>
                        <input type="text" id="callsign" name="callsign" class="iz-input iz-input-mono"
                               value="{{ old('callsign') }}" placeholder="e.g. M0XYZ" required
                               oninput="this.value=this.value.toUpperCase()">
                        @error('callsign')<div class="iz-err">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="iz-field">
                    <label class="iz-label" for="email">Email Address <span class="iz-label-opt">(required)</span></label>
                    <input type="email" id="email" name="email" class="iz-input"
                           value="{{ old('email') }}" placeholder="you@example.com" required>
                    @error('email')<div class="iz-err">{{ $message }}</div>@enderror
                </div>

                <div class="iz-row">
                    <div class="iz-field">
                        <label class="iz-label" for="password">Password <span class="iz-label-opt">(min 10 chars)</span></label>
                        <input type="password" id="password" name="password" class="iz-input"
                               placeholder="Choose a strong password" required minlength="10"
                               oninput="checkStrength(this.value)">
                        <div id="strengthBar" style="height:3px;background:#e5e7eb;margin-top:.4rem;transition:all .3s"><div id="strengthFill" style="height:100%;width:0;transition:all .3s"></div></div>
                        @error('password')<div class="iz-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="iz-field">
                        <label class="iz-label" for="password_confirmation">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="iz-input"
                               placeholder="Repeat your password" required>
                    </div>
                </div>

                <div class="iz-info iz-info-info">
                    <span class="iz-info-icon">🔑</span>
                    <div>Use a strong, unique password. This account will have full access to your RAYNET CMS installation.</div>
                </div>

            </div>
            <div class="iz-card-foot">
                <a href="{{ route('install.step1') }}" class="iz-btn iz-btn-ghost">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                    Back
                </a>
                <button type="submit" class="iz-btn iz-btn-primary">
                    Create Account
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>
        </form>
    </div>

    {{-- ── STEP 3: COMPLETE ── --}}
    @elseif($step === 'step3')
    <div class="iz-card">
        <div class="iz-card-accent"></div>
        <div class="iz-card-body">
            <div class="iz-complete">
                <div class="iz-complete-icon">🎉</div>
                <h2 class="iz-complete-title">{{ $groupName ?? 'Your Group' }} is ready!</h2>
                <p class="iz-complete-sub">Your RAYNET CMS site has been configured successfully.</p>
            </div>

            <div class="iz-checklist">
                <div class="iz-check-item">Group details saved</div>
                <div class="iz-check-item">Admin account created</div>
                <div class="iz-check-item">Database configured</div>
                <div class="iz-check-item">Update server connected</div>
            </div>

            <div class="iz-next-steps">
                <strong>After logging in:</strong><br>
                • Go to <strong>Admin → Settings</strong> to upload your group logo<br>
                • Go to <strong>Admin → Pages</strong> to customise your About, Home &amp; Training pages<br>
                • Go to <strong>Module Manager</strong> to install additional features<br>
                • Check for module updates from the RAYNET CMS GitHub repository
            </div>
        </div>
        <div class="iz-card-foot">
            <span style="font-size:.72rem;color:#9ca3af">RAYNET CMS · Powered by RAYNET Liverpool</span>
            <form method="POST" action="{{ route('install.complete') }}">
                @csrf
                <button type="submit" class="iz-btn iz-btn-success">
                    ✓ Launch My Site
                </button>
            </form>
        </div>
    </div>
    @endif

    <div class="iz-footer">
        RAYNET CMS · Built for <a href="https://www.raynet-uk.net" target="_blank">RAYNET UK</a> groups ·
        <a href="https://github.com/raynet-uk/raynet-cms-modules" target="_blank">GitHub</a>
    </div>

</div>

<script>
function checkStrength(v) {
    let score = 0;
    if (v.length >= 10) score++;
    if (v.length >= 14) score++;
    if (/[A-Z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;
    const colors = ['#C8102E','#f59e0b','#f59e0b','#1a6b3c','#1a6b3c'];
    const widths = ['20%','40%','60%','80%','100%'];
    const fill = document.getElementById('strengthFill');
    if (fill && v.length > 0) {
        fill.style.width = widths[score-1] || '10%';
        fill.style.background = colors[score-1] || '#C8102E';
    } else if (fill) {
        fill.style.width = '0';
    }
}
</script>
</body>
</html>