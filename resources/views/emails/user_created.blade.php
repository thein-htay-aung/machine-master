<!DOCTYPE html>
<html>
<head>
    <title>User Account Created</title>
</head>
<body>
    <h1>Welcome to the System</h1>
    <p>Dear {{ $user->name }},</p>
    <p>Please verify your email address by clicking the link below:</p>
    <p><a href="{{ $verificationUrl }}">Verify Email</a></p>

    @isset($password)
        <p>Once verified, you can log in with your email and the following password:</p>
        <p><strong>Password: {{ $password }}</strong></p>
        <p>Please change your password after first login.</p>
    @endisset
    <p>Best regards,<br>Admin</p>
</body>
</html>