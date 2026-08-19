@extends('layouts.app')

@section('title', ($title ?? 'Página').' | Refugio Gastronómico')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($content ?? ''), 160))

@php
    $isLeadPage = (bool) ($show_inquiry_form ?? false);
    $isComplaintPage = (bool) ($show_complaint_form ?? false);
    $remoteSlug = $remote_slug ?? '';
    $bannerImage = $hero_image
        ?: asset(($isLeadPage || $isComplaintPage) ? 'images/refugio/bg_contacto-home.jpg' : 'images/refugio/bg-nosotros.png');
    $formTitle = $remoteSlug === 'convocatorias' ? 'Postula o escribe' : 'Escríbenos';
    $formSubtitle = $remoteSlug === 'convocatorias'
        ? 'Cuéntanos sobre tu marca o propuesta y te respondemos.'
        : 'Cuéntanos en qué te podemos ayudar y te contactamos.';
    $layoutClass = $isLeadPage
        ? 'rg-mirror-layout--lead'
        : ($isComplaintPage ? 'rg-mirror-layout--complaint' : 'rg-mirror-layout--legal');
    $heroClass = ($isLeadPage || $isComplaintPage) ? 'rg-mirror-hero--photo' : 'rg-mirror-hero--brand';
@endphp

@section('content')
{{-- Hero full-bleed --}}
<section
    class="rg-mirror-hero {{ $heroClass }}"
    style="background-image: url('{{ $bannerImage }}');"
>
    <div class="rg-mirror-hero-shade" aria-hidden="true"></div>

    <img
        src="{{ asset('images/refugio/hojas-footer.png') }}"
        alt=""
        class="rg-mirror-hero-deco rg-mirror-hero-deco--right"
        aria-hidden="true"
    >
    <img
        src="{{ asset('images/refugio/hojas-footer.png') }}"
        alt=""
        class="rg-mirror-hero-deco rg-mirror-hero-deco--left"
        aria-hidden="true"
    >

    <div class="container-refugio rg-mirror-hero-inner">
        <h1 class="rg-mirror-hero-title">{{ $title ?? 'Página' }}</h1>
    </div>
</section>

{{-- Divisor hojas --}}
<div
    class="rg-mirror-leaf-divider"
    style="background-image: url('{{ asset('images/refugio/divisor-hojas-home.svg') }}');"
    aria-hidden="true"
></div>

{{-- Contenido --}}
<section class="rg-mirror-body">
    <div class="container-refugio">
        @if(session('inquiry_success'))
            <div class="rg-mirror-success" role="status">
                {{ session('inquiry_success') }}
            </div>
        @endif

        <div class="rg-mirror-layout {{ $layoutClass }}">
            @if(trim(strip_tags($content ?? '')) !== '')
                <article class="rg-mirror-copy" data-aos="fade-up">
                    {!! $content !!}
                </article>
            @endif

            @if($isLeadPage)
                <aside class="rg-mirror-aside" data-aos="fade-up" data-aos-delay="80">
                    @if(! empty($visit))
                        <div class="rg-mirror-contact-meta">
                            <h2 class="rg-mirror-aside-kicker">Ubícanos</h2>
                            @if($visit->address)
                                <p class="rg-mirror-contact-line">{{ $visit->address }}</p>
                            @endif
                            @if($visit->email)
                                <p class="rg-mirror-contact-line">
                                    <a href="mailto:{{ $visit->email }}">{{ $visit->email }}</a>
                                </p>
                            @endif
                            @if($visit->phone_reservations || $visit->phone_events)
                                <p class="rg-mirror-contact-line">
                                    @if($visit->phone_reservations)
                                        Reservas: {{ $visit->phone_reservations }}
                                    @endif
                                    @if($visit->phone_reservations && $visit->phone_events)
                                        <br>
                                    @endif
                                    @if($visit->phone_events)
                                        Eventos: {{ $visit->phone_events }}
                                    @endif
                                </p>
                            @endif
                        </div>
                    @endif

                    <div class="rg-mirror-form-panel">
                        <h2 class="rg-mirror-form-title">{{ $formTitle }}</h2>
                        <p class="rg-mirror-form-subtitle">{{ $formSubtitle }}</p>

                        <form method="POST" action="{{ route('info.store') }}" class="rg-mirror-form" novalidate>
                            @csrf
                            <input type="hidden" name="page_slug" value="{{ $remoteSlug ?: 'convocatorias' }}">
                            <input type="text" name="website" class="rg-mirror-hp" tabindex="-1" autocomplete="off" aria-hidden="true">

                            <div class="rg-mirror-form-grid">
                                <label class="rg-mirror-field">
                                    <span>Nombre completo</span>
                                    <input type="text" name="full_name" value="{{ old('full_name') }}" required placeholder="Tu nombre">
                                    @error('full_name') <small>{{ $message }}</small> @enderror
                                </label>

                                <label class="rg-mirror-field">
                                    <span>Correo</span>
                                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="tu@correo.com">
                                    @error('email') <small>{{ $message }}</small> @enderror
                                </label>

                                <label class="rg-mirror-field">
                                    <span>Teléfono</span>
                                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Opcional">
                                    @error('phone') <small>{{ $message }}</small> @enderror
                                </label>

                                <label class="rg-mirror-field">
                                    <span>Empresa</span>
                                    <input type="text" name="company" value="{{ old('company') }}" placeholder="Opcional">
                                    @error('company') <small>{{ $message }}</small> @enderror
                                </label>
                            </div>

                            <label class="rg-mirror-field">
                                <span>Mensaje</span>
                                <textarea name="message" rows="5" required placeholder="Cuéntanos tu consulta…">{{ old('message') }}</textarea>
                                @error('message') <small>{{ $message }}</small> @enderror
                            </label>

                            <button type="submit" class="rg-mirror-submit">Enviar mensaje</button>
                        </form>
                    </div>
                </aside>
            @endif

            @if($isComplaintPage)
                <div class="rg-mirror-form-panel rg-complaint-panel" data-aos="fade-up">
                    <h2 class="rg-mirror-form-title">Hoja de reclamación</h2>
                    <p class="rg-mirror-form-subtitle">
                        Completa tus datos para registrar una queja o reclamo conforme al Libro de Reclamaciones.
                    </p>

                    <form method="POST" action="{{ route('legal.complaints.store') }}" class="rg-mirror-form rg-complaint-form" novalidate>
                        @csrf
                        <input type="text" name="website" class="rg-mirror-hp" tabindex="-1" autocomplete="off" aria-hidden="true">

                        <fieldset class="rg-complaint-section">
                            <legend>1. Información personal</legend>

                            <div class="rg-mirror-field">
                                <span>Tipo de documento</span>
                                <div class="rg-choice-row" role="radiogroup" aria-label="Tipo de documento">
                                    @foreach (['DNI', 'CE', 'Pasaporte', 'RUC'] as $docType)
                                        <label class="rg-choice">
                                            <input
                                                type="radio"
                                                name="document_type"
                                                value="{{ $docType }}"
                                                @checked(old('document_type') === $docType)
                                                required
                                            >
                                            <span>{{ $docType }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('document_type') <small>{{ $message }}</small> @enderror
                            </div>

                            <div class="rg-mirror-form-grid">
                                <label class="rg-mirror-field">
                                    <span>N° de documento</span>
                                    <input type="text" name="document_number" value="{{ old('document_number') }}" required placeholder="N° de documento">
                                    @error('document_number') <small>{{ $message }}</small> @enderror
                                </label>

                                <label class="rg-mirror-field">
                                    <span>Departamento</span>
                                    <select name="department" required>
                                        <option value="">Seleccione un departamento</option>
                                        @foreach (($departments ?? []) as $department)
                                            <option value="{{ $department }}" @selected(old('department') === $department)>
                                                {{ $department }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department') <small>{{ $message }}</small> @enderror
                                </label>

                                <label class="rg-mirror-field">
                                    <span>Nombres</span>
                                    <input type="text" name="first_name" value="{{ old('first_name') }}" required placeholder="Nombres">
                                    @error('first_name') <small>{{ $message }}</small> @enderror
                                </label>

                                <label class="rg-mirror-field">
                                    <span>Apellidos</span>
                                    <input type="text" name="last_name" value="{{ old('last_name') }}" required placeholder="Apellidos">
                                    @error('last_name') <small>{{ $message }}</small> @enderror
                                </label>

                                <label class="rg-mirror-field rg-mirror-field--full">
                                    <span>Dirección</span>
                                    <input type="text" name="address" value="{{ old('address') }}" required placeholder="Dirección">
                                    @error('address') <small>{{ $message }}</small> @enderror
                                </label>

                                <label class="rg-mirror-field">
                                    <span>Teléfono</span>
                                    <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="Teléfono">
                                    @error('phone') <small>{{ $message }}</small> @enderror
                                </label>

                                <label class="rg-mirror-field">
                                    <span>Correo</span>
                                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="Correo">
                                    @error('email') <small>{{ $message }}</small> @enderror
                                </label>

                                <label class="rg-mirror-field rg-mirror-field--full">
                                    <span>Nombre del padre o madre <em>(si aplica, menores de edad)</em></span>
                                    <input type="text" name="parent_name" value="{{ old('parent_name') }}" placeholder="Nombre del padre o madre">
                                    @error('parent_name') <small>{{ $message }}</small> @enderror
                                </label>
                            </div>
                        </fieldset>

                        <fieldset class="rg-complaint-section">
                            <legend>2. Identificación del bien o servicio contratado</legend>

                            <div class="rg-mirror-form-grid">
                                <label class="rg-mirror-field">
                                    <span>Monto reclamado</span>
                                    <input type="text" name="claimed_amount" value="{{ old('claimed_amount') }}" placeholder="Monto reclamado">
                                    @error('claimed_amount') <small>{{ $message }}</small> @enderror
                                </label>
                            </div>

                            <label class="rg-mirror-field">
                                <span>Descripción del producto o servicio</span>
                                <textarea name="product_description" rows="4" required placeholder="Descripción del producto o servicio contratado">{{ old('product_description') }}</textarea>
                                @error('product_description') <small>{{ $message }}</small> @enderror
                            </label>
                        </fieldset>

                        <fieldset class="rg-complaint-section">
                            <legend>3. Detalle de la reclamación y pedido</legend>

                            <div class="rg-mirror-field">
                                <span>Queja o reclamo</span>
                                <div class="rg-choice-stack">
                                    <label class="rg-choice rg-choice--block">
                                        <input type="radio" name="claim_type" value="Queja" @checked(old('claim_type') === 'Queja') required>
                                        <span>
                                            <strong>Queja</strong>
                                            <small>Disconformidad no relacionada a un producto o servicio, o malestar/descontento respecto a la atención al público.</small>
                                        </span>
                                    </label>
                                    <label class="rg-choice rg-choice--block">
                                        <input type="radio" name="claim_type" value="Reclamo" @checked(old('claim_type') === 'Reclamo') required>
                                        <span>
                                            <strong>Reclamo</strong>
                                            <small>Disconformidad relacionada a productos o servicios.</small>
                                        </span>
                                    </label>
                                </div>
                                @error('claim_type') <small>{{ $message }}</small> @enderror
                            </div>

                            <label class="rg-mirror-field">
                                <span>Detalle del reclamo</span>
                                <textarea name="claim_detail" rows="4" required placeholder="Detalle del reclamo">{{ old('claim_detail') }}</textarea>
                                @error('claim_detail') <small>{{ $message }}</small> @enderror
                            </label>

                            <label class="rg-mirror-field">
                                <span>Pedido del consumidor</span>
                                <textarea name="consumer_request" rows="4" required placeholder="Pedido del consumidor">{{ old('consumer_request') }}</textarea>
                                @error('consumer_request') <small>{{ $message }}</small> @enderror
                            </label>
                        </fieldset>

                        <div class="rg-complaint-notes">
                            <p>* La formulación del reclamo no impide acudir a otras vías de solución de controversias ni es requisito previo para interponer una denuncia ante el INDECOPI.</p>
                            <p>* El proveedor deberá dar respuesta al reclamo en un plazo no mayor a treinta (30) días calendario, pudiendo ampliar el plazo hasta por treinta (30) días más, previa comunicación al consumidor.</p>
                            <p>* Los datos personales ingresados en este formulario serán utilizados por REFUGIO para procesar su solicitud y cumplir con todos los requisitos legales y contractuales.</p>
                        </div>

                        <button type="submit" class="rg-mirror-submit">Enviar reclamación</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
