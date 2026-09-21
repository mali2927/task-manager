<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccessRequestRejectedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public ?string $rejectionReason = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Update on Your Access Request',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.access-requests.rejected',
        );
    }
}
