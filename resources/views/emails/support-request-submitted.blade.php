<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>New RAYNET Support Request</title>
</head>
<body style="margin:0;padding:0;background:#F2F2F2;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#F2F2F2;padding:40px 0;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

        <!-- BANNER -->
        <tr>
          <td style="border-radius:8px 8px 0 0;overflow:hidden;line-height:0;">
            <img src="https://{{ \App\Helpers\RaynetSetting::siteUrl() }}/images/raynet-uk-liverpool-banner.png" alt="{{ \App\Helpers\RaynetSetting::groupName() }}" width="600" style="display:block;width:100%;max-width:600px;">
          </td>
        </tr>

        <!-- ALERT BANNER -->
        <tr>
          <td style="background:#C8102E;padding:12px 40px;text-align:center;">
            <p style="margin:0;font-size:15px;color:#ffffff;font-weight:bold;">📡 New Support Request — {{ $data['event_name'] ?? 'Unknown Event' }}</p>
          </td>
        </tr>

        <!-- BODY -->
        <tr>
          <td style="background:#ffffff;padding:30px 40px;">

            <p style="margin:0 0 24px;font-size:15px;color:#4A4A4A;">A new event support request has been submitted via <a href="https://{{ \App\Helpers\RaynetSetting::siteUrl() }}" style="color:#C8102E;">{{ \App\Helpers\RaynetSetting::siteUrl() }}</a>. Details are below.</p>

            <!-- Event Details -->
            <h2 style="margin:0 0 16px;font-size:16px;font-weight:bold;color:#003366;border-bottom:2px solid #F2F2F2;padding-bottom:8px;">🗓️ Event Details</h2>
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
              <tr>
                <td style="padding:8px 0;border-bottom:1px solid #F2F2F2;width:40%;font-size:14px;color:#4A4A4A;font-weight:bold;">Event Name</td>
                <td style="padding:8px 0;border-bottom:1px solid #F2F2F2;font-size:14px;color:#1A1A1A;">{{ $data['event_name'] ?? '-' }}</td>
              </tr>
              <tr>
                <td style="padding:8px 0;border-bottom:1px solid #F2F2F2;font-size:14px;color:#4A4A4A;font-weight:bold;">Event Date</td>
                <td style="padding:8px 0;border-bottom:1px solid #F2F2F2;font-size:14px;color:#1A1A1A;">{{ $data['event_date'] ?? 'Not specified' }}</td>
              </tr>
              <tr>
                <td style="padding:8px 0;border-bottom:1px solid #F2F2F2;font-size:14px;color:#4A4A4A;font-weight:bold;">Location</td>
                <td style="padding:8px 0;border-bottom:1px solid #F2F2F2;font-size:14px;color:#1A1A1A;">{{ $data['location'] ?? '-' }}</td>
              </tr>
              <tr>
                <td style="padding:8px 0;font-size:14px;color:#4A4A4A;font-weight:bold;">Organising Body</td>
                <td style="padding:8px 0;font-size:14px;color:#1A1A1A;">{{ $data['org'] ?? 'Not specified' }}</td>
              </tr>
            </table>

            <!-- Contact Details -->
            <h2 style="margin:0 0 16px;font-size:16px;font-weight:bold;color:#003366;border-bottom:2px solid #F2F2F2;padding-bottom:8px;">👤 Contact Details</h2>
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
              <tr>
                <td style="padding:8px 0;border-bottom:1px solid #F2F2F2;width:40%;font-size:14px;color:#4A4A4A;font-weight:bold;">Name</td>
                <td style="padding:8px 0;border-bottom:1px solid #F2F2F2;font-size:14px;color:#1A1A1A;">{{ $data['contact_name'] ?? '-' }}</td>
              </tr>
              <tr>
                <td style="padding:8px 0;border-bottom:1px solid #F2F2F2;font-size:14px;color:#4A4A4A;font-weight:bold;">Email</td>
                <td style="padding:8px 0;border-bottom:1px solid #F2F2F2;font-size:14px;color:#1A1A1A;"><a href="mailto:{{ $data['contact_email'] ?? '' }}" style="color:#C8102E;">{{ $data['contact_email'] ?? '-' }}</a></td>
              </tr>
              <tr>
                <td style="padding:8px 0;font-size:14px;color:#4A4A4A;font-weight:bold;">Phone</td>
                <td style="padding:8px 0;font-size:14px;color:#1A1A1A;">{{ $data['contact_phone'] ?? 'Not provided' }}</td>
              </tr>
            </table>

            <!-- Details -->
            <h2 style="margin:0 0 16px;font-size:16px;font-weight:bold;color:#003366;border-bottom:2px solid #F2F2F2;padding-bottom:8px;">📋 Event Outline & RAYNET Help Needed</h2>
            <div style="background:#F2F2F2;border-left:4px solid #C8102E;border-radius:4px;padding:16px;font-size:14px;color:#1A1A1A;line-height:1.6;">
              {!! nl2br(e($data['details'] ?? '-')) !!}
            </div>

          </td>
        </tr>

        <!-- FOOTER -->
        <tr>
          <td style="background:#003366;border-radius:0 0 8px 8px;padding:20px 40px;text-align:center;">
            <p style="margin:0;font-size:13px;color:#aac4e0;">{{ \App\Helpers\RaynetSetting::groupName() }} · {{ \App\Helpers\RaynetSetting::groupNumber() }}/ · <a href="https://{{ \App\Helpers\RaynetSetting::siteUrl() }}" style="color:#ffffff;">{{ \App\Helpers\RaynetSetting::siteUrl() }}</a></p>
            <p style="margin:6px 0 0;font-size:12px;color:#6a8faf;">This email was automatically generated from a website form submission.</p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>
