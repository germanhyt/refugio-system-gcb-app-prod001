<?php

namespace App\Mail;

use App\Models\ComplaintBookEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ComplaintBookReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ComplaintBookEntry $entry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva hoja del Libro de reclamaciones — Refugio Gastronómico',
            replyTo: filled($this->entry->email) ? [$this->entry->email] : [],
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'mail.complaint-book-received',
        );
    }
}
