@extends('layouts.layout')
@section('title', 'Jornadas de Trabalho')

@section('content')

    <x-success/>
    <x-error/>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-4">
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('work-hours.create') }}"
                    id="btn-new-work-hour"
                    class="bg-[var(--color-main)] hover:bg-[var(--color-main-dark)] text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i>
                    Nova Jornada
                </a>
            </div>
        </div>

        @include('auth.registrations.work-hours.partials.filters', ['workHours' => $workHours, 'activeCount' => $activeCount, 'inactiveCount' => $inactiveCount])

        <div id="work-hours-table-container">
            @include('auth.registrations.work-hours.partials.table', ['workHours' => $workHours])
        </div>

        <div id="pagination-container">
            @include('auth.registrations.work-hours.partials.pagination', ['workHours' => $workHours])
        </div>
    </div>

    @include('auth.registrations.work-hours.partials.modal-delete')
    </div>

@endsection

@push('scripts')
    @vite(['resources/js/menus/work-hours.js'])
@endpush
