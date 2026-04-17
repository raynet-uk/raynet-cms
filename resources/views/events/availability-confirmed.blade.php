<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Availability Confirmed — {{ \App\Helpers\RaynetSetting::groupName() }}</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, sans-serif; background: #f4f5f7; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem; }
.card { background: #fff; max-width: 480px; width: 100%; box-shadow: 0 4px 24px rgba(0,51,102,.12); overflow: hidden; }
.card-header { background: #003366; border-bottom: 3px solid #C8102E; padding: 1.25rem 1.5rem; }
.card-logo { background: #C8102E; display: inline-block; padding: 4px 10px; font-size: 10px; font-weight: bold; color: #fff; letter-spacing: .08em; text-transform: uppercase; margin-bottom: .5rem; }
.card-title { font-size: 16px; font-weight: bold; color: #fff; }
.card-body { padding: 2rem 1.5rem; text-align: center; }
.icon { font-size: 3.5rem; margin-bottom: 1rem; }
.status { font-size: 18px; font-weight: bold; margin-bottom: .5rem; }
.status.available { color: #1a6b3c; }
.status.unavailable { color: #C8102E; }
.message { font-size: 14px; color: #6b7f96; line-height: 1.65; margin-bottom: 1.5rem; }
.event-name { font-size: 14px; font-weight: bold; color: #003366; background: #e8eef5; border: 1px solid rgba(0,51,102,.15); padding: .65rem 1rem; margin-bottom: 1.5rem; }
.btn { display: inline-block; background: #003366; color: #fff; font-weight: bold; font-size: 13px; padding: .65rem 1.5rem; text-decoration: none; }
.footer { padding: .85rem 1.5rem; background: #f4f5f7; border-top: 1px solid #dde2e8; text-align: center; font-size: 11px; color: #9aa3ae; }
</style>
</head>
<body>
<div class="card">
    <div class="card-header">
        <div class="card-logo">RAYNET</div>
        <div class="card-title">{{ \App\Helpers\RaynetSetting::groupName() }}</div>
    </div>
    <div class="card-body">
        @if($response === 'available')
            <div class="icon">✅</div>
            <div class="status available">You're marked as available</div>
            <p class="message">Thanks {{ $member->name }} — we've recorded that you're available for this event. The group controller will be in touch if you're selected for the crew.</p>
        @else
            <div class="icon">❌</div>
            <div class="status unavailable">You're marked as unavailable</div>
            <p class="message">Thanks {{ $member->name }} — we've noted that you're not available for this event. No further action is needed.</p>
        @endif
        <div class="event-name">{{ $event->title }}@if($event->starts_at) · {{ $event->starts_at->format('j M Y') }}@endif</div>
        <a href="{{ url('/members') }}" class="btn">Go to Members Area →</a>
    </div>
    <div class="footer">{{ \App\Helpers\RaynetSetting::groupName() }} · Member of RAYNET-UK</div>
</div>
</body>
</html>
