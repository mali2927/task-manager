<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Access Approved</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f4f4f5; margin: 0; padding: 24px; color: #18181b;">
    <div style="max-width: 560px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; border: 1px solid #e4e4e7; padding: 32px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <div style="margin-bottom: 24px;">
            <span style="font-size: 18px; font-weight: 800; color: #4f46e5; letter-spacing: -0.5px;">STMU MIS Portal</span>
        </div>
        <h1 style="font-size: 20px; font-weight: 700; color: #18181b; margin-top: 0; margin-bottom: 12px;">Access Request Approved!</h1>
        <p style="font-size: 14px; line-height: 22px; color: #52525b; margin-bottom: 16px;">
            Hello <strong>{{ $user->name }}</strong>,
        </p>
        <p style="font-size: 14px; line-height: 22px; color: #52525b; margin-bottom: 24px;">
            Your request to join the workspace has been approved with the role of <strong style="color: #4f46e5;">{{ ucfirst($role) }}</strong>. Please click the button below to set up your password and access your account.
        </p>
        <div style="margin-bottom: 28px;">
            <a href="{{ $passwordResetUrl }}" style="display: inline-block; background-color: #4f46e5; color: #ffffff; padding: 12px 24px; font-size: 14px; font-weight: 600; text-decoration: none; border-radius: 8px;">
                Set Your Password &amp; Get Started
            </a>
        </div>
        <p style="font-size: 12px; line-height: 18px; color: #71717a; margin-bottom: 0;">
            If the button doesn't work, copy and paste this link into your browser:<br>
            <a href="{{ $passwordResetUrl }}" style="color: #4f46e5; word-break: break-all;">{{ $passwordResetUrl }}</a>
        </p>
    </div>
</body>
</html>
