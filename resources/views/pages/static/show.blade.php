@extends('layouts.app')

@section('title', ($page['title'] ?? 'Información').' | Refugio Gastronómico')

@section('content')
    <x-static-info-page :page="$page" />
@endsection
