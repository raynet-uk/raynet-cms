@extends('layouts.app')

@section('content')
<div style="max-width:560px;margin:60px auto;text-align:center;font-family:Arial,sans-serif;">

    <div style="background:#003366;padding:30px;border-radius:12px 12px 0 0;border-bottom:4px solid #C8102E;">
        <div style="background:#C8102E;display:inline-block;padding:8px 14px;border-radius:6px;margin-bottom:16px;">
            <span style="color:white;font-weight:900;font-size:18px;letter-spacing:2px;">RAY<br>NET</span>
        </div>
        <h1 style="color:white;font-size:22px;font-weight:700;margin:0;">DMR Network Dashboard</h1>
        <p style="color:#aac4e8;font-size:13px;margin-top:6px;">Liverpool RAYNET &middot; Zone 10 &middot; Merseyside</p>
    </div>

    <div style="background:white;padding:32px;border-radius:0 0 12px 12px;border:1px solid #e2e8f0;border-top:none;box-shadow:0 4px 16px rgba(0,40,100,0.08);">
        <div style="font-size:48px;margin-bottom:16px;">📡</div>
        <h2 style="color:#003366;font-size:18px;font-weight:700;margin-bottom:10px;">Access Not Granted</h2>
        <p style="color:#555;font-size:14px;line-height:1.7;margin-bottom:24px;">
            Your account does not currently have permission to access the Liverpool RAYNET DMR Network Dashboard.
            If you believe you should have access, please contact an administrator.
        </p>

        <div style="background:#f0f4ff;border:1px solid #d0ddf0;border-left:4px solid #003366;padding:14px 16px;border-radius:6px;text-align:left;margin-bottom:24px;">
            <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#003366;margin-bottom:4px;">Logged in as</div>
            <div style="font-size:14px;font-weight:600;color:#1c2b4a;">{{ auth()->user()->name }}</div>
            @if(auth()->user()->callsign)
            <div style="font-size:12px;color:#7fafd4;margin-top:2px;">{{ auth()->user()->callsign }}</div>
            @endif
        </div>

        <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
            <a href="{{ route('members') }}" style="background:#003366;color:white;padding:10px 22px;border-radius:6px;font-size:13px;font-weight:700;text-decoration:none;">
                &larr; Members Area
            </a>
            <a href="mailto:{{ config('mail.from.address') }}" style="background:#f0f4ff;color:#003366;border:1px solid #d0ddf0;padding:10px 22px;border-radius:6px;font-size:13px;font-weight:700;text-decoration:none;">
                Contact Admin
            </a>
        </div>
    </div>

</div>
@endsection
