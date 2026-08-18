<?php

namespace App\Mail;

use App\Models\PageInquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PageInquiryReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PageInquiry $inquiry) {}

    public function envelope(): Envelope
    {
        $page = $this->inquiry->page_slug === 'convocatorias' ? 'Convocatoria' : 'Contacto';

        return new Envelope(
            subject: "Nuevo mensaje de {$page} — Refugio Gastronómico",
            replyTo: [$this->inquiry->email],
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'mail.page-inquiry-received',
        );
    }
}
