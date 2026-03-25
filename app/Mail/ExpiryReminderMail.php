<?php

namespace App\Mail;

use App\Models\Wedding;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExpiryReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @var string Nama pemesan */
    public string $customerName;

    public function __construct(
        public Wedding $wedding,
        string $customerName,
    ) {
        $this->customerName = $customerName;
    }

    public function envelope(): Envelope
    {
        $name = $this->wedding->bride_name;
        if ($this->wedding->groom_name) {
            $name .= ' & ' . $this->wedding->groom_name;
        }
        return new Envelope(
            subject: "🔔 2 Hari Lagi Expired — Undangan «{$name}» | " . config('app.name', 'TretanInvite'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.expiry-reminder');
    }
}
