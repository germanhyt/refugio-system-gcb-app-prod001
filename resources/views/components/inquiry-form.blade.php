@props([
    'pageSlug' => 'contacto',
    'title' => 'Envíanos un mensaje',
    'subtitle' => 'Cuéntanos en qué te podemos ayudar y te contactamos.',
])

<div class="rg-inquiry-form-panel">
    @if(filled($title))
        <h2 class="rg-inquiry-form-title">{{ $title }}</h2>
    @endif
    @if(filled($subtitle))
        <p class="rg-inquiry-form-subtitle">{{ $subtitle }}</p>
    @endif

    @if(session('inquiry_success'))
        <div class="rg-inquiry-success" role="status">{{ session('inquiry_success') }}</div>
    @endif

    <form method="POST" action="{{ route('info.store') }}" class="rg-inquiry-form" novalidate>
        @csrf
        <input type="hidden" name="page_slug" value="{{ $pageSlug }}">
        <input type="text" name="website" class="rg-inquiry-hp" tabindex="-1" autocomplete="off" aria-hidden="true">

        <div class="rg-inquiry-form-grid">
            <label class="rg-inquiry-field">
                <span>Nombre completo</span>
                <input type="text" name="full_name" value="{{ old('full_name') }}" required placeholder="Tu nombre">
                @error('full_name') <small>{{ $message }}</small> @enderror
            </label>

            <label class="rg-inquiry-field">
                <span>Correo</span>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="tu@correo.com">
                @error('email') <small>{{ $message }}</small> @enderror
            </label>

            <label class="rg-inquiry-field">
                <span>Teléfono</span>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Opcional">
                @error('phone') <small>{{ $message }}</small> @enderror
            </label>

            <label class="rg-inquiry-field">
                <span>Empresa</span>
                <input type="text" name="company" value="{{ old('company') }}" placeholder="Opcional">
                @error('company') <small>{{ $message }}</small> @enderror
            </label>
        </div>

        <label class="rg-inquiry-field">
            <span>Mensaje</span>
            <textarea name="message" rows="5" required placeholder="Cuéntanos tu consulta…">{{ old('message') }}</textarea>
            @error('message') <small>{{ $message }}</small> @enderror
        </label>

        <button type="submit" class="rg-inquiry-submit">Enviar mensaje</button>
    </form>
</div>
