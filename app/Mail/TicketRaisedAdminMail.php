<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketRaisedAdminMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New Ticket Raised: [{$this->ticket->ticket_number}] {$this->ticket->subject}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tickets.raised-admin',
        );
    }
}
