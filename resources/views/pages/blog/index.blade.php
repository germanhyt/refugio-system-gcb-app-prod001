@extends('layouts.app')

@section('title', 'Blog de foodies | Refugio Gastronómico')
@section('meta_description', 'Reseñas y experiencias gastronómicas de foodies en Refugio Gastronómico.')

@section('content')
<section class="rg-blog-page-hero">
    <div class="container-refugio relative z-10 px-6 text-center">
        <p class="rg-blog-page-kicker">Foodies</p>
        <h1 class="rg-blog-page-title">Blog de foodies para foodies</h1>
        <p class="rg-blog-page-lead">Historias, reseñas y hallazgos gastronómicos alrededor de Refugio.</p>
    </div>
</section>

<section class="rg-blog-page-body">
    <div class="container-refugio">
        @if($posts->isEmpty())
            <p class="text-center font-display text-lg text-[#776f69]">Pronto publicaremos nuevas reseñas.</p>
        @else
            <div class="grid gap-6 tablet:grid-cols-2 desktop:grid-cols-3">
                @foreach($posts as $i => $post)
                    @php($img = $post->getFirstMediaUrl('featured_image'))
                    <article
                        class="group flex flex-col bg-white shadow-sm transition duration-500 hover:-translate-y-2 hover:shadow-[var(--shadow-card)]"
                        data-aos="fade-up"
                        data-aos-delay="{{ min($i * 60, 240) }}"
                    >
                        <a href="{{ route('blog.show', $post) }}" class="relative block overflow-hidden">
                            <div class="aspect-[16/9] bg-[#1a1210]">
                                @if($img)
                                    <img
                                        src="{{ $img }}"
                                        alt="{{ $post->title }}"
                                        class="h-full w-full object-cover transition duration-700 group-hover:scale-105"
                                        loading="lazy"
                                    >
                                @endif
                            </div>
                            @if($post->author_handle)
                                <span class="absolute left-3 top-3 bg-white/95 px-2.5 py-1 font-accent text-sm text-primary shadow-sm">
                                    {{ $post->author_handle }}
                                </span>
                            @endif
                            <span class="absolute bottom-3 left-3 bg-[#29B587] px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-white">
                                {{ $post->category ?: 'Sin categoría' }}
                            </span>
                        </a>
                        <div class="flex flex-1 flex-col p-5">
                            <h2 class="font-display text-xl font-semibold leading-snug text-primary transition group-hover:text-accent">
                                <a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a>
                            </h2>
                            @if($post->excerpt)
                                <p class="mt-2 line-clamp-3 font-display text-sm font-light leading-relaxed text-[#776f69]">
                                    {{ $post->excerpt }}
                                </p>
                            @endif
                            <div class="mt-auto flex items-center justify-between gap-3 pt-4">
                                @if($post->rating)
                                    <div class="flex items-center gap-2 text-accent" aria-label="{{ number_format($post->rating, 1) }} de 5">
                                        <div class="flex gap-0.5 text-sm">
                                            @for($s = 1; $s <= 5; $s++)
                                                <span class="{{ $s <= round($post->rating) ? 'opacity-100' : 'opacity-25' }}">★</span>
                                            @endfor
                                        </div>
                                        <span class="text-xs text-muted">{{ number_format($post->rating, 1) }}/5</span>
                                    </div>
                                @else
                                    <span></span>
                                @endif
                                @if($post->published_at)
                                    <time class="text-xs uppercase tracking-wide text-[#776f69]" datetime="{{ $post->published_at->toDateString() }}">
                                        {{ $post->published_at->format('d/m/Y') }}
                                    </time>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
