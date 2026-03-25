<?php

namespace App\Mail;

use App\Models\Guest;
use App\Models\Wedding;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RsvpNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Wedding $wedding,
        public Guest   $guest,
    ) {}

    public function envelope(): Envelope
    {
        $status = $this->guest->is_attending ? '✔ Hadir' : '✘ Tidak Hadir';
        return new Envelope(
            subject: "[RSVP] {$this->guest->guest_name} — {$status} | {$this->wedding->bride_name}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.rsvp-notification');
    }
}
