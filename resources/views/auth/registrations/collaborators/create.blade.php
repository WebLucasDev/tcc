@extends('layouts.layout')
@section('title', isset($collaborator) ? 'Editar Colaborador' : 'Novo Colaborador')

@section('content')

    <x-error/>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[var(--color-text)]">
                    <i class="fa-solid fa-{{ isset($collaborator) ? 'edit' : 'user-plus' }} text-[var(--color-main)] mr-2"></i>
                    {{ isset($collaborator) ? 'Editar Colaborador' : 'Novo Colaborador' }}
                </h1>
                <p class="text-sm text-[var(--color-text)] opacity-70 mt-1">
                    {{ isset($collaborator) ? 'Edite as informações do colaborador' : 'Cadastre um novo colaborador no sistema' }}
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('collaborator.index') }}"
                    class="bg-[var(--color-main)] hover:bg-[var(--color-main-dark)] text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i>
                    Voltar
                </a>
            </div>
        </div>

        <div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
            <div class="p-6">
                <form action="{{ isset($collaborator) ? route('collaborator.update', $collaborator->id) : route('collaborator.store') }}" method="POST" class="space-y-6">
                    @csrf
                    @if(isset($collaborator))
                        @method('PUT')
                    @endif

                    <!-- Dados Pessoais -->
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h3 class="text-lg font-semibold text-[var(--color-text)] mb-4">
                            <i class="fa-solid fa-user text-[var(--color-main)] mr-2"></i>
                            Dados Pessoais
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                    Nome Completo
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    value="{{ old('name', isset($collaborator) ? $collaborator->name : '') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] placeholder-gray-500 focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200"
                                    placeholder="Ex: João Silva Santos"
                                    required>
                            </div>

                            <div>
                                <label for="cpf" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                    CPF
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="cpf"
                                    name="cpf"
                                    value="{{ old('cpf', isset($collaborator) ? $collaborator->cpf : '') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] placeholder-gray-500 focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200"
                                    placeholder="000.000.000-00"
                                    maxlength="14"
                                    required>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                    Email
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email', isset($collaborator) ? $collaborator->email : '') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] placeholder-gray-500 focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200"
                                    placeholder="joao@exemplo.com"
                                    required>
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                    Senha
                                    @if(isset($collaborator))
                                        <span class="text-sm text-gray-500">(deixe em branco para manter a atual)</span>
                                    @else
                                        <span class="text-red-500">*</span>
                                    @endif
                                </label>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] placeholder-gray-500 focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200"
                                    placeholder="Digite a senha"
                                    @if(!isset($collaborator)) required @endif>
                                @error('password')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                    Telefone
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="phone"
                                    name="phone"
                                    value="{{ old('phone', isset($collaborator) ? $collaborator->phone : '') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] placeholder-gray-500 focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200"
                                    placeholder="(11) 99999-9999"
                                    maxlength="15"
                                    required>
                            </div>

                            <div>
                                <label for="admission_date" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                    Data de Admissão
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    id="admission_date"
                                    name="admission_date"
                                    value="{{ old('admission_date', isset($collaborator) ? $collaborator->admission_date?->format('Y-m-d') : '') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] placeholder-gray-500 focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200"
                                    required>
                            </div>
                        </div>
                    </div>

                    <!-- Endereço -->
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h3 class="text-lg font-semibold text-[var(--color-text)] mb-4">
                            <i class="fa-solid fa-map-marker-alt text-[var(--color-main)] mr-2"></i>
                            Endereço
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label for="zip_code" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                    CEP
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="zip_code"
                                    name="zip_code"
                                    value="{{ old('zip_code', isset($collaborator) ? $collaborator->zip_code : '') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] placeholder-gray-500 focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200"
                                    placeholder="00000-000"
                                    maxlength="9"
                                    required>
                            </div>

                            <div class="md:col-span-2">
                                <label for="street" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                    Rua
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="street"
                                    name="street"
                                    value="{{ old('street', isset($collaborator) ? $collaborator->street : '') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] placeholder-gray-500 focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200"
                                    placeholder="Rua das Flores"
                                    required>
                            </div>

                            <div>
                                <label for="number" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                    Número
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="number"
                                    name="number"
                                    value="{{ old('number', isset($collaborator) ? $collaborator->number : '') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] placeholder-gray-500 focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200"
                                    placeholder="123"
                                    required>
                            </div>

                            <div class="md:col-span-2">
                                <label for="neighborhood" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                    Bairro
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="neighborhood"
                                    name="neighborhood"
                                    value="{{ old('neighborhood', isset($collaborator) ? $collaborator->neighborhood : '') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] placeholder-gray-500 focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200"
                                    placeholder="Centro"
                                    required>
                            </div>
                        </div>
                    </div>

                    <!-- Cargo e Departamento -->
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h3 class="text-lg font-semibold text-[var(--color-text)] mb-4">
                            <i class="fa-solid fa-briefcase text-[var(--color-main)] mr-2"></i>
                            Cargo e Departamento
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="position_id" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                    Cargo
                                    <span class="text-red-500">*</span>
                                </label>
                                                                <select
                                    id="position_id"
                                    name="position_id"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200"
                                    required>
                                    <option value="">Selecione um cargo</option>
                                    @if(isset($positions))
                                        @foreach($positions as $position)
                                            <option value="{{ $position->id }}"
                                                    data-department-id="{{ $position->department_id }}"
                                                    data-department-name="{{ $position->department ? $position->department->name : '' }}"
                                                    {{ old('position_id', isset($collaborator) ? $collaborator->position_id : '') == $position->id ? 'selected' : '' }}>
                                                {{ $position->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                    Status
                                    <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="status"
                                    name="status"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200"
                                    required>
                                    <option value="">Selecione o status</option>
                                    <option value="ativo" {{ old('status', isset($collaborator) ? $collaborator->status->value ?? 'ativo' : 'ativo') == 'ativo' ? 'selected' : '' }}>
                                        <i class="fa-solid fa-check-circle text-green-500 mr-2"></i>
                                        Ativo
                                    </option>
                                    <option value="inativo" {{ old('status', isset($collaborator) ? $collaborator->status->value ?? '' : '') == 'inativo' ? 'selected' : '' }}>
                                        <i class="fa-solid fa-times-circle text-red-500 mr-2"></i>
                                        Inativo
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="department_display" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                    Departamento
                                </label>
                                <input
                                    type="text"
                                    id="department_display"
                                    name="department_display"
                                    value="{{ old('department_display', isset($collaborator) && $collaborator->position && $collaborator->position->department ? $collaborator->position->department->name : '') }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-[var(--color-text)] cursor-not-allowed"
                                    placeholder="Selecione um cargo primeiro"
                                    readonly>
                                <p class="text-xs text-[var(--color-text)] opacity-60 mt-1">
                                    <i class="fa-solid fa-info-circle mr-1"></i>
                                    O departamento é definido automaticamente com base no cargo selecionado
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Jornada de Trabalho -->
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h3 class="text-lg font-semibold text-[var(--color-text)] mb-4">
                            <i class="fa-solid fa-clock text-[var(--color-main)] mr-2"></i>
                            Jornada de Trabalho
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="work_hours_id" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                    Jornada de Trabalho
                                    <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="work_hours_id"
                                    name="work_hours_id"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200"
                                    required>
                                    <option value="">Selecione uma jornada de trabalho</option>
                                    @if(isset($workHours))
                                        @foreach($workHours as $workHour)
                                            <option value="{{ $workHour->id }}"
                                                    data-weekly-hours="{{ $workHour->total_weekly_hours }}"
                                                    data-description="{{ $workHour->description }}"
                                                    {{ old('work_hours_id', isset($collaborator) ? $collaborator->work_hours_id : '') == $workHour->id ? 'selected' : '' }}>
                                                {{ $workHour->name }} - {{ $workHour->total_weekly_hours }}h semanais
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('work_hours_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                    Resumo da Jornada
                                </label>
                                <div id="work-hours-summary" class="p-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <div class="text-sm text-[var(--color-text)] opacity-70">
                                        <i class="fa-solid fa-info-circle mr-2"></i>
                                        Selecione uma jornada de trabalho para ver o resumo
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <i class="fa-solid fa-lightbulb text-blue-500 mt-0.5"></i>
                                    <div class="text-sm text-blue-700 dark:text-blue-300">
                                        <p class="font-medium mb-1">Sobre as Jornadas de Trabalho:</p>
                                        <ul class="list-disc list-inside space-y-1 text-xs">
                                            <li>As jornadas de trabalho definem os horários e dias da semana que o colaborador deve trabalhar</li>
                                            <li>Você pode gerenciar jornadas em <a href="{{ route('work-hours.index') }}" class="underline hover:text-blue-600">Cadastros > Jornadas de Trabalho</a></li>
                                            <li>O sistema calculará automaticamente as horas trabalhadas baseado na jornada selecionada</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        <button
                            type="submit"
                            id="save-collaborator-btn"
                            class="bg-[var(--color-main)] hover:bg-[var(--color-main-dark)] text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fa-solid fa-save"></i>
                            <span class="btn-text">{{ isset($collaborator) ? 'Atualizar Colaborador' : 'Salvar Colaborador' }}</span>
                        </button>

                        <a href="{{ route('collaborator.index') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-times"></i>
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        window.collaboratorDepartments = @json($departments ?? []);
        window.workHoursData = @json($workHours ?? []);

        // Função para atualizar o resumo da jornada de trabalho
        document.addEventListener('DOMContentLoaded', function() {
            const workHoursSelect = document.getElementById('work_hours_id');
            const summaryDiv = document.getElementById('work-hours-summary');

            if (workHoursSelect && summaryDiv) {
                workHoursSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];

                    if (this.value && selectedOption) {
                        const weeklyHours = selectedOption.getAttribute('data-weekly-hours');
                        const description = selectedOption.getAttribute('data-description');

                        summaryDiv.innerHTML = `
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-clock text-[var(--color-main)]"></i>
                                    <span class="font-medium text-[var(--color-text)]">${selectedOption.text}</span>
                                </div>
                                <div class="text-sm text-[var(--color-text)] opacity-70">
                                    <i class="fa-solid fa-calendar-week text-green-600 mr-1"></i>
                                    <strong>${weeklyHours}h</strong> semanais
                                </div>
                                ${description ? `<div class="text-xs text-[var(--color-text)] opacity-60 mt-2">${description}</div>` : ''}
                            </div>
                        `;
                    } else {
                        summaryDiv.innerHTML = `
                            <div class="text-sm text-[var(--color-text)] opacity-70">
                                <i class="fa-solid fa-info-circle mr-2"></i>
                                Selecione uma jornada de trabalho para ver o resumo
                            </div>
                        `;
                    }
                });

                // Disparar evento change se já houver um valor selecionado (para edição)
                if (workHoursSelect.value) {
                    workHoursSelect.dispatchEvent(new Event('change'));
                }
            }
        });
    </script>

@endsection

@push('scripts')
    @vite(['resources/js/menus/collaborators.js'])
@endpush
