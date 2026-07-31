@extends('layouts.app')

@section('title', 'Restaurantes | Refugio Gastronómico')

@section('content')
{{-- Hero --}}
<section
    class="rg-rest-hero"
    style="background-image: url('{{ asset('images/refugio/restaurantes-hero.jpg') }}');"
>
    <div class="rg-rest-hero-overlay" aria-hidden="true"></div>
    <div class="container-refugio relative z-10 px-6 text-center">
        <h1 class="rg-rest-hero-title">Restaurantes</h1>
    </div>
</section>

{{-- Filtros por categoría --}}
<section class="rg-rest-filters">
    <div class="container-refugio">
        <x-category-filter :categories="$categories" :active="$categorySlug" />
    </div>
</section>

{{-- Grilla --}}
<section class="rg-rest-listing">
    <div class="container-refugio">
        <div class="rg-rest-grid">
            @forelse($restaurants as $i => $restaurant)
                <div data-aos="fade-up" data-aos-delay="{{ min($i * 40, 240) }}">
                    <x-restaurant-card :restaurant="$restaurant" />
                </div>
            @empty
                <p class="col-span-full py-16 text-center text-[#776f69]">No hay restaurantes en esta categoría.</p>
            @endforelse
        </div>

        @if($restaurants->hasPages())
            <div class="rg-rest-pagination mt-12">
                {{ $restaurants->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
