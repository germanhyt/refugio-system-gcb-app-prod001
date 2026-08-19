Nueva hoja del Libro de reclamaciones

Tipo: {{ $entry->claim_type }}
Fecha: {{ $entry->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}

Consumidor
Nombre: {{ trim($entry->first_name.' '.$entry->last_name) }}
Documento: {{ $entry->document_type }} {{ $entry->document_number }}
Departamento: {{ $entry->department }}
Dirección: {{ $entry->address }}
Teléfono: {{ $entry->phone }}
Correo: {{ $entry->email }}
@if($entry->parent_name)
Padre/madre: {{ $entry->parent_name }}
@endif

Bien o servicio
@if($entry->claimed_amount)
Monto reclamado: {{ $entry->claimed_amount }}
@endif
{{ $entry->product_description }}

Detalle
{{ $entry->claim_detail }}

Pedido del consumidor
{{ $entry->consumer_request }}
