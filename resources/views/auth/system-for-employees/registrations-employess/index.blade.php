@extends('layouts.layout')

@section('title', 'Meus Dados')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-[var(--color-background)] rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold text-[var(--color-main)]">
            <i class="fa-solid fa-user-edit mr-2"></i>
            Meus Dados Pessoais
        </h1>
        <p class="text-[var(--color-text)] mt-1">
            Visualize e mantenha suas informações pessoais atualizadas.
        </p>
    </div>

    <!-- Formulário de Dados Pessoais -->
    <div class="bg-[var(--color-background)] rounded-lg shadow-lg p-6">
        <form action="#" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Informações Básicas -->
            <div>
                <h2 class="text-xl font-semibold text-[var(--color-main)] mb-4">
                    <i class="fa-solid fa-user mr-2"></i>
                    Informações Básicas
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                            Nome Completo *
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ auth('collaborator')->user()->name ?? '' }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)]"
                               required>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                            Email *
                        </label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ auth('collaborator')->user()->email ?? '' }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)]"
                               required>
                    </div>

                    <div>
                        <label for="cpf" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                            CPF *
                        </label>
                        <input type="text"
                               id="cpf"
                               name="cpf"
                               value="{{ auth('collaborator')->user()->cpf ?? '' }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)]"
                               placeholder="000.000.000-00"
                               required readonly>
                        <p class="text-xs text-[var(--color-text)] mt-1">* Campo não editável</p>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                            Telefone
                        </label>
                        <input type="text"
                               id="phone"
                               name="phone"
                               value="{{ auth('collaborator')->user()->phone ?? '' }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)]"
                               placeholder="(00) 00000-0000">
                    </div>
                </div>
            </div>

            <!-- Informações de Endereço -->
            <div>
                <h2 class="text-xl font-semibold text-[var(--color-main)] mb-4">
                    <i class="fa-solid fa-map-marker-alt mr-2"></i>
                    Endereço
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label for="zip_code" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                            CEP
                        </label>
                        <input type="text"
                               id="zip_code"
                               name="zip_code"
                               value="{{ auth('collaborator')->user()->zip_code ?? '' }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)]"
                               placeholder="00000-000">
                    </div>

                    <div class="md:col-span-2">
                        <label for="street" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                            Rua/Avenida
                        </label>
                        <input type="text"
                               id="street"
                               name="street"
                               value="{{ auth('collaborator')->user()->street ?? '' }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)]">
                    </div>

                    <div>
                        <label for="number" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                            Número
                        </label>
                        <input type="text"
                               id="number"
                               name="number"
                               value="{{ auth('collaborator')->user()->number ?? '' }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)]">
                    </div>

                    <div class="md:col-span-2">
                        <label for="neighborhood" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                            Bairro
                        </label>
                        <input type="text"
                               id="neighborhood"
                               name="neighborhood"
                               value="{{ auth('collaborator')->user()->neighborhood ?? '' }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)]">
                    </div>
                </div>
            </div>

            <!-- Informações Profissionais (Somente Leitura) -->
            <div>
                <h2 class="text-xl font-semibold text-[var(--color-main)] mb-4">
                    <i class="fa-solid fa-briefcase mr-2"></i>
                    Informações Profissionais
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-[var(--color-text)] mb-2">
                            Cargo
                        </label>
                        <div class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-700">
                            {{ auth('collaborator')->user()->position->name ?? 'Não informado' }}
                        </div>
                        <p class="text-xs text-[var(--color-text)] mt-1">* Campo não editável</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[var(--color-text)] mb-2">
                            Data de Admissão
                        </label>
                        <div class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-700">
                            {{ auth('collaborator')->user()->admission_date ? auth('collaborator')->user()->admission_date->format('d/m/Y') : 'Não informado' }}
                        </div>
                        <p class="text-xs text-[var(--color-text)] mt-1">* Campo não editável</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[var(--color-text)] mb-2">
                            Status
                        </label>
                        <div class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100">
                            <span class="px-2 py-1 rounded-full text-xs {{ auth('collaborator')->user()->status->badgeClass() ?? 'bg-gray-100 text-gray-800' }}">
                                {{ auth('collaborator')->user()->status->label() ?? 'Não informado' }}
                            </span>
                        </div>
                        <p class="text-xs text-[var(--color-text)] mt-1">* Campo não editável</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[var(--color-text)] mb-2">
                            Jornada de Trabalho
                        </label>
                        <div class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-700">
                            {{ auth('collaborator')->user()->workHours->name ?? 'Não informado' }}
                        </div>
                        <p class="text-xs text-[var(--color-text)] mt-1">* Campo não editável</p>
                    </div>
                </div>
            </div>

            <!-- Alterar Senha -->
            <div>
                <h2 class="text-xl font-semibold text-[var(--color-main)] mb-4">
                    <i class="fa-solid fa-lock mr-2"></i>
                    Alterar Senha
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                            Senha Atual
                        </label>
                        <input type="password"
                               id="current_password"
                               name="current_password"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)]">
                    </div>

                    <div>
                        <label for="new_password" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                            Nova Senha
                        </label>
                        <input type="password"
                               id="new_password"
                               name="new_password"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)]">
                    </div>

                    <div class="md:col-span-2">
                        <label for="new_password_confirmation" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                            Confirmar Nova Senha
                        </label>
                        <input type="password"
                               id="new_password_confirmation"
                               name="new_password_confirmation"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)]">
                    </div>
                </div>
            </div>

            <!-- Botões -->
            <div class="flex items-center justify-end space-x-4 pt-6">
                <a href="{{ route('system-for-employees.dashboard.index') }}"
                   class="px-6 py-2 border border-gray-300 rounded-lg text-[var(--color-text)] hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-[var(--color-main)] text-white rounded-lg hover:bg-[var(--color-main)]/80 transition-colors">
                    <i class="fa-solid fa-save mr-2"></i>
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
