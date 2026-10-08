<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket Status Updated</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f4f4f5; margin: 0; padding: 24px; color: #18181b;">
    <div style="max-width: 560px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; border: 1px solid #e4e4e7; padding: 32px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <div style="margin-bottom: 24px;">
            <span style="font-size: 18px; font-weight: 800; color: #4f46e5; letter-spacing: -0.5px;">STMU MIS Support</span>
        </div>
        <div style="display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; background-color: #e0e7ff; color: #4338ca; margin-bottom: 12px;">
            {{ $ticket->ticket_number }}
        </div>
        <h1 style="font-size: 20px; font-weight: 700; color: #18181b; margin-top: 0; margin-bottom: 12px;">Ticket Status Updated</h1>
        <p style="font-size: 14px; line-height: 22px; color: #52525b; margin-bottom: 16px;">
            Hello <strong>{{ $ticket->raisedBy?->name }}</strong>,
        </p>
        <p style="font-size: 14px; line-height: 22px; color: #52525b; margin-bottom: 16px;">
            Your ticket <strong>"{{ $ticket->subject }}"</strong> has been changed from <span style="text-decoration: line-through; color: #71717a;">{{ ucfirst(str_replace('_', ' ', $oldStatus)) }}</span> to <strong style="color: #4f46e5;">{{ ucfirst(str_replace('_', ' ', $newStatus)) }}</strong>.
        </p>
        <div style="margin-top: 24px;">
            <a href="{{ url('/workspace/' . $ticket->workspace->slug . '/tickets/my') }}" style="display: inline-block; background-color: #4f46e5; color: #ffffff; padding: 10px 20px; font-size: 13px; font-weight: 600; text-decoration: none; border-radius: 6px;">
                View Ticket Details
            </a>
        </div>
    </div>
</body>
</html>
