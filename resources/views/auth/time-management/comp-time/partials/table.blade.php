<div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
    <!-- Cabeçalho da Tabela -->
    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-[var(--color-background)]">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-[var(--color-main)] to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fa-solid fa-clock text-white"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-[var(--color-text)]">
                    Banco de Horas por Colaborador
                </h3>
                <p class="text-sm text-[var(--color-text)] opacity-60">
                    {{ count($compTimeData) }} colaborador{{ count($compTimeData) != 1 ? 'es' : '' }} analisado{{ count($compTimeData) != 1 ? 's' : '' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Conteúdo da Tabela -->
    @if(count($compTimeData) > 0)
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($compTimeData as $data)
                @php
                    $collaborator = $data['collaborator'];
                    $bankHours = $data['bank_hours'];
                    $balance = $bankHours['bank_balance_minutes'];
                @endphp

                <div class="transition-all duration-300">
                    <!-- Cabeçalho do Colaborador (sempre visível) -->
                    <div class="p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all duration-300 select-none"
                         onclick="toggleCollaboratorDetails({{ $collaborator->id }})">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            <!-- Informações principais -->
                            <div class="flex items-center gap-4 flex-1">
                                <!-- Avatar e seta de expansão -->
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <div class="w-12 h-12 bg-gradient-to-br from-[var(--color-main)] to-red-600 text-white rounded-xl flex items-center justify-center font-bold text-sm shadow-lg">
                                            {{ strtoupper(substr($collaborator->name, 0, 2)) }}
                                        </div>
                                        <!-- Indicador de saldo -->
                                        <div class="absolute -bottom-1 -right-1 w-5 h-5 {{ $balance >= 0 ? 'bg-green-500' : 'bg-red-500' }} rounded-full flex items-center justify-center border-2 border-white dark:border-gray-900 shadow-sm">
                                            <i class="fa-solid fa-{{ $balance >= 0 ? 'plus' : 'minus' }} text-white text-xs"></i>
                                        </div>
                                    </div>
                                    <!-- Seta de expansão -->
                                    <div class="text-[var(--color-text)] opacity-50 transition-all duration-300 hover:opacity-80"
                                         id="arrow-{{ $collaborator->id }}">
                                        <i class="fa-solid fa-chevron-right text-sm"></i>
                                    </div>
                                </div>

                                <!-- Informações do colaborador -->
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-base font-bold text-[var(--color-text)] hover:text-[var(--color-main)] transition-colors duration-300">
                                        {{ $collaborator->name }}
                                    </h4>
                                    <div class="flex items-center gap-3 mt-1">
                                        <span class="text-xs text-[var(--color-text)] opacity-60">{{ $collaborator->email }}</span>
                                        <span class="text-xs text-[var(--color-text)] opacity-50 bg-[var(--color-background)] border border-gray-200 dark:border-gray-600 px-2 py-1 rounded-lg">
                                            {{ $collaborator->position->name ?? 'Sem cargo' }}
                                        </span>
                                        <span class="text-xs text-[var(--color-text)] opacity-40 font-medium">
                                            <i class="fa-solid fa-hand-pointer mr-1"></i>Clique para ver detalhes
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Saldo do Banco de Horas -->
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                <!-- Estatísticas rápidas -->
                                <div class="flex gap-4 text-sm">
                                    <div class="text-center">
                                        <p class="text-[var(--color-text)] opacity-60 text-xs">Trabalhadas</p>
                                        <p class="font-bold text-[var(--color-text)]">
                                            {{ sprintf('%02d:%02d', intval($bankHours['total_worked_minutes'] / 60), $bankHours['total_worked_minutes'] % 60) }}
                                        </p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-[var(--color-text)] opacity-60 text-xs">Esperadas</p>
                                        <p class="font-bold text-[var(--color-text)]">
                                            {{ sprintf('%02d:%02d', intval($bankHours['total_standard_minutes'] / 60), $bankHours['total_standard_minutes'] % 60) }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Saldo Badge -->
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold {{ $balance >= 0 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }} shadow-md">
                                    <i class="fa-solid fa-{{ $balance >= 0 ? 'plus' : 'minus' }} mr-2"></i>
                                    @if($balance < 0)
                                        -{{ sprintf('%02d:%02d', intval(abs($balance) / 60), abs($balance) % 60) }}
                                    @else
                                        {{ sprintf('%02d:%02d', intval($balance / 60), $balance % 60) }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Detalhes do Colaborador (expansível) -->
                    <div class="hidden bg-gray-50 dark:bg-gray-800/30 border-t border-gray-200 dark:border-gray-700"
                         id="details-{{ $collaborator->id }}">
                        <div class="p-6 space-y-6">

                            <!-- Horário Padrão -->
                            <div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-clock text-white text-xs"></i>
                                    </div>
                                    <h5 class="text-sm font-bold text-[var(--color-text)]">Horário Padrão de Trabalho</h5>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    <div class="text-center bg-[var(--color-background)] p-3 rounded border border-gray-200 dark:border-gray-600">
                                        <div class="text-xs font-semibold text-[var(--color-text)] opacity-60 mb-1">Entrada Manhã</div>
                                        <div class="text-sm font-bold text-[var(--color-text)]">
                                            {{ $collaborator->entry_time_1 ? \Carbon\Carbon::parse($collaborator->entry_time_1)->format('H:i') : '-' }}
                                        </div>
                                    </div>
                                    <div class="text-center bg-[var(--color-background)] p-3 rounded border border-gray-200 dark:border-gray-600">
                                        <div class="text-xs font-semibold text-[var(--color-text)] opacity-60 mb-1">Saída Almoço</div>
                                        <div class="text-sm font-bold text-[var(--color-text)]">
                                            {{ $collaborator->return_time_1 ? \Carbon\Carbon::parse($collaborator->return_time_1)->format('H:i') : '-' }}
                                        </div>
                                    </div>
                                    <div class="text-center bg-[var(--color-background)] p-3 rounded border border-gray-200 dark:border-gray-600">
                                        <div class="text-xs font-semibold text-[var(--color-text)] opacity-60 mb-1">Volta Almoço</div>
                                        <div class="text-sm font-bold text-[var(--color-text)]">
                                            {{ $collaborator->entry_time_2 ? \Carbon\Carbon::parse($collaborator->entry_time_2)->format('H:i') : '-' }}
                                        </div>
                                    </div>
                                    <div class="text-center bg-[var(--color-background)] p-3 rounded border border-gray-200 dark:border-gray-600">
                                        <div class="text-xs font-semibold text-[var(--color-text)] opacity-60 mb-1">Saída Tarde</div>
                                        <div class="text-sm font-bold text-[var(--color-text)]">
                                            {{ $collaborator->return_time_2 ? \Carbon\Carbon::parse($collaborator->return_time_2)->format('H:i') : '-' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3 text-center">
                                    <span class="text-sm text-[var(--color-text)] opacity-70">
                                        Carga horária diária:
                                        <strong>{{ sprintf('%02d:%02d', intval($bankHours['standard_daily_minutes'] / 60), $bankHours['standard_daily_minutes'] % 60) }}</strong>
                                    </span>
                                </div>
                            </div>

                            <!-- Resumo do Período -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-calendar-days text-white"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-blue-800 dark:text-blue-200">Dias Úteis</p>
                                            <p class="text-xl font-bold text-blue-900 dark:text-blue-100">{{ $bankHours['work_days_count'] }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-clock text-white"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-green-800 dark:text-green-200">Horas Trabalhadas</p>
                                            <p class="text-xl font-bold text-green-900 dark:text-green-100">
                                                {{ sprintf('%02d:%02d', intval($bankHours['total_worked_minutes'] / 60), $bankHours['total_worked_minutes'] % 60) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-clock text-white"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-purple-800 dark:text-purple-200">Horas Esperadas</p>
                                            <p class="text-xl font-bold text-purple-900 dark:text-purple-100">
                                                {{ sprintf('%02d:%02d', intval($bankHours['total_standard_minutes'] / 60), $bankHours['total_standard_minutes'] % 60) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Histórico Diário (últimos 10 dias com registro) -->
                            @if(count($bankHours['work_days']) > 0)
                                <div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
                                    <div class="flex items-center gap-3 mb-4">
                                        <div class="w-6 h-6 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-list text-white text-xs"></i>
                                        </div>
                                        <h5 class="text-sm font-bold text-[var(--color-text)]">Registros Diários do Período ({{ count($bankHours['work_days']) }} dias)</h5>
                                    </div>

                                    <div class="space-y-2 max-h-64 overflow-y-auto">
                                        @foreach($bankHours['work_days'] as $day)
                                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 {{ $day['difference_minutes'] >= 0 ? 'bg-green-100 text-green-600' : ($day['tracking'] ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600') }} rounded-lg flex items-center justify-center">
                                                        <i class="fa-solid fa-{{ $day['tracking'] ? ($day['difference_minutes'] >= 0 ? 'plus' : 'minus') : 'ban' }} text-xs"></i>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-[var(--color-text)]">
                                                            {{ $day['date']->format('d/m/Y') }} - {{ $day['date']->locale('pt_BR')->dayName }}
                                                        </p>
                                                        <p class="text-xs text-[var(--color-text)] opacity-60">
                                                            @if($day['tracking'])
                                                                {{ sprintf('%02d:%02d', intval($day['worked_minutes'] / 60), $day['worked_minutes'] % 60) }} trabalhadas
                                                            @else
                                                                Ausente
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-sm font-bold {{ $day['difference_minutes'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                        @if($day['difference_minutes'] < 0)
                                                            -{{ sprintf('%02d:%02d', intval(abs($day['difference_minutes']) / 60), abs($day['difference_minutes']) % 60) }}
                                                        @else
                                                            +{{ sprintf('%02d:%02d', intval($day['difference_minutes'] / 60), $day['difference_minutes'] % 60) }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Estado Vazio -->
        <div class="text-center py-16 px-6">
            <div class="w-32 h-32 mx-auto mb-6 bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 dark:from-gray-700 dark:via-gray-800 dark:to-gray-900 rounded-2xl flex items-center justify-center shadow-lg border-2 border-gray-200 dark:border-gray-600">
                <i class="fa-solid fa-clock text-5xl text-gray-500 dark:text-gray-400"></i>
            </div>
            <h3 class="text-xl font-bold text-[var(--color-text)] mb-3">Nenhum dado encontrado</h3>
            <p class="text-[var(--color-text)] opacity-60 max-w-md mx-auto leading-relaxed">
                Não foram encontrados dados de banco de horas para os critérios selecionados.
                Verifique se há colaboradores ativos e registros de ponto no período selecionado.
            </p>
        </div>
    @endif
</div>
