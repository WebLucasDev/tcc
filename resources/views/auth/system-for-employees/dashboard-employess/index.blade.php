@extends('layouts.layout')

@section('title', 'Dashboard - Colaborador')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-[var(--color-background)] rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-[var(--color-main)]">
                    Bem-vindo, {{ auth('collaborator')->user()->name ?? 'Colaborador' }}!
                </h1>
                <p class="text-[var(--color-text)] mt-1">
                    Aqui você pode visualizar suas informações e gerenciar seu ponto eletrônico.
                </p>
            </div>
            <div class="text-right">
                <p class="text-sm text-[var(--color-text)]">{{ now()->format('d/m/Y') }}</p>
                <p class="text-lg font-semibold text-[var(--color-main)]">{{ now()->format('H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Cards de Ações Rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card Bater Ponto -->
        <div class="bg-[var(--color-background)] rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-[var(--color-main)]">Bater Ponto</h3>
                    <p class="text-sm text-[var(--color-text)] mt-1">Registre sua entrada/saída</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fa-solid fa-clock text-green-600 text-xl"></i>
                </div>
            </div>
            <button class="w-full mt-4 bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
                Registrar Ponto
            </button>
        </div>

        <!-- Card Histórico -->
        <div class="bg-[var(--color-background)] rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-[var(--color-main)]">Histórico</h3>
                    <p class="text-sm text-[var(--color-text)] mt-1">Visualize seus registros</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fa-solid fa-history text-blue-600 text-xl"></i>
                </div>
            </div>
            <button class="w-full mt-4 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                Ver Histórico
            </button>
        </div>

        <!-- Card Solicitações -->
        <div class="bg-[var(--color-background)] rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-[var(--color-main)]">Solicitações</h3>
                    <p class="text-sm text-[var(--color-text)] mt-1">Gerencie suas solicitações</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fa-solid fa-file-alt text-yellow-600 text-xl"></i>
                </div>
            </div>
            <button class="w-full mt-4 bg-yellow-600 text-white py-2 px-4 rounded-lg hover:bg-yellow-700 transition-colors">
                Nova Solicitação
            </button>
        </div>

        <!-- Card Banco de Horas -->
        <div class="bg-[var(--color-background)] rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-[var(--color-main)]">Banco de Horas</h3>
                    <p class="text-sm text-[var(--color-text)] mt-1">Saldo atual</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fa-solid fa-calendar-check text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 text-center">
                <p class="text-2xl font-bold text-[var(--color-main)]">+8:30h</p>
                <p class="text-sm text-[var(--color-text)]">Saldo positivo</p>
            </div>
        </div>
    </div>

    <!-- Seção de Informações Pessoais -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Informações do Colaborador -->
        <div class="bg-[var(--color-background)] rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold text-[var(--color-main)] mb-4">
                <i class="fa-solid fa-user mr-2"></i>
                Minhas Informações
            </h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-[var(--color-text)]">Nome:</span>
                    <span class="font-medium text-[var(--color-main)]">{{ auth('collaborator')->user()->name ?? 'Não informado' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[var(--color-text)]">Email:</span>
                    <span class="font-medium text-[var(--color-main)]">{{ auth('collaborator')->user()->email ?? 'Não informado' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[var(--color-text)]">Cargo:</span>
                    <span class="font-medium text-[var(--color-main)]">{{ auth('collaborator')->user()->position->name ?? 'Não informado' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[var(--color-text)]">Status:</span>
                    <span class="px-2 py-1 rounded-full text-xs {{ auth('collaborator')->user()->status->badgeClass() ?? 'bg-gray-100 text-gray-800' }}">
                        {{ auth('collaborator')->user()->status->label() ?? 'Não informado' }}
                    </span>
                </div>
            </div>
            <a href="{{ route('system-for-employees.registrations.index') }}"
               class="inline-block mt-4 text-[var(--color-main)] hover:underline">
                <i class="fa-solid fa-edit mr-1"></i>
                Editar informações
            </a>
        </div>

        <!-- Últimos Registros de Ponto -->
        <div class="bg-[var(--color-background)] rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold text-[var(--color-main)] mb-4">
                <i class="fa-solid fa-clock mr-2"></i>
                Últimos Registros
            </h2>
            <div class="space-y-3">
                <!-- Aqui virão os registros dinâmicos -->
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <div>
                        <p class="font-medium text-[var(--color-main)]">Entrada</p>
                        <p class="text-sm text-[var(--color-text)]">Hoje</p>
                    </div>
                    <span class="text-green-600 font-semibold">08:00</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <div>
                        <p class="font-medium text-[var(--color-main)]">Saída para Almoço</p>
                        <p class="text-sm text-[var(--color-text)]">Hoje</p>
                    </div>
                    <span class="text-yellow-600 font-semibold">12:00</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <div>
                        <p class="font-medium text-[var(--color-main)]">Retorno do Almoço</p>
                        <p class="text-sm text-[var(--color-text)]">Hoje</p>
                    </div>
                    <span class="text-green-600 font-semibold">13:00</span>
                </div>
            </div>
            <a href="#" class="inline-block mt-4 text-[var(--color-main)] hover:underline">
                <i class="fa-solid fa-history mr-1"></i>
                Ver histórico completo
            </a>
        </div>
    </div>
</div>
@endsection
