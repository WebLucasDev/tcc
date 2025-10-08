@extends('layouts.layout')

@section('title', 'Banco de Horas')

@section('content')
<div class="space-y-6">

    <x-error/>
    <x-success/>

    <!-- Header -->
    <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
        <h1 class="text-3xl font-bold text-[var(--color-main)]">
            <i class="fa-solid fa-clock mr-2"></i>
            Meu Banco de Horas
        </h1>
        <p class="text-[var(--color-text)] mt-2">
            Acompanhe seu saldo de horas trabalhadas
        </p>
    </div>

    <!-- Filtro -->
    @include('auth.system-for-employees.comp-time-employees.partials.filter')

    <!-- Resumo -->
    <div id="summary-container">
        @include('auth.system-for-employees.comp-time-employees.partials.summary')
    </div>

    <!-- Detalhamento -->
    <div id="details-container">
        @include('auth.system-for-employees.comp-time-employees.partials.details')
    </div>

</div>
@endsection

@push('scripts')
    @vite(['resources/js/menus/for-employees/comp-time.js'])
@endpush
