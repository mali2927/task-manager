<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccessRequestApprovedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $passwordResetUrl,
        public string $role = 'member'
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Access Request has been Approved',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.access-requests.approved',
        );
    }
}
