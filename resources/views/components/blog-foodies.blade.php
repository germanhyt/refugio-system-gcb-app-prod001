@props(['posts'])

<section class="rg-blog-section relative overflow-hidden py-[var(--section-gap)]">
    <div
        class="pointer-events-none absolute inset-0 bg-cover bg-center opacity-40"
        style="background-image: url('{{ asset('images/refugio/bg_contacto-home.jpg') }}');"
        aria-hidden="true"
    ></div>
    <div class="container-refugio relative">
        <div class="mb-12 text-center" data-aos="fade-up">
            <h2 class="section-title text-3xl tablet:text-5xl">
                Blog <span class="accent reveal-line">de foodies para foodies</span>
            </h2>
        </div>

        <div class="grid gap-6 tablet:grid-cols-2 desktop:grid-cols-4">
            @foreach($posts as $i => $post)
                @php($img = $post->getFirstMediaUrl('featured_image'))
                <article
                    class="group flex flex-col bg-white shadow-sm transition duration-500 hover:-translate-y-2 hover:shadow-[var(--shadow-card)]"
                    data-aos="fade-up"
                    data-aos-delay="{{ min($i * 80, 320) }}"
                >
                    <a href="{{ $post->external_url ?: '#' }}" @if($post->external_url) target="_blank" rel="noopener" @endif class="relative block overflow-hidden">
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
                    <div class="flex flex-1 flex-col p-4">
                        <h3 class="font-display text-lg font-semibold leading-snug text-primary transition group-hover:text-accent">
                            <a href="{{ $post->external_url ?: '#' }}" @if($post->external_url) target="_blank" rel="noopener" @endif>
                                {{ $post->title }}
                            </a>
                        </h3>
                        @if($post->rating)
                            <div class="mt-3 flex items-center gap-2 text-accent" aria-label="{{ number_format($post->rating, 1) }} de 5">
                                <div class="flex gap-0.5 text-sm">
                                    @for($s = 1; $s <= 5; $s++)
                                        <span class="{{ $s <= round($post->rating) ? 'opacity-100' : 'opacity-25' }}">★</span>
                                    @endfor
                                </div>
                                <span class="text-xs text-muted">{{ number_format($post->rating, 1) }}/5</span>
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
