@extends('layouts.layout')
@section('title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        <!-- Header do Dashboard -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-[var(--color-main)]">Dashboard</h1>
                <p class="text-[var(--color-text)] mt-2">Visão geral do sistema de controle de ponto</p>
            </div>
            <div class="text-sm text-[var(--color-text)]">
                <i class="fa-solid fa-calendar mr-2"></i>
                {{ \Carbon\Carbon::now()->format('d/m/Y - H:i') }}
            </div>
        </div>

        <!-- Cards de Estatísticas Principais -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total de Funcionários -->
            <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-gray-200/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[var(--color-text)] text-sm font-medium">Total de Funcionários</p>
                        <p class="text-3xl font-bold text-[var(--color-main)] mt-2">127</p>
                        <p class="text-green-500 text-sm mt-1">
                            <i class="fa-solid fa-arrow-up mr-1"></i>
                            +5 este mês
                        </p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fa-solid fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Presentes Hoje -->
            <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-gray-200/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[var(--color-text)] text-sm font-medium">Presentes Hoje</p>
                        <p class="text-3xl font-bold text-[var(--color-main)] mt-2">98</p>
                        <p class="text-green-500 text-sm mt-1">
                            <i class="fa-solid fa-check mr-1"></i>
                            77% presença
                        </p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fa-solid fa-user-check text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Faltas Hoje -->
            <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-gray-200/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[var(--color-text)] text-sm font-medium">Faltas Hoje</p>
                        <p class="text-3xl font-bold text-[var(--color-main)] mt-2">8</p>
                        <p class="text-red-500 text-sm mt-1">
                            <i class="fa-solid fa-user-times mr-1"></i>
                            6% ausência
                        </p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <i class="fa-solid fa-user-times text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Horas Extras -->
            <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-gray-200/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[var(--color-text)] text-sm font-medium">Horas Extras (Mês)</p>
                        <p class="text-3xl font-bold text-[var(--color-main)] mt-2">248</p>
                        <p class="text-yellow-500 text-sm mt-1">
                            <i class="fa-solid fa-clock mr-1"></i>
                            +12h esta semana
                        </p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fa-solid fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seção Principal - Gráficos e Tabelas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Gráfico de Presença Semanal -->
            <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-gray-200/20">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-[var(--color-main)]">Presença Semanal</h3>
                    <select class="text-sm border border-gray-300 rounded-lg px-3 py-1 text-[var(--color-text)]">
                        <option>Última semana</option>
                        <option>Últimas 2 semanas</option>
                        <option>Último mês</option>
                    </select>
                </div>

                <!-- Simulação de gráfico com barras CSS -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-[var(--color-text)] w-12">Seg</span>
                        <div class="flex-1 bg-gray-200 rounded-full h-3 mx-3">
                            <div class="bg-blue-500 h-3 rounded-full" style="width: 85%"></div>
                        </div>
                        <span class="text-sm text-[var(--color-text)] w-8">85%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-[var(--color-text)] w-12">Ter</span>
                        <div class="flex-1 bg-gray-200 rounded-full h-3 mx-3">
                            <div class="bg-blue-500 h-3 rounded-full" style="width: 92%"></div>
                        </div>
                        <span class="text-sm text-[var(--color-text)] w-8">92%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-[var(--color-text)] w-12">Qua</span>
                        <div class="flex-1 bg-gray-200 rounded-full h-3 mx-3">
                            <div class="bg-blue-500 h-3 rounded-full" style="width: 78%"></div>
                        </div>
                        <span class="text-sm text-[var(--color-text)] w-8">78%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-[var(--color-text)] w-12">Qui</span>
                        <div class="flex-1 bg-gray-200 rounded-full h-3 mx-3">
                            <div class="bg-blue-500 h-3 rounded-full" style="width: 88%"></div>
                        </div>
                        <span class="text-sm text-[var(--color-text)] w-8">88%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-[var(--color-text)] w-12">Sex</span>
                        <div class="flex-1 bg-gray-200 rounded-full h-3 mx-3">
                            <div class="bg-blue-500 h-3 rounded-full" style="width: 95%"></div>
                        </div>
                        <span class="text-sm text-[var(--color-text)] w-8">95%</span>
                    </div>
                </div>
            </div>

            <!-- Funcionários Recentes -->
            <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-gray-200/20">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-[var(--color-main)]">Registros Recentes</h3>
                    <a href="#" class="text-sm text-blue-600 hover:text-blue-800">Ver todos</a>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fa-solid fa-sign-in-alt text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-[var(--color-main)]">João Silva</p>
                                <p class="text-sm text-[var(--color-text)]">Entrada - 08:15</p>
                            </div>
                        </div>
                        <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full">No horário</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fa-solid fa-sign-out-alt text-red-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-[var(--color-main)]">Maria Santos</p>
                                <p class="text-sm text-[var(--color-text)]">Saída - 18:22</p>
                            </div>
                        </div>
                        <span class="text-xs text-orange-600 bg-orange-100 px-2 py-1 rounded-full">Hora extra</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fa-solid fa-sign-in-alt text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-[var(--color-main)]">Carlos Oliveira</p>
                                <p class="text-sm text-[var(--color-text)]">Entrada - 08:45</p>
                            </div>
                        </div>
                        <span class="text-xs text-red-600 bg-red-100 px-2 py-1 rounded-full">Atraso</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fa-solid fa-sign-in-alt text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-[var(--color-main)]">Ana Costa</p>
                                <p class="text-sm text-[var(--color-text)]">Entrada - 08:00</p>
                            </div>
                        </div>
                        <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full">No horário</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seção de Departamentos e Ações Rápidas -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Departamentos -->
            <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-gray-200/20">
                <h3 class="text-xl font-semibold text-[var(--color-main)] mb-4">Departamentos</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fa-solid fa-building text-blue-600 mr-3"></i>
                            <span class="text-[var(--color-text)]">TI</span>
                        </div>
                        <span class="text-sm font-medium text-[var(--color-main)]">15 funcionários</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fa-solid fa-chart-line text-green-600 mr-3"></i>
                            <span class="text-[var(--color-text)]">Vendas</span>
                        </div>
                        <span class="text-sm font-medium text-[var(--color-main)]">28 funcionários</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fa-solid fa-users text-purple-600 mr-3"></i>
                            <span class="text-[var(--color-text)]">RH</span>
                        </div>
                        <span class="text-sm font-medium text-[var(--color-main)]">8 funcionários</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fa-solid fa-calculator text-orange-600 mr-3"></i>
                            <span class="text-[var(--color-text)]">Financeiro</span>
                        </div>
                        <span class="text-sm font-medium text-[var(--color-main)]">12 funcionários</span>
                    </div>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="lg:col-span-2 bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-gray-200/20">
                <h3 class="text-xl font-semibold text-[var(--color-main)] mb-4">Ações Rápidas</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <button class="flex flex-col items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors duration-200 group">
                        <div class="bg-blue-500 p-3 rounded-full mb-2 group-hover:bg-blue-600 transition-colors duration-200">
                            <i class="fa-solid fa-user-plus text-white"></i>
                        </div>
                        <span class="text-sm font-medium text-[var(--color-text)]">Novo Funcionário</span>
                    </button>

                    <button class="flex flex-col items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition-colors duration-200 group">
                        <div class="bg-green-500 p-3 rounded-full mb-2 group-hover:bg-green-600 transition-colors duration-200">
                            <i class="fa-solid fa-clock text-white"></i>
                        </div>
                        <span class="text-sm font-medium text-[var(--color-text)]">Registrar Ponto</span>
                    </button>

                    <button class="flex flex-col items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors duration-200 group">
                        <div class="bg-purple-500 p-3 rounded-full mb-2 group-hover:bg-purple-600 transition-colors duration-200">
                            <i class="fa-solid fa-file-invoice text-white"></i>
                        </div>
                        <span class="text-sm font-medium text-[var(--color-text)]">Gerar Relatório</span>
                    </button>

                    <button class="flex flex-col items-center p-4 bg-orange-50 hover:bg-orange-100 rounded-lg transition-colors duration-200 group">
                        <div class="bg-orange-500 p-3 rounded-full mb-2 group-hover:bg-orange-600 transition-colors duration-200">
                            <i class="fa-solid fa-calendar-alt text-white"></i>
                        </div>
                        <span class="text-sm font-medium text-[var(--color-text)]">Folha de Ponto</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Alertas e Notificações -->
        <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-gray-200/20">
            <h3 class="text-xl font-semibold text-[var(--color-main)] mb-4">Alertas e Notificações</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <i class="fa-solid fa-exclamation-triangle text-yellow-600 mr-3"></i>
                    <div>
                        <p class="font-medium text-yellow-800">5 funcionários</p>
                        <p class="text-sm text-yellow-600">com mais de 3 atrasos</p>
                    </div>
                </div>

                <div class="flex items-center p-4 bg-red-50 border border-red-200 rounded-lg">
                    <i class="fa-solid fa-calendar-times text-red-600 mr-3"></i>
                    <div>
                        <p class="font-medium text-red-800">2 funcionários</p>
                        <p class="text-sm text-red-600">faltaram mais de 3 dias</p>
                    </div>
                </div>

                <div class="flex items-center p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <i class="fa-solid fa-clock text-blue-600 mr-3"></i>
                    <div>
                        <p class="font-medium text-blue-800">12 solicitações</p>
                        <p class="text-sm text-blue-600">de horas extras pendentes</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


