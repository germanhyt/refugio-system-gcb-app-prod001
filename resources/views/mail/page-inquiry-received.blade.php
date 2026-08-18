Nuevo mensaje desde {{ $inquiry->page_slug === 'convocatorias' ? 'Convocatoria' : 'Contacto' }}

Nombre: {{ $inquiry->full_name }}
Correo: {{ $inquiry->email }}
@if($inquiry->phone)
Teléfono: {{ $inquiry->phone }}
@endif
@if($inquiry->company)
Empresa: {{ $inquiry->company }}
@endif

Mensaje:
{{ $inquiry->message }}

---
Enviado el {{ $inquiry->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}
