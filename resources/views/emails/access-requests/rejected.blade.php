<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Access Request Update</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f4f4f5; margin: 0; padding: 24px; color: #18181b;">
    <div style="max-width: 560px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; border: 1px solid #e4e4e7; padding: 32px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <div style="margin-bottom: 24px;">
            <span style="font-size: 18px; font-weight: 800; color: #4f46e5; letter-spacing: -0.5px;">STMU MIS Portal</span>
        </div>
        <h1 style="font-size: 20px; font-weight: 700; color: #18181b; margin-top: 0; margin-bottom: 12px;">Access Request Update</h1>
        <p style="font-size: 14px; line-height: 22px; color: #52525b; margin-bottom: 16px;">
            Hello <strong>{{ $name }}</strong>,
        </p>
        <p style="font-size: 14px; line-height: 22px; color: #52525b; margin-bottom: 16px;">
            Thank you for your interest in accessing the workspace. After reviewing your application, we regret to inform you that your access request could not be approved at this time.
        </p>
        @if($rejectionReason)
            <div style="background-color: #f4f4f5; border-left: 4px solid #ef4444; padding: 14px 16px; border-radius: 4px; margin-bottom: 20px;">
                <p style="font-size: 13px; font-weight: 600; color: #18181b; margin: 0 0 4px 0;">Reason provided:</p>
                <p style="font-size: 13px; color: #52525b; margin: 0; line-height: 20px;">{{ $rejectionReason }}</p>
            </div>
        @endif
        <p style="font-size: 13px; line-height: 20px; color: #71717a; margin-top: 24px;">
            If you believe this is in error, please contact your department administrator.
        </p>
    </div>
</body>
</html>
