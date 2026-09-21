<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Ticket Raised</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f4f4f5; margin: 0; padding: 24px; color: #18181b;">
    <div style="max-width: 560px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; border: 1px solid #e4e4e7; padding: 32px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <div style="margin-bottom: 24px;">
            <span style="font-size: 18px; font-weight: 800; color: #4f46e5; letter-spacing: -0.5px;">STMU MIS Support</span>
        </div>
        <div style="display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; background-color: #e0e7ff; color: #4338ca; margin-bottom: 12px;">
            {{ $ticket->ticket_number }}
        </div>
        <h1 style="font-size: 20px; font-weight: 700; color: #18181b; margin-top: 0; margin-bottom: 12px;">{{ $ticket->subject }}</h1>
        <p style="font-size: 14px; line-height: 22px; color: #52525b; margin-bottom: 16px;">
            A new support ticket has been submitted by <strong>{{ $ticket->raisedBy?->name }}</strong> and is currently in the triage queue.
        </p>
        <div style="background-color: #fafafa; border: 1px solid #e4e4e7; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
            <div style="font-size: 12px; color: #71717a; margin-bottom: 6px;">
                <strong>Category:</strong> {{ $ticket->category?->name ?? 'General' }} | 
                <strong>Priority:</strong> <span style="text-transform: capitalize;">{{ $ticket->priority }}</span>
            </div>
            <div style="font-size: 13px; color: #3f3f46; line-height: 20px;">
                {{ Str::limit($ticket->description, 200) }}
            </div>
        </div>
        <div style="margin-top: 24px;">
            <a href="{{ url('/workspace/' . $ticket->workspace->slug . '/tickets/queue') }}" style="display: inline-block; background-color: #4f46e5; color: #ffffff; padding: 10px 20px; font-size: 13px; font-weight: 600; text-decoration: none; border-radius: 6px;">
                View in Triage Queue
            </a>
        </div>
    </div>
</body>
</html>
