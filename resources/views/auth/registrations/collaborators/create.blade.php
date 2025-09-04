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
                                    value="{{ old('admission_date', isset($collaborator) ? $collaborator->admission_date : '') }}"
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
                                                    {{ old('position_id', isset($collaborator) ? $collaborator->position_id : '') == $position->id ? 'selected' : '' }}>
                                                {{ $position->name }}
                                                @if($position->department)
                                                    - {{ $position->department->name }}
                                                @endif
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
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-[var(--color-text)] cursor-not-allowed"
                                    placeholder="Será preenchido automaticamente"
                                    readonly>
                                <p class="text-xs text-[var(--color-text)] opacity-60 mt-1">
                                    <i class="fa-solid fa-info-circle mr-1"></i>
                                    O departamento é definido automaticamente com base no cargo selecionado
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Horários -->
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h3 class="text-lg font-semibold text-[var(--color-text)] mb-4">
                            <i class="fa-solid fa-clock text-[var(--color-main)] mr-2"></i>
                            Horários de Trabalho
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <h4 class="text-sm font-medium text-[var(--color-text)] opacity-80">Primeiro Turno</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="entry_time_1" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                            Entrada 1
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="time"
                                            id="entry_time_1"
                                            name="entry_time_1"
                                            value="{{ old('entry_time_1', isset($collaborator) ? $collaborator->entry_time_1 : '') }}"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200"
                                            required>
                                    </div>

                                    <div>
                                        <label for="return_time_1" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                            Saída 1
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="time"
                                            id="return_time_1"
                                            name="return_time_1"
                                            value="{{ old('return_time_1', isset($collaborator) ? $collaborator->return_time_1 : '') }}"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <h4 class="text-sm font-medium text-[var(--color-text)] opacity-80">Segundo Turno (Opcional)</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="entry_time_2" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                            Entrada 2
                                        </label>
                                        <input
                                            type="time"
                                            id="entry_time_2"
                                            name="entry_time_2"
                                            value="{{ old('entry_time_2', isset($collaborator) ? $collaborator->entry_time_2 : '') }}"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200">
                                    </div>

                                    <div>
                                        <label for="return_time_2" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                            Saída 2
                                        </label>
                                        <input
                                            type="time"
                                            id="return_time_2"
                                            name="return_time_2"
                                            value="{{ old('return_time_2', isset($collaborator) ? $collaborator->return_time_2 : '') }}"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200">
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
        document.addEventListener('DOMContentLoaded', function() {
            const positionSelect = document.getElementById('position_id');
            const departmentDisplay = document.getElementById('department_display');

            // Dados dos departamentos para lookup
            const departments = @json($departments ?? []);

            function updateDepartment() {
                const selectedOption = positionSelect.options[positionSelect.selectedIndex];
                const departmentId = selectedOption.getAttribute('data-department-id');

                if (departmentId) {
                    const department = departments.find(dept => dept.id == departmentId);
                    departmentDisplay.value = department ? department.name : '';
                } else {
                    departmentDisplay.value = '';
                }
            }

            positionSelect.addEventListener('change', updateDepartment);

            // Aplicar máscara nos campos
            if (document.getElementById('cpf')) {
                document.getElementById('cpf').addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    value = value.replace(/(\d{3})(\d)/, '$1.$2');
                    value = value.replace(/(\d{3})(\d)/, '$1.$2');
                    value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                    e.target.value = value;
                });
            }

            if (document.getElementById('phone')) {
                document.getElementById('phone').addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    value = value.replace(/(\d{2})(\d)/, '($1) $2');
                    value = value.replace(/(\d{5})(\d)/, '$1-$2');
                    e.target.value = value;
                });
            }

            if (document.getElementById('zip_code')) {
                document.getElementById('zip_code').addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    value = value.replace(/(\d{5})(\d)/, '$1-$2');
                    e.target.value = value;
                });
            }
        });
    </script>

@endsection
