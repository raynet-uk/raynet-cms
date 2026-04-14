@extends('layouts.app')

@section('title', 'Roles')

@section('content')

@php
    /** @var \App\Models\Role|null $editingRole */
@endphp

<style>
/* ─── RAYNET BRAND TOKENS (Brand Book v2) ────────────────────────────────────
   Navy Blue     Pantone 295C  #003366
   White                       #FFFFFF
   Emergency Red Pantone 186C  #C8102E
   Light Grey                  #F2F2F2
   Headings: Arial Bold / Helvetica Neue Bold
   Body:     Arial Regular / Helvetica Neue Regular
─────────────────────────────────────────────────────────────────────────── */
:root {
    --navy:       #003366;
    --navy-mid:   #004080;
    --navy-faint: #e8eef5;
    --red:        #C8102E;
    --red-faint:  #fdf0f2;
    --white:      #FFFFFF;
    --grey:       #F2F2F2;
    --grey-mid:   #dde2e8;
    --grey-dark:  #9aa3ae;
    --text:       #001f40;
    --text-mid:   #2d4a6b;
    --text-muted: #6b7f96;
    --green:      #1a6b3c;
    --green-bg:   #eef7f2;
    --font: Arial, 'Helvetica Neue', Helvetica, sans-serif;
    --shadow-sm: 0 1px 3px rgba(0,51,102,.09);
    --shadow-md: 0 4px 14px rgba(0,51,102,.11);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { background: var(--grey); color: var(--text); font-family: var(--font); font-size: 14px; min-height: 100vh; }

/* ─── HEADER ─── */
.rn-header {
    background: var(--navy); border-bottom: 4px solid var(--red);
    position: sticky; top: 0; z-index: 100; box-shadow: 0 2px 10px rgba(0,0,0,.3);
}
.rn-header-inner {
    max-width: 960px; margin: 0 auto; padding: 0 1.5rem;
    display: flex; align-items: center; justify-content: space-between; gap: 1rem;
}
.rn-brand { display: flex; align-items: center; gap: .85rem; padding: .75rem 0; }
.rn-logo { background: var(--red); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.rn-logo span { font-size: 10px; font-weight: bold; color: var(--white); letter-spacing: .06em; text-align: center; line-height: 1.15; text-transform: uppercase; }
.rn-org { font-size: 14px; font-weight: bold; color: var(--white); letter-spacing: .04em; text-transform: uppercase; }
.rn-sub { font-size: 11px; color: rgba(255,255,255,.5); margin-top: 2px; text-transform: uppercase; letter-spacing: .04em; }
.rn-back { font-size: 12px; font-weight: bold; color: rgba(255,255,255,.8); text-decoration: none; border: 1px solid rgba(255,255,255,.25); padding: .35rem .9rem; transition: all .15s; white-space: nowrap; }
.rn-back:hover { background: rgba(255,255,255,.1); color: var(--white); }

/* ─── PAGE BAND ─── */
.page-band { background: var(--white); border-bottom: 1px solid var(--grey-mid); box-shadow: var(--shadow-sm); }
.page-band-inner { max-width: 960px; margin: 0 auto; padding: 1.25rem 1.5rem; }
.page-eyebrow { font-size: 10px; font-weight: bold; color: var(--red); text-transform: uppercase; letter-spacing: .18em; margin-bottom: .3rem; display: flex; align-items: center; gap: .45rem; }
.page-eyebrow::before { content: ''; width: 14px; height: 2px; background: var(--red); display: inline-block; }
.page-title { font-size: 22px; font-weight: bold; color: var(--navy); line-height: 1; }
.page-desc  { font-size: 13px; color: var(--text-muted); margin-top: .4rem; max-width: 52rem; }

/* ─── WRAP ─── */
.wrap { max-width: 960px; margin: 0 auto; padding: 1.5rem 1.5rem 4rem; }

/* ─── ALERT ─── */
.alert-success {
    display: flex; align-items: center; gap: .6rem; margin-bottom: 1.25rem;
    padding: .65rem 1rem; background: var(--green-bg);
    border: 1px solid #b8ddc9; border-left: 3px solid var(--green);
    font-size: 13px; font-weight: bold; color: var(--green);
}
.alert-error {
    margin: .85rem 1.2rem 0; padding: .6rem .85rem;
    background: var(--red-faint); border: 1px solid rgba(200,16,46,.25); border-left: 3px solid var(--red);
    font-size: 12px; font-weight: bold; color: var(--red);
}

/* ─── FORM CARD ─── */
.form-card {
    background: var(--white); border: 1px solid var(--grey-mid);
    border-top: 3px solid var(--navy);
    box-shadow: var(--shadow-sm); margin-bottom: 1.75rem;
}
.form-head {
    padding: .8rem 1.2rem; border-bottom: 1px solid var(--grey-mid);
    background: var(--grey); display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;
}
.form-head-title { font-size: 13px; font-weight: bold; color: var(--navy); text-transform: uppercase; letter-spacing: .05em; }
.form-head-sub   { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
.edit-cancel { font-size: 12px; font-weight: bold; color: var(--red); text-decoration: none; text-transform: uppercase; letter-spacing: .04em; transition: opacity .12s; }
.edit-cancel:hover { opacity: .7; }

.form-body {
    padding: 1.1rem; display: grid; gap: .85rem;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
}
.form-field { display: flex; flex-direction: column; gap: .3rem; }
.form-field label { font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: .1em; color: var(--text-muted); }
.form-field input {
    background: var(--white); border: 1px solid var(--grey-mid);
    padding: .5rem .75rem; color: var(--text); font-family: var(--font);
    font-size: 13px; outline: none; width: 100%; transition: border-color .15s, box-shadow .15s;
}
.form-field input:focus { border-color: var(--navy); box-shadow: 0 0 0 3px rgba(0,51,102,.08); }
.form-field input::placeholder { color: var(--grey-dark); }
.form-field-note { font-size: 11px; color: var(--text-muted); margin-top: 3px; line-height: 1.5; }

.colour-input-wrap { position: relative; }
.colour-swatch {
    position: absolute; right: .65rem; top: 50%; transform: translateY(-50%);
    width: 18px; height: 18px; border: 1px solid var(--grey-mid);
    pointer-events: none; transition: background .15s;
}

.form-footer {
    padding: .85rem 1.2rem; border-top: 1px solid var(--grey-mid);
    background: var(--grey); display: flex; align-items: center; gap: .75rem;
}

/* ─── BUTTONS ─── */
.btn {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .48rem 1.2rem; border: 1px solid; font-family: var(--font);
    font-size: 12px; font-weight: bold; cursor: pointer; transition: all .12s;
    white-space: nowrap; text-transform: uppercase; letter-spacing: .05em;
}
.btn-primary { background: var(--navy); border-color: var(--navy); color: var(--white); }
.btn-primary:hover { background: var(--navy-mid); }
.btn-green   { background: var(--green-bg); border-color: #b8ddc9; color: var(--green); }
.btn-green:hover { background: #d6ede3; border-color: var(--green); }

/* ─── TABLE ─── */
.table-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: .85rem; gap: 1rem; flex-wrap: wrap;
}
.table-title { font-size: 14px; font-weight: bold; color: var(--navy); text-transform: uppercase; letter-spacing: .05em; }
.table-count {
    font-size: 11px; color: var(--text-muted);
    background: var(--white); border: 1px solid var(--grey-mid);
    padding: 2px 9px;
}

.table-card { background: var(--white); border: 1px solid var(--grey-mid); box-shadow: var(--shadow-sm); overflow: hidden; }

table { width: 100%; border-collapse: collapse; font-size: 13px; }
thead { background: var(--navy); border-bottom: 2px solid var(--red); }
thead th {
    padding: .55rem .9rem; text-align: left;
    font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: .12em;
    color: rgba(255,255,255,.75); white-space: nowrap;
}
thead th:last-child { text-align: right; }
tbody tr { border-top: 1px solid var(--grey-mid); transition: background .1s; }
tbody tr:hover { background: var(--navy-faint); }
td { padding: .7rem .9rem; vertical-align: middle; }
td:last-child { text-align: right; white-space: nowrap; }

.role-name { font-weight: bold; color: var(--text); }

.sort-badge {
    display: inline-flex; align-items: center; justify-content: center;
    width: 26px; height: 26px; background: var(--grey);
    border: 1px solid var(--grey-mid); font-size: 12px; font-weight: bold; color: var(--text-muted);
}

.colour-cell { display: inline-flex; align-items: center; gap: .5rem; font-size: 12px; color: var(--text-muted); font-weight: bold; }
.colour-blob { width: 16px; height: 16px; border: 1px solid var(--grey-mid); flex-shrink: 0; }

.role-pill {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: 2px 9px; font-size: 11px; font-weight: bold;
    border: 1px solid; text-transform: uppercase; letter-spacing: .04em;
}
.role-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }

.action-edit {
    font-size: 11px; font-weight: bold; color: var(--navy); text-decoration: none;
    text-transform: uppercase; letter-spacing: .04em; transition: opacity .12s;
}
.action-edit:hover { opacity: .65; }
.action-sep { color: var(--grey-mid); margin: 0 .35rem; }
.action-delete {
    font-size: 11px; font-weight: bold; color: var(--red);
    background: none; border: none; cursor: pointer; padding: 0;
    text-transform: uppercase; letter-spacing: .04em; transition: opacity .12s;
}
.action-delete:hover { opacity: .65; }

.empty-state { padding: 3rem 1rem; text-align: center; }
.empty-icon  { font-size: 2rem; opacity: .2; margin-bottom: .75rem; }
.empty-text  { font-size: 13px; color: var(--text-muted); }

/* ─── ANIMATIONS ─── */
@keyframes fadeUp { from { opacity:0; transform:translateY(5px); } to { opacity:1; transform:none; } }
.fade-in { animation: fadeUp .3s ease both; }

/* ─── RESPONSIVE ─── */
@media(max-width:600px) {
    .form-body { grid-template-columns: 1fr; }
    thead th:nth-child(3), td:nth-child(3) { display: none; } /* hide colour col */
}
</style>

{{-- ─── HEADER ─── --}}
<header class="rn-header fade-in">
    <div class="rn-header-inner">
        <div class="rn-brand">
            <div class="rn-logo"><span>RAY<br>NET</span></div>
            <div>
                <div class="rn-org">Liverpool RAYNET</div>
                <div class="rn-sub">Admin · Manage Roles</div>
            </div>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="rn-back">← Back to admin</a>
    </div>
</header>

{{-- ─── PAGE BAND ─── --}}
<div class="page-band fade-in">
    <div class="page-band-inner">
        <div class="page-eyebrow">Admin Panel</div>
        <h1 class="page-title">Manage Roles</h1>
        <p class="page-desc">Controlled list of roles — Group Controller, Secretary, Treasurer, Operator etc. Operators pick from this list so everything stays consistent across the site.</p>
    </div>
</div>

<div class="wrap">

    {{-- Alert --}}
    @if (session('status'))
        <div class="alert-success fade-in">✓ {{ session('status') }}</div>
    @endif

    {{-- ─── ADD / EDIT FORM ─── --}}
    <div class="form-card fade-in">
        <div class="form-head">
            <div>
                <div class="form-head-title">
                    {{ isset($editingRole) ? '✏ Edit Role' : '+ Add Role' }}
                </div>
                @if (isset($editingRole))
                    <div class="form-head-sub">Editing: {{ $editingRole->name }}</div>
                @endif
            </div>
            @if (isset($editingRole))
                <a href="{{ route('admin.roles') }}" class="edit-cancel">✕ Cancel</a>
            @endif
        </div>

        @if ($errors->any())
            <div class="alert-error">⚠ {{ $errors->first() }}</div>
        @endif

        <form method="POST"
              action="{{ isset($editingRole) ? route('admin.roles.update', $editingRole->id) : route('admin.roles.store') }}">
            @csrf
            @if (isset($editingRole))
                @method('PUT')
            @endif

            <div class="form-body">
                <div class="form-field">
                    <label>Role name *</label>
                    <input name="name" type="text"
                           value="{{ old('name', $editingRole->name ?? '') }}"
                           placeholder="e.g. Group Controller, Operator…">
                </div>

                <div class="form-field">
                    <label>Sort order</label>
                    <input name="sort_order" type="number"
                           value="{{ old('sort_order', $editingRole->sort_order ?? 0) }}"
                           min="0" step="1">
                    <div class="form-field-note">Lower numbers appear earlier in dropdowns.</div>
                </div>

                <div class="form-field">
                    <label>Badge colour</label>
                    <div class="colour-input-wrap">
                        <input name="colour" type="text" id="colourInput"
                               value="{{ old('colour', $editingRole->colour ?? '') }}"
                               placeholder="#22c55e"
                               oninput="updateSwatch(this.value)"
                               style="padding-right:2.2rem;">
                        <div class="colour-swatch" id="colourSwatch"
                             style="background:{{ old('colour', $editingRole->colour ?? 'transparent') }};"></div>
                    </div>
                    <div class="form-field-note">Optional hex e.g. <strong>#22c55e</strong>. Leave blank for default styling.</div>
                </div>
            </div>

            <div class="form-footer">
                <button type="submit" class="{{ isset($editingRole) ? 'btn btn-primary' : 'btn btn-green' }}">
                    {{ isset($editingRole) ? '✓ Update Role' : '+ Save Role' }}
                </button>
            </div>
        </form>
    </div>

    {{-- ─── ROLES TABLE ─── --}}
    <div class="fade-in">
        <div class="table-header">
            <div style="display:flex;align-items:center;gap:.65rem;">
                <span class="table-title">Role List</span>
                @if ($roles->isNotEmpty())
                    <span class="table-count">{{ $roles->count() }} {{ Str::plural('role', $roles->count()) }}</span>
                @endif
            </div>
        </div>

        <div class="table-card">
            @if ($roles->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon">🗂️</div>
                    <div class="empty-text">No roles yet. Add the first one above — it will appear in the operators dropdown immediately.</div>
                </div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Role</th>
                            <th>Sort</th>
                            <th>Colour</th>
                            <th>Preview</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                        @php $colour = $role->colour ?: null; @endphp
                        <tr>
                            <td class="role-name">{{ $role->name }}</td>

                            <td>
                                <span class="sort-badge">{{ $role->sort_order }}</span>
                            </td>

                            <td>
                                @if ($colour)
                                    <div class="colour-cell">
                                        <span class="colour-blob" style="background:{{ $colour }};"></span>
                                        {{ $colour }}
                                    </div>
                                @else
                                    <span style="font-size:11px;color:var(--text-muted);">Default</span>
                                @endif
                            </td>

                            <td>
                                @if ($colour)
                                    <span class="role-pill"
                                          style="background:{{ $colour }}1a;border-color:{{ $colour }};color:{{ $colour }};">
                                        <span class="role-dot" style="background:{{ $colour }};"></span>
                                        {{ $role->name }}
                                    </span>
                                @else
                                    <span class="role-pill"
                                          style="background:var(--navy-faint);border-color:rgba(0,51,102,.2);color:var(--navy);">
                                        {{ $role->name }}
                                    </span>
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('admin.roles', ['edit' => $role->id]) }}" class="action-edit">Edit</a>
                                <span class="action-sep">·</span>
                                <form method="POST"
                                      action="{{ route('admin.roles.delete', $role->id) }}"
                                      style="display:inline;"
                                      onsubmit="return confirm('Delete role \'{{ addslashes($role->name) }}\'? This won\'t change existing operator records but removes it from the dropdown.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-delete">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

</div>

<script>
function updateSwatch(val) {
    const swatch = document.getElementById('colourSwatch');
    swatch.style.background = val.match(/^#([0-9a-f]{3}|[0-9a-f]{6})$/i) ? val : 'transparent';
}
</script>

@endsection