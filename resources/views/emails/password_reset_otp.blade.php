<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Password Reset OTP' }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #0d1526;
            margin: 0;
            padding: 30px 15px;
            color: #f8fafc;
            line-height: 1.6;
        }
        .container {
            max-width: 540px;
            margin: 0 auto;
            background: #1e293b;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            border: 1px solid rgba(99,102,241,0.25);
        }
        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 30px 25px;
            text-align: center;
            border-bottom: 1px solid rgba(99,102,241,0.2);
        }
        .header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 6px 0 0 0;
            color: #ff7828;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .body {
            padding: 32px 28px;
        }
        .greeting {
            font-size: 16px;
            font-weight: 600;
            color: #f8fafc;
            margin-bottom: 12px;
        }
        .text {
            font-size: 14px;
            color: #94a3b8;
            margin-bottom: 24px;
            line-height: 1.65;
        }
        .otp-box {
            background: rgba(99,102,241,0.1);
            border: 2px dashed #6366f1;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin: 24px 0;
        }
        .otp-code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 34px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 10px;
            margin: 0;
            text-shadow: 0 0 20px rgba(99,102,241,0.6);
        }
        .otp-sub {
            font-size: 12px;
            color: #a5b4fc;
            margin-top: 8px;
            font-weight: 500;
        }
        .info-card {
            background: rgba(15, 23, 42, 0.6);
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 20px;
            border-left: 3px solid #ff7828;
        }
        .info-card p {
            margin: 0;
            font-size: 13px;
            color: #cbd5e1;
        }
        .footer {
            background: #0f172a;
            padding: 18px 25px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid rgba(148, 163, 184, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $appName }}</h1>
            <p>Security & Verification</p>
        </div>
        <div class="body">
            <div class="greeting">Hello, {{ $userName ?? 'User' }}!</div>
            <div class="text">
                We received a request to reset your password for your <strong>{{ $appName }}</strong> account. Use the following One-Time Password (OTP) to complete your password reset:
            </div>

            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
                <div class="otp-sub">⏰ Valid for 10 minutes only</div>
            </div>

            <div class="info-card">
                <p>🔒 <strong>Security Warning:</strong> Never share this OTP code with anyone. Our support team will never ask for your OTP.</p>
            </div>

            <div class="text" style="font-size: 13px; color: #64748b; margin-bottom: 0;">
                If you did not request a password reset, you can safely ignore this email. Your password will remain unchanged.
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
        </div>
    </div>
</body>
</html>
