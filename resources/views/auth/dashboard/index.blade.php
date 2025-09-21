@extends('layouts.layout')
@section('title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        <!-- Header do Dashboard com Filtros -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-[var(--color-main)]">Dashboard</h1>
                <p class="text-[var(--color-text)] mt-2">
                    Relatórios e análises - {{ $metrics['period']['month_name'] }} {{ $metrics['period']['year'] }}
                </p>
            </div>

            <!-- Filtros -->
            <div class="flex flex-wrap gap-3">
                <form method="GET" class="flex flex-wrap gap-3" id="dashboardFilters">
                    <div class="flex items-center gap-2">
                        <label class="text-sm text-[var(--color-text)]">Período:</label>
                        <input type="month"
                               name="month"
                               value="{{ $month }}"
                               class="px-3 py-2 bg-[var(--color-background)] border border-[var(--color-text)]/20 text-[var(--color-text)] rounded-lg text-sm focus:border-[var(--color-main)] focus:ring-1 focus:ring-[var(--color-main)]"
                               onchange="document.getElementById('dashboardFilters').submit();">
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="text-sm text-[var(--color-text)]">Departamento:</label>
                        <select name="department_id"
                                class="px-3 py-2 bg-[var(--color-background)] border border-[var(--color-text)]/20 text-[var(--color-text)] rounded-lg text-sm focus:border-[var(--color-main)] focus:ring-1 focus:ring-[var(--color-main)]"
                                onchange="document.getElementById('dashboardFilters').submit();">
                            <option value="">Todos</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ $department_id == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cards de Métricas Principais -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total de Colaboradores -->
            <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[var(--color-text)] text-sm font-medium">Colaboradores Ativos</p>
                        <p class="text-3xl font-bold text-[var(--color-main)] mt-2">{{ $metrics['overview']['total_collaborators'] }}</p>
                        <p class="text-blue-500 text-sm mt-1">
                            <i class="fa-solid fa-users mr-1"></i>
                            Base de cálculo
                        </p>
                    </div>
                    <div class="bg-blue-500/10 p-3 rounded-full">
                        <i class="fa-solid fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Taxa de Pontualidade -->
            <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[var(--color-text)] text-sm font-medium">Pontualidade</p>
                        <p class="text-3xl font-bold text-[var(--color-main)] mt-2">{{ $metrics['punctuality']['punctuality_rate'] }}%</p>
                        <p class="text-green-500 text-sm mt-1">
                            <i class="fa-solid fa-check mr-1"></i>
                            {{ $metrics['punctuality']['on_time'] }} de {{ $metrics['punctuality']['on_time'] + $metrics['punctuality']['late'] }} registros
                        </p>
                    </div>
                    <div class="bg-green-500/10 p-3 rounded-full">
                        <i class="fa-solid fa-clock text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Registros Completos -->
            <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[var(--color-text)] text-sm font-medium">Registros Completos</p>
                        <p class="text-3xl font-bold text-[var(--color-main)] mt-2">{{ $metrics['overview']['completion_rate'] }}%</p>
                        <p class="text-purple-500 text-sm mt-1">
                            <i class="fa-solid fa-clipboard-check mr-1"></i>
                            {{ $metrics['overview']['complete_records'] }} de {{ $metrics['overview']['total_records'] }}
                        </p>
                    </div>
                    <div class="bg-purple-500/10 p-3 rounded-full">
                        <i class="fa-solid fa-clipboard-check text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Horas Trabalhadas -->
            <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[var(--color-text)] text-sm font-medium">Total de Horas</p>
                        <p class="text-3xl font-bold text-[var(--color-main)] mt-2">{{ $metrics['overview']['total_worked_hours'] }}h</p>
                        <p class="text-orange-500 text-sm mt-1">
                            <i class="fa-solid fa-business-time mr-1"></i>
                            {{ $metrics['overview']['average_worked_hours'] }}h por dia médio
                        </p>
                    </div>
                    <div class="bg-orange-500/10 p-3 rounded-full">
                        <i class="fa-solid fa-business-time text-orange-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos e Análises -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Gráfico de Presença Diária -->
            <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-[var(--color-main)]">Presença Diária do Mês</h3>
                    <div class="text-sm text-[var(--color-text)]">
                        <i class="fa-solid fa-chart-line mr-1"></i>
                        {{ $metrics['period']['month_name'] }}
                    </div>
                </div>

                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @foreach($metrics['daily_attendance'] as $day)
                    <div class="flex items-center justify-between py-2 {{ $day['is_weekend'] ? 'opacity-60' : '' }}">
                        <div class="flex items-center w-20">
                            <span class="text-xs text-[var(--color-text)]">{{ $day['date']->format('d/m') }}</span>
                            <span class="text-xs text-[var(--color-text)] ml-2">{{ substr($day['day_name'], 0, 3) }}</span>
                        </div>
                        <div class="flex-1 bg-[var(--color-text)]/20 rounded-full h-2 mx-3">
                            <div class="h-2 rounded-full transition-all duration-300 {{ $day['is_weekend'] ? 'bg-[var(--color-text)]/40' : 'bg-blue-500' }}"
                                 style="width: {{ $day['attendance_rate'] }}%"></div>
                        </div>
                        <div class="w-16 text-right">
                            <span class="text-xs text-[var(--color-text)]">{{ $day['attendance_rate'] }}%</span>
                            <span class="text-xs text-[var(--color-text)] block">({{ $day['present_count'] }})</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Registros Recentes -->
            <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-[var(--color-main)]">Registros de Hoje</h3>
                    <a href="{{ route('time-tracking.index') }}" class="text-sm text-blue-600 hover:text-blue-800">Ver todos</a>
                </div>

                <div class="space-y-3">
                    @forelse($metrics['recent_records'] as $record)
                    <div class="flex items-center justify-between p-3 bg-[var(--color-text)]/5 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-500/10 rounded-full flex items-center justify-center mr-3">
                                @if(in_array($record['last_action'], ['Entrada', 'Retorno']))
                                    <i class="fa-solid fa-sign-in-alt text-blue-600 text-xs"></i>
                                @else
                                    <i class="fa-solid fa-sign-out-alt text-blue-600 text-xs"></i>
                                @endif
                            </div>
                            <div>
                                <p class="font-medium text-[var(--color-main)] text-sm">{{ $record['collaborator_name'] }}</p>
                                <p class="text-xs text-[var(--color-text)]">{{ $record['last_action'] }} - {{ $record['time'] }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs px-2 py-1 rounded-full
                                @if($record['status'] == 'completo') bg-green-500/20 text-green-700 dark:text-green-400
                                @elseif($record['status'] == 'incompleto') bg-yellow-500/20 text-yellow-700 dark:text-yellow-400
                                @else bg-red-500/20 text-red-700 dark:text-red-400 @endif">
                                {{ ucfirst($record['status']) }}
                            </span>
                            <p class="text-xs text-[var(--color-text)] mt-1">{{ $record['total_hours'] }}h</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-[var(--color-text)]">
                        <i class="fa-solid fa-clock text-2xl mb-2"></i>
                        <p>Nenhum registro encontrado para hoje</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Análises Detalhadas -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Top Performers -->
            <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-[var(--color-main)]">Top Performers</h3>
                    <i class="fa-solid fa-trophy text-yellow-500"></i>
                </div>

                <div class="space-y-4">
                    @forelse($metrics['top_performers'] as $index => $performer)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center mr-3
                                @if($index == 0) bg-yellow-500/10 text-yellow-600
                                @elseif($index == 1) bg-[var(--color-text)]/10 text-[var(--color-text)]
                                @elseif($index == 2) bg-orange-500/10 text-orange-600
                                @else bg-blue-500/10 text-blue-600 @endif">
                                {{ $index + 1 }}º
                            </div>
                            <div>
                                <p class="font-medium text-[var(--color-main)] text-sm">{{ $performer['name'] }}</p>
                                <p class="text-xs text-[var(--color-text)]">{{ $performer['completion_rate'] }}% completo</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-[var(--color-main)]">{{ $performer['total_hours'] }}h</p>
                            <p class="text-xs text-[var(--color-text)]">{{ $performer['total_days'] }} dias</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-[var(--color-text)]">
                        <p class="text-sm">Nenhum registro no período</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Solicitações -->
            <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-[var(--color-main)]">Solicitações</h3>
                    <a href="{{ route('solicitation.index') }}" class="text-sm text-blue-600 hover:text-blue-800">Ver todas</a>
                </div>

                <div class="space-y-4">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-[var(--color-main)]">{{ $metrics['solicitations_summary']['total'] }}</p>
                        <p class="text-sm text-[var(--color-text)]">Total no período</p>
                    </div>

                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="bg-yellow-500/10 p-3 rounded-lg">
                            <p class="text-lg font-semibold text-yellow-700 dark:text-yellow-400">{{ $metrics['solicitations_summary']['pending'] }}</p>
                            <p class="text-xs text-yellow-600 dark:text-yellow-500">Pendentes</p>
                        </div>
                        <div class="bg-green-500/10 p-3 rounded-lg">
                            <p class="text-lg font-semibold text-green-700 dark:text-green-400">{{ $metrics['solicitations_summary']['approved'] }}</p>
                            <p class="text-xs text-green-600 dark:text-green-500">Aprovadas</p>
                        </div>
                        <div class="bg-red-500/10 p-3 rounded-lg">
                            <p class="text-lg font-semibold text-red-700 dark:text-red-400">{{ $metrics['solicitations_summary']['rejected'] }}</p>
                            <p class="text-xs text-red-600 dark:text-red-500">Rejeitadas</p>
                        </div>
                    </div>

                    <div class="text-center pt-2 border-t border-[var(--color-text)]/10">
                        <p class="text-sm text-[var(--color-text)]">Taxa de Aprovação</p>
                        <p class="text-lg font-semibold text-[var(--color-main)]">{{ $metrics['solicitations_summary']['approval_rate'] }}%</p>
                    </div>
                </div>
            </div>

            <!-- Alertas -->
            <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-[var(--color-main)]">Alertas</h3>
                    <i class="fa-solid fa-exclamation-triangle text-yellow-500"></i>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center p-3 bg-yellow-500/10 border border-yellow-500/20 rounded-lg">
                        <i class="fa-solid fa-clock text-yellow-600 mr-3"></i>
                        <div>
                            <p class="font-medium text-yellow-800 dark:text-yellow-400">{{ $metrics['alerts']['frequent_late'] }}</p>
                            <p class="text-xs text-yellow-600 dark:text-yellow-500">Atrasos frequentes (5+)</p>
                        </div>
                    </div>

                    <div class="flex items-center p-3 bg-red-500/10 border border-red-500/20 rounded-lg">
                        <i class="fa-solid fa-user-times text-red-600 mr-3"></i>
                        <div>
                            <p class="font-medium text-red-800 dark:text-red-400">{{ $metrics['alerts']['frequent_absent'] }}</p>
                            <p class="text-xs text-red-600 dark:text-red-500">Ausências frequentes (3+)</p>
                        </div>
                    </div>

                    <div class="flex items-center p-3 bg-blue-500/10 border border-blue-500/20 rounded-lg">
                        <i class="fa-solid fa-file-alt text-blue-600 mr-3"></i>
                        <div>
                            <p class="font-medium text-blue-800 dark:text-blue-400">{{ $metrics['alerts']['pending_solicitations'] }}</p>
                            <p class="text-xs text-blue-600 dark:text-blue-500">Solicitações pendentes</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumo de Pontualidade -->
        <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
            <h3 class="text-xl font-semibold text-[var(--color-main)] mb-6">Análise de Pontualidade</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-center">
                <div>
                    <p class="text-2xl font-bold text-green-600">{{ $metrics['punctuality']['on_time'] }}</p>
                    <p class="text-sm text-[var(--color-text)]">Registros no horário</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-red-600">{{ $metrics['punctuality']['late'] }}</p>
                    <p class="text-sm text-[var(--color-text)]">Registros em atraso</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[var(--color-main)]">{{ $metrics['punctuality']['average_late_minutes'] }} min</p>
                    <p class="text-sm text-[var(--color-text)]">Atraso médio</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[var(--color-main)]">{{ round($metrics['punctuality']['total_late_minutes'] / 60, 1) }}h</p>
                    <p class="text-sm text-[var(--color-text)]">Total de atrasos</p>
                </div>
            </div>
        </div>
    </div>
@endsection


