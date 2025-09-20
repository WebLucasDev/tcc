@extends('layouts.layout')
@section('title', isset($workHour) ? 'Editar Jornada de Trabalho' : 'Nova Jornada de Trabalho')

@section('content')

    <x-error/>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[var(--color-text)]">
                    <i class="fa-solid fa-{{ isset($workHour) ? 'edit' : 'clock' }} text-[var(--color-main)] mr-2"></i>
                    {{ isset($workHour) ? 'Editar Jornada de Trabalho' : 'Nova Jornada de Trabalho' }}
                </h1>
                <p class="text-sm text-[var(--color-text)] opacity-70 mt-1">
                    {{ isset($workHour) ? 'Edite as informações da jornada de trabalho' : 'Cadastre uma nova jornada de trabalho no sistema' }}
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('work-hours.index') }}"
                    class="bg-[var(--color-main)] hover:bg-[var(--color-main-dark)] text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i>
                    Voltar
                </a>
            </div>
        </div>

        <div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
            <div class="p-6">
                <form action="{{ isset($workHour) ? route('work-hours.update', $workHour->id) : route('work-hours.store') }}" method="POST" class="space-y-6">
                    @csrf
                    @if(isset($workHour))
                        @method('PUT')
                    @endif

                    <!-- Informações Básicas -->
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h3 class="text-lg font-semibold text-[var(--color-text)] mb-4">
                            <i class="fa-solid fa-info-circle text-[var(--color-main)] mr-2"></i>
                            Informações Básicas
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                    Nome da Jornada
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    value="{{ old('name', isset($workHour) ? $workHour->name : '') }}"
                                    class="w-full px-3 py-2 bg-[var(--color-background)] border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[var(--color-main)] focus:border-[var(--color-main)] text-[var(--color-text)]"
                                    placeholder="Ex: CLT Padrão 44h, Meio Período, etc."
                                    required>
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                    Status
                                    <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="status"
                                    name="status"
                                    class="w-full px-3 py-2 bg-[var(--color-background)] border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[var(--color-main)] focus:border-[var(--color-main)] text-[var(--color-text)]"
                                    required>
                                    <option value="ativo" {{ old('status', isset($workHour) ? $workHour->status->value : 'ativo') == 'ativo' ? 'selected' : '' }}>
                                        Ativo
                                    </option>
                                    <option value="inativo" {{ old('status', isset($workHour) ? $workHour->status->value : '') == 'inativo' ? 'selected' : '' }}>
                                        Inativo
                                    </option>
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="description" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                Descrição
                            </label>
                            <textarea
                                id="description"
                                name="description"
                                rows="3"
                                class="w-full px-3 py-2 bg-[var(--color-background)] border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[var(--color-main)] focus:border-[var(--color-main)] text-[var(--color-text)]"
                                placeholder="Descreva as características desta jornada de trabalho">{{ old('description', isset($workHour) ? $workHour->description : '') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Horários por Dia da Semana -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-semibold text-[var(--color-text)] mb-4">
                            <i class="fa-solid fa-calendar-week text-[var(--color-main)] mr-2"></i>
                            Configuração de Horários
                        </h3>

                        <!-- Instruções -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <i class="fa-solid fa-info-circle text-blue-500 mt-0.5"></i>
                                <div class="text-sm text-blue-700 dark:text-blue-300">
                                    <p class="font-medium mb-1">Instruções:</p>
                                    <ul class="list-disc list-inside space-y-1 text-xs">
                                        <li>Marque os dias da semana em que o colaborador deve trabalhar</li>
                                        <li>Configure até 2 períodos por dia (ex: manhã e tarde com intervalo para almoço)</li>
                                        <li>Use apenas um período para jornadas contínuas</li>
                                        <li>O sistema calculará automaticamente as horas semanais</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Dias da Semana -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            @foreach($days as $day => $dayName)
                                <div class="bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="font-medium text-[var(--color-text)] flex items-center gap-2">
                                            <i class="fa-solid fa-calendar-day text-[var(--color-main)] text-sm"></i>
                                            {{ $dayName }}
                                        </h4>
                                        <label class="flex items-center cursor-pointer">
                                            <input
                                                type="checkbox"
                                                id="{{ $day }}_active"
                                                name="{{ $day }}_active"
                                                value="1"
                                                class="sr-only peer day-toggle"
                                                data-day="{{ $day }}"
                                                {{ old($day.'_active', isset($workHour) && $workHour->{$day.'_active'} ? 'checked' : '') }}>
                                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-[var(--color-main)]"></div>
                                        </label>
                                    </div>

                                    <div id="{{ $day }}_inputs" class="space-y-3 {{ old($day.'_active', isset($workHour) && $workHour->{$day.'_active'}) ? '' : 'hidden' }}">
                                        <!-- Período 1 -->
                                        <div>
                                            <label class="block text-xs font-medium text-[var(--color-text)] mb-1 opacity-80">
                                                Período 1
                                            </label>
                                            <div class="grid grid-cols-2 gap-2">
                                                <input
                                                    type="time"
                                                    name="{{ $day }}_entry_1"
                                                    value="{{ old($day.'_entry_1', isset($workHour) ? $workHour->{$day.'_entry_1'} : '') }}"
                                                    class="px-2 py-1.5 text-xs bg-[var(--color-background)] border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-[var(--color-main)] focus:border-[var(--color-main)] text-[var(--color-text)]"
                                                    placeholder="Entrada">
                                                <input
                                                    type="time"
                                                    name="{{ $day }}_exit_1"
                                                    value="{{ old($day.'_exit_1', isset($workHour) ? $workHour->{$day.'_exit_1'} : '') }}"
                                                    class="px-2 py-1.5 text-xs bg-[var(--color-background)] border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-[var(--color-main)] focus:border-[var(--color-main)] text-[var(--color-text)]"
                                                    placeholder="Saída">
                                            </div>
                                        </div>

                                        <!-- Período 2 -->
                                        <div>
                                            <label class="block text-xs font-medium text-[var(--color-text)] mb-1 opacity-80">
                                                Período 2 (opcional)
                                            </label>
                                            <div class="grid grid-cols-2 gap-2">
                                                <input
                                                    type="time"
                                                    name="{{ $day }}_entry_2"
                                                    value="{{ old($day.'_entry_2', isset($workHour) ? $workHour->{$day.'_entry_2'} : '') }}"
                                                    class="px-2 py-1.5 text-xs bg-[var(--color-background)] border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-[var(--color-main)] focus:border-[var(--color-main)] text-[var(--color-text)]"
                                                    placeholder="Entrada">
                                                <input
                                                    type="time"
                                                    name="{{ $day }}_exit_2"
                                                    value="{{ old($day.'_exit_2', isset($workHour) ? $workHour->{$day.'_exit_2'} : '') }}"
                                                    class="px-2 py-1.5 text-xs bg-[var(--color-background)] border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-[var(--color-main)] focus:border-[var(--color-main)] text-[var(--color-text)]"
                                                    placeholder="Saída">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Resumo de Horas -->
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-calculator text-green-600"></i>
                                <div>
                                    <h4 class="font-medium text-green-800 dark:text-green-200">
                                        Carga Horária Semanal
                                    </h4>
                                    <p id="weekly-hours" class="text-sm text-green-600 dark:text-green-300 mt-1">
                                        Configure os horários para calcular automaticamente
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button
                            type="submit"
                            class="bg-[var(--color-main)] hover:bg-[var(--color-main-dark)] text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-save"></i>
                            {{ isset($workHour) ? 'Atualizar Jornada' : 'Salvar Jornada' }}
                        </button>

                        <a
                            href="{{ route('work-hours.index') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-times"></i>
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    @vite('resources/js/menus/work-hours.js')
@endpush
