@extends('layouts.layout')
@section('title', 'Registro de Ponto')

@section('content')

    <x-success/>
    <x-error/>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[var(--color-text)] flex items-center gap-2">
                    <i class="fa-solid fa-clock text-[var(--color-main)]"></i>
                    Registro de Ponto
                </h1>
                <p class="text-[var(--color-text)] opacity-70 text-sm mt-1">Registre os horários de entrada e saída dos colaboradores</p>
            </div>
        </div>
        
    </div>    
@endsection

@push('scripts')
    @vite(['resources/js/menus/time-tracking.js'])
@endpush
