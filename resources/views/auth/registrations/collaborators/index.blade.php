@extends('layouts.layout')
@section('title', 'Colaboradores')

@section('content')

    <x-success/>
    <x-error/>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-4">
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('collaborator.create') }}"
                    id="btn-new-position"
                    class="bg-[var(--color-main)] hover:bg-[var(--color-main-dark)] text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i>
                    Novo Colaborador
                </a>
            </div>
        </div>


    </div>

@endsection
