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

        <!-- Relógio Digital -->
        <div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-[var(--color-text)] flex items-center gap-2">
                    <i class="fa-solid fa-clock text-[var(--color-main)]"></i>
                    Horário Atual
                </h3>
            </div>
            <div class="p-8 text-center">
                <div class="clock-container mx-auto">
                    <div class="digital-clock">
                        <div id="current-time" class="time-display">00:00:00</div>
                        <div id="current-date" class="date-display">Carregando...</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros e Busca -->
        @include('auth.time-management.time-tracking.partials.filters')

        <!-- Histórico de Pontos -->
        @include('auth.time-management.time-tracking.partials.table')
    </div>
@endsection

@push('scripts')
    <script>
        window.timeTrackingData = {
            collaborators: @json($collaborators)
        };
    </script>
    @vite(['resources/js/menus/time-tracking.js'])
@endpush
