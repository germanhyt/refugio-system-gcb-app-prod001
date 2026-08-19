<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SiteMailTest extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $recipient) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Prueba de correo — Refugio Gastronómico',
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'mail.site-mail-test',
        );
    }
}
