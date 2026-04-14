<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Reset your password — {{ \App\Helpers\RaynetSetting::groupName() }}</title>
<style>
    /* Reset */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
    img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
    body { width: 100% !important; min-width: 100%; background-color: #f2f5f9; }

    /* Tokens */
    .navy       { color: #003366 !important; }
    .red        { color: #C8102E !important; }
    .muted      { color: #6b7f96 !important; }
    .white      { color: #ffffff !important; }

    /* Responsive */
    @media only screen and (max-width: 600px) {
        .email-container { width: 100% !important; }
        .fluid { max-width: 100% !important; height: auto !important; }
        .stack-column { display: block !important; width: 100% !important; }
        .px-mobile { padding-left: 24px !important; padding-right: 24px !important; }
        .btn-mobile { width: 100% !important; text-align: center !important; }
        .hide-mobile { display: none !important; }
    }
</style>
</head>
<body style="margin:0;padding:0;background-color:#f2f5f9;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;">

{{-- Outer wrapper --}}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f2f5f9;padding:32px 16px;">
<tr><td align="center">

<table role="presentation" class="email-container" width="560" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%;">

    {{-- ── HEADER — navy with diagonal texture + red accent ── --}}
    <tr>
        <td style="background-color:#003366;border-bottom:4px solid #C8102E;padding:0;overflow:hidden;position:relative;">

            {{-- Diagonal stripe overlay (VML for Outlook, CSS for others) --}}
            <!--[if gte mso 9]>
            <v:rect xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false" style="width:560px;height:90px;">
            <v:fill type="tile" color="#003366"/>
            <v:textbox inset="0,0,0,0"><![endif]-->
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td style="padding:28px 32px 24px;background-color:#003366;background-image:repeating-linear-gradient(-45deg,transparent,transparent 20px,rgba(255,255,255,.025) 20px,rgba(255,255,255,.025) 21px);">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="52" valign="middle">
                            {{-- RAYNET logo box --}}
                            <table role="presentation" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="width:46px;height:46px;background-color:#C8102E;text-align:center;vertical-align:middle;padding:0;">
                                    <span style="font-size:10px;font-weight:bold;color:#ffffff;letter-spacing:.06em;line-height:1.2;text-transform:uppercase;display:block;padding:4px;">RAY<br>NET</span>
                                </td>
                            </tr>
                            </table>
                        </td>
                        <td style="padding-left:14px;" valign="middle">
                            <div style="font-size:15px;font-weight:bold;color:#ffffff;letter-spacing:.04em;text-transform:uppercase;line-height:1.2;">{{ \App\Helpers\RaynetSetting::groupName() }}</div>
                            <div style="font-size:11px;color:rgba(255,255,255,.45);margin-top:3px;text-transform:uppercase;letter-spacing:.06em;">Members' Portal</div>
                        </td>
                        <td align="right" valign="middle">
                            <span style="display:inline-block;padding:3px 10px;border:1px solid rgba(255,255,255,.2);font-size:10px;font-weight:bold;color:rgba(255,255,255,.6);text-transform:uppercase;letter-spacing:.08em;">🔒 Secure</span>
                        </td>
                    </tr>
                    </table>
                </td>
            </tr>
            </table>
            <!--[if gte mso 9]></v:textbox></v:rect><![endif]-->

        </td>
    </tr>

    {{-- ── EYEBROW BAND ── --}}
    <tr>
        <td style="background-color:#eef1f6;border-bottom:1px solid #dde2e8;padding:10px 32px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <span style="display:inline-block;width:12px;height:2px;background:#C8102E;vertical-align:middle;margin-right:6px;"></span>
                    <span style="font-size:10px;font-weight:bold;color:#C8102E;text-transform:uppercase;letter-spacing:.16em;vertical-align:middle;">Password Reset</span>
                </td>
                <td align="right">
                    <span style="font-size:10px;color:#9aa3ae;font-weight:bold;">{{ now()->format('d M Y') }}</span>
                </td>
            </tr>
            </table>
        </td>
    </tr>

    {{-- ── BODY ── --}}
    <tr>
        <td style="background-color:#ffffff;padding:36px 32px 28px;" class="px-mobile">

            {{-- Greeting --}}
            <p style="font-size:13px;color:#6b7f96;margin:0 0 6px;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;">Hi {{ $notifiable->name }},</p>
            <h1 style="font-size:22px;font-weight:bold;color:#003366;margin:0 0 16px;line-height:1.2;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;">Reset your password</h1>

            <p style="font-size:13px;color:#2d4a6b;line-height:1.7;margin:0 0 24px;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;">
                We received a request to reset the password for the account associated with
                <strong style="color:#003366;">{{ $notifiable->email }}</strong>.
                Click the button below to choose a new password.
            </p>

            {{-- CTA button --}}
            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 28px;">
            <tr>
                <td style="background-color:#003366;" class="btn-mobile">
                    <a href="{{ $url }}"
                       style="display:inline-block;padding:13px 32px;font-size:13px;font-weight:bold;color:#ffffff;text-decoration:none;text-transform:uppercase;letter-spacing:.06em;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;background-color:#003366;border:1px solid #003366;">
                        Reset my password →
                    </a>
                </td>
            </tr>
            </table>

            {{-- Expiry warning --}}
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
            <tr>
                <td style="background-color:#fdf8ec;border-left:3px solid #c49a00;padding:12px 16px;">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="20" valign="top" style="padding-top:1px;">
                            <span style="font-size:13px;">⏳</span>
                        </td>
                        <td style="padding-left:8px;">
                            <div style="font-size:10px;font-weight:bold;color:#8a5500;text-transform:uppercase;letter-spacing:.1em;margin-bottom:3px;">Link expires in 60 minutes</div>
                            <div style="font-size:12px;color:#6b7f96;line-height:1.55;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;">This reset link is only valid for one hour. If it expires, return to the portal and request a new link.</div>
                        </td>
                    </tr>
                    </table>
                </td>
            </tr>
            </table>

            {{-- Didn't request notice --}}
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
            <tr>
                <td style="background-color:#e8eef5;border-left:3px solid #003366;padding:12px 16px;">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="20" valign="top" style="padding-top:1px;">
                            <span style="font-size:13px;">ℹ</span>
                        </td>
                        <td style="padding-left:8px;">
                            <div style="font-size:10px;font-weight:bold;color:#003366;text-transform:uppercase;letter-spacing:.1em;margin-bottom:3px;">Didn't request this?</div>
                            <div style="font-size:12px;color:#6b7f96;line-height:1.55;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;">If you didn't request a password reset, no action is needed — your password will remain unchanged. If you're concerned about your account, contact your Group Controller.</div>
                        </td>
                    </tr>
                    </table>
                </td>
            </tr>
            </table>

            {{-- Divider --}}
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">
            <tr><td style="border-top:1px solid #dde2e8;font-size:0;line-height:0;">&nbsp;</td></tr>
            </table>

            {{-- Fallback URL --}}
            <p style="font-size:11px;color:#9aa3ae;line-height:1.6;margin:0;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;">
                If the button above doesn't work, copy and paste this link into your browser:
            </p>
            <p style="font-size:11px;margin:6px 0 0;word-break:break-all;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;">
                <a href="{{ $url }}" style="color:#003366;text-decoration:underline;">{{ $url }}</a>
            </p>

        </td>
    </tr>

    {{-- ── CALLSIGN STRIP ── --}}
    @if ($notifiable->callsign)
    <tr>
        <td style="background-color:#f2f5f9;border-top:1px solid #dde2e8;border-bottom:1px solid #dde2e8;padding:10px 32px;" class="px-mobile">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <span style="font-size:10px;color:#9aa3ae;font-weight:bold;text-transform:uppercase;letter-spacing:.08em;">Account</span>
                </td>
                <td align="right">
                    <span style="display:inline-block;padding:2px 10px;background:#e8eef5;border:1px solid rgba(0,51,102,.2);font-size:11px;font-weight:bold;color:#003366;letter-spacing:.08em;text-transform:uppercase;">
                        {{ strtoupper($notifiable->callsign) }}
                    </span>
                    <span style="font-size:11px;color:#6b7f96;margin-left:8px;">{{ $notifiable->email }}</span>
                </td>
            </tr>
            </table>
        </td>
    </tr>
    @endif

    {{-- ── FOOTER ── --}}
    <tr>
        <td style="background-color:#eef1f6;padding:20px 32px;border-top:1px solid #dde2e8;" class="px-mobile">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td valign="top" width="28">
                    <table role="presentation" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="width:26px;height:26px;background-color:#C8102E;text-align:center;vertical-align:middle;">
                            <span style="font-size:8px;font-weight:bold;color:#fff;letter-spacing:.04em;text-transform:uppercase;line-height:1.2;display:block;padding:2px;">RAY<br>NET</span>
                        </td>
                    </tr>
                    </table>
                </td>
                <td style="padding-left:10px;">
                    <div style="font-size:11px;font-weight:bold;color:#003366;text-transform:uppercase;letter-spacing:.04em;margin-bottom:2px;">{{ \App\Helpers\RaynetSetting::groupName() }} · Members' Portal</div>
                    <div style="font-size:11px;color:#9aa3ae;line-height:1.55;">
                        This is an automated security email. Do not reply.<br>
                        Radio Amateurs' Emergency Network · Zone 10 · Merseyside
                    </div>
                </td>
            </tr>
            </table>
        </td>
    </tr>

    {{-- ── BOTTOM RULE ── --}}
    <tr>
        <td style="background-color:#C8102E;height:4px;font-size:0;line-height:0;">&nbsp;</td>
    </tr>

</table>

</td></tr>
</table>

</body>
</html>