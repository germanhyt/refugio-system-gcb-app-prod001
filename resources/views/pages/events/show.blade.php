@extends('layouts.app')

@section('title', $event->title.' | Eventos | Refugio Gastronómico')

@push('json-ld')
    @if(! empty($eventLd))
        <script type="application/ld+json">{!! $eventLd !!}</script>
    @endif
@endpush

@section('content')
<section class="relative bg-[var(--color-primary)] pb-10 pt-28 text-white">
    <div class="container-refugio">
        <a href="{{ route('events.index') }}" class="inline-flex text-sm font-semibold uppercase tracking-wide text-white/70 hover:text-[var(--color-accent)]">← Eventos</a>
        <div class="mt-6 flex flex-wrap items-end gap-4">
            <div class="rounded-lg bg-white/10 px-4 py-3 text-center">
                <p class="text-xs uppercase tracking-wider">{{ $event->day_abbreviation }}</p>
                <p class="font-display text-4xl font-bold leading-none">{{ $event->day_number }}</p>
            </div>
            <div>
                <h1 class="font-display text-4xl font-bold tablet:text-5xl">{{ $event->title }}</h1>
                <p class="mt-2 text-white/70">{{ $event->event_date?->format('d/m/Y') }}@if($event->event_time) · {{ \Illuminate\Support\Str::of($event->event_time)->substr(0,5) }}@endif</p>
            </div>
        </div>
    </div>
</section>

<section class="py-[var(--section-gap)]">
    <div class="container-refugio grid gap-10 desktop:grid-cols-2">
        @if($event->getFirstMediaUrl('featured_image'))
            <img src="{{ $event->getFirstMediaUrl('featured_image') }}" alt="{{ $event->title }}" class="w-full rounded-card object-cover">
        @endif
        <div class="prose max-w-none">
            @if($event->description)
                {!! $event->description !!}
            @else
                <p>Únete a este show en Refugio Gastronómico. Ingreso libre.</p>
            @endif
            <a href="{{ $siteSettings->whatsapp_url }}" target="_blank" rel="noopener" class="btn-cta mt-6 inline-flex no-underline">Reservar</a>
        </div>
    </div>
</section>
@endsection
