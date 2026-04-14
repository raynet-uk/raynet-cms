{{-- resources/views/committee/layout.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="committee-wrapper">

    {{-- ── Committee sidebar nav ─────────────────────────────────────────── --}}
    <aside class="committee-sidebar">
        <div class="committee-sidebar__header">
            <div class="committee-sidebar__badge">COMMITTEE</div>
            <p class="committee-sidebar__sub">Operational Management</p>
        </div>

        <nav class="committee-sidebar__nav">

            <a href="{{ route('committee.dashboard') }}"
               class="committee-nav-item {{ request()->routeIs('committee.dashboard') ? 'committee-nav-item--active' : '' }}">
                <svg class="committee-nav-item__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Command Desk
            </a>

            <div class="committee-nav-section">Readiness</div>

            <a href="{{ route('committee.readiness.index') }}"
               class="committee-nav-item {{ request()->routeIs('committee.readiness.*') ? 'committee-nav-item--active' : '' }}">
                <svg class="committee-nav-item__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Readiness & Assurance
            </a>

            <a href="{{ route('committee.readiness.lrf') }}"
               class="committee-nav-item {{ request()->routeIs('committee.readiness.lrf') ? 'committee-nav-item--active' : '' }}">
                <svg class="committee-nav-item__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                LRF Reporting
            </a>

            <div class="committee-nav-section">People & Capability</div>

            <a href="{{ route('committee.people.index') }}"
               class="committee-nav-item {{ request()->routeIs('committee.people.*') ? 'committee-nav-item--active' : '' }}">
                <svg class="committee-nav-item__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                People & Availability
            </a>

            <div class="committee-nav-section">Assets & Systems</div>

            <a href="{{ route('committee.assets.index') }}"
               class="committee-nav-item {{ request()->routeIs('committee.assets.*') ? 'committee-nav-item--active' : '' }}">
                <svg class="committee-nav-item__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Assets & Field Kits
            </a>

            <a href="{{ route('committee.networks.index') }}"
               class="committee-nav-item {{ request()->routeIs('committee.networks.*') ? 'committee-nav-item--active' : '' }}">
                <svg class="committee-nav-item__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                </svg>
                Networks & Systems
            </a>

            <div class="committee-nav-section">Operations</div>

            <a href="{{ route('committee.exercises.index') }}"
               class="committee-nav-item {{ request()->routeIs('committee.exercises.*') ? 'committee-nav-item--active' : '' }}">
                <svg class="committee-nav-item__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Exercises & Deployments
            </a>

            <a href="{{ route('committee.actions.index') }}"
               class="committee-nav-item {{ request()->routeIs('committee.actions.*') ? 'committee-nav-item--active' : '' }}">
                <svg class="committee-nav-item__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                Actions
            </a>

            <a href="{{ route('committee.risks.index') }}"
               class="committee-nav-item {{ request()->routeIs('committee.risks.*') ? 'committee-nav-item--active' : '' }}">
                <svg class="committee-nav-item__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Risks & Issues
            </a>

        </nav>
    </aside>

    {{-- ── Main content area ────────────────────────────────────────────────── --}}
    <main class="committee-main">

        @if(session('success'))
            <div class="alert alert--success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert--error">{{ session('error') }}</div>
        @endif

        @yield('committee-content')

    </main>

</div>
@endsection

@push('styles')
<style>
:root {
    --raynet-navy:  #003366;
    --raynet-red:   #C8102E;
    --raynet-grey:  #F2F2F2;
    --raynet-white: #FFFFFF;
    --status-green:  #16a34a;
    --status-amber:  #d97706;
    --status-red:    #dc2626;
    --status-blue:   #2563eb;
    --status-orange: #ea580c;
    --sidebar-w: 240px;
}

.committee-wrapper {
    display: flex;
    min-height: calc(100vh - 64px);
    font-family: Arial, sans-serif;
}

/* Sidebar */
.committee-sidebar {
    width: var(--sidebar-w);
    flex-shrink: 0;
    background: var(--raynet-navy);
    color: #fff;
    display: flex;
    flex-direction: column;
}
.committee-sidebar__header {
    padding: 20px 16px 16px;
    border-bottom: 1px solid rgba(255,255,255,.15);
}
.committee-sidebar__badge {
    display: inline-block;
    background: var(--raynet-red);
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .08em;
    padding: 3px 8px;
    border-radius: 3px;
    margin-bottom: 4px;
}
.committee-sidebar__sub {
    font-size: 11px;
    color: rgba(255,255,255,.6);
    margin: 0;
}
.committee-sidebar__nav {
    flex: 1;
    padding: 8px 0 16px;
    overflow-y: auto;
}
.committee-nav-section {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .1em;
    color: rgba(255,255,255,.4);
    text-transform: uppercase;
    padding: 12px 16px 4px;
}
.committee-nav-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 16px;
    font-size: 13px;
    color: rgba(255,255,255,.85);
    text-decoration: none;
    transition: background .15s;
}
.committee-nav-item:hover {
    background: rgba(255,255,255,.08);
    color: #fff;
    text-decoration: none;
}
.committee-nav-item--active {
    background: rgba(255,255,255,.14);
    color: #fff;
    font-weight: 600;
    border-right: 3px solid var(--raynet-red);
}
.committee-nav-item__icon {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
    opacity: .8;
}

/* Main */
.committee-main {
    flex: 1;
    background: #f8f9fa;
    padding: 28px 32px;
    overflow-x: hidden;
}

/* Page header */
.committee-page-header {
    margin-bottom: 24px;
}
.committee-page-header h1 {
    font-size: 22px;
    font-weight: 700;
    color: var(--raynet-navy);
    margin: 0 0 4px;
    font-family: Arial, sans-serif;
}
.committee-page-header p {
    font-size: 13px;
    color: #6b7280;
    margin: 0;
}

/* Metric cards */
.metric-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 14px;
    margin-bottom: 28px;
}
.metric-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 16px;
}
.metric-card__label {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: .05em;
    text-transform: uppercase;
    color: #9ca3af;
    margin-bottom: 6px;
}
.metric-card__value {
    font-size: 28px;
    font-weight: 700;
    color: var(--raynet-navy);
    line-height: 1;
}
.metric-card__sub {
    font-size: 11px;
    color: #6b7280;
    margin-top: 4px;
}
.metric-card--red    { border-left: 4px solid var(--status-red); }
.metric-card--amber  { border-left: 4px solid var(--status-amber); }
.metric-card--green  { border-left: 4px solid var(--status-green); }
.metric-card--blue   { border-left: 4px solid var(--status-blue); }
.metric-card--navy   { border-left: 4px solid var(--raynet-navy); }

/* Section panels */
.committee-panel {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin-bottom: 20px;
    overflow: hidden;
}
.committee-panel__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 20px;
    border-bottom: 1px solid #f3f4f6;
    background: #fff;
}
.committee-panel__title {
    font-size: 14px;
    font-weight: 700;
    color: var(--raynet-navy);
    margin: 0;
}
.committee-panel__body { padding: 20px; }

/* Data table */
.committee-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.committee-table th {
    background: var(--raynet-grey);
    text-align: left;
    padding: 9px 12px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .05em;
    text-transform: uppercase;
    color: #6b7280;
    border-bottom: 1px solid #e5e7eb;
}
.committee-table td {
    padding: 10px 12px;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}
.committee-table tr:last-child td { border-bottom: none; }
.committee-table tr:hover td { background: #f9fafb; }

/* Status pills */
.pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 999px;
}
.pill--green  { background: #dcfce7; color: #15803d; }
.pill--amber  { background: #fef9c3; color: #92400e; }
.pill--red    { background: #fee2e2; color: #b91c1c; }
.pill--blue   { background: #dbeafe; color: #1d4ed8; }
.pill--orange { background: #ffedd5; color: #c2410c; }
.pill--grey   { background: #f3f4f6; color: #6b7280; }

/* Alerts */
.alert {
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 16px;
    font-size: 14px;
}
.alert--success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.alert--error   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

/* Forms */
.committee-form { max-width: 680px; }
.form-group { margin-bottom: 16px; }
.form-group label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--raynet-navy);
    margin-bottom: 4px;
}
.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 8px 12px;
    font-size: 13px;
    font-family: Arial, sans-serif;
    background: #fff;
    color: #111827;
    transition: border-color .15s;
}
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--raynet-navy);
    box-shadow: 0 0 0 3px rgba(0,51,102,.1);
}
.form-group .hint {
    font-size: 11px;
    color: #9ca3af;
    margin-top: 3px;
}
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 18px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    font-family: Arial, sans-serif;
    cursor: pointer;
    border: none;
    transition: opacity .15s;
    text-decoration: none;
}
.btn:hover { opacity: .88; text-decoration: none; }
.btn--primary   { background: var(--raynet-navy); color: #fff; }
.btn--danger    { background: var(--raynet-red);  color: #fff; }
.btn--secondary { background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; }
.btn--sm { padding: 5px 12px; font-size: 12px; }

/* Score sliders */
.score-row {
    display: grid;
    grid-template-columns: 80px 1fr 280px;
    align-items: start;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid #f3f4f6;
}
.score-row:last-child { border-bottom: none; }
.score-code {
    font-size: 12px;
    font-weight: 700;
    color: var(--raynet-navy);
    font-family: 'Courier New', monospace;
    padding-top: 2px;
}
.score-indicator-name {
    font-size: 12px;
    color: #374151;
    line-height: 1.4;
}
.score-indicator-evidence {
    font-size: 11px;
    color: #9ca3af;
    margin-top: 2px;
    font-style: italic;
}
.score-input-area { display: flex; flex-direction: column; gap: 6px; }
.score-buttons {
    display: flex;
    gap: 4px;
}
.score-btn {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    border: 2px solid #e5e7eb;
    background: #fff;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all .1s;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #374151;
}
.score-btn:hover { border-color: var(--raynet-navy); color: var(--raynet-navy); }
.score-btn.active-0 { background: #fee2e2; border-color: var(--status-red);   color: var(--status-red); }
.score-btn.active-1 { background: #ffedd5; border-color: #f97316; color: #f97316; }
.score-btn.active-2 { background: #fef9c3; border-color: var(--status-amber); color: var(--status-amber); }
.score-btn.active-3 { background: #dbeafe; border-color: var(--status-blue);  color: var(--status-blue); }
.score-btn.active-4 { background: #d1fae5; border-color: #059669; color: #059669; }
.score-btn.active-5 { background: #dcfce7; border-color: var(--status-green); color: var(--status-green); }
.score-evidence-mini {
    display: flex;
    gap: 6px;
}
.score-evidence-mini input {
    flex: 1;
    font-size: 11px;
    padding: 4px 8px;
    border: 1px solid #d1d5db;
    border-radius: 5px;
    font-family: Arial, sans-serif;
}
.score-evidence-mini input[type=date] { flex: 0 0 130px; }
.score-evidence-mini button {
    padding: 4px 10px;
    font-size: 11px;
    background: var(--raynet-navy);
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-family: Arial, sans-serif;
}

/* Risk matrix */
.risk-matrix {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    grid-template-rows: repeat(5, 1fr);
    gap: 3px;
    width: 200px;
}
.risk-matrix-cell {
    height: 36px;
    border-radius: 3px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
    color: #fff;
}

/* Progress bar */
.progress-bar { background: #e5e7eb; border-radius: 999px; height: 8px; overflow: hidden; }
.progress-bar__fill { height: 100%; border-radius: 999px; transition: width .3s; }
.progress-bar__fill--green  { background: var(--status-green); }
.progress-bar__fill--amber  { background: var(--status-amber); }
.progress-bar__fill--red    { background: var(--status-red); }
.progress-bar__fill--navy   { background: var(--raynet-navy); }

@media (max-width: 900px) {
    .committee-sidebar { display: none; }
    .committee-main { padding: 16px; }
}
</style>
@endpush
