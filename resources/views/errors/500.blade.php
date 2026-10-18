@extends('errors.layout')

@section('code', '500')
@section('icon', '🎲')
@section('title', "Erreur serveur")
@section('message', "Les dés sont mal tombés : une erreur est survenue. Réessayez dans un instant.")

{{-- Detail de l'exception uniquement en local (APP_DEBUG) --}}
@section('details')
    @if (config('app.debug') && isset($exception))
        <p class="error-debug">
            {{ $exception->getMessage() }}<br/>
            {{ $exception->getFile() }} : {{ $exception->getLine() }}
        </p>
    @endif
@endsection
