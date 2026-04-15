<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $subject ?? '{{ \App\Helpers\RaynetSetting::groupName() }}' }}</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { background:#f0f4f8; font-family:Arial,'Helvetica Neue',Helvetica,sans-serif; font-size:14px; color:#001f40; }
.wrapper { max-width:600px; margin:0 auto; padding:24px 16px; }
.card { background:#ffffff; border-radius:0; overflow:hidden; box-shadow:0 4px 24px rgba(0,31,64,.12); }
.header { background:#003366; padding:28px 32px; position:relative; border-bottom:4px solid #C8102E; }
.header-logo { display:flex; align-items:center; gap:12px; margin-bottom:20px; }
.header-logo-box { background:#C8102E; width:40px; height:40px; display:flex; align-items:center; justify-content:center; font-size:9px; font-weight:bold; color:#fff; text-align:center; line-height:1.2; text-transform:uppercase; letter-spacing:.05em; flex-shrink:0; }
.header-org { font-size:14px; font-weight:bold; color:#fff; text-transform:uppercase; letter-spacing:.06em; }
.header-sub { font-size:10px; color:rgba(255,255,255,.5); text-transform:uppercase; letter-spacing:.1em; margin-top:2px; }
.header-title { font-size:22px; font-weight:bold; color:#fff; line-height:1.3; }
.header-subtitle { font-size:13px; color:rgba(255,255,255,.6); margin-top:6px; }
.body { padding:32px; }
.greeting { font-size:16px; font-weight:bold; color:#003366; margin-bottom:12px; }
.text { font-size:14px; color:#2d4a6b; line-height:1.7; margin-bottom:16px; }
.highlight-box { background:#e8eef5; border-left:4px solid #003366; padding:16px 20px; margin:20px 0; }
.highlight-box .course-title { font-size:17px; font-weight:bold; color:#003366; margin-bottom:4px; }
.highlight-box .course-meta { font-size:12px; color:#6b7f96; }
.btn-wrap { text-align:center; margin:28px 0; }
.btn { display:inline-block; background:#003366; color:#ffffff !important; text-decoration:none; padding:14px 32px; font-size:13px; font-weight:bold; text-transform:uppercase; letter-spacing:.08em; }
.btn-teal { background:#0288d1; }
.btn-green { background:#1a6b3c; }
.badge-row { display:flex; align-items:center; gap:12px; padding:12px 16px; background:#f5f8ff; border:1px solid #dde2e8; margin-bottom:8px; }
.badge-hex { flex-shrink:0; }
.badge-name { font-size:13px; font-weight:bold; color:#001f40; }
.badge-sub { font-size:11px; color:#6b7f96; margin-top:2px; }
.badge-pill { margin-left:auto; font-size:10px; font-weight:bold; padding:3px 10px; border:1px solid; flex-shrink:0; }
.badge-pill-green { background:#eef7f2; border-color:#b8ddc9; color:#1a6b3c; }
.badge-pill-navy { background:#e8eef5; border-color:rgba(0,51,102,.2); color:#003366; }
.progress-wrap { margin:20px 0; }
.progress-label { display:flex; justify-content:space-between; font-size:11px; color:#6b7f96; margin-bottom:6px; font-weight:bold; text-transform:uppercase; letter-spacing:.08em; }
.progress-track { height:8px; background:#dde2e8; overflow:hidden; }
.progress-fill { height:100%; background:#003366; }
.divider { height:1px; background:#dde2e8; margin:24px 0; }
.meta-grid { display:table; width:100%; border-collapse:collapse; margin:16px 0; }
.meta-row { display:table-row; }
.meta-label { display:table-cell; font-size:11px; font-weight:bold; text-transform:uppercase; letter-spacing:.1em; color:#6b7f96; padding:6px 12px 6px 0; width:40%; vertical-align:top; }
.meta-val { display:table-cell; font-size:13px; font-weight:bold; color:#001f40; padding:6px 0; vertical-align:top; }
.footer { background:#f2f5f9; padding:20px 32px; border-top:1px solid #dde2e8; text-align:center; }
.footer-text { font-size:11px; color:#9aa3ae; line-height:1.6; }
.footer-link { color:#003366; text-decoration:none; font-weight:bold; }
.callout { background:#fffbeb; border:1px solid #f59e0b; border-left:4px solid #f59e0b; padding:14px 18px; margin:20px 0; }
.callout-title { font-size:12px; font-weight:bold; color:#78350f; margin-bottom:4px; text-transform:uppercase; letter-spacing:.06em; }
.callout-text { font-size:13px; color:#92400e; line-height:1.6; }
.success-banner { background:#003366; padding:24px 32px; text-align:center; }
.success-icon { font-size:48px; margin-bottom:12px; }
.success-title { font-size:20px; font-weight:bold; color:#fff; margin-bottom:6px; }
.success-sub { font-size:13px; color:rgba(255,255,255,.65); }
</style>
</head>
<body>
<div class="wrapper">
    <div class="card">
        <div class="header">
            <div class="header-logo">
                <div class="header-logo-box">RAY<br>NET</div>
                <div>
                    <div class="header-org">{{ \App\Helpers\RaynetSetting::groupName() }}</div>
                    <div class="header-sub">Training Portal</div>
                </div>
            </div>
            <div class="header-title">{{ $headerTitle ?? '' }}</div>
            @if(!empty($headerSubtitle))
            <div class="header-subtitle">{{ $headerSubtitle }}</div>
            @endif
        </div>

        {{ $slot }}

        <div class="footer">
            <div class="footer-text">
                {{ \App\Helpers\RaynetSetting::groupName() }} Group ({{ \App\Helpers\RaynetSetting::groupNumber() }}) · Affiliated to RAYNET-UK<br>
                Volunteer emergency communications for {{ \App\Helpers\RaynetSetting::groupRegion() }}<br>
                <a href="https://{{ \App\Helpers\RaynetSetting::siteUrl() }}" class="footer-link">{{ \App\Helpers\RaynetSetting::siteUrl() }}</a>
                &nbsp;·&nbsp;
                <a href="https://{{ \App\Helpers\RaynetSetting::siteUrl() }}/my-training" class="footer-link">Training Portal</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>