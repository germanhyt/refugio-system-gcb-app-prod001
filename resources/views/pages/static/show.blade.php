@extends('layouts.app')

@section('title', ($page['title'] ?? 'Información').' | Refugio Gastronómico')

@push('json-ld')
    @if(! empty($faqLd))
        <script type="application/ld+json">{!! $faqLd !!}</script>
    @endif
@endpush

@section('content')
    <x-static-info-page :page="$page" />
@endsection
