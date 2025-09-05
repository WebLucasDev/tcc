<!-- Tabela de Solicitações -->
<div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
    <!-- Cabeçalho da Tabela -->
    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-[var(--color-background)]">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-[var(--color-main)] to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-clipboard-list text-white"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-[var(--color-text)]">
                        Solicitações de Ajuste
                    </h3>
                    <p id="results-summary" class="text-sm text-[var(--color-text)] opacity-60">
                        {{ $solicitations->total() }} resultado{{ $solicitations->total() != 1 ? 's' : '' }} encontrado{{ $solicitations->total() != 1 ? 's' : '' }}
                    </p>
                </div>
            </div>

            <!-- Controles de Ordenação -->
            <div class="flex items-center gap-3 bg-[var(--color-background)] border border-gray-200 dark:border-gray-600 rounded-xl p-3 shadow-sm">
                <span class="text-sm text-[var(--color-text)] opacity-70 font-medium">Ordenar:</span>
                <select
                    name="sort_by"
                    class="px-3 py-2 text-sm border-0 bg-[var(--color-background)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-main)] rounded-lg shadow-sm">
                    <option value="created_at" {{ request('sort_by', 'created_at') == 'created_at' ? 'selected' : '' }}>Data de Criação</option>
                    <option value="colaborator_name" {{ request('sort_by') == 'colaborator_name' ? 'selected' : '' }}>Nome do Colaborador</option>
                    <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
                </select>

                <button
                    type="button"
                    name="sort_direction"
                    value="{{ request('sort_direction', 'desc') }}"
                    class="p-2 text-sm text-[var(--color-main)] hover:text-red-700 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all duration-200 shadow-sm">
                    <i class="fa-solid fa-sort-{{ request('sort_direction', 'desc') == 'asc' ? 'up' : 'down' }}"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Conteúdo da Tabela -->
    @if($solicitations->count() > 0)
        <!-- Cards de Solicitações -->
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($solicitations as $solicitation)
                <div class="transition-all duration-300">
                    <!-- Cabeçalho da Solicitação (sempre visível) -->
                    <div class="p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all duration-300 select-none"
                         onclick="toggleSolicitationDetails({{ $solicitation->id }})">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            <!-- Informações principais -->
                            <div class="flex items-center gap-4 flex-1">
                                <!-- Avatar e seta de expansão -->
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <div class="w-12 h-12 bg-gradient-to-br from-[var(--color-main)] to-red-600 text-white rounded-xl flex items-center justify-center font-bold text-sm shadow-lg">
                                            {{ strtoupper(substr($solicitation->colaborator->name, 0, 2)) }}
                                        </div>
                                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-white dark:bg-gray-900 rounded-full flex items-center justify-center border-2 border-gray-200 dark:border-gray-600 shadow-sm">
                                            <div class="w-1.5 h-1.5 bg-green-500 rounded-full"></div>
                                        </div>
                                    </div>
                                    <!-- Seta de expansão -->
                                    <div class="text-[var(--color-text)] opacity-50 transition-all duration-300 hover:opacity-80"
                                         id="arrow-{{ $solicitation->id }}">
                                        <i class="fa-solid fa-chevron-right text-sm"></i>
                                    </div>
                                </div>

                                <!-- Informações do colaborador -->
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-base font-bold text-[var(--color-text)] hover:text-[var(--color-main)] transition-colors duration-300">
                                        {{ $solicitation->colaborator->name }}
                                    </h4>
                                    <div class="flex items-center gap-3 mt-1">
                                        <span class="text-xs text-[var(--color-text)] opacity-60">{{ $solicitation->colaborator->email }}</span>
                                        <span class="text-xs text-[var(--color-text)] opacity-50 bg-[var(--color-background)] border border-gray-200 dark:border-gray-600 px-2 py-1 rounded-lg">
                                            {{ $solicitation->created_at->format('d/m/Y H:i') }}
                                        </span>
                                        <span class="text-xs text-[var(--color-text)] opacity-40 font-medium">
                                            <i class="fa-solid fa-hand-pointer mr-1"></i>Clique para ver detalhes
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Status e Ações -->
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                <!-- Status Badge -->
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold {{ $solicitation->status->color() }} shadow-md">
                                    <i class="{{ $solicitation->status->icon() }} mr-1.5"></i>
                                    {{ $solicitation->status->label() }}
                                </span>

                                <!-- Botões de Ação -->
                                @if($solicitation->status->value === 'pending')
                                    <div class="flex gap-2" onclick="event.stopPropagation();">
                                        <button type="button"
                                                class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-semibold shadow-md hover:shadow-lg transition-all duration-300"
                                                onclick="openActionModal('approve', {{ $solicitation->id }}, '{{ addslashes($solicitation->colaborator->name) }}')">
                                            <i class="fa-solid fa-check mr-1"></i>Aprovar
                                        </button>
                                        <button type="button"
                                                class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-semibold shadow-md hover:shadow-lg transition-all duration-300"
                                                onclick="openActionModal('reject', {{ $solicitation->id }}, '{{ addslashes($solicitation->colaborator->name) }}')">
                                            <i class="fa-solid fa-times mr-1"></i>Rejeitar
                                        </button>
                                        <button type="button"
                                                class="px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-xs font-semibold shadow-md hover:shadow-lg transition-all duration-300"
                                                onclick="openActionModal('cancel', {{ $solicitation->id }}, '{{ addslashes($solicitation->colaborator->name) }}')">
                                            <i class="fa-solid fa-ban mr-1"></i>Cancelar
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Detalhes da Solicitação (expansível) -->
                    <div class="hidden bg-gray-50 dark:bg-gray-800/30 border-t border-gray-200 dark:border-gray-700"
                         id="details-{{ $solicitation->id }}">
                        <div class="p-4 space-y-4">
                            <!-- Motivo -->
                            <div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
                                <div class="flex items-start gap-3">
                                    <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-message text-white text-xs"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h5 class="text-sm font-bold text-[var(--color-text)] mb-2">Motivo da Solicitação</h5>
                                        <p class="text-sm text-[var(--color-text)] opacity-80 leading-relaxed bg-[var(--color-background)] p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                                            {{ $solicitation->reason ?? 'Não informado' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Comparação de Horários -->
                            @if($solicitation->old_time_start || $solicitation->old_time_finish || $solicitation->new_time_start || $solicitation->new_time_finish)
                                <div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="w-6 h-6 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-clock text-white text-xs"></i>
                                        </div>
                                        <h5 class="text-sm font-bold text-[var(--color-text)]">Comparação de Horários</h5>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <!-- Horários Anteriores -->
                                        <div class="bg-[var(--color-background)] border-2 border-red-200 dark:border-red-800 rounded-lg overflow-hidden">
                                            <div class="bg-gradient-to-r from-red-500 to-red-600 text-white px-3 py-2">
                                                <div class="flex items-center gap-2">
                                                    <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                                                    <span class="font-bold text-xs">Horários Anteriores</span>
                                                </div>
                                            </div>
                                            <div class="p-3 bg-red-50 dark:bg-red-900/20">
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div class="text-center bg-[var(--color-background)] p-2 rounded border border-red-200 dark:border-red-700">
                                                        <div class="text-xs font-semibold text-[var(--color-text)] opacity-60 mb-1">Início</div>
                                                        <div class="text-sm font-bold text-[var(--color-text)]">
                                                            {{ $solicitation->old_time_start ? $solicitation->old_time_start->format('H:i') : '-' }}
                                                        </div>
                                                    </div>
                                                    <div class="text-center bg-[var(--color-background)] p-2 rounded border border-red-200 dark:border-red-700">
                                                        <div class="text-xs font-semibold text-[var(--color-text)] opacity-60 mb-1">Fim</div>
                                                        <div class="text-sm font-bold text-[var(--color-text)]">
                                                            {{ $solicitation->old_time_finish ? $solicitation->old_time_finish->format('H:i') : '-' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Horários Propostos -->
                                        <div class="bg-[var(--color-background)] border-2 border-green-200 dark:border-green-800 rounded-lg overflow-hidden">
                                            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white px-3 py-2">
                                                <div class="flex items-center gap-2">
                                                    <i class="fa-solid fa-clock text-xs"></i>
                                                    <span class="font-bold text-xs">Horários Propostos</span>
                                                </div>
                                            </div>
                                            <div class="p-3 bg-green-50 dark:bg-green-900/20">
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div class="text-center bg-[var(--color-background)] p-2 rounded border border-green-200 dark:border-green-700">
                                                        <div class="text-xs font-semibold text-[var(--color-text)] opacity-60 mb-1">Início</div>
                                                        <div class="text-sm font-bold text-[var(--color-text)]">
                                                            {{ $solicitation->new_time_start ? $solicitation->new_time_start->format('H:i') : '-' }}
                                                        </div>
                                                    </div>
                                                    <div class="text-center bg-[var(--color-background)] p-2 rounded border border-green-200 dark:border-green-700">
                                                        <div class="text-xs font-semibold text-[var(--color-text)] opacity-60 mb-1">Fim</div>
                                                        <div class="text-sm font-bold text-[var(--color-text)]">
                                                            {{ $solicitation->new_time_finish ? $solicitation->new_time_finish->format('H:i') : '-' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Comentário do Administrador -->
                            @if($solicitation->admin_comment)
                                <div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
                                    <div class="flex items-start gap-3">
                                        <div class="w-6 h-6 bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-user-tie text-white text-xs"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="text-sm font-bold text-[var(--color-text)] mb-2">Comentário do Administrador</h5>
                                            <p class="text-sm text-[var(--color-text)] opacity-80 leading-relaxed bg-[var(--color-background)] p-3 rounded-lg border border-gray-200 dark:border-gray-600">{{ $solicitation->admin_comment }}</p>
                                        </div>
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
                <i class="fa-solid fa-file-contract text-5xl text-gray-500 dark:text-gray-400"></i>
            </div>
            <h3 class="text-xl font-bold text-[var(--color-text)] mb-3">Nenhuma solicitação encontrada</h3>
            <p class="text-[var(--color-text)] opacity-60 max-w-md mx-auto leading-relaxed">
                @if(request()->anyFilled(['search', 'status']))
                    Não foram encontradas solicitações que correspondam aos filtros aplicados.
                    Tente ajustar os critérios de busca para encontrar o que procura.
                @else
                    Ainda não há solicitações de ajuste de ponto cadastradas no sistema.
                    As solicitações aparecerão aqui quando forem criadas pelos funcionários.
                @endif
            </p>
            @if(request()->anyFilled(['search', 'status']))
                <div class="mt-6">
                    <a  href="{{ route('solicitation.index') }}"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[var(--color-main)] to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <i class="fa-solid fa-refresh mr-2"></i>
                        Ver Todas as Solicitações
                    </a>
                </div>
            @endif
        </div>
    @endif
</div>
