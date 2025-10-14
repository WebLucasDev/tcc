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
                    <p class="text-3xl font-bold {{ $metrics['bank_hours']['is_positive'] ? 'text-green-600' : 'text-red-600' }} mt-2">
                        {{ $metrics['bank_hours']['balance_formatted'] }}h
                    </p>
                    <p class="{{ $metrics['bank_hours']['is_positive'] ? 'text-green-500' : 'text-red-500' }} text-sm mt-1">
                        <i class="fa-solid fa-calendar-check mr-1"></i>
                        Saldo {{ $metrics['bank_hours']['is_positive'] ? 'positivo' : 'negativo' }}
                    </p>
                </div>
                <div class="bg-{{ $metrics['bank_hours']['is_positive'] ? 'green' : 'red' }}-500/10 p-3 rounded-full">
                    <i class="fa-solid fa-calendar-check text-{{ $metrics['bank_hours']['is_positive'] ? 'green' : 'red' }}-600 text-xl"></i>
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
                <a href="{{ route('system-for-employees.time-tracking.index') }}" class="text-sm text-blue-600 hover:text-blue-800">Ver todos</a>
            </div>

            @if($metrics['today_records']['record'])
                <div class="space-y-3">
                    @if($metrics['today_records']['has_entry_1'])
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
                                    Registrado
                                </span>
                                <p class="text-sm font-medium text-[var(--color-main)] mt-1">
                                    {{ \Carbon\Carbon::parse($metrics['today_records']['record']->entry_time_1)->format('H:i') }}
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($metrics['today_records']['has_return_1'])
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
                                    Registrado
                                </span>
                                <p class="text-sm font-medium text-[var(--color-main)] mt-1">
                                    {{ \Carbon\Carbon::parse($metrics['today_records']['record']->return_time_1)->format('H:i') }}
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($metrics['today_records']['has_entry_2'])
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
                                    Registrado
                                </span>
                                <p class="text-sm font-medium text-[var(--color-main)] mt-1">
                                    {{ \Carbon\Carbon::parse($metrics['today_records']['record']->entry_time_2)->format('H:i') }}
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($metrics['today_records']['has_return_2'])
                        <div class="flex items-center justify-between p-3 bg-[var(--color-text)]/5 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-red-500/10 rounded-full flex items-center justify-center mr-3">
                                    <i class="fa-solid fa-sign-out-alt text-red-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-[var(--color-main)] text-sm">Saída Final</p>
                                    <p class="text-xs text-[var(--color-text)]">Hoje - Final do dia</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs px-2 py-1 rounded-full bg-red-500/20 text-red-700 dark:text-red-400">
                                    Registrado
                                </span>
                                <p class="text-sm font-medium text-[var(--color-main)] mt-1">
                                    {{ \Carbon\Carbon::parse($metrics['today_records']['record']->return_time_2)->format('H:i') }}
                                </p>
                            </div>
                        </div>
                    @else
                        @if($metrics['today_records']['has_entry_2'])
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
                                </div>
                            </div>
                        @endif
                    @endif

                    @if($metrics['today_records']['total_hours'] > 0)
                        <div class="mt-4 p-3 bg-blue-500/10 border border-blue-500/20 rounded-lg text-center">
                            <p class="text-sm text-[var(--color-text)]">Total trabalhado hoje</p>
                            <p class="text-lg font-bold text-blue-600">{{ $metrics['today_records']['total_hours'] }}h</p>
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center py-8 text-[var(--color-text)]">
                    <i class="fa-solid fa-clock text-4xl mb-3 opacity-30"></i>
                    <p class="text-sm">Nenhum registro de ponto hoje</p>
                    <p class="text-xs opacity-60 mt-1">Faça seu primeiro registro para começar</p>
                </div>
            @endif
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
                    <p class="text-3xl font-bold text-[var(--color-main)]">{{ $metrics['month_statistics']['attendance_rate'] }}%</p>
                    <p class="text-sm text-[var(--color-text)]">Taxa de presença</p>
                </div>

                <div class="grid grid-cols-2 gap-3 text-center">
                    <div class="bg-green-500/10 p-3 rounded-lg">
                        <p class="text-lg font-semibold text-green-700 dark:text-green-400">{{ $metrics['month_statistics']['present_days'] }}</p>
                        <p class="text-xs text-green-600 dark:text-green-500">Dias presentes</p>
                    </div>
                    <div class="bg-red-500/10 p-3 rounded-lg">
                        <p class="text-lg font-semibold text-red-700 dark:text-red-400">{{ $metrics['month_statistics']['absent_days'] }}</p>
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
                    <p class="text-3xl font-bold text-[var(--color-main)]">{{ $metrics['punctuality']['punctuality_rate'] }}%</p>
                    <p class="text-sm text-[var(--color-text)]">Taxa de pontualidade</p>
                </div>

                <div class="grid grid-cols-2 gap-3 text-center">
                    <div class="bg-blue-500/10 p-3 rounded-lg">
                        <p class="text-lg font-semibold text-blue-700 dark:text-blue-400">{{ $metrics['punctuality']['on_time'] }}</p>
                        <p class="text-xs text-blue-600 dark:text-blue-500">No horário</p>
                    </div>
                    <div class="bg-orange-500/10 p-3 rounded-lg">
                        <p class="text-lg font-semibold text-orange-700 dark:text-orange-400">{{ $metrics['punctuality']['late'] }}</p>
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
                    <p class="text-3xl font-bold text-[var(--color-main)]">{{ $metrics['month_statistics']['total_worked_hours'] }}h</p>
                    <p class="text-sm text-[var(--color-text)]">Total do mês</p>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-[var(--color-text)]">Média diária:</span>
                        <span class="text-sm font-medium text-[var(--color-main)]">{{ $metrics['month_statistics']['average_daily_hours'] }}h</span>
                    </div>
                    @if($collaborator->workHours)
                        @php
                            $expectedMonthlyHours = $collaborator->workHours->total_weekly_hours * 4;
                            $progressPercentage = $expectedMonthlyHours > 0 ? min(round(($metrics['month_statistics']['total_worked_hours'] / $expectedMonthlyHours) * 100, 1), 100) : 0;
                        @endphp
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-[var(--color-text)]">Meta mensal:</span>
                            <span class="text-sm font-medium text-[var(--color-main)]">{{ $expectedMonthlyHours }}h</span>
                        </div>
                        <div class="flex-1 bg-[var(--color-text)]/20 rounded-full h-2">
                            <div class="h-2 rounded-full bg-purple-500 transition-all duration-300" style="width: {{ $progressPercentage }}%"></div>
                        </div>
                    @endif
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
                <a href="{{ route('system-for-employees.solicitation.index') }}" class="text-sm bg-blue-600 text-white px-3 py-1 rounded-lg hover:bg-blue-700">
                    + Nova
                </a>
            </div>

            @if($metrics['recent_solicitations']->count() > 0)
                <div class="space-y-3">
                    @foreach($metrics['recent_solicitations'] as $solicitation)
                        <div class="flex items-center justify-between p-3 bg-{{ $solicitation->status->value == 'pending' ? 'yellow' : ($solicitation->status->value == 'approved' ? 'green' : 'red') }}-500/10 border border-{{ $solicitation->status->value == 'pending' ? 'yellow' : ($solicitation->status->value == 'approved' ? 'green' : 'red') }}-500/20 rounded-lg">
                            <div class="flex items-center">
                                <i class="fa-solid fa-{{ $solicitation->status->value == 'pending' ? 'clock' : ($solicitation->status->value == 'approved' ? 'check-circle' : 'times-circle') }} text-{{ $solicitation->status->value == 'pending' ? 'yellow' : ($solicitation->status->value == 'approved' ? 'green' : 'red') }}-600 mr-3"></i>
                                <div>
                                    <p class="font-medium text-[var(--color-main)] text-sm">{{ $solicitation->type }}</p>
                                    <p class="text-xs text-[var(--color-text)]">
                                        {{ $solicitation->date ? \Carbon\Carbon::parse($solicitation->date)->format('d/m/Y') : 'Sem data' }} -
                                        {{ $solicitation->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            <span class="text-xs px-2 py-1 rounded-full bg-{{ $solicitation->status->value == 'pending' ? 'yellow' : ($solicitation->status->value == 'approved' ? 'green' : 'red') }}-500/20 text-{{ $solicitation->status->value == 'pending' ? 'yellow' : ($solicitation->status->value == 'approved' ? 'green' : 'red') }}-700 dark:text-{{ $solicitation->status->value == 'pending' ? 'yellow' : ($solicitation->status->value == 'approved' ? 'green' : 'red') }}-400">
                                {{ $solicitation->status->label() }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 text-center text-sm text-[var(--color-text)]">
                    <a href="{{ route('system-for-employees.solicitation.index') }}" class="text-blue-600 hover:underline">Ver todas as solicitações</a>
                </div>
            @else
                <div class="text-center py-8 text-[var(--color-text)]">
                    <i class="fa-solid fa-file-alt text-4xl mb-3 opacity-30"></i>
                    <p class="text-sm">Nenhuma solicitação encontrada</p>
                    <p class="text-xs opacity-60 mt-1">Crie sua primeira solicitação</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
