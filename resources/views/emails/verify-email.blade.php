<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify your email — {{ \App\Helpers\RaynetSetting::groupName() }}</title>
</head>
<body style="margin:0;padding:0;background:#f0f4f8;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8;padding:40px 20px;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

        {{-- HEADER --}}
        <tr>
          <td style="background:#003366;border-radius:12px 12px 0 0;padding:0;overflow:hidden;">

            {{-- Top accent bar --}}
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="background:#C8102E;height:4px;font-size:0;line-height:0;">&nbsp;</td>
              </tr>
            </table>

            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="padding:28px 36px 24px;">

                  {{-- Brand row --}}
                  <table cellpadding="0" cellspacing="0">
                    <tr>
                      <td style="background:#C8102E;width:44px;height:44px;border-radius:8px;text-align:center;vertical-align:middle;">
                        <span style="font-size:22px;line-height:44px;">📻</span>
                      </td>
                      <td style="padding-left:14px;">
                        <div style="font-size:16px;font-weight:bold;color:#ffffff;letter-spacing:0.04em;text-transform:uppercase;">{{ \App\Helpers\RaynetSetting::groupName() }}</div>
                        <div style="font-size:11px;color:rgba(255,255,255,0.5);letter-spacing:0.06em;text-transform:uppercase;margin-top:2px;">Members' Portal</div>
                      </td>
                    </tr>
                  </table>

                  {{-- Heading --}}
                  <div style="margin-top:24px;">
                    <div style="font-size:11px;font-weight:bold;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.14em;margin-bottom:8px;">Account Setup</div>
                    <div style="font-size:26px;font-weight:bold;color:#ffffff;line-height:1.2;">Verify your email address</div>
                    <div style="font-size:14px;color:rgba(255,255,255,0.6);margin-top:8px;line-height:1.6;">You're almost ready to access the {{ \App\Helpers\RaynetSetting::groupName() }} members' portal. Click the button below to confirm your email address.</div>
                  </div>

                </td>
              </tr>
            </table>
          </td>
        </tr>

        {{-- BODY --}}
        <tr>
          <td style="background:#ffffff;padding:36px;">

            {{-- Info block --}}
            <table width="100%" cellpadding="0" cellspacing="0" style="background:#e8eef5;border:1px solid rgba(0,51,102,0.15);border-left:3px solid #003366;border-radius:0 6px 6px 0;margin-bottom:28px;">
              <tr>
                <td style="padding:14px 16px;">
                  <div style="font-size:12px;font-weight:bold;color:#003366;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:4px;">Why do I need to do this?</div>
                  <div style="font-size:13px;color:#2d4a6b;line-height:1.6;">Verifying your email confirms you have access to this address and helps keep the RAYNET member database accurate. This step is required before you can access the members' area.</div>
                </td>
              </tr>
            </table>

            {{-- CTA Button --}}
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td align="center" style="padding-bottom:28px;">
                  <a href="{{ $url }}"
                     style="display:inline-block;background:#003366;color:#ffffff;font-size:15px;font-weight:bold;text-decoration:none;padding:14px 36px;border-radius:8px;letter-spacing:0.04em;">
                    ✓ &nbsp;Verify my email address
                  </a>
                </td>
              </tr>
            </table>

            {{-- Divider --}}
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
              <tr>
                <td style="border-top:1px solid #dde2e8;font-size:0;height:1px;">&nbsp;</td>
              </tr>
            </table>

            {{-- Details --}}
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="padding-bottom:16px;">
                  <div style="font-size:12px;font-weight:bold;color:#6b7f96;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:6px;">Link not working?</div>
                  <div style="font-size:13px;color:#2d4a6b;line-height:1.6;">Copy and paste the URL below into your browser:</div>
                  <div style="margin-top:8px;padding:10px 12px;background:#f0f4f8;border:1px solid #dde2e8;border-radius:6px;font-size:11px;color:#003366;font-family:'Courier New',monospace;word-break:break-all;">{{ $url }}</div>
                </td>
              </tr>
            </table>

            {{-- Expiry notice --}}
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:16px;">
              <tr>
                <td style="background:#fdf8ec;border:1px solid #f5d87a;border-left:3px solid #c49a00;border-radius:0 6px 6px 0;padding:10px 14px;">
                  <div style="font-size:12px;color:#8a5500;font-weight:bold;">⏳ This link expires in 60 minutes.</div>
                  <div style="font-size:12px;color:#8a5500;margin-top:2px;">If it has expired, log in to the portal and request a new verification email.</div>
                </td>
              </tr>
            </table>

          </td>
        </tr>

        {{-- FOOTER --}}
        <tr>
          <td style="background:#f0f4f8;border:1px solid #dde2e8;border-top:none;border-radius:0 0 12px 12px;padding:20px 36px;">
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td>
                  <div style="font-size:12px;color:#6b7f96;line-height:1.6;">
                    This email was sent to <strong>{{ $notifiable->email }}</strong> because an account was registered on the {{ \App\Helpers\RaynetSetting::groupName() }} members' portal.
                    If you did not register, you can safely ignore this email.
                  </div>
                  <div style="margin-top:10px;font-size:11px;color:#9aa3ae;">
                    {{ \App\Helpers\RaynetSetting::groupName() }} · {{ \App\Helpers\RaynetSetting::groupNumber() }}<br>
                    Radio Amateurs' Emergency Network
                  </div>
                </td>
              </tr>
            </table>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>