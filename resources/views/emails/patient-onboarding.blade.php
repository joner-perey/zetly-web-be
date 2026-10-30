<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
  body { margin: 0; padding: 0; background-color: #f8fafc; font-family: 'Segoe UI', Arial, sans-serif; color: #0f172a; }
  .wrapper { padding: 32px 16px; }
  .card { max-width: 560px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
  .card-header { background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%); padding: 24px 28px; }
  .card-header h1 { margin: 0; font-size: 20px; font-weight: 700; color: #ffffff; letter-spacing: -0.3px; }
  .card-header p { margin: 6px 0 0; font-size: 13px; color: #99f6e4; }
  .card-body { padding: 28px; }
  .greeting { font-size: 15px; margin-bottom: 16px; }
  .details-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; margin-bottom: 24px; }
  .details-box table { width: 100%; border-collapse: collapse; }
  .details-box td { padding: 5px 0; font-size: 14px; }
  .details-box td:first-child { font-weight: 600; color: #475569; width: 50%; }
  .badge { display: inline-block; padding: 3px 10px; border-radius: 99px; font-size: 12px; font-weight: 700; }
  .badge-green { background: #d1fae5; color: #065f46; }
  .badge-slate { background: #f1f5f9; color: #475569; }
  .qr-section { text-align: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 24px; margin-bottom: 24px; }
  .qr-section h3 { margin: 0 0 6px; font-size: 14px; font-weight: 700; color: #0f172a; }
  .qr-section p { margin: 0 0 16px; font-size: 13px; color: #64748b; }
  .qr-section img { max-width: 220px; border-radius: 8px; border: 1px solid #e2e8f0; }
  .qr-section .caption { margin: 12px 0 0; font-size: 12px; color: #94a3b8; }
  .btn-link { display: inline-block; margin-top: 16px; background-color: #0f766e; color: #ffffff !important; text-decoration: none; padding: 11px 22px; border-radius: 7px; font-weight: 700; font-size: 14px; }
  .note { font-size: 13px; color: #64748b; line-height: 1.6; margin-bottom: 24px; }
  .card-footer { border-top: 1px solid #e2e8f0; padding: 18px 28px; background: #f8fafc; }
  .card-footer p { margin: 0; font-size: 12px; color: #94a3b8; line-height: 1.7; }
  .card-footer a { color: #0f766e; text-decoration: none; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="card">
    <!-- Header -->
    <div class="card-header">
      <h1>Welcome to Zetly Cashless Healthcare</h1>
      <p>Your account has been pre-registered by your healthcare administrator.</p>
    </div>

    <!-- Body -->
    <div class="card-body">
      <p class="greeting">Dear <strong>{{ $patientName }}</strong>,</p>
      <p class="note">
        Your cashless clinic privileges have been set up. Please scan the QR code below
        using the Zetly App to complete your account setup by choosing a username and password.
      </p>

      <!-- Details -->
      <div class="details-box">
        <table>
          <tr>
            <td>Zetly Member ID</td>
            <td><strong>{{ $clientCode }}</strong></td>
          </tr>
          <tr>
            <td>Insurance Provider</td>
            <td>{{ $insurance }}</td>
          </tr>
          <tr>
            <td>Policy / Certificate</td>
            <td>{{ $policyNumber }}</td>
          </tr>
          <tr>
            <td>Plan Coverage</td>
            <td>{{ $planType }}</td>
          </tr>
          <tr>
            <td>Cashless Clearance</td>
            <td>
              @if($cashless)
                <span class="badge badge-green">Eligible</span>
              @else
                <span class="badge badge-slate">Standard</span>
              @endif
            </td>
          </tr>
        </table>
      </div>

      <!-- QR Code Section -->
      <div class="qr-section">
        <h3>Scan with Zetly App to Activate</h3>
        <p>Open the Zetly App and scan the QR code attached to this email.</p>
        <p class="caption">QR image is attached as <strong>{{ $qrFileName }}</strong></p>
        <br>
        <a href="{{ $onboardUrl }}" class="btn-link">Activate Account Online Instead</a>
      </div>

      <p class="note">
        Once you complete the setup, your digital member pass will be active and you can use
        cashless consultation at any Zetly panel clinic.
      </p>
    </div>

    <!-- Footer -->
    <div class="card-footer">
      <p>
        Best regards,<br>
        <strong>Claims &amp; Operations Team</strong><br>
        Zetly Healthcare &bull; <a href="https://app.zetly.health">app.zetly.health</a>
      </p>
      <p style="margin-top: 10px;">
        If you did not expect this email, please ignore it or contact your administrator.
      </p>
    </div>
  </div>
</div>
</body>
</html>
