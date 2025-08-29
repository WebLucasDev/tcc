@extends('layouts.layout')
@section('title', isset($department) ? 'Editar Departamento' : 'Novo Departamento')

@section('content')

    <x-error/>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[var(--color-text)]">
                    <i class="fa-solid fa-building text-[var(--color-main)] mr-2"></i>
                    {{ isset($department) ? 'Editar Departamento' : 'Novo Departamento' }}
                </h1>
                <p class="text-sm text-[var(--color-text)] opacity-70 mt-1">
                    {{ isset($department) ? 'Edite o departamento ' . $department->name : 'Cadastre um novo departamento no sistema' }}
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('department.index') }}"
                    class="bg-[var(--color-main)] hover:bg-[var(--color-main-dark)] text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i>
                    Voltar
                </a>
            </div>
        </div>

        <div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
            <div class="p-6">
                <form action="{{ isset($department) ? route('department.update', $department->id) : route('department.store') }}" method="POST" class="space-y-6">
                    @csrf
                    @if(isset($department))
                        @method('PUT')
                    @endif

                    <div>
                        <label for="name" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                            Nome do Departamento
                            <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', isset($department) ? $department->name : '') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] placeholder-gray-500 focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200"
                            placeholder="Ex: Tecnologia da Informação, Recursos Humanos, etc."
                            required>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button
                            type="submit"
                            id="save-department-btn"
                            class="bg-[var(--color-main)] hover:bg-[var(--color-main-dark)] text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fa-solid fa-save"></i>
                            <span class="btn-text">{{ isset($department) ? 'Atualizar Departamento' : 'Salvar Departamento' }}</span>
                        </button>

                        <a href="{{ route('department.index') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-times"></i>
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
