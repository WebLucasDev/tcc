@extends('layouts.layout')
@section('title', 'Cargos')

@section('content')

    <x-success/>
    <x-error/>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-4">
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('position.create') }}"
                    id="btn-new-position"
                    class="bg-[var(--color-main)] hover:bg-[var(--color-main-dark)] text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i>
                    Novo Cargo
                </a>
            </div>
        </div>

        @include('auth.registrations.positions.partials.filters')

        <div id="positions-table-container">
            @include('auth.registrations.positions.partials.table', ['positions' => $positions])
        </div>

        <div id="pagination-container">
            @include('auth.registrations.positions.partials.pagination', ['positions' => $positions])
        </div>

    </div>

    @include('auth.registrations.positions.partials.modal-delete')

@endsection
