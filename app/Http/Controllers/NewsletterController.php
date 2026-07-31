<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:newsletter_subscribers,email'],
        ], [
            'email.unique' => 'Este correo ya está suscrito.',
        ]);

        NewsletterSubscriber::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subscribed_at' => now(),
        ]);

        return back()->with('newsletter_success', '¡Gracias! Ya estás suscrito.');
    }
}
