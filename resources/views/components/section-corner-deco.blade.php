@props([
    'position' => 'top-right',
    'tone' => 'teal',
    'image' => 'images/refugio/hojas-footer.png',
])

<img
    src="{{ asset($image) }}"
    alt=""
    class="rg-section-deco rg-section-deco--{{ $position }} rg-section-deco--tone-{{ $tone }}"
    aria-hidden="true"
    {{ $attributes }}
>
