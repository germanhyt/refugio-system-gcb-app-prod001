@props(['event'])

<a href="{{ route('events.show', $event) }}" class="rg-event-item group">
    <div class="rg-event-date">
        <span class="rg-event-day-abbr">{{ $event->day_abbreviation }}</span>
        <span class="rg-event-day-num">{{ $event->day_number }}</span>
    </div>
    <h3 class="rg-event-title">{{ $event->title }}</h3>
</a>
