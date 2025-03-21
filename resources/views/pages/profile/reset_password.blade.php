<!DOCTYPE html>
<html>
<head>
    <title>Reset Your Password</title>
</head>
<body>
    <p>Hello,</p>
    <p>You requested to reset your password. Click the button below to set a new password:</p>
    <p>
        <a href="{{ $resetUrl }}" style="
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
        ">Reset Password</a>
    </p>
    <p>If you didn't request a password reset, please ignore this email.</p>
    <p>Thanks,<br>Your App Team</p>
</body>
</html>
