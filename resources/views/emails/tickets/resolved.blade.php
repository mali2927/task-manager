<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket Resolved</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f4f4f5; margin: 0; padding: 24px; color: #18181b;">
    <div style="max-width: 560px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; border: 1px solid #e4e4e7; padding: 32px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <div style="margin-bottom: 24px;">
            <span style="font-size: 18px; font-weight: 800; color: #4f46e5; letter-spacing: -0.5px;">STMU MIS Support</span>
        </div>
        <div style="display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; background-color: #d1fae5; color: #065f46; margin-bottom: 12px;">
            {{ $ticket->ticket_number }} • RESOLVED
        </div>
        <h1 style="font-size: 20px; font-weight: 700; color: #18181b; margin-top: 0; margin-bottom: 12px;">Your Ticket Has Been Resolved</h1>
        <p style="font-size: 14px; line-height: 22px; color: #52525b; margin-bottom: 16px;">
            Hello <strong>{{ $ticket->raisedBy?->name }}</strong>,
        </p>
        <p style="font-size: 14px; line-height: 22px; color: #52525b; margin-bottom: 16px;">
            Your ticket <strong>"{{ $ticket->subject }}"</strong> has been marked as resolved by <strong>{{ $ticket->assignedTo?->name ?? 'our support team' }}</strong>.
        </p>
        @if($ticket->resolution_summary)
            <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
                <p style="font-size: 13px; font-weight: 700; color: #166534; margin: 0 0 6px 0;">Resolution Summary:</p>
                <p style="font-size: 13px; color: #15803d; margin: 0; line-height: 20px;">{{ $ticket->resolution_summary }}</p>
            </div>
        @endif
        <p style="font-size: 13px; line-height: 20px; color: #71717a; margin-bottom: 20px;">
            If you feel this issue is not fully resolved, you can reopen this ticket within <strong>7 days</strong>.
        </p>
        <div style="margin-top: 24px;">
            <a href="{{ url('/workspace/' . $ticket->workspace->slug . '/tickets/my') }}" style="display: inline-block; background-color: #10b981; color: #ffffff; padding: 10px 20px; font-size: 13px; font-weight: 600; text-decoration: none; border-radius: 6px;">
                Review Ticket &amp; Confirm
            </a>
        </div>
    </div>
</body>
</html>
