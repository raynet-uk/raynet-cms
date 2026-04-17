@extends('layouts.admin')
@section('title', 'Edit Activity Log — Admin')
@section('content')

<style>
:root {
    --navy:      #003366;
    --navy-mid:  #004080;
    --navy-faint:#e8eef5;
    --red:       #C8102E;
    --red-faint: #fdf0f2;
    --white:     #FFFFFF;
    --grey:      #F2F2F2;
    --grey-mid:  #dde2e8;
    --grey-dark: #9aa3ae;
    --text:      #001f40;
    --text-mid:  #2d4a6b;
    --text-muted:#6b7f96;
    --green:     #1a6b3c;
    --green-bg:  #eef7f2;
    --amber:     #8a5500;
    --amber-bg:  #fdf8ec;
    --font: Arial, 'Helvetica Neue', Helvetica, sans-serif;
    --shadow-sm: 0 1px 3px rgba(0,51,102,.09);
    --shadow-md: 0 4px 14px rgba(0,51,102,.11);
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { background: var(--grey); color: var(--text); font-family: var(--font); font-size: 14px; min-height: 100vh; }

.rn-header { background: var(--navy); border-bottom: 4px solid var(--red); position: sticky; top: 0; z-index: 100; box-shadow: 0 2px 10px rgba(0,0,0,.3); }
.rn-header-inner { max-width: 900px; margin: 0 auto; padding: 0 1.5rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; }
.rn-brand { display: flex; align-items: center; gap: .85rem; padding: .75rem 0; }
.rn-logo-block { background: var(--red); width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.rn-logo-block span { font-size: 11px; font-weight: bold; color: #fff; letter-spacing: .06em; text-align: center; line-height: 1.15; text-transform: uppercase; }
.rn-org { font-size: 15px; font-weight: bold; color: #fff; letter-spacing: .04em; text-transform: uppercase; }
.rn-sub { font-size: 11px; color: rgba(255,255,255,.55); margin-top: 2px; letter-spacing: .05em; text-transform: uppercase; }
.rn-back { font-size: 12px; font-weight: bold; color: rgba(255,255,255,.8); text-decoration: none; border: 1px solid rgba(255,255,255,.25); padding: .35rem .9rem; transition: all .15s; }
.rn-back:hover { background: rgba(255,255,255,.1); color: #fff; }

.page-band { background: var(--white); border-bottom: 1px solid var(--grey-mid); box-shadow: var(--shadow-sm); margin-bottom: 2rem; }
.page-band-inner { max-width: 900px; margin: 0 auto; padding: 1.25rem 1.5rem; display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
.page-eyebrow { font-size: 11px; font-weight: bold; color: var(--red); text-transform: uppercase; letter-spacing: .18em; margin-bottom: .3rem; display: flex; align-items: center; gap: .5rem; }
.page-eyebrow::before { content: ''; width: 16px; height: 2px; background: var(--red); display: inline-block; }
.page-title { font-size: 24px; font-weight: bold; color: var(--navy); line-height: 1; }
.page-desc { font-size: 13px; color: var(--text-muted); margin-top: .35rem; }

.wrap { max-width: 900px; margin: 0 auto; padding: 0 1.5rem 4rem; }

/* Meta strip */
.meta-strip { display: flex; gap: 1.5rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
.meta-item { display: flex; flex-direction: column; gap: 2px; }
.meta-label { font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: .12em; color: var(--text-muted); }
.meta-value { font-size: 12px; font-weight: bold; color: var(--text-mid); }

.card { background: var(--white); border: 1px solid var(--grey-mid); border-top: 3px solid var(--navy); box-shadow: var(--shadow-sm); }
.card-head { padding: .8rem 1.25rem; background: var(--grey); border-bottom: 1px solid var(--grey-mid); display: flex; align-items: center; justify-content: space-between; gap: .65rem; }
.card-head-left { display: flex; align-items: center; gap: .65rem; }
.card-head-icon { width: 30px; height: 30px; background: var(--navy-faint); border: 1px solid rgba(0,51,102,.15); display: flex; align-items: center; justify-content: center; font-size: .9rem; flex-shrink: 0; }
.card-head h2 { font-size: 12px; font-weight: bold; color: var(--navy); text-transform: uppercase; letter-spacing: .08em; }
.entry-id { font-size: 11px; font-weight: bold; color: var(--text-muted); background: var(--grey-mid); padding: 2px 8px; }
.card-body { padding: 1.5rem 1.25rem; }

.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
@media (max-width: 600px) { .form-grid { grid-template-columns: 1fr; } }
.span-2 { grid-column: span 2; }
@media (max-width: 600px) { .span-2 { grid-column: span 1; } }

.form-section { font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: .15em; color: var(--text-muted); padding-bottom: .4rem; margin-bottom: .75rem; margin-top: 1.25rem; border-bottom: 1px solid var(--grey-mid); grid-column: 1 / -1; }
.form-section:first-child { margin-top: 0; }

.field { display: flex; flex-direction: column; gap: .3rem; }
.field label { font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: .1em; color: var(--text-muted); display: flex; align-items: center; gap: .3rem; }
.field label .req { color: var(--red); font-size: 13px; line-height: 1; }
.field label .opt { font-size: 10px; color: var(--grey-dark); font-weight: normal; text-transform: none; letter-spacing: 0; }
.input-wrap { position: relative; }
.input-icon { position: absolute; left: .75rem; top: 50%; transform: translateY(-50%); font-size: .85rem; color: var(--text-muted); pointer-events: none; }
.field input,
.field select,
.field textarea {
    width: 100%; padding: .52rem .75rem .52rem 2.1rem;
    border: 1px solid var(--grey-mid); background: var(--white); color: var(--text);
    font-family: var(--font); font-size: 13px; outline: none;
    transition: border-color .15s, box-shadow .15s;
}
.field textarea { padding-left: 2.1rem; resize: vertical; min-height: 80px; }
.field input:focus,
.field select:focus,
.field textarea:focus { border-color: var(--navy); box-shadow: 0 0 0 3px rgba(0,51,102,.08); }
.field-hint { font-size: 11px; color: var(--text-muted); margin-top: 1px; }
.field-error { font-size: 11px; color: var(--red); font-weight: bold; margin-top: 2px; }

.form-footer { padding: 1rem 1.25rem; border-top: 1px solid var(--grey-mid); background: var(--grey); display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; }
.form-footer-btns { display: flex; gap: .6rem; }
.btn-submit { padding: .52rem 1.4rem; background: var(--navy); color: var(--white); border: 1px solid var(--navy); font-family: var(--font); font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: .06em; cursor: pointer; transition: background .12s, box-shadow .12s; display: inline-flex; align-items: center; gap: .4rem; }
.btn-submit:hover { background: var(--navy-mid); box-shadow: 0 4px 12px rgba(0,51,102,.18); }
.btn-cancel { padding: .52rem 1.1rem; background: var(--white); color: var(--text-muted); border: 1px solid var(--grey-mid); font-family: var(--font); font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: .06em; text-decoration: none; display: inline-flex; align-items: center; transition: all .12s; }
.btn-cancel:hover { border-color: var(--navy); color: var(--navy); }
.btn-delete { padding: .52rem 1.1rem; background: var(--white); color: var(--red); border: 1px solid rgba(200,16,46,.3); font-family: var(--font); font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: .06em; cursor: pointer; display: inline-flex; align-items: center; transition: all .12s; margin-left: auto; }
.btn-delete:hover { background: var(--red-faint); border-color: var(--red); }

.error-alert { display: flex; align-items: flex-start; gap: .65rem; padding: .75rem 1rem; margin-bottom: 1.25rem; background: var(--red-faint); border: 1px solid rgba(200,16,46,.25); border-left: 3px solid var(--red); color: var(--red); font-size: 12px; }
.error-alert ul { margin: .3rem 0 0 1rem; padding: 0; }
.error-alert li { margin: .2rem 0; }

@keyframes fadeUp { from { opacity:0; transform:translateY(5px); } to { opacity:1; transform:none; } }
.fade-in { animation: fadeUp .3s ease both; }
</style>

<header class="rn-header">
    <div class="rn-header-inner">
        <div class="rn-brand">
            <div class="rn-logo-block"><span>RAY<br>NET</span></div>
            <div>
                <div class="rn-org">{{ \App\Helpers\RaynetSetting::groupName() }}</div>
                <div class="rn-sub">Admin · Activity Logs</div>
            </div>
        </div>
        <a href="{{ route('admin.activity-logs.index') }}" class="rn-back">← Back to logs</a>
    </div>
</header>

<div class="page-band fade-in">
    <div class="page-band-inner">
        <div>
            <div class="page-eyebrow">Activity Logs</div>
            <h1 class="page-title">Edit Log Entry <span style="color:var(--text-muted);font-weight:normal;font-size:18px;">#{{ $activityLog->id }}</span></h1>
            <p class="page-desc">Update the details for this activity log entry.</p>
        </div>
        <div style="font-size:11px;color:var(--text-muted);background:var(--grey);border:1px solid var(--grey-mid);padding:.28rem .7rem;">
            {{ now()->format('D d M Y · H:i') }}
        </div>
    </div>
</div>

<div class="wrap">

    {{-- Meta strip --}}
    <div class="meta-strip fade-in">
        <div class="meta-item">
            <div class="meta-label">Entry ID</div>
            <div class="meta-value">#{{ $activityLog->id }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Created</div>
            <div class="meta-value">{{ $activityLog->created_at->format('d M Y · H:i') }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Last Updated</div>
            <div class="meta-value">{{ $activityLog->updated_at->format('d M Y · H:i') }}</div>
        </div>
        @if ($activityLog->loggedByUser)
        <div class="meta-item">
            <div class="meta-label">Logged By</div>
            <div class="meta-value">{{ $activityLog->loggedByUser->name }}</div>
        </div>
        @endif
    </div>

    @if ($errors->any())
        <div class="error-alert fade-in">
            <div>
                <strong>✕ Please fix the following:</strong>
                <ul>
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="card fade-in">
        <div class="card-head">
            <div class="card-head-left">
                <div class="card-head-icon">📋</div>
                <h2>Activity Log Details</h2>
            </div>
            <span class="entry-id">Entry #{{ $activityLog->id }}</span>
        </div>

        <form method="POST" action="{{ route('admin.activity-logs.update', $activityLog) }}">
            @csrf
            @method('PUT')

            <div class="card-body">
                <div class="form-grid">

                    <div class="form-section">Event Details</div>

                    {{-- Member --}}
                    <div class="field">
                        <label for="user_id">Member <span class="req">*</span></label>
                        <div class="input-wrap">
                            <span class="input-icon">👤</span>
                            <select id="user_id" name="user_id" required>
                                <option value="">— Select member —</option>
                                @foreach ($users as $u)
                                    <option value="{{ $u->id }}"
                                        {{ old('user_id', $activityLog->user_id) == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('user_id')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    {{-- Hours --}}
                    <div class="field">
                        <label for="hours">Volunteer Hours <span class="req">*</span></label>
                        <div class="input-wrap">
                            <span class="input-icon">⏱</span>
                            <input type="number" id="hours" name="hours"
                                   value="{{ old('hours', $activityLog->hours) }}"
                                   min="0.5" max="24" step="0.5"
                                   placeholder="e.g. 3.5" required>
                        </div>
                        <div class="field-hint">Minimum 0.5 hours, maximum 24.</div>
                        @error('hours')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    {{-- Event Name --}}
                    <div class="field span-2">
                        <label for="event_name">Event Name <span class="req">*</span></label>
                        <div class="input-wrap">
                            <span class="input-icon">📅</span>
                            <input type="text" id="event_name" name="event_name"
                                   value="{{ old('event_name', $activityLog->event_name) }}"
                                   placeholder="e.g. Mersey Marathon 2026" required>
                        </div>
                        @error('event_name')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    {{-- Event Date --}}
                    <div class="field">
                        <label for="event_date">Event Date <span class="req">*</span></label>
                        <div class="input-wrap">
                            <span class="input-icon">🗓</span>
                            <input type="date" id="event_date" name="event_date"
                                   value="{{ old('event_date', \Carbon\Carbon::parse($activityLog->event_date)->format('Y-m-d')) }}"
                                   required>
                        </div>
                        @error('event_date')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    {{-- Notes --}}
                    <div class="field span-2">
                        <label for="notes">Notes <span class="opt">(optional)</span></label>
                        <div class="input-wrap">
                            <span class="input-icon" style="top:1rem;transform:none;">📝</span>
                            <textarea id="notes" name="notes"
                                      placeholder="Any additional notes about this activity…">{{ old('notes', $activityLog->notes ?? '') }}</textarea>
                        </div>
                        @error('notes')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                </div>
            </div>

            <div class="form-footer">
                <div class="form-footer-btns">
                    <a href="{{ route('admin.activity-logs.index') }}" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-submit">✓ Update Entry</button>
                </div>

                {{-- Delete — separate form to avoid nesting --}}
                <div style="margin-left:auto;">
                    <button type="button" class="btn-delete"
                            onclick="document.getElementById('deleteForm').submit()">
                        ✕ Delete Entry
                    </button>
                </div>
            </div>

        </form>
    </div>

</div>

{{-- Delete form — outside the update form to avoid nesting --}}
<form id="deleteForm" method="POST" action="{{ route('admin.activity-logs.destroy', $activityLog) }}"
      onsubmit="return confirm('Permanently delete entry #{{ $activityLog->id }}? This cannot be undone.')">
    @csrf
    @method('DELETE')
</form>

@endsection