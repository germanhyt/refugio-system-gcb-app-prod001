@extends('layouts.app')

@section('title', ($post->title).' | Blog | Refugio Gastronómico')
@section('meta_description', $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->body ?? ''), 160))

@php
    $heroImage = $post->getFirstMediaUrl('featured_image');
@endphp

@section('content')
<article class="rg-blog-detail">
    <header class="rg-blog-detail-hero" @if($heroImage) style="background-image: url('{{ $heroImage }}');" @endif>
        <div class="rg-blog-detail-hero-overlay"></div>
        <div class="container-refugio relative z-10">
            <a href="{{ route('blog.index') }}" class="rg-blog-detail-back">← Blog</a>
            <div class="rg-blog-detail-hero-copy">
                @if($post->category)
                    <p class="rg-blog-detail-cat">{{ $post->category }}</p>
                @endif
                <h1 class="rg-blog-detail-title">{{ $post->title }}</h1>
                <div class="rg-blog-detail-meta">
                    @if($post->author_handle)
                        <span>{{ $post->author_handle }}</span>
                    @endif
                    @if($post->published_at)
                        <span aria-hidden="true">·</span>
                        <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('d/m/Y') }}</time>
                    @endif
                    @if($post->rating)
                        <span aria-hidden="true">·</span>
                        <span aria-label="{{ number_format($post->rating, 1) }} de 5">★ {{ number_format($post->rating, 1) }}/5</span>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <section class="rg-blog-detail-body">
        <div class="container-refugio">
            <div class="rg-blog-detail-layout">
                <div class="rg-blog-detail-content" data-aos="fade-up">
                    @if($post->excerpt)
                        <p class="rg-blog-detail-excerpt">{{ $post->excerpt }}</p>
                    @endif

                    @if($post->body)
                        <div class="rg-blog-detail-copy">
                            {!! $post->body !!}
                        </div>
                    @else
                        <p class="rg-blog-detail-copy">
                            Estamos preparando el contenido completo de este artículo.
                            @if($post->external_url)
                                Mientras tanto puedes
                                <a href="{{ $post->external_url }}" target="_blank" rel="noopener noreferrer">ver la nota original</a>.
                            @endif
                        </p>
                    @endif

                    <div class="rg-blog-detail-actions">
                        <a href="{{ route('blog.index') }}" class="btn-outline">Volver al blog</a>
                        @if($post->external_url)
                            <a href="{{ $post->external_url }}" target="_blank" rel="noopener noreferrer" class="rg-blog-detail-origin">
                                Ver en origen
                            </a>
                        @endif
                    </div>
                </div>

                @if($related->isNotEmpty())
                    <aside class="rg-blog-detail-related" data-aos="fade-up" data-aos-delay="80">
                        <h2 class="rg-blog-detail-related-title">También te puede gustar</h2>
                        <ul class="rg-blog-detail-related-list">
                            @foreach($related as $item)
                                <li>
                                    <a href="{{ route('blog.show', $item) }}" class="rg-blog-detail-related-card">
                                        @if($item->getFirstMediaUrl('featured_image'))
                                            <img src="{{ $item->getFirstMediaUrl('featured_image') }}" alt="{{ $item->title }}" loading="lazy">
                                        @endif
                                        <span>
                                            <strong>{{ $item->title }}</strong>
                                            @if($item->author_handle)
                                                <small>{{ $item->author_handle }}</small>
                                            @endif
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </aside>
                @endif
            </div>
        </div>
    </section>
</article>
@endsection
