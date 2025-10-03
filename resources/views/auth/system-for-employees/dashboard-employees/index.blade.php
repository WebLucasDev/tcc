@extends('layouts.layout')

@section('title', 'Dashboard - Colaborador')

@section('content')
<div class="space-y-6">
    <!-- Header do Dashboard -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-[var(--color-main)]">
                Bem-vindo, {{ auth('collaborator')->user()->name ?? 'Colaborador' }}!
            </h1>
            <p class="text-[var(--color-text)] mt-2">
                Painel de controle pessoal - {{ now()->format('d/m/Y') }}
            </p>
        </div>

        <div class="flex items-center gap-4">
            <div class="text-right">
                <p class="text-sm text-[var(--color-text)]">Hora atual</p>
                <p class="text-2xl font-bold text-[var(--color-main)]">{{ now()->format('H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Cards de Métricas Principais -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card Bater Ponto -->
        <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[var(--color-text)] text-sm font-medium">Bater Ponto</p>
                    <p class="text-lg font-semibold text-[var(--color-main)] mt-2">Registre agora</p>
                    <p class="text-green-500 text-sm mt-1">
                        <i class="fa-solid fa-clock mr-1"></i>
                        Entrada/Saída
                    </p>
                </div>
                <div class="bg-green-500/10 p-3 rounded-full">
                    <i class="fa-solid fa-clock text-green-600 text-xl"></i>
                </div>
            </div>
            <button class="w-full mt-4 bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors font-medium">
                Registrar Ponto
            </button>
        </div>

        <!-- Card Histórico -->
        <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[var(--color-text)] text-sm font-medium">Histórico</p>
                    <p class="text-lg font-semibold text-[var(--color-main)] mt-2">Meus registros</p>
                    <p class="text-blue-500 text-sm mt-1">
                        <i class="fa-solid fa-history mr-1"></i>
                        Consultar
                    </p>
                </div>
                <div class="bg-blue-500/10 p-3 rounded-full">
                    <i class="fa-solid fa-history text-blue-600 text-xl"></i>
                </div>
            </div>
            <button class="w-full mt-4 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                Ver Histórico
            </button>
        </div>

        <!-- Card Solicitações -->
        <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[var(--color-text)] text-sm font-medium">Solicitações</p>
                    <p class="text-lg font-semibold text-[var(--color-main)] mt-2">Gerenciar</p>
                    <p class="text-yellow-500 text-sm mt-1">
                        <i class="fa-solid fa-file-alt mr-1"></i>
                        Nova solicitação
                    </p>
                </div>
                <div class="bg-yellow-500/10 p-3 rounded-full">
                    <i class="fa-solid fa-file-alt text-yellow-600 text-xl"></i>
                </div>
            </div>
            <button class="w-full mt-4 bg-yellow-600 text-white py-2 px-4 rounded-lg hover:bg-yellow-700 transition-colors font-medium">
                Nova Solicitação
            </button>
        </div>

        <!-- Card Banco de Horas -->
        <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[var(--color-text)] text-sm font-medium">Banco de Horas</p>
                    <p class="text-3xl font-bold text-[var(--color-main)] mt-2">+8:30h</p>
                    <p class="text-purple-500 text-sm mt-1">
                        <i class="fa-solid fa-calendar-check mr-1"></i>
                        Saldo positivo
                    </p>
                </div>
                <div class="bg-purple-500/10 p-3 rounded-full">
                    <i class="fa-solid fa-calendar-check text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção de Informações e Registros -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Informações do Colaborador -->
        <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-[var(--color-main)]">
                    <i class="fa-solid fa-user mr-2"></i>
                    Minhas Informações
                </h3>
                <a href="{{ route('system-for-employees.registrations.index') }}"
                   class="text-sm text-blue-600 hover:text-blue-800">
                    Editar
                </a>
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-[var(--color-text)]/5 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-500/10 rounded-full flex items-center justify-center mr-3">
                            <i class="fa-solid fa-user text-blue-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs text-[var(--color-text)]">Nome</p>
                            <p class="font-medium text-[var(--color-main)] text-sm">{{ auth('collaborator')->user()->name ?? 'Não informado' }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 bg-[var(--color-text)]/5 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-purple-500/10 rounded-full flex items-center justify-center mr-3">
                            <i class="fa-solid fa-envelope text-purple-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs text-[var(--color-text)]">Email</p>
                            <p class="font-medium text-[var(--color-main)] text-sm">{{ auth('collaborator')->user()->email ?? 'Não informado' }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 bg-[var(--color-text)]/5 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-orange-500/10 rounded-full flex items-center justify-center mr-3">
                            <i class="fa-solid fa-briefcase text-orange-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs text-[var(--color-text)]">Cargo</p>
                            <p class="font-medium text-[var(--color-main)] text-sm">{{ auth('collaborator')->user()->position->name ?? 'Não informado' }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 bg-[var(--color-text)]/5 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500/10 rounded-full flex items-center justify-center mr-3">
                            <i class="fa-solid fa-check-circle text-green-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs text-[var(--color-text)]">Status</p>
                            <span class="text-xs px-2 py-1 rounded-full {{ auth('collaborator')->user()->status->badgeClass() ?? 'bg-gray-100 text-gray-800' }}">
                                {{ auth('collaborator')->user()->status->label() ?? 'Não informado' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 bg-[var(--color-text)]/5 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-yellow-500/10 rounded-full flex items-center justify-center mr-3">
                            <i class="fa-solid fa-calendar text-yellow-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs text-[var(--color-text)]">Data de Admissão</p>
                            <p class="font-medium text-[var(--color-main)] text-sm">
                                {{ auth('collaborator')->user()->admission_date ? auth('collaborator')->user()->admission_date->format('d/m/Y') : 'Não informado' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Últimos Registros de Ponto -->
        <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-[var(--color-main)]">
                    <i class="fa-solid fa-clock mr-2"></i>
                    Registros de Hoje
                </h3>
                <a href="#" class="text-sm text-blue-600 hover:text-blue-800">Ver todos</a>
            </div>

            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-[var(--color-text)]/5 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500/10 rounded-full flex items-center justify-center mr-3">
                            <i class="fa-solid fa-sign-in-alt text-green-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="font-medium text-[var(--color-main)] text-sm">Entrada</p>
                            <p class="text-xs text-[var(--color-text)]">Hoje - Manhã</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs px-2 py-1 rounded-full bg-green-500/20 text-green-700 dark:text-green-400">
                            Completo
                        </span>
                        <p class="text-sm font-medium text-[var(--color-main)] mt-1">08:00</p>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 bg-[var(--color-text)]/5 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-yellow-500/10 rounded-full flex items-center justify-center mr-3">
                            <i class="fa-solid fa-sign-out-alt text-yellow-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="font-medium text-[var(--color-main)] text-sm">Saída para Almoço</p>
                            <p class="text-xs text-[var(--color-text)]">Hoje - Meio-dia</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs px-2 py-1 rounded-full bg-yellow-500/20 text-yellow-700 dark:text-yellow-400">
                            Completo
                        </span>
                        <p class="text-sm font-medium text-[var(--color-main)] mt-1">12:00</p>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 bg-[var(--color-text)]/5 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500/10 rounded-full flex items-center justify-center mr-3">
                            <i class="fa-solid fa-sign-in-alt text-green-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="font-medium text-[var(--color-main)] text-sm">Retorno do Almoço</p>
                            <p class="text-xs text-[var(--color-text)]">Hoje - Tarde</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs px-2 py-1 rounded-full bg-green-500/20 text-green-700 dark:text-green-400">
                            Completo
                        </span>
                        <p class="text-sm font-medium text-[var(--color-main)] mt-1">13:00</p>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 bg-[var(--color-text)]/5 rounded-lg border-2 border-dashed border-blue-500/30">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-500/10 rounded-full flex items-center justify-center mr-3">
                            <i class="fa-solid fa-clock text-blue-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="font-medium text-[var(--color-main)] text-sm">Próximo Registro</p>
                            <p class="text-xs text-[var(--color-text)]">Saída - Final do expediente</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs px-2 py-1 rounded-full bg-blue-500/20 text-blue-700 dark:text-blue-400">
                            Pendente
                        </span>
                        <p class="text-sm font-medium text-[var(--color-main)] mt-1">~18:00</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas do Mês -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Estatísticas de Presença -->
        <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-[var(--color-main)]">Presença do Mês</h3>
                <i class="fa-solid fa-calendar-check text-green-500"></i>
            </div>

            <div class="space-y-4">
                <div class="text-center">
                    <p class="text-3xl font-bold text-[var(--color-main)]">95%</p>
                    <p class="text-sm text-[var(--color-text)]">Taxa de presença</p>
                </div>

                <div class="grid grid-cols-2 gap-3 text-center">
                    <div class="bg-green-500/10 p-3 rounded-lg">
                        <p class="text-lg font-semibold text-green-700 dark:text-green-400">18</p>
                        <p class="text-xs text-green-600 dark:text-green-500">Dias presentes</p>
                    </div>
                    <div class="bg-red-500/10 p-3 rounded-lg">
                        <p class="text-lg font-semibold text-red-700 dark:text-red-400">1</p>
                        <p class="text-xs text-red-600 dark:text-red-500">Ausências</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas de Pontualidade -->
        <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-[var(--color-main)]">Pontualidade</h3>
                <i class="fa-solid fa-clock text-blue-500"></i>
            </div>

            <div class="space-y-4">
                <div class="text-center">
                    <p class="text-3xl font-bold text-[var(--color-main)]">92%</p>
                    <p class="text-sm text-[var(--color-text)]">Taxa de pontualidade</p>
                </div>

                <div class="grid grid-cols-2 gap-3 text-center">
                    <div class="bg-blue-500/10 p-3 rounded-lg">
                        <p class="text-lg font-semibold text-blue-700 dark:text-blue-400">17</p>
                        <p class="text-xs text-blue-600 dark:text-blue-500">No horário</p>
                    </div>
                    <div class="bg-orange-500/10 p-3 rounded-lg">
                        <p class="text-lg font-semibold text-orange-700 dark:text-orange-400">2</p>
                        <p class="text-xs text-orange-600 dark:text-orange-500">Atrasos</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Horas Trabalhadas -->
        <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-[var(--color-main)]">Horas Trabalhadas</h3>
                <i class="fa-solid fa-business-time text-purple-500"></i>
            </div>

            <div class="space-y-4">
                <div class="text-center">
                    <p class="text-3xl font-bold text-[var(--color-main)]">152h</p>
                    <p class="text-sm text-[var(--color-text)]">Total do mês</p>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-[var(--color-text)]">Média diária:</span>
                        <span class="text-sm font-medium text-[var(--color-main)]">8.4h</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-[var(--color-text)]">Meta mensal:</span>
                        <span class="text-sm font-medium text-[var(--color-main)]">160h</span>
                    </div>
                    <div class="flex-1 bg-[var(--color-text)]/20 rounded-full h-2">
                        <div class="h-2 rounded-full bg-purple-500 transition-all duration-300" style="width: 95%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Avisos e Solicitações -->
    <div class="grid grid-cols-1 gap-6">
        <!-- Minhas Solicitações -->
        <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-[var(--color-main)]">Minhas Solicitações</h3>
                <button class="text-sm bg-blue-600 text-white px-3 py-1 rounded-lg hover:bg-blue-700">
                    + Nova
                </button>
            </div>

            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-yellow-500/10 border border-yellow-500/20 rounded-lg">
                    <div class="flex items-center">
                        <i class="fa-solid fa-clock text-yellow-600 mr-3"></i>
                        <div>
                            <p class="font-medium text-[var(--color-main)] text-sm">Ajuste de Ponto</p>
                            <p class="text-xs text-[var(--color-text)]">15/09/2025 - Solicitado há 2 dias</p>
                        </div>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full bg-yellow-500/20 text-yellow-700 dark:text-yellow-400">
                        Pendente
                    </span>
                </div>

                <div class="flex items-center justify-between p-3 bg-green-500/10 border border-green-500/20 rounded-lg">
                    <div class="flex items-center">
                        <i class="fa-solid fa-check-circle text-green-600 mr-3"></i>
                        <div>
                            <p class="font-medium text-[var(--color-main)] text-sm">Férias</p>
                            <p class="text-xs text-[var(--color-text)]">10/09/2025 - Aprovado há 1 semana</p>
                        </div>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full bg-green-500/20 text-green-700 dark:text-green-400">
                        Aprovado
                    </span>
                </div>
            </div>

            <div class="mt-4 text-center text-sm text-[var(--color-text)]">
                <a href="#" class="text-blue-600 hover:underline">Ver todas as solicitações</a>
            </div>
        </div>
    </div>
</div>
@endsection
