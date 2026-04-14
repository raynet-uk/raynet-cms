{{--
    Impersonation warning banner.
    Include this in layouts/app.blade.php immediately before (or inside) your <body> content,
    so it appears on every page during an active impersonation session.

    In layouts/app.blade.php, add:
        @include('partials.impersonation-banner')
    as the very first element inside <body> (or at the top of your page wrap).
--}}

@if (session('original_admin_id'))
@php
    $impersonatedUser = auth()->user();
@endphp
<div role="alert" style="
    position: sticky;
    top: 0;
    z-index: 9999;
    background: #7c2d00;
    border-bottom: 3px solid #ea580c;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .55rem 1.5rem;
    gap: 1rem;
    flex-wrap: wrap;
    box-shadow: 0 3px 12px rgba(0,0,0,.4);
    font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
">
    <div style="display:flex; align-items:center; gap:.75rem;">
        <div style="
            width:28px; height:28px; background:#ea580c;
            display:flex; align-items:center; justify-content:center;
            font-size:13px; flex-shrink:0;
        ">👤</div>
        <div>
            <div style="font-size:13px; font-weight:bold; color:#fed7aa; letter-spacing:.02em;">
                ⚠ Admin impersonation active — you are viewing as
                <span style="color:#fdba74;">{{ $impersonatedUser->name }}</span>
                <span style="color:#fb923c; font-weight:normal; font-size:12px;">({{ $impersonatedUser->email }})</span>
            </div>
            <div style="font-size:11px; color:#fb923c; margin-top:1px;">
                Actions taken now affect this member's account. Exit to return to your admin session.
            </div>
        </div>
    </div>
    <form method="POST" action="{{ route('admin.impersonate.exit') }}" style="flex-shrink:0;">
        @csrf
        <button type="submit" style="
            padding:.4rem 1.1rem;
            background:#ea580c; border:1px solid #c2410c;
            color:white;
            font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;
            font-size:11px; font-weight:bold;
            cursor:pointer; text-transform:uppercase; letter-spacing:.08em;
            transition:all .12s;
        " onmouseover="this.style.background='#c2410c'" onmouseout="this.style.background='#ea580c'">
            ✕ Exit impersonation
        </button>
    </form>
</div>
@endif
