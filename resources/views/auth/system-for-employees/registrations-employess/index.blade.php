@extends('layouts.layout')

@section('title', 'Meus Dados')

@push('styles')
    @vite(['resources/js/menus/for-employees/registrations.js'])
@endpush

@section('content')
<div class="space-y-6">

    <x-error/>
    <x-success/>

    <!-- Header Section -->
    <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
        <h1 class="text-3xl font-bold text-[var(--color-main)]">
            <i class="fa-solid fa-user-circle mr-2"></i>
            Meus Dados Pessoais
        </h1>
        <p class="text-[var(--color-text)] mt-2">
            Visualize suas informações pessoais e altere sua senha quando necessário.
        </p>
    </div>

    @if(session('error'))
    <div class="bg-red-500/10 border border-red-500/20 rounded-lg p-4">
        <p class="text-red-700 dark:text-red-400">
            <i class="fa-solid fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </p>
    </div>
    @endif

    <!-- Informações Básicas (Somente Visualização) -->
    <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
        <h2 class="text-xl font-semibold text-[var(--color-main)] mb-6">
            <i class="fa-solid fa-user mr-2"></i>
            Informações Básicas
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-4 bg-[var(--color-text)]/5 rounded-lg">
                <label class="block text-sm font-medium text-[var(--color-text)] mb-2">
                    Nome Completo
                </label>
                <p class="text-[var(--color-main)] font-medium text-lg">
                    {{ $collaborator->name ?? 'Não informado' }}
                </p>
            </div>

            <div class="p-4 bg-[var(--color-text)]/5 rounded-lg">
                <label class="block text-sm font-medium text-[var(--color-text)] mb-2">
                    Email
                </label>
                <p class="text-[var(--color-main)] font-medium text-lg">
                    {{ $collaborator->email ?? 'Não informado' }}
                </p>
            </div>

            <div class="p-4 bg-[var(--color-text)]/5 rounded-lg">
                <label class="block text-sm font-medium text-[var(--color-text)] mb-2">
                    CPF
                </label>
                <p class="text-[var(--color-main)] font-medium text-lg">
                    {{ $collaborator->cpf ?? 'Não informado' }}
                </p>
            </div>

            <div class="p-4 bg-[var(--color-text)]/5 rounded-lg">
                <label class="block text-sm font-medium text-[var(--color-text)] mb-2">
                    Telefone
                </label>
                <p class="text-[var(--color-main)] font-medium text-lg">
                    {{ $collaborator->phone ?? 'Não informado' }}
                </p>
            </div>
        </div>

        <div class="mt-4 p-3 bg-gray-500/10 border border-gray-500/20 rounded-lg">
            <p class="text-sm text-gray-700 dark:text-gray-400">
                <i class="fa-solid fa-lock mr-2"></i>
                Estas informações são gerenciadas pelo departamento de RH. Para alterações, entre em contato com o setor responsável.
            </p>
        </div>
    </div>

    <!-- Informações de Endereço (Somente Visualização) -->
    <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
        <h2 class="text-xl font-semibold text-[var(--color-main)] mb-6">
            <i class="fa-solid fa-map-marker-alt mr-2"></i>
            Endereço
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="p-4 bg-[var(--color-text)]/5 rounded-lg">
                <label class="block text-sm font-medium text-[var(--color-text)] mb-2">
                    CEP
                </label>
                <p class="text-[var(--color-main)] font-medium text-lg">
                    {{ $collaborator->zip_code ?? 'Não informado' }}
                </p>
            </div>

            <div class="p-4 bg-[var(--color-text)]/5 rounded-lg md:col-span-2">
                <label class="block text-sm font-medium text-[var(--color-text)] mb-2">
                    Rua/Avenida
                </label>
                <p class="text-[var(--color-main)] font-medium text-lg">
                    {{ $collaborator->street ?? 'Não informado' }}
                </p>
            </div>

            <div class="p-4 bg-[var(--color-text)]/5 rounded-lg">
                <label class="block text-sm font-medium text-[var(--color-text)] mb-2">
                    Número
                </label>
                <p class="text-[var(--color-main)] font-medium text-lg">
                    {{ $collaborator->number ?? 'Não informado' }}
                </p>
            </div>

            <div class="p-4 bg-[var(--color-text)]/5 rounded-lg md:col-span-2">
                <label class="block text-sm font-medium text-[var(--color-text)] mb-2">
                    Bairro
                </label>
                <p class="text-[var(--color-main)] font-medium text-lg">
                    {{ $collaborator->neighborhood ?? 'Não informado' }}
                </p>
            </div>
        </div>

        <div class="mt-4 p-3 bg-gray-500/10 border border-gray-500/20 rounded-lg">
            <p class="text-sm text-gray-700 dark:text-gray-400">
                <i class="fa-solid fa-lock mr-2"></i>
                Estas informações são gerenciadas pelo departamento de RH. Para alterações, entre em contato com o setor responsável.
            </p>
        </div>
    </div>

    <!-- Informações Profissionais (Somente Leitura) -->
    <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
        <h2 class="text-xl font-semibold text-[var(--color-main)] mb-6">
            <i class="fa-solid fa-briefcase mr-2"></i>
            Informações Profissionais
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-4 bg-[var(--color-text)]/5 rounded-lg">
                <label class="block text-sm font-medium text-[var(--color-text)] mb-2">
                    Cargo
                </label>
                <p class="text-[var(--color-main)] font-medium text-lg">
                    {{ $collaborator->position->name ?? 'Não informado' }}
                </p>
            </div>

            <div class="p-4 bg-[var(--color-text)]/5 rounded-lg">
                <label class="block text-sm font-medium text-[var(--color-text)] mb-2">
                    Data de Admissão
                </label>
                <p class="text-[var(--color-main)] font-medium text-lg">
                    {{ $collaborator->admission_date ? $collaborator->admission_date->format('d/m/Y') : 'Não informado' }}
                </p>
            </div>

            <div class="p-4 bg-[var(--color-text)]/5 rounded-lg">
                <label class="block text-sm font-medium text-[var(--color-text)] mb-2">
                    Status
                </label>
                <div>
                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $collaborator->status->badgeClass() ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $collaborator->status->label() ?? 'Não informado' }}
                    </span>
                </div>
            </div>

            <div class="p-4 bg-[var(--color-text)]/5 rounded-lg">
                <label class="block text-sm font-medium text-[var(--color-text)] mb-2">
                    Jornada de Trabalho
                </label>
                <p class="text-[var(--color-main)] font-medium text-lg">
                    {{ $collaborator->workHours->name ?? 'Não informado' }}
                </p>
            </div>
        </div>

        <div class="mt-4 p-3 bg-gray-500/10 border border-gray-500/20 rounded-lg">
            <p class="text-sm text-gray-700 dark:text-gray-400">
                <i class="fa-solid fa-lock mr-2"></i>
                Estas informações são gerenciadas exclusivamente pelo departamento de RH e não podem ser alteradas por você.
            </p>
        </div>
    </div>

    <!-- Alterar Senha (Editável) -->
    <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-[var(--color-main)]">
                <i class="fa-solid fa-key mr-2"></i>
                Alterar Senha
            </h2>
            <p class="text-sm text-[var(--color-text)] mt-1">
                Mantenha sua senha segura. Use no mínimo 6 caracteres.
            </p>
        </div>

        <form action="{{ route('system-for-employees.registrations.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="current_password" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                        Senha Atual *
                    </label>
                    <div class="relative">
                        <input type="password"
                               id="current_password"
                               name="current_password"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)] @error('current_password') border-red-500 @enderror"
                               required>
                        <button type="button"
                                onclick="togglePassword('current_password')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 transition-colors">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="new_password" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                        Nova Senha *
                    </label>
                    <div class="relative">
                        <input type="password"
                               id="new_password"
                               name="new_password"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)] @error('new_password') border-red-500 @enderror"
                               required>
                        <button type="button"
                                onclick="togglePassword('new_password')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 transition-colors">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Mínimo de 6 caracteres</p>
                    @error('new_password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="new_password_confirmation" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                        Confirmar Nova Senha *
                    </label>
                    <div class="relative">
                        <input type="password"
                               id="new_password_confirmation"
                               name="new_password_confirmation"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)]"
                               required>
                        <button type="button"
                                onclick="togglePassword('new_password_confirmation')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 transition-colors">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-blue-500/10 border border-blue-500/20 rounded-lg">
                <p class="text-sm text-blue-700 dark:text-blue-400">
                    <i class="fa-solid fa-info-circle mr-2"></i>
                    Sua senha será alterada imediatamente após confirmar. Certifique-se de memorizar a nova senha.
                </p>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-[var(--color-text)]/10">
                <a href="{{ route('system-for-employees.dashboard.index') }}"
                   class="px-6 py-3 border border-gray-300 rounded-lg text-[var(--color-text)] hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors font-medium">
                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    Voltar
                </a>
                <button type="submit"
                        class="px-6 py-3 bg-[var(--color-main)] text-white rounded-lg hover:bg-[var(--color-main)]/80 transition-colors font-medium shadow-lg">
                    <i class="fa-solid fa-key mr-2"></i>
                    Alterar Senha
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endpush
