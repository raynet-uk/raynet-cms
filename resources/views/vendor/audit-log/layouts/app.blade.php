<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Audit Log') — {{ \App\Helpers\RaynetSetting::groupName() }}</title>
    <script>
        (function () {
            try {
                var stored = localStorage.getItem('al-theme');
                var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (stored === 'dark' || (!stored && prefersDark)) { document.documentElement.classList.add('dark'); }
            } catch (e) {}
        })();
    </script>
    <link rel="stylesheet" href="{{ $auditAssets['dashboard.css'] ?? route('audit-log.asset', 'dashboard.css') }}">
    <script src="{{ $auditAssets['lucide.js'] ?? route('audit-log.asset', 'lucide.js') }}"></script>
    <style>
        @font-face {
            font-family: "Inter Variable"; font-style: normal; font-display: swap; font-weight: 100 900;
            src: url("{{ $auditAssets["inter-latin.woff2"] ?? route("audit-log.asset", "inter-latin.woff2") }}") format("woff2-variations");
        }
        :root {
            --background: 0 0% 100%;
            --foreground: 214 95% 13%;
            --card: 0 0% 100%;
            --card-foreground: 214 95% 13%;
            --popover: 0 0% 100%;
            --popover-foreground: 214 95% 13%;
            --primary: 214 100% 20%;
            --primary-foreground: 0 0% 100%;
            --secondary: 210 20% 94%;
            --secondary-foreground: 214 95% 13%;
            --muted: 210 20% 94%;
            --muted-foreground: 210 18% 45%;
            --accent: 210 30% 90%;
            --accent-foreground: 214 95% 13%;
            --destructive: 350 83% 44%;
            --destructive-foreground: 0 0% 100%;
            --success: 142 71% 36%;
            --success-foreground: 0 0% 98%;
            --warning: 35 92% 45%;
            --warning-foreground: 48 96% 8%;
            --info: 214 100% 25%;
            --info-foreground: 0 0% 98%;
            --border: 210 18% 86%;
            --input: 210 18% 86%;
            --ring: 214 100% 20%;
            --brand: 350 83% 44%;
            --brand-foreground: 0 0% 100%;
            --radius: 0.25rem;
        }
        .dark {
            --background: 214 50% 8%;
            --foreground: 0 0% 95%;
            --card: 214 40% 11%;
            --card-foreground: 0 0% 95%;
            --popover: 214 40% 11%;
            --popover-foreground: 0 0% 95%;
            --primary: 0 0% 98%;
            --primary-foreground: 214 95% 13%;
            --secondary: 214 30% 16%;
            --secondary-foreground: 0 0% 95%;
            --muted: 214 30% 14%;
            --muted-foreground: 210 15% 60%;
            --accent: 214 30% 16%;
            --accent-foreground: 0 0% 95%;
            --destructive: 350 83% 55%;
            --destructive-foreground: 0 0% 100%;
            --success: 142 71% 45%;
            --success-foreground: 144 80% 8%;
            --warning: 35 92% 55%;
            --warning-foreground: 48 96% 8%;
            --info: 214 80% 60%;
            --info-foreground: 210 40% 98%;
            --border: 214 30% 18%;
            --input: 214 30% 20%;
            --ring: 214 80% 60%;
            --brand: 350 83% 60%;
            --brand-foreground: 0 0% 100%;
        }
        html, body { font-family: "Inter Variable", Arial, ui-sans-serif, system-ui, sans-serif; }
        body { -webkit-font-smoothing: antialiased; }
        *::-webkit-scrollbar { width: 10px; height: 10px; }
        *::-webkit-scrollbar-track { background: transparent; }
        *::-webkit-scrollbar-thumb { background: hsl(var(--border)); border-radius: 10px; border: 2px solid transparent; background-clip: padding-box; }
        [data-lucide] { width: 1em; height: 1em; stroke-width: 2; }
        .al-input { width: 100%; height: 2.25rem; border-radius: calc(var(--radius) - 2px); border: 1px solid hsl(var(--border)); background: hsl(var(--card)); color: hsl(var(--foreground)); padding: 0 0.6rem; font-size: 0.8125rem; }
        .al-input:focus { outline: none; box-shadow: 0 0 0 2px hsl(var(--ring) / 0.4); }
        .al-input--active { border-color: hsl(var(--brand) / 0.4); background: hsl(var(--brand) / 0.05); font-weight: 500; }
        .al-datefield input[type="date"] { color-scheme: light; }
        .dark .al-datefield input[type="date"] { color-scheme: dark; }
        .al-datefield input[type="date"]::-webkit-calendar-picker-indicator { opacity: 0; cursor: pointer; }

        /* RAYNET admin bar */
        #rn-adminbar { display:flex; align-items:center; justify-content:space-between; background:#002244; padding:.45rem 1.25rem; font-family:Arial,sans-serif; font-size:11px; font-weight:bold; letter-spacing:.04em; border-bottom:2px solid #C8102E; }
        #rn-adminbar-left { display:flex; align-items:center; gap:.75rem; }
        #rn-adminbar .rn-logo { width:22px; height:22px; background:#C8102E; display:flex; align-items:center; justify-content:center; font-size:6px; color:#fff; font-weight:bold; text-align:center; line-height:1.1; text-transform:uppercase; flex-shrink:0; }
        #rn-adminbar .rn-group { color:rgba(255,255,255,.5); text-transform:uppercase; letter-spacing:.1em; }
        #rn-adminbar .rn-sep { color:rgba(255,255,255,.2); }
        #rn-adminbar .rn-page { color:#fff; text-transform:uppercase; letter-spacing:.1em; }
        #rn-adminbar-right { display:flex; align-items:center; gap:.65rem; }
        #rn-adminbar a.rn-back { display:inline-flex; align-items:center; gap:.35rem; padding:.28rem .75rem; background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.15); color:rgba(255,255,255,.85); font-size:11px; font-weight:bold; text-decoration:none; text-transform:uppercase; letter-spacing:.05em; transition:background .12s; }
        #rn-adminbar a.rn-back:hover { background:rgba(255,255,255,.15); color:#fff; }
        #rn-adminbar .rn-user { color:rgba(255,255,255,.6); }
        #rn-adminbar .rn-star { color:#C8102E; }

        /* Force solid white nav */
        nav.sticky { background: #fff !important; backdrop-filter: none !important; -webkit-backdrop-filter: none !important; }

        /* Hide yammi logo text, keep icon */
    </style>
</head>
<body class="bg-background text-foreground min-h-screen antialiased">

{{-- RAYNET Admin Bar --}}
<div id="rn-adminbar">
    <div id="rn-adminbar-left">
        <div class="rn-logo">RAY<br>NET</div>
        <span class="rn-group">{{ \App\Helpers\RaynetSetting::groupName() }}</span>
        <span class="rn-sep">/</span>
        <span class="rn-group">Super Admin</span>
        <span class="rn-sep">/</span>
        <span class="rn-page">📋 Audit Log</span>
    </div>
    <div id="rn-adminbar-right">
        <span class="rn-user"><span class="rn-star">★</span> {{ auth()->user()?->name ?? 'Admin' }}</span>
        <a href="{{ route('admin.super.index') }}#audit-log" class="rn-back">← Super Admin Panel</a>
        <a href="{{ route('admin.dashboard') }}" class="rn-back">⊞ Dashboard</a>
    </div>
</div>

    <div aria-hidden="true" class="pointer-events-none fixed inset-x-0 top-0 -z-10 h-[420px] overflow-hidden">
        <div class="absolute left-1/2 top-[-140px] h-[420px] w-[900px] -translate-x-1/2 rounded-full bg-brand/10 blur-3xl"></div>
    </div>

    <nav class="sticky top-0 z-40 border-b border-border" style="background:#fff;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-14 items-center gap-4">
                {{-- Logo hidden via CSS above --}}
                <a href="{{ route('audit-log.dashboard') }}" class="flex items-center gap-2.5 shrink-0">
                    <div class="relative flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-brand to-brand/70 text-brand-foreground shadow-sm ring-1 ring-inset ring-white/10">
                        <i data-lucide="history" class="text-[15px]"></i>
                    </div>
                    <div class="flex flex-col leading-tight">
                        <span class="font-semibold text-sm tracking-tight" style="color:#003366;">RAYNET Audit Log</span>
                        <span class="hidden sm:block text-[10px] -mt-0.5" style="color:#6b7f96;">{{ \App\Helpers\RaynetSetting::groupName() }}</span>
                    </div>
                </a>

                <div class="flex items-center gap-1.5 sm:gap-2 min-w-0">
                    @php $onLog = request()->routeIs('audit-log.dashboard') || request()->routeIs('audit-log.trace'); @endphp
                    <a href="{{ route('audit-log.dashboard') }}" title="Log"
                       class="inline-flex items-center gap-1.5 rounded-md px-2.5 sm:px-3 h-8 text-xs font-semibold border transition-colors {{ $onLog ? 'border-brand/30 bg-brand/10 text-brand' : 'border-border bg-card text-muted-foreground hover:text-foreground hover:bg-accent' }}">
                        <i data-lucide="list" class="text-[14px]"></i> <span class="hidden sm:inline">Log</span>
                    </a>
                    <a href="{{ route('audit-log.noise') }}" title="Noise"
                       class="inline-flex items-center gap-1.5 rounded-md px-2.5 sm:px-3 h-8 text-xs font-semibold border transition-colors {{ request()->routeIs('audit-log.noise') ? 'border-warning/40 bg-warning/10 text-warning' : 'border-border bg-card text-muted-foreground hover:text-foreground hover:bg-accent' }}">
                        <i data-lucide="alert-triangle" class="text-[14px]"></i> <span class="hidden sm:inline">Noise</span>
                        @if (($auditNoiseCount ?? 0) > 0)
                            <span class="inline-flex items-center justify-center min-w-[1.1rem] h-[1.1rem] px-1 rounded-full bg-warning/20 text-warning text-[10px] font-bold tabular-nums">{{ $auditNoiseCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('audit-log.stats') }}" title="Stats"
                       class="inline-flex items-center gap-1.5 rounded-md px-2.5 sm:px-3 h-8 text-xs font-semibold border transition-colors {{ request()->routeIs('audit-log.stats') ? 'border-brand/30 bg-brand/10 text-brand' : 'border-border bg-card text-muted-foreground hover:text-foreground hover:bg-accent' }}">
                        <i data-lucide="bar-chart-3" class="text-[14px]"></i> <span class="hidden sm:inline">Stats</span>
                    </a>
                    <a href="{{ route('audit-log.anomalies') }}" title="Anomalies"
                       class="inline-flex items-center gap-1.5 rounded-md px-2.5 sm:px-3 h-8 text-xs font-semibold border transition-colors {{ request()->routeIs('audit-log.anomalies') ? 'border-warning/40 bg-warning/10 text-warning' : 'border-border bg-card text-muted-foreground hover:text-foreground hover:bg-accent' }}">
                        <i data-lucide="siren" class="text-[14px]"></i> <span class="hidden lg:inline">Anomalies</span>
                        @if (($auditAnomalyCount ?? 0) > 0)
                            <span class="inline-flex items-center justify-center min-w-[1.1rem] h-[1.1rem] px-1 rounded-full bg-warning/20 text-warning text-[10px] font-bold tabular-nums">{{ $auditAnomalyCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('audit-log.time-machine') }}" title="Time machine"
                       class="inline-flex items-center gap-1.5 rounded-md px-2.5 sm:px-3 h-8 text-xs font-semibold border transition-colors {{ request()->routeIs('audit-log.time-machine') ? 'border-brand/30 bg-brand/10 text-brand' : 'border-border bg-card text-muted-foreground hover:text-foreground hover:bg-accent' }}">
                        <i data-lucide="calendar-clock" class="text-[14px]"></i> <span class="hidden lg:inline">Time machine</span>
                    </a>
                    <a href="{{ route('audit-log.settings') }}" title="Settings"
                       class="inline-flex items-center gap-1.5 rounded-md px-2.5 sm:px-3 h-8 text-xs font-semibold border transition-colors {{ request()->routeIs('audit-log.settings') || request()->routeIs('audit-log.settings.*') || request()->routeIs('audit-log.playground') ? 'border-brand/30 bg-brand/10 text-brand' : 'border-border bg-card text-muted-foreground hover:text-foreground hover:bg-accent' }}">
                        <i data-lucide="settings" class="text-[14px]"></i> <span class="hidden sm:inline">Settings</span>
                    </a>
                    <button type="button" onclick="__alToggleTheme()" title="Toggle theme"
                            class="inline-flex items-center justify-center h-8 w-8 shrink-0 rounded-md border border-border bg-card text-muted-foreground hover:text-foreground hover:bg-accent transition-colors">
                        <i data-lucide="sun-moon" class="text-[15px]"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 animate-fade-in">
        @yield('content')
    </main>

    <script>
        function __alToggleTheme() {
            var isDark = document.documentElement.classList.toggle('dark');
            try { localStorage.setItem('al-theme', isDark ? 'dark' : 'light'); } catch (e) {}
        }
        function __alIcons() {
            if (window.lucide && typeof window.lucide.createIcons === 'function') { window.lucide.createIcons(); }
        }
        function __alToggleRow(id) {
            var row = document.getElementById(id);
            if (row) { row.classList.toggle('hidden'); }
        }
        function __alCloseSelects(except) {
            document.querySelectorAll('[data-al-select]').forEach(function (sel) {
                if (sel === except) { return; }
                var dd = sel.querySelector('[data-al-select-dropdown]');
                var caret = sel.querySelector('[data-al-select-caret]');
                if (dd) { dd.classList.add('hidden'); }
                if (caret) { caret.classList.remove('rotate-180'); }
                var trigger = sel.querySelector('[data-al-select-trigger]');
                if (trigger) { trigger.setAttribute('aria-expanded', 'false'); }
            });
        }
        document.addEventListener('click', function (e) {
            var trigger = e.target.closest('[data-al-select-trigger]');
            if (trigger) {
                var sel = trigger.closest('[data-al-select]');
                var dd = sel.querySelector('[data-al-select-dropdown]');
                var caret = sel.querySelector('[data-al-select-caret]');
                var willOpen = dd.classList.contains('hidden');
                __alCloseSelects(sel);
                if (willOpen) { dd.classList.remove('hidden'); caret.classList.add('rotate-180'); trigger.setAttribute('aria-expanded', 'true'); __alIcons(); }
                else { dd.classList.add('hidden'); caret.classList.remove('rotate-180'); }
                return;
            }
            var option = e.target.closest('[data-al-select-option]');
            if (option) {
                var host = option.closest('[data-al-select]');
                var input = host.querySelector('[data-al-select-input]');
                var labelEl = option.querySelector('[data-al-select-option-label]');
                input.value = option.getAttribute('data-value') || '';
                host.querySelector('[data-al-select-label]').textContent = labelEl ? labelEl.textContent.trim() : input.value;
                __alCloseSelects(null);
                var form = host.closest('form');
                if (form && !host.hasAttribute('data-al-select-nosubmit')) { form.requestSubmit ? form.requestSubmit() : form.submit(); }
                return;
            }
            if (!e.target.closest('[data-al-select]')) { __alCloseSelects(null); }
        });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { __alCloseSelects(null); } });
        __alIcons();
    </script>
    @stack('scripts')
</body>
</html>
