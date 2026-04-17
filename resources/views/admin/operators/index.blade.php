@extends('layouts.admin')
@section('title', 'Manage operators')

@section('content')

@php
    /** @var \App\Models\Operator|null $editingOperator */
    /** @var \Illuminate\Support\Collection|\App\Models\Role[] $roles */
    $statuses = ['Active', 'Training', 'On hold', 'Inactive'];
    $currentRole = old('role', $editingOperator->role ?? '');
    $hasRolesDefined = $roles->isNotEmpty();
    $roleColours = $roles->pluck('colour', 'name');
    $statusColours = [
        'Active' => ['bg'=>'rgba(34,212,125,0.12)', 'border'=>'rgba(34,212,125,0.4)', 'text'=>'#22d47d'],
        'Training' => ['bg'=>'rgba(56,189,248,0.12)', 'border'=>'rgba(56,189,248,0.4)', 'text'=>'#38bdf8'],
        'On hold' => ['bg'=>'rgba(251,191,36,0.12)', 'border'=>'rgba(251,191,36,0.4)', 'text'=>'#fbbf24'],
        'Inactive' => ['bg'=>'rgba(100,116,139,0.12)', 'border'=>'rgba(100,116,139,0.4)', 'text'=>'#64748b'],
    ];
    // Determine current login value for the edit form
    $currentLogin = old('login', $editingOperator->login ?? $editingOperator->callsign ?? '');
@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet">

<style>
:root {
    --bg: #03050f;
    --surface: #090e1e;
    --surface2:#0d1425;
    --border: rgba(99,125,255,0.18);
    --border2: rgba(99,125,255,0.08);
    --accent: #637dff;
    --accent2: #a78bfa;
    --green: #22d47d;
    --red: #f87171;
    --amber: #fbbf24;
    --text: #e2e8f0;
    --muted: #64748b;
    --muted2: #94a3b8;
    --mono: 'Space Mono', monospace;
    --sans: 'Syne', sans-serif;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{background:var(--bg);color:var(--text);font-family:var(--sans);min-height:100vh;overflow-x:hidden;}
body::after{content:'';position:fixed;top:-30vh;right:-20vw;width:70vw;height:70vw;border-radius:50%;background:radial-gradient(circle,rgba(99,125,255,.07) 0%,transparent 65%);pointer-events:none;z-index:0;animation:drift 18s ease-in-out infinite alternate;}
@keyframes drift{to{transform:translate(-5vw,8vh) scale(1.08);}}
.wrap{position:relative;z-index:1;max-width:1300px;margin:0 auto;padding:0 1.5rem 4rem;}
/* TOP BAR */
.topbar{display:flex;align-items:center;justify-content:space-between;padding:1.2rem 0;border-bottom:1px solid var(--border2);margin-bottom:2rem;gap:1rem;flex-wrap:wrap;}
.brand{display:flex;align-items:center;gap:.75rem;}
.brand-badge{width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,var(--accent),var(--accent2));display:flex;align-items:center;justify-content:center;font-size:1.1rem;box-shadow:0 0 18px rgba(99,125,255,.4);}
.brand-name{font-size:.82rem;font-weight:700;letter-spacing:.18em;text-transform:uppercase;}
.brand-sub{font-size:.68rem;color:var(--muted);font-family:var(--mono);letter-spacing:.08em;}
.back-btn{display:flex;align-items:center;gap:.4rem;padding:.38rem .85rem;border-radius:999px;border:1px solid var(--border);background:var(--surface);color:var(--muted2);font-size:.78rem;text-decoration:none;font-family:var(--mono);transition:all .15s;}
.back-btn:hover{border-color:var(--accent);color:var(--accent);}
/* PAGE HEADER */
.page-header{margin-bottom:2rem;}
.page-header h1{font-size:1.6rem;font-weight:800;margin-bottom:.25rem;}
.page-header p{font-size:.85rem;color:var(--muted2);}
/* TOAST */
.status-toast{display:flex;align-items:center;gap:.55rem;padding:.65rem 1rem;border-radius:.7rem;margin-bottom:1.5rem;background:rgba(34,212,125,.08);border:1px solid rgba(34,212,125,.35);color:#86efac;font-size:.82rem;animation:fadeIn .4s ease;}
@keyframes fadeIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:none;}}
/* FORM CARD */
.form-card{background:var(--surface);border:1px solid var(--border);border-radius:1.1rem;overflow:hidden;margin-bottom:2rem;}
.form-card::before{content:'';display:block;height:3px;background:linear-gradient(90deg,var(--accent),var(--accent2),transparent);}
.form-head{padding:.9rem 1.2rem;border-bottom:1px solid var(--border2);background:var(--surface2);display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;}
.form-head-title{font-size:.92rem;font-weight:700;}
.form-head-sub{font-size:.72rem;color:var(--muted);font-family:var(--mono);margin-top:.1rem;}
.edit-cancel{font-size:.72rem;color:var(--accent);font-family:var(--mono);text-decoration:none;}
.edit-cancel:hover{text-decoration:underline;}
/* Section dividers inside form */
.form-section{padding:1rem 1.2rem;border-bottom:1px solid var(--border2);}
.form-section:last-of-type{border-bottom:none;}
.section-label{font-size:.6rem;text-transform:uppercase;letter-spacing:.2em;color:var(--muted);font-family:var(--mono);margin-bottom:.8rem;display:flex;align-items:center;gap:.5rem;}
.section-label::after{content:'';flex:1;height:1px;background:var(--border2);}
.form-grid{display:grid;gap:.85rem;}
.form-grid-auto{grid-template-columns:repeat(auto-fit,minmax(190px,1fr));}
.form-grid-2{grid-template-columns:1fr 1fr;}
.form-grid-3{grid-template-columns:1fr 1fr 1fr;}
@media(max-width:700px){.form-grid-2,.form-grid-3{grid-template-columns:1fr;}}
.form-field{display:flex;flex-direction:column;gap:.28rem;}
.form-field label{font-size:.68rem;color:var(--muted2);letter-spacing:.1em;text-transform:uppercase;font-family:var(--mono);}
.form-field input,.form-field select{
    background:var(--surface2);border:1px solid var(--border);
    border-radius:.5rem;padding:.52rem .75rem;
    color:var(--text);font-family:var(--sans);font-size:.82rem;
    outline:none;transition:border-color .2s,box-shadow .2s;width:100%;
}
.form-field input:focus,.form-field select:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(99,125,255,.12);}
.form-field select option{background:#0d1425;}
.form-field-note{font-size:.67rem;color:var(--muted);font-family:var(--mono);margin-top:.22rem;line-height:1.5;}
.form-field-note a{color:var(--accent);}
.form-field-note.warn{color:#f97316;}
.form-field-note.warn a{color:#fdba74;}
/* Login username — highlight as special field */
.login-field input{
    font-family:var(--mono);letter-spacing:.08em;
    border-color:rgba(99,125,255,.35);
    background:rgba(99,125,255,.05);
}
.login-field input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(99,125,255,.15);}
.login-sync-hint{font-size:.67rem;font-family:var(--mono);margin-top:.22rem;}
.login-sync-hint.synced{color:var(--muted);}
.login-sync-hint.custom{color:var(--amber);}
/* Password field */
.pwd-wrap{position:relative;}
.pwd-wrap input{padding-right:2.5rem;}
.pwd-toggle{position:absolute;right:.65rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;font-size:.75rem;font-family:var(--mono);padding:0;transition:color .15s;}
.pwd-toggle:hover{color:var(--accent2);}
.pwd-strength{height:3px;border-radius:2px;margin-top:.3rem;transition:all .2s;background:var(--border2);}
.pwd-strength-label{font-size:.62rem;font-family:var(--mono);margin-top:.18rem;}
/* Checkbox */
.checkbox-field{display:flex;align-items:center;gap:.5rem;padding:.52rem .75rem;background:var(--surface2);border:1px solid var(--border);border-radius:.5rem;cursor:pointer;transition:border-color .2s;}
.checkbox-field:hover{border-color:var(--accent2);}
.checkbox-field input[type="checkbox"]{width:15px;height:15px;accent-color:var(--accent2);cursor:pointer;flex-shrink:0;}
.checkbox-field span{font-size:.82rem;color:var(--text);}
/* BUTTONS */
.btn-primary{padding:.48rem 1.2rem;border-radius:999px;background:var(--accent);border:none;color:#fff;font-family:var(--sans);font-size:.82rem;font-weight:700;cursor:pointer;transition:all .18s;box-shadow:0 4px 15px rgba(99,125,255,.35);}
.btn-primary:hover{background:#7c93ff;box-shadow:0 6px 20px rgba(99,125,255,.5);transform:translateY(-1px);}
.btn-success{padding:.48rem 1.2rem;border-radius:999px;background:var(--green);border:none;color:#03050f;font-family:var(--sans);font-size:.82rem;font-weight:700;cursor:pointer;transition:all .18s;box-shadow:0 4px 15px rgba(34,212,125,.3);}
.btn-success:hover{background:#34d399;box-shadow:0 6px 20px rgba(34,212,125,.45);transform:translateY(-1px);}
/* TABLE */
.table-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;gap:1rem;flex-wrap:wrap;}
.table-title{font-size:1rem;font-weight:700;}
.table-count{font-size:.72rem;color:var(--muted);font-family:var(--mono);background:var(--surface2);border:1px solid var(--border2);padding:.2rem .65rem;border-radius:999px;}
.search-wrap{position:relative;}
.search-wrap input{background:var(--surface);border:1px solid var(--border);border-radius:999px;padding:.38rem .9rem .38rem 2.1rem;color:var(--text);font-family:var(--mono);font-size:.72rem;width:220px;outline:none;transition:border-color .2s,box-shadow .2s;}
.search-wrap input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(99,125,255,.12);}
.search-icon{position:absolute;left:.65rem;top:50%;transform:translateY(-50%);color:var(--muted);font-size:.75rem;pointer-events:none;}
.table-wrap{background:var(--surface);border:1px solid var(--border);border-radius:1.1rem;overflow:hidden;}
table{width:100%;border-collapse:collapse;font-size:.8rem;}
thead tr{background:var(--surface2);}
thead th{padding:.65rem .85rem;text-align:left;font-size:.63rem;text-transform:uppercase;letter-spacing:.12em;color:var(--muted);font-family:var(--mono);font-weight:600;white-space:nowrap;}
tbody tr{border-top:1px solid var(--border2);transition:background .12s;}
tbody tr:hover{background:rgba(99,125,255,.03);}
td{padding:.6rem .85rem;vertical-align:middle;}
/* Inline badges */
.role-pill{display:inline-flex;align-items:center;gap:.35rem;padding:.12rem .5rem;border-radius:999px;font-size:.7rem;white-space:nowrap;}
.role-dot{width:7px;height:7px;border-radius:50%;flex-shrink:0;}
.status-pill{display:inline-flex;align-items:center;gap:.3rem;padding:.12rem .5rem;border-radius:999px;font-size:.7rem;font-family:var(--mono);font-weight:600;white-space:nowrap;}
.status-dot{width:5px;height:5px;border-radius:50%;flex-shrink:0;animation:pulse-s 2s infinite;}
@keyframes pulse-s{0%,100%{opacity:1}50%{opacity:.5}}
.admin-badge{display:inline-flex;align-items:center;gap:.25rem;padding:.1rem .45rem;border-radius:999px;font-size:.65rem;font-family:var(--mono);font-weight:700;background:rgba(167,139,250,.15);border:1px solid rgba(167,139,250,.45);color:var(--accent2);}
.callsign{font-family:var(--mono);font-size:.75rem;color:var(--muted2);letter-spacing:.05em;}
.login-tag{font-family:var(--mono);font-size:.7rem;color:var(--accent);letter-spacing:.04em;}
.level-badge{display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:var(--surface2);border:1px solid var(--border);font-family:var(--mono);font-size:.7rem;color:var(--muted2);}
.has-pwd{font-size:.65rem;font-family:var(--mono);color:var(--green);}
.no-pwd{font-size:.65rem;font-family:var(--mono);color:var(--muted);}
/* Actions */
.action-edit{font-size:.72rem;color:var(--accent);text-decoration:none;font-family:var(--mono);transition:color .15s;}
.action-edit:hover{color:#93c5fd;text-decoration:underline;}
.action-delete{font-size:.72rem;color:var(--red);text-decoration:none;font-family:var(--mono);transition:color .15s;background:none;border:none;cursor:pointer;padding:0;}
.action-delete:hover{color:#fca5a5;text-decoration:underline;}
/* Empty state */
.empty-state{padding:3rem 1rem;text-align:center;font-size:.82rem;color:var(--muted);font-family:var(--mono);}
.empty-state-icon{font-size:2rem;margin-bottom:.75rem;opacity:.4;}
/* Pagination */
.pagination-wrap{padding:.75rem 1rem;border-top:1px solid var(--border2);}
.fade-in{animation:fadeIn .4s ease both;}
.stagger>*{animation:fadeIn .4s ease both;}
.stagger>*:nth-child(1){animation-delay:.04s;}
.stagger>*:nth-child(2){animation-delay:.09s;}
</style>

<div class="wrap">
    {{-- TOP BAR --}}
    <nav class="topbar fade-in">
        <div class="brand">
            <div class="brand-badge">📡</div>
            <div>
                <div class="brand-name">{{ \App\Helpers\RaynetSetting::groupName() }}</div>
                <div class="brand-sub">admin control panel</div>
            </div>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="back-btn">← Back to admin</a>
    </nav>

    <div class="page-header fade-in">
        <h1>Manage operators</h1>
        <p>Master list of {{ \App\Helpers\RaynetSetting::groupName() }} operators — callsigns, roles, statuses, login credentials and admin flags.</p>
    </div>

    @if (session('status'))
        <div class="status-toast"><span>✓</span> {{ session('status') }}</div>
    @endif

    {{-- ═══════════════════════
         ADD / EDIT FORM
    ═══════════════════════ --}}
    <div class="form-card fade-in">
        <div class="form-head">
            <div>
                <div class="form-head-title">
                    {{ isset($editingOperator) ? '✏️ Edit operator' : '➕ Add operator' }}
                </div>
                @if (isset($editingOperator))
                    <div class="form-head-sub">
                        Editing: {{ $editingOperator->name }}
                        @if ($editingOperator->callsign) ({{ $editingOperator->callsign }}) @endif
                    </div>
                @endif
            </div>
            @if (isset($editingOperator))
                <a href="{{ route('admin.operators') }}" class="edit-cancel">✕ Cancel edit</a>
            @endif
        </div>

        @if ($errors->any())
            <div class="error-alert">⚠ {{ $errors->first() }}</div>
        @endif

        <form method="POST"
              action="{{ isset($editingOperator)
                            ? route('admin.operators.update', $editingOperator->id)
                            : route('admin.operators.store') }}"
              id="operatorForm">
            @csrf
            @if (isset($editingOperator)) @method('PUT') @endif

            {{-- ─── SECTION 1: IDENTITY ─── --}}
            <div class="form-section">
                <div class="section-label">Identity</div>
                <div class="form-grid form-grid-auto">
                    <div class="form-field">
                        <label>Full name *</label>
                        <input name="name" type="text"
                               value="{{ old('name', $editingOperator->name ?? '') }}"
                               placeholder="Nathan Dillon">
                    </div>
                    <div class="form-field">
                        <label>Callsign</label>
                        <input name="callsign" id="callsignInput" type="text"
                               value="{{ old('callsign', $editingOperator->callsign ?? '') }}"
                               placeholder="M7NDN"
                               style="font-family:var(--mono);letter-spacing:.06em;text-transform:uppercase;"
                               oninput="onCallsignChange(this.value)">
                    </div>
                    <div class="form-field">
                        <label>Email address</label>
                        <input name="email" type="email"
                               value="{{ old('email', $editingOperator->email ?? '') }}"
                               placeholder="operator@example.com">
                    </div>
                </div>
            </div>

            {{-- ─── SECTION 2: ROLE, LEVEL & STATUS ─── --}}
            <div class="form-section">
                <div class="section-label">Role & Status</div>
                <div class="form-grid form-grid-auto">
                    <div class="form-field">
                        <label>Role</label>
                        @if ($hasRolesDefined)
                            <select name="role">
                                <option value="">— Select role —</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}"
                                            {{ $currentRole === $role->name ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-field-note">
                                <a href="{{ route('admin.roles') }}">Manage roles →</a>
                            </div>
                        @else
                            <input name="role" type="text" value="{{ $currentRole }}"
                                   placeholder="Group Controller, Operator…">
                            <div class="form-field-note warn">
                                No roles defined yet.
                                <a href="{{ route('admin.roles') }}">Create roles →</a>
                            </div>
                        @endif
                    </div>
                    <div class="form-field">
                        <label>Level</label>
                        <input name="level" type="text"
                               value="{{ old('level', $editingOperator->level ?? '') }}"
                               placeholder="0 – 5">
                    </div>
                    <div class="form-field">
                        <label>Status *</label>
                        @php $currentStatus = old('status', $editingOperator->status ?? 'Active'); @endphp
                        <select name="status">
                            @foreach ($statuses as $st)
                                <option value="{{ $st }}" {{ $currentStatus === $st ? 'selected' : '' }}>
                                    {{ $st }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-field" style="justify-content:flex-end;">
                        <label>Admin access</label>
                        <label class="checkbox-field">
                            <input type="checkbox" name="is_admin" value="1"
                                   {{ old('is_admin', $editingOperator->is_admin ?? false) ? 'checked' : '' }}>
                            <span>Has admin access</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- ─── SECTION 3: LOGIN CREDENTIALS ─── --}}
            <div class="form-section">
                <div class="section-label">Login credentials</div>
                <div class="form-grid form-grid-2">
                    {{-- Login username --}}
                    <div class="form-field login-field">
                        <label>Login username *</label>
                        <input name="login" id="loginInput" type="text"
                               value="{{ old('login', $currentLogin) }}"
                               placeholder="Defaults to callsign"
                               style="font-family:var(--mono);letter-spacing:.06em;"
                               oninput="onLoginChange(this.value)">
                        <div class="login-sync-hint synced" id="loginSyncHint">
                            @if ($currentLogin && $currentLogin !== ($editingOperator->callsign ?? ''))
                                ⚠ Custom username — differs from callsign
                            @else
                                ↑ Synced to callsign — edit to override
                            @endif
                        </div>
                        <div class="form-field-note">
                            This is what the operator types to log in.
                            Defaults to their callsign; change only if needed.
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="form-field">
                        <label>
                            @if (isset($editingOperator))
                                New password
                                <span style="color:var(--muted);font-size:.62rem;margin-left:.3rem;">(leave blank to keep current)</span>
                            @else
                                Password *
                            @endif
                        </label>
                        <div class="pwd-wrap">
                            <input name="password" id="passwordInput" type="password"
                                   placeholder="{{ isset($editingOperator) ? '••••••••' : 'Set a password' }}"
                                   autocomplete="new-password"
                                   oninput="checkStrength(this.value)">
                            <button type="button" class="pwd-toggle" id="pwdToggle"
                                    onclick="togglePassword()">show</button>
                        </div>
                        <div class="pwd-strength" id="pwdStrengthBar"></div>
                        <div class="pwd-strength-label" id="pwdStrengthLabel"
                             style="color:var(--muted);font-size:.62rem;font-family:var(--mono);min-height:.9rem;"></div>
                        @if (isset($editingOperator))
                            <div class="form-field-note">
                                Last set:
                                {{ optional($editingOperator->password_changed_at)->format('d M Y') ?? 'Unknown' }}
                            </div>
                        @else
                            <div class="form-field-note">
                                Min. 8 characters. Operator can change it after first login.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Confirm password (new operators only) --}}
                @if (!isset($editingOperator))
                    <div class="form-grid form-grid-2" style="margin-top:.85rem;">
                        <div class="form-field">
                            <label>Confirm password *</label>
                            <div class="pwd-wrap">
                                <input name="password_confirmation" id="passwordConfirm" type="password"
                                       placeholder="Repeat password"
                                       autocomplete="new-password"
                                       oninput="checkMatch()">
                                <button type="button" class="pwd-toggle" onclick="toggleConfirm()">show</button>
                            </div>
                            <div id="pwdMatchHint" style="font-size:.62rem;font-family:var(--mono);margin-top:.2rem;min-height:.9rem;"></div>
                        </div>
                        <div class="form-field" style="justify-content:flex-end;">
                            <label>Force password change</label>
                            <label class="checkbox-field">
                                <input type="checkbox" name="must_change_password" value="1" checked>
                                <span>Prompt to change on first login</span>
                            </label>
                            <div class="form-field-note">Recommended for new accounts.</div>
                        </div>
                    </div>
                @else
                    <div class="form-grid form-grid-2" style="margin-top:.85rem;">
                        <div></div>
                        <div class="form-field">
                            <label>Force password reset</label>
                            <label class="checkbox-field">
                                <input type="checkbox" name="must_change_password" value="1"
                                       {{ old('must_change_password', $editingOperator->must_change_password ?? false) ? 'checked' : '' }}>
                                <span>Prompt to change on next login</span>
                            </label>
                        </div>
                    </div>
                @endif
            </div>

            <div class="form-footer">
                <button type="submit" class="{{ isset($editingOperator) ? 'btn-primary' : 'btn-success' }}">
                    {{ isset($editingOperator) ? '✓ Update operator' : '+ Save operator' }}
                </button>
                @if (isset($editingOperator))
                    <a href="{{ route('admin.operators') }}" class="edit-cancel" style="font-size:.8rem;">Cancel</a>
                @endif
            </div>
        </form>
    </div>

    {{-- ═══════════════════════
         OPERATOR TABLE
    ═══════════════════════ --}}
    <div class="fade-in">
        <div class="table-header">
            <div style="display:flex;align-items:center;gap:.75rem;">
                <span class="table-title">Operator list</span>
                @if ($operators->isNotEmpty())
                    <span class="table-count">{{ $operators->total() }} records</span>
                @endif
            </div>
            <div class="search-wrap">
                <span class="search-icon">⌕</span>
                <input type="text" placeholder="Filter by name or callsign…"
                       oninput="filterTable(this.value)">
            </div>
        </div>

        @if ($operators->isEmpty())
            <div class="table-wrap">
                <div class="empty-state">
                    <div class="empty-state-icon">🧑‍🚒</div>
                    No operators yet. Add the first record using the form above.
                </div>
            </div>
        @else
            <div class="table-wrap">
                <table id="operatorTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Callsign</th>
                            <th>Login</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Lvl</th>
                            <th>Status</th>
                            <th>Admin</th>
                            <th>Password</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($operators as $op)
                            @php
                                $roleName = $op->role;
                                $roleColour = $roleName ? ($roleColours[$roleName] ?? null) : null;
                                $sc = $statusColours[$op->status] ?? $statusColours['Inactive'];
                                $loginVal = $op->login ?? $op->callsign ?? '—';
                                $loginIsCallsign = ($loginVal === $op->callsign);
                            @endphp
                            <tr>
                                <td style="font-weight:600;">{{ $op->name }}</td>
                                <td>
                                    @if ($op->callsign)
                                        <span class="callsign">{{ $op->callsign }}</span>
                                    @else
                                        <span style="color:var(--muted);">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="login-tag">{{ $loginVal }}</span>
                                    @if (!$loginIsCallsign && $op->callsign)
                                        <span style="font-size:.6rem;color:var(--amber);font-family:var(--mono);display:block;">≠ callsign</span>
                                    @endif
                                </td>
                                <td style="font-size:.75rem;color:var(--muted2);">{{ $op->email ?? '—' }}</td>
                                <td>
                                    @if ($roleName && $roleColour)
                                        <span class="role-pill"
                                              style="border:1px solid {{ $roleColour }};background:{{ $roleColour }}1a;color:{{ $roleColour }};">
                                            <span class="role-dot" style="background:{{ $roleColour }};"></span>
                                            {{ $roleName }}
                                        </span>
                                    @elseif ($roleName)
                                        <span style="color:var(--muted2);font-size:.78rem;">{{ $roleName }}</span>
                                    @else
                                        <span style="color:var(--muted);">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($op->level !== null && $op->level !== '')
                                        <span class="level-badge">{{ $op->level }}</span>
                                    @else
                                        <span style="color:var(--muted);">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="status-pill"
                                          style="background:{{ $sc['bg'] }};border:1px solid {{ $sc['border'] }};color:{{ $sc['text'] }};">
                                        <span class="status-dot" style="background:{{ $sc['text'] }};"></span>
                                        {{ $op->status }}
                                    </span>
                                </td>
                                <td>
                                    @if ($op->is_admin)
                                        <span class="admin-badge">⚡ Admin</span>
                                    @endif
                                </td>
                                <td>
                                    @if (!empty($op->password))
                                        <span class="has-pwd">✓ set</span>
                                        @if (!empty($op->must_change_password))
                                            <span style="display:block;font-size:.6rem;font-family:var(--mono);color:var(--amber);">⚠ must reset</span>
                                        @endif
                                    @else
                                        <span class="no-pwd">not set</span>
                                    @endif
                                </td>
                                <td style="text-align:right;white-space:nowrap;">
                                    <a href="{{ route('admin.operators', ['edit' => $op->id]) }}"
                                       class="action-edit">Edit</a>
                                    &nbsp;·&nbsp;
                                    <form method="POST"
                                          action="{{ route('admin.operators.delete', $op->id) }}"
                                          style="display:inline;"
                                          onsubmit="return confirm('Delete {{ addslashes($op->name) }}?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-delete">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if ($operators->hasPages())
                    <div class="pagination-wrap">{{ $operators->links() }}</div>
                @endif
            </div>
        @endif
    </div>
</div>

<script>
// ── Callsign → auto-fill login (if login hasn't been manually edited)
let loginManuallyEdited = {{ $currentLogin && $currentLogin !== ($editingOperator->callsign ?? '') ? 'true' : 'false' }};
function onCallsignChange(val) {
    if (!loginManuallyEdited) {
        const loginInput = document.getElementById('loginInput');
        loginInput.value = val.toUpperCase();
        updateLoginHint(loginInput.value, val.toUpperCase(), false);
    }
}
function onLoginChange(val) {
    const callsign = document.getElementById('callsignInput').value.toUpperCase();
    loginManuallyEdited = (val !== '' && val.toUpperCase() !== callsign);
    updateLoginHint(val, callsign, loginManuallyEdited);
}
function updateLoginHint(login, callsign, isDifferent) {
    const hint = document.getElementById('loginSyncHint');
    if (!hint) return;
    if (isDifferent && login !== '') {
        hint.textContent = '⚠ Custom username — differs from callsign';
        hint.className = 'login-sync-hint custom';
    } else {
        hint.textContent = '↑ Synced to callsign — edit to override';
        hint.className = 'login-sync-hint synced';
    }
}
// ── Password strength meter
function checkStrength(val) {
    const bar = document.getElementById('pwdStrengthBar');
    const label = document.getElementById('pwdStrengthLabel');
    if (!bar) return;
    if (!val) { bar.style.width='0'; bar.style.background=''; label.textContent=''; return; }
    let score = 0;
    if (val.length >= 8) score++;
    if (val.length >= 12) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const levels = [
        { pct:'20%', colour:'#f87171', text:'Very weak' },
        { pct:'40%', colour:'#fb923c', text:'Weak' },
        { pct:'60%', colour:'#fbbf24', text:'Fair' },
        { pct:'80%', colour:'#34d399', text:'Good' },
        { pct:'100%',colour:'#22d47d', text:'Strong' },
    ];
    const lvl = levels[Math.min(score - 1, 4)] || levels[0];
    bar.style.width = lvl.pct;
    bar.style.background = lvl.colour;
    label.textContent = lvl.text;
    label.style.color = lvl.colour;
    checkMatch();
}
// ── Password match check
function checkMatch() {
    const p = document.getElementById('passwordInput');
    const c = document.getElementById('passwordConfirm');
    const h = document.getElementById('pwdMatchHint');
    if (!p || !c || !h) return;
    if (!c.value) { h.textContent=''; return; }
    if (p.value === c.value) {
        h.textContent = '✓ Passwords match';
        h.style.color = 'var(--green)';
    } else {
        h.textContent = '✗ Passwords do not match';
        h.style.color = 'var(--red)';
    }
}
// ── Toggle password visibility
function togglePassword() {
    const inp = document.getElementById('passwordInput');
    const btn = document.getElementById('pwdToggle');
    if (!inp) return;
    if (inp.type === 'password') { inp.type='text'; btn.textContent='hide'; }
    else { inp.type='password'; btn.textContent='show'; }
}
function toggleConfirm() {
    const inp = document.getElementById('passwordConfirm');
    if (!inp) return;
    inp.type = inp.type === 'password' ? 'text' : 'password';
}
// ── Table filter
function filterTable(q) {
    q = q.toLowerCase();
    document.querySelectorAll('#operatorTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>

@endsection