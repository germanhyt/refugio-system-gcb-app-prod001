<?php

namespace App\Http\Controllers;

use App\Mail\ComplaintBookReceived;
use App\Mail\PageInquiryReceived;
use App\Models\ComplaintBookEntry;
use App\Models\PageInquiry;
use App\Models\SiteSetting;
use App\Models\VisitInfo;
use App\Services\Scraper\MirroredPageScraper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MirroredPageController extends Controller
{
    public function terms(MirroredPageScraper $scraper): View
    {
        return $this->render('terminos-y-condiciones', $scraper);
    }

    public function privacy(MirroredPageScraper $scraper): View
    {
        return $this->render('politica-privacidad', $scraper);
    }

    public function complaintsBook(MirroredPageScraper $scraper): View
    {
        return $this->render('libro-de-reclamaciones', $scraper);
    }

    public function convocatoria(MirroredPageScraper $scraper): View
    {
        return $this->render('convocatoria', $scraper);
    }

    public function storeInquiry(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'page_slug' => ['required', 'in:convocatorias'],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'company' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:4000'],
            'website' => ['nullable', 'max:0'], // honeypot
        ], [
            'message.min' => 'Tu mensaje debe tener al menos 10 caracteres.',
        ]);

        $inquiry = PageInquiry::query()->create([
            'page_slug' => $validated['page_slug'],
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'company' => $validated['company'] ?? null,
            'message' => $validated['message'],
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        $recipient = VisitInfo::current()->email;

        if ($recipient) {
            try {
                Mail::to($recipient)->send(new PageInquiryReceived($inquiry));
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        return back()->with('inquiry_success', 'Gracias, recibimos tu mensaje y te responderemos pronto.');
    }

    public function storeComplaint(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'document_type' => ['required', Rule::in(['DNI', 'CE', 'Pasaporte', 'RUC'])],
            'document_number' => ['required', 'string', 'max:40'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'department' => ['required', Rule::in(ComplaintBookEntry::departments())],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
            'email' => ['required', 'email', 'max:255'],
            'parent_name' => ['nullable', 'string', 'max:255'],
            'claimed_amount' => ['nullable', 'string', 'max:80'],
            'product_description' => ['required', 'string', 'min:10', 'max:4000'],
            'claim_type' => ['required', Rule::in(['Queja', 'Reclamo'])],
            'claim_detail' => ['required', 'string', 'min:10', 'max:4000'],
            'consumer_request' => ['required', 'string', 'min:10', 'max:4000'],
            'website' => ['nullable', 'max:0'],
        ], [
            'document_type.required' => 'Selecciona un tipo de documento.',
            'claim_type.required' => 'Indica si es queja o reclamo.',
            'product_description.min' => 'Describe el producto o servicio con más detalle.',
            'claim_detail.min' => 'El detalle del reclamo debe tener al menos 10 caracteres.',
            'consumer_request.min' => 'El pedido del consumidor debe tener al menos 10 caracteres.',
        ]);

        unset($validated['website']);

        $entry = ComplaintBookEntry::query()->create([
            ...$validated,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        $recipients = SiteSetting::current()->complaintBookRecipients();

        if ($recipients !== []) {
            try {
                Mail::to($recipients)->send(new ComplaintBookReceived($entry));
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        return back()->with(
            'inquiry_success',
            'Registramos tu hoja de reclamación. Te responderemos en un plazo no mayor a 30 días calendario.'
        );
    }

    private function render(string $page, MirroredPageScraper $scraper): View
    {
        $payload = $scraper->get($page);

        $remoteSlug = $payload['remote_slug'];
        $payload['show_inquiry_form'] = $remoteSlug === 'convocatorias';
        $payload['show_complaint_form'] = $remoteSlug === 'libro-de-reclamaciones';
        $payload['visit'] = $payload['show_inquiry_form'] ? VisitInfo::current() : null;
        $payload['departments'] = ComplaintBookEntry::departments();

        if ($payload['show_complaint_form']) {
            $settings = SiteSetting::current();
            $payload['title'] = filled($settings->hero_title_complaints)
                ? $settings->hero_title_complaints
                : ($payload['title'] ?? 'Libro de reclamaciones');
            $payload['hero_image'] = $settings->pageHeroBannerUrl('complaints') ?: ($payload['hero_image'] ?? null);
        }

        return view('pages.mirrored-page', $payload);
    }
}
