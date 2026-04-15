<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access Restricted — {{ \App\Helpers\RaynetSetting::groupName() }}</title>
    <style>
        :root {
            --navy:       #003366;
            --navy-light: #e8eef5;
            --red:        #C8102E;
            --red-light:  #fdf0f2;
            --grey-bg:    #f5f5f7;
            --grey-mid:   #d2d2d7;
            --grey-dark:  #86868b;
            --text:       #1d1d1f;
            --text-mid:   #3a3a3c;
            --text-muted: #6e6e73;
            --white:      #ffffff;
            --font:       Arial, 'Helvetica Neue', Helvetica, sans-serif;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { -webkit-font-smoothing: antialiased; }

        body {
            font-family: var(--font);
            background: var(--grey-bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
        }

        /* ── LOGO / BRAND MARK ── */
        .brand {
            display: flex;
            align-items: center;
            gap: .65rem;
            margin-bottom: 2.5rem;
            animation: fadeDown .5s cubic-bezier(.16,1,.3,1) both;
        }
        .brand-logo {
            width: 38px; height: 38px;
            background: var(--navy);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .brand-logo span {
            font-size: 9px; font-weight: bold; color: white;
            letter-spacing: .06em; text-align: center;
            line-height: 1.2; text-transform: uppercase;
        }
        .brand-name {
            font-size: 15px; font-weight: bold;
            color: var(--navy); letter-spacing: .02em;
        }
        .brand-divider {
            width: 1px; height: 18px;
            background: var(--grey-mid);
        }
        .brand-sub {
            font-size: 13px; color: var(--grey-dark);
        }

        /* ── CARD ── */
        .card {
            background: var(--white);
            border-radius: 18px;
            width: 100%;
            max-width: 480px;
            overflow: hidden;
            box-shadow:
                0 2px 4px rgba(0,0,0,.04),
                0 8px 24px rgba(0,0,0,.08),
                0 0 0 0.5px rgba(0,0,0,.08);
            animation: fadeUp .55s .05s cubic-bezier(.16,1,.3,1) both;
        }

        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: none; }
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: none; }
        }

        /* ── CARD TOP ACCENT ── */
        .card-accent {
            height: 4px;
            background: var(--card-color, var(--navy));
        }

        /* ── ICON AREA ── */
        .card-icon-wrap {
            display: flex;
            justify-content: center;
            padding: 2.5rem 2rem 0;
        }
        .card-icon {
            width: 72px; height: 72px;
            border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            font-size: 32px;
            background: var(--icon-bg, #e8eef5);
            flex-shrink: 0;
        }

        /* ── CARD BODY ── */
        .card-body {
            padding: 1.5rem 2rem 2rem;
            text-align: center;
        }

        .error-code {
            font-size: 11px; font-weight: bold;
            text-transform: uppercase; letter-spacing: .12em;
            color: var(--card-color, var(--navy));
            margin-bottom: .65rem;
            opacity: .7;
        }

        h1 {
            font-size: 22px; font-weight: bold;
            color: var(--text); line-height: 1.25;
            margin-bottom: .75rem;
            letter-spacing: -.01em;
        }

        .description {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.65;
            margin-bottom: .65rem;
            max-width: 360px;
            margin-left: auto;
            margin-right: auto;
        }
        .description strong { color: var(--text-mid); font-weight: bold; }

        /* ── BADGE (role label) ── */
        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            font-size: 11px; font-weight: bold;
            padding: 3px 10px;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: .07em;
            margin-bottom: 1.1rem;
            border: 1px solid;
        }
        .role-badge-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: currentColor; flex-shrink: 0;
        }

        /* ── CLEARANCE ROW ── */
        .clearance-row {
            display: inline-flex;
            align-items: center;
            gap: .6rem;
            background: var(--grey-bg);
            border: 0.5px solid var(--grey-mid);
            border-radius: 10px;
            padding: .6rem 1rem;
            margin-top: .5rem;
            margin-bottom: 1.25rem;
        }
        .clearance-chip {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            font-size: 11px; font-weight: bold;
            padding: 3px 9px;
            border-radius: 6px;
            border: 0.5px solid;
            text-transform: uppercase;
            letter-spacing: .06em;
        }
        .clearance-arrow {
            font-size: 12px;
            color: var(--grey-mid);
        }

        /* ── DIVIDER ── */
        .divider {
            height: 0.5px;
            background: var(--grey-mid);
            margin: 1.5rem 0;
            opacity: .6;
        }

        /* ── BUTTONS ── */
        .btn-group {
            display: flex;
            flex-direction: column;
            gap: .65rem;
        }
        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            width: 100%;
            padding: .7rem 1.2rem;
            border-radius: 10px;
            font-family: var(--font);
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: opacity .15s, filter .15s;
            letter-spacing: -.01em;
        }
        .btn:hover { filter: brightness(1.06); }
        .btn:active { filter: brightness(.96); }

        .btn-primary {
            background: var(--navy);
            color: white;
        }
        .btn-secondary {
            background: var(--navy-light);
            color: var(--navy);
        }
        .btn-red {
            background: var(--red);
            color: white;
        }
        .btn-red-light {
            background: var(--red-light);
            color: var(--red);
        }
        .btn-orange {
            background: #fff7ed;
            color: #ea580c;
        }

        /* ── IMPERSONATION NOTICE ── */
        .impersonate-notice {
            background: #fff7ed;
            border: 0.5px solid rgba(234,88,12,.25);
            border-radius: 10px;
            padding: .85rem 1rem;
            margin-bottom: 1.25rem;
            text-align: left;
        }
        .impersonate-notice-title {
            font-size: 13px; font-weight: bold;
            color: #c2410c; margin-bottom: .2rem;
        }
        .impersonate-notice-body {
            font-size: 12px; color: #9a3412; line-height: 1.5;
        }

        /* ── FOOTER ── */
        .page-footer {
            margin-top: 1.75rem;
            font-size: 11px;
            color: var(--grey-dark);
            text-align: center;
            line-height: 1.7;
            animation: fadeUp .5s .15s cubic-bezier(.16,1,.3,1) both;
        }
        .page-footer a {
            color: var(--navy);
            text-decoration: none;
        }
        .page-footer a:hover { text-decoration: underline; }

        @media (max-width: 480px) {
            .card { border-radius: 14px; }
            .card-body { padding: 1.25rem 1.5rem 1.5rem; }
            h1 { font-size: 19px; }
        }
    </style>
</head>
<body>

@php
    // Redirect guests straight to login — nothing to show them here
    if (! auth()->check()) {
        redirect()->route('login')->send();
        exit;
    }

    $authUser        = auth()->user();
    $userRole        = $authUser ? ($authUser->getRoleNames()->first() ?? 'member') : 'guest';
    $isImpersonating = session('original_admin_id');

    $knownRoles   = ['super-admin', 'admin', 'committee', 'member'];
    $exceptionMsg = isset($exception) && $exception->getMessage() ? trim($exception->getMessage()) : null;

    $requiredRole = $requiredRole
        ?? (in_array($exceptionMsg, $knownRoles) ? $exceptionMsg : null)
        ?? null;

    if (! $requiredRole) {
        $path = request()->path();
        if (str_contains($path, 'super'))         $requiredRole = 'super-admin';
        elseif (str_contains($path, 'admin'))     $requiredRole = 'admin';
        elseif (str_contains($path, 'committee')) $requiredRole = 'committee';
    }

    $roleHierarchy = ['guest' => -1, 'member' => 0, 'committee' => 1, 'admin' => 2, 'super-admin' => 3];
    $userLevel     = $roleHierarchy[$userRole]    ?? 0;
    $requiredLevel = $requiredRole ? ($roleHierarchy[$requiredRole] ?? 0) : 999;

    if ($authUser && $requiredRole && $requiredLevel <= $userLevel) {
        $requiredRole  = null;
        $requiredLevel = 999;
    }

    $roleConfig = [
        'super-admin' => [
            'icon'         => '★',
            'label'        => 'Super Admin Only',
            'color'        => '#7c3aed',
            'icon_bg'      => '#f5f3ff',
            'badge_bg'     => '#f5f3ff',
            'badge_border' => 'rgba(91,33,182,.25)',
            'badge_text'   => '#6d28d9',
            'chip_bg'      => '#f5f3ff',
            'chip_border'  => 'rgba(91,33,182,.2)',
            'chip_color'   => '#6d28d9',
            'chip_label'   => 'Super Admin',
            'emoji'        => '⭐️',
        ],
        'admin' => [
            'icon'         => '⚡',
            'label'        => 'Admin Access Required',
            'color'        => '#C8102E',
            'icon_bg'      => '#fdf0f2',
            'badge_bg'     => '#fdf0f2',
            'badge_border' => 'rgba(200,16,46,.2)',
            'badge_text'   => '#C8102E',
            'chip_bg'      => '#fdf0f2',
            'chip_border'  => 'rgba(200,16,46,.2)',
            'chip_color'   => '#C8102E',
            'chip_label'   => 'Admin',
            'emoji'        => '🔐',
        ],
        'committee' => [
            'icon'         => '◈',
            'label'        => 'Committee Access Required',
            'color'        => '#b45309',
            'icon_bg'      => '#fffbeb',
            'badge_bg'     => '#fffbeb',
            'badge_border' => 'rgba(180,83,9,.2)',
            'badge_text'   => '#b45309',
            'chip_bg'      => '#fffbeb',
            'chip_border'  => 'rgba(180,83,9,.2)',
            'chip_color'   => '#b45309',
            'chip_label'   => 'Committee',
            'emoji'        => '🔒',
        ],
        'member' => [
            'icon'         => '◉',
            'label'        => 'Members Only',
            'color'        => '#003366',
            'icon_bg'      => '#e8eef5',
            'badge_bg'     => '#e8eef5',
            'badge_border' => 'rgba(0,51,102,.15)',
            'badge_text'   => '#003366',
            'chip_bg'      => '#e8eef5',
            'chip_border'  => 'rgba(0,51,102,.15)',
            'chip_color'   => '#003366',
            'chip_label'   => 'Member',
            'emoji'        => '🔒',
        ],
    ];

    $cfg = $roleConfig[$requiredRole] ?? $roleConfig['admin'];

    if ($isImpersonating && in_array($requiredRole, ['super-admin', 'admin'])) {
        $heading     = 'Action Blocked';
        $explanation = 'Admin and Super Admin actions are disabled while you\'re impersonating a member, to protect account security.';
        $sub         = 'Exit impersonation to continue from your own admin session.';
        $iconEmoji   = '⚠️';
        $accentColor = '#ea580c';
        $iconBg      = '#fff7ed';
    } elseif ($requiredRole === 'super-admin') {
        $heading     = 'Super Admin Access Required';
        $explanation = 'This area is restricted to <strong>Super Administrators</strong> only. Your account doesn\'t include this level of access.';
        $sub         = 'Contact another Super Administrator if you believe your permissions need updating.';
        $iconEmoji   = $cfg['emoji'];
        $accentColor = $cfg['color'];
        $iconBg      = $cfg['icon_bg'];
    } elseif ($requiredRole === 'admin' && in_array($userRole, ['member', 'committee'])) {
        $heading     = 'Admin Access Required';
        $explanation = 'This area is restricted to <strong>Administrators</strong>. Your current role doesn\'t include access to this section.';
        $sub         = 'Contact a Group Administrator if you need access.';
        $iconEmoji   = $cfg['emoji'];
        $accentColor = $cfg['color'];
        $iconBg      = $cfg['icon_bg'];
    } elseif ($requiredRole === 'committee' && $userRole === 'member') {
        $heading     = 'Committee Area';
        $explanation = 'This section is restricted to Committee members and above. Your account doesn\'t currently have Committee access.';
        $sub         = 'Speak to a Group Administrator or Committee member if you think you should have access.';
        $iconEmoji   = $cfg['emoji'];
        $accentColor = $cfg['color'];
        $iconBg      = $cfg['icon_bg'];
    } else {
        $heading     = 'Access Restricted';
        $explanation = 'You don\'t have permission to view this page.';
        $sub         = 'If you think this is a mistake, please contact a Group Administrator.';
        $iconEmoji   = '🔒';
        $accentColor = '#C8102E';
        $iconBg      = '#fdf0f2';
    }

    if (! $requiredRole) {
        $requiredRole  = 'admin';
        $requiredLevel = $userLevel;
        $cfg           = $roleConfig['admin'];
    }

    $prevUrl = url()->previous();
    $currUrl = url()->current();
    $backUrl = ($prevUrl && $prevUrl !== $currUrl) ? $prevUrl : null;
    if (! $backUrl) {
        if ($userRole === 'super-admin') $backUrl = route('admin.super.index');
        elseif ($userRole === 'admin')   $backUrl = route('admin.dashboard');
        elseif ($userRole === 'committee') $backUrl = route('committee.dashboard');
        else                             $backUrl = route('members');
    }
@endphp

{{-- ── BRAND HEADER ── --}}
<div class="brand">
    <div class="brand-logo"><span>RAY<br>NET</span></div>
    <span class="brand-name">{{ \App\Helpers\RaynetSetting::groupName() }}</span>
    <div class="brand-divider"></div>
    <span class="brand-sub">Members Portal</span>
</div>

{{-- ── CARD ── --}}
<div class="card">
    <div class="card-accent" style="background: {{ $accentColor }};"></div>

    <div class="card-icon-wrap">
        <div class="card-icon" style="background: {{ $iconBg }};">
            {{ $iconEmoji }}
        </div>
    </div>

    <div class="card-body">

        {{-- Role badge --}}
        @if ($requiredRole)
            <div class="role-badge" style="background:{{ $cfg['badge_bg'] }};border-color:{{ $cfg['badge_border'] }};color:{{ $cfg['badge_text'] }};">
                <span class="role-badge-dot"></span>
                {{ $cfg['icon'] }} {{ $cfg['label'] }}
            </div>
        @endif

        <div class="error-code" style="color:{{ $accentColor }}">Error 403 — Forbidden</div>

        <h1>{{ $heading }}</h1>

        <p class="description">{!! $explanation !!}</p>
        @if ($sub)
            <p class="description">{{ $sub }}</p>
        @endif

        {{-- Clearance gap chips --}}
        @if ($requiredLevel > $userLevel)
            @php
                $yourCfg = $roleConfig[$userRole] ?? $roleConfig['member'];
                $reqCfg  = $roleConfig[$requiredRole] ?? $cfg;
            @endphp
            <div style="display:flex;justify-content:center;">
                <div class="clearance-row">
                    <span class="clearance-chip" style="background:{{ $yourCfg['chip_bg'] }};border-color:{{ $yourCfg['chip_border'] }};color:{{ $yourCfg['chip_color'] }};">
                        {{ $yourCfg['icon'] }} {{ $yourCfg['chip_label'] }}
                    </span>
                    <span class="clearance-arrow">→</span>
                    <span class="clearance-chip" style="background:{{ $reqCfg['chip_bg'] }};border-color:{{ $reqCfg['chip_border'] }};color:{{ $reqCfg['chip_color'] }};">
                        {{ $reqCfg['icon'] }} {{ $reqCfg['chip_label'] }}
                    </span>
                </div>
            </div>
        @endif

        {{-- Impersonation notice --}}
        @if ($isImpersonating)
            <div class="impersonate-notice">
                <div class="impersonate-notice-title">⚠ Impersonation active — {{ $authUser->name }}</div>
                <div class="impersonate-notice-body">Exit impersonation to perform this action using your own admin permissions.</div>
            </div>
        @endif

        <div class="divider"></div>

        <div class="btn-group">
            <a href="{{ $backUrl }}" class="btn btn-primary">Go Back</a>

            @if ($isImpersonating)
                <form method="POST" action="{{ route('admin.impersonate.exit') }}" style="display:contents;">
                    @csrf
                    <button type="submit" class="btn btn-orange">↩ Exit Impersonation</button>
                </form>
            @else
                @if ($backUrl !== route('members'))
                    <a href="{{ route('members') }}" class="btn btn-secondary">Go to Members Area</a>
                @endif
            @endif
        </div>

    </div>
</div>

{{-- ── PAGE FOOTER ── --}}
<div class="page-footer">
    {{ \App\Helpers\RaynetSetting::groupName() }}<br>
    Volunteer emergency communications for {{ \App\Helpers\RaynetSetting::groupRegion() }}
</div>

</body>
</html>