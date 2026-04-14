<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="margin:0;padding:0;background:#f2f5f9;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td align="center" style="padding:2rem 1rem;">
<table width="100%" style="max-width:480px;">

  <tr><td style="background:#003366;padding:1.2rem 1.5rem;border-bottom:3px solid #C8102E;">
    <span style="font-size:13px;font-weight:bold;color:#fff;text-transform:uppercase;letter-spacing:.04em;">
      {{ \App\Helpers\RaynetSetting::groupName() }} · Members' Portal
    </span>
  </td></tr>

  <tr><td style="background:#fff;padding:1.75rem 1.5rem;">
    <p style="font-size:13px;color:#6b7f96;margin:0 0 1rem;">Hi {{ $user->name }},</p>
    <p style="font-size:13px;color:#001f40;margin:0 0 1.5rem;line-height:1.6;">
      Your sign-in code for the {{ \App\Helpers\RaynetSetting::groupName() }} Members' Portal is:
    </p>

    <div style="text-align:center;padding:1.25rem;background:#f2f5f9;border:1px solid #dde2e8;border-left:4px solid #003366;margin-bottom:1.5rem;">
      <span style="font-size:2.4rem;font-weight:bold;color:#003366;letter-spacing:.25em;font-variant-numeric:tabular-nums;">
        {{ $code }}
      </span>
    </div>

    <p style="font-size:12px;color:#6b7f96;margin:0 0 .5rem;line-height:1.6;">
      This code expires in <strong>10 minutes</strong>. Do not share it with anyone.
    </p>
    <p style="font-size:12px;color:#6b7f96;margin:0;line-height:1.6;">
      If you didn't request this code, you can safely ignore this email.
    </p>
  </td></tr>

  <tr><td style="background:#f2f5f9;padding:.85rem 1.5rem;border-top:1px solid #dde2e8;">
    <p style="font-size:11px;color:#9aa3ae;margin:0;line-height:1.6;">
      🔒 {{ \App\Helpers\RaynetSetting::groupName() }} Members' Portal · This is an automated message, do not reply.
    </p>
  </td></tr>

</table>
</td></tr>
</table>
</body>
</html>