<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Event::query()
            ->active()
            ->orderByDesc('event_date')
            ->with('media')
            ->paginate(12);

        return view('pages.events.index', compact('events'));
    }

    public function show(Event $event): View
    {
        abort_unless($event->is_active, 404);

        $event->load('media');

        return view('pages.events.show', compact('event'));
    }
}
