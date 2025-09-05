@extends('layouts.layout')
@section('title', 'Solicitações')

@section('content')

    <x-success/>
    <x-error/>

    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[var(--color-text)] flex items-center gap-2">
                    <i class="fa-solid fa-file-contract text-[var(--color-main)]"></i>
                    Solicitações de Ajuste
                </h1>
                <p class="text-[var(--color-text)] opacity-70 text-sm mt-1">Gerencie as solicitações de ajuste de ponto dos funcionários</p>
            </div>
        </div>

        @include('auth.time-management.solicitations.partials.filters')

        <div id="table-container">
            @include('auth.time-management.solicitations.partials.table')
        </div>

        <div id="pagination-container">
            @include('auth.time-management.solicitations.partials.pagination')
        </div>

        @include('auth.time-management.solicitations.partials.modal-action')
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/menus/solicitations.js'])
@endpush
