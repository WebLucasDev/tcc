@extends('layouts.layout')

@section('title', 'Bater Ponto')

@section('content')
<div class="space-y-6">

    <x-error/>
    <x-success/>

    <!-- Header -->
    <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
        <h1 class="text-3xl font-bold text-[var(--color-main)]">
            <i class="fa-solid fa-clock mr-2"></i>
            Registro de Ponto
        </h1>
        <p class="text-[var(--color-text)] mt-2">
            Registre seus horários de entrada e saída
        </p>
    </div>

    @include('auth.system-for-employees.time-tracking-employees.partials.clock')

    @include('auth.system-for-employees.time-tracking-employees.partials.table')

        @if($timeTrackings->hasPages())
            @include('auth.system-for-employees.time-tracking-employees.partials.pagination')
        @endif
    </div>

    <!-- Modal de Edição -->
    @include('auth.system-for-employees.time-tracking-employees.partials.modal-edit')

    <!-- Modal de Cancelamento -->
    @include('auth.system-for-employees.time-tracking-employees.partials.modal-cancel')

    <!-- Modal de Restauração -->
    @include('auth.system-for-employees.time-tracking-employees.partials.modal-restore')
</div>
@endsection

@push('scripts')
    @vite(['resources/js/menus/for-employees/time-tracking.js'])
@endpush
