<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Code</title>
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8fafc;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding: 30px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .content {
            background: white;
            padding: 40px;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .verification-code {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            font-size: 2.5rem;
            font-weight: bold;
            text-align: center;
            padding: 20px;
            border-radius: 12px;
            letter-spacing: 8px;
            margin: 30px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>AI Legal Assistant</h1>
            <p>Two-Factor Authentication</p>
        </div>
        <div class="content">
            <h2>Your Verification Code</h2>
            <p>Hello,</p>
            <p>Use the following verification code to complete your login:</p>
            
            <div class="verification-code">
                {{ $verificationCode }}
            </div>
            
            <p>This code will expire in 10 minutes. If you didn't request this code, please ignore this email.</p>
            
            <div class="footer">
                <p>&copy; {{ date('Y') }} AI Legal Assistant. All rights reserved.</p>
                <p>This is an automated message, please do not reply.</p>
            </div>
        </div>
    </div>
</body>
</html>