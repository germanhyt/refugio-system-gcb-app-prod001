<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventOffer;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $offers = EventOffer::query()
            ->active()
            ->ordered()
            ->with('media')
            ->get();

        return view('pages.events.index', compact('offers'));
    }

    public function show(Event $event): View
    {
        abort_unless($event->is_active, 404);

        $event->load('media');

        return view('pages.events.show', compact('event'));
    }
}
