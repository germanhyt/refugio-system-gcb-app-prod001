@props(['categories', 'active' => ''])

<nav class="rg-rest-filter-nav" aria-label="Categorías de restaurantes">
    <a
        href="{{ route('restaurants.index') }}"
        class="rg-rest-filter-link {{ $active === '' ? 'is-active' : '' }}"
    >Todos</a>
    @foreach($categories as $category)
        <a
            href="{{ route('restaurants.index', ['categoria' => $category->slug]) }}"
            class="rg-rest-filter-link {{ $active === $category->slug ? 'is-active' : '' }}"
        >{{ $category->name }}</a>
    @endforeach
</nav>
