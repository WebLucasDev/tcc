<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .content {
            padding: 20px 0;
        }
        .button {
            display: inline-block;
            background-color: #e67e22;
            color: white !important;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .footer {
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Password Reset</h2>
    </div>
    
    <div class="content">
        <p>Hello {{ $user->name ?? 'user' }},</p>
        
        <p>You have requested a password reset for your account. To create a new password, click the button below:</p>
        
        <p style="text-align: center;">
            <a href="{{ url('forgot-password/'.$token.'?email='.$email) }}" class="button">Reset Password</a>
        </p>
        
        <p>If you did not request a password reset, please ignore this email.</p>
        
        <p>This link expires in 60 minutes.</p>
        
        <p>Best regards,<br>Support Team</p>
    </div>
    
    <div class="footer">
        <p>This is an automated email, please do not reply.</p>
    </div>
</body>
</html>
