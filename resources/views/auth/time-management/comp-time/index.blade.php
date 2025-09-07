@extends('layouts.layout')
@section('title', 'Banco de Horas')

@section('content')

    <x-success/>
    <x-error/>

    <div class="space-y-6">
        <!-- Filtros -->
        @include('auth.time-management.comp-time.partials.filters')

        <!-- Resumo -->
        <div id="summary-container">
            @include('auth.time-management.comp-time.partials.summary')
        </div>

        <!-- Tabela de Banco de Horas -->
        <div id="comp-time-container">
            @include('auth.time-management.comp-time.partials.table')
        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/menus/comp-time.js'])
@endpush
