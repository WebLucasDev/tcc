@extends('layouts.layout')

@section('title', 'Solicitações')

@section('content')
<div class="space-y-6">

    <x-error/>
    <x-success/>

    <!-- Header -->
    <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-xl sm:text-3xl font-bold text-[var(--color-main)]">
                    <i class="fa-solid fa-file-lines mr-2"></i>
                    Solicitações
                </h1>
                <p class="text-sm sm:text-base text-[var(--color-text)] mt-2">
                    Solicite alterações em seus registros de ponto
                </p>
            </div>
            <a href="{{ route('system-for-employees.solicitation.create') }}"
               class="block bg-[var(--color-main)] hover:bg-[var(--color-main)]/90 text-white px-3 py-1.5 rounded-lg font-semibold transition-colors shadow-lg hover:shadow-xl text-sm sm:text-base">
                <i class="fa-solid fa-plus mr-2"></i>
                Nova Solicitação
            </a>
        </div>
    </div>

    @include('auth.system-for-employees.solicitation-employees.partials.table')

    @if($solicitations->hasPages())
        @include('auth.system-for-employees.solicitation-employees.partials.pagination')
    @endif

    <!-- Modal de Detalhes -->
    @include('auth.system-for-employees.solicitation-employees.partials.modal-details')

    <!-- Modal de Cancelamento -->
    @include('auth.system-for-employees.solicitation-employees.partials.modal-cancel')
</div>
@endsection

@push('scripts')
    @vite(['resources/js/menus/for-employees/solicitations.js'])
@endpush
