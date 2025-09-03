<!-- Tabela de Registros de Ponto -->
<div id="time-tracking-container" class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
    <!-- Cabeçalho da Tabela -->
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h3 class="text-lg font-semibold text-[var(--color-text)]">
                Histórico de Pontos
                <span id="results-summary" class="text-sm font-normal opacity-70" style="display: none;">
                    ({{ $timeTrackings->total() }} resultado{{ $timeTrackings->total() != 1 ? 's' : '' }} encontrado{{ $timeTrackings->total() != 1 ? 's' : '' }})
                </span>
            </h3>

            <!-- Ordenação -->
            <div class="flex items-center gap-2">
                <span class="text-sm text-[var(--color-text)] opacity-70">Ordenar por:</span>
                <div class="flex gap-2">
                    <select
                        name="sort_by"
                        class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded bg-[var(--color-background)] text-[var(--color-text)]">
                        <option value="date" {{ request('sort_by', 'date') == 'date' ? 'selected' : '' }}>Data</option>
                        <option value="collaborator" {{ request('sort_by') == 'collaborator' ? 'selected' : '' }}>Colaborador</option>
                    </select>

                    <button
                        type="button"
                        name="sort_direction"
                        value="{{ request('sort_direction', 'desc') }}"
                        class="px-2 py-1 text-sm text-[var(--color-main)] hover:text-[var(--color-main-dark)] transition-colors">
                        <i class="fa-solid fa-sort-{{ request('sort_direction', 'desc') == 'asc' ? 'up' : 'down' }}"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Conteúdo da Tabela -->
    @if($timeTrackings->count() > 0)
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Data
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Colaborador
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Entrada
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Saída Almoço
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Volta Almoço
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Saída
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($timeTrackings as $tracking)
                        <tr class="group hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-[var(--color-text)] group-hover:text-gray-900 dark:group-hover:text-gray-100">
                                    {{ \Carbon\Carbon::parse($tracking->date)->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-[var(--color-text)] group-hover:text-gray-900 dark:group-hover:text-gray-100">
                                        {{ $tracking->collaborator->name }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--color-text)] group-hover:text-gray-900 dark:group-hover:text-gray-100">
                                <div class="flex items-center gap-2">
                                    <span>{{ $tracking->entry_time_1 ? \Carbon\Carbon::parse($tracking->entry_time_1)->format('H:i') : '-' }}</span>
                                    @if($tracking->entry_time_1_observation)
                                        <div class="relative group/tooltip">
                                            <i class="fa-solid fa-info-circle text-blue-500 text-xs cursor-help"></i>
                                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 text-white text-xs rounded whitespace-nowrap opacity-0 group-hover/tooltip:opacity-100 transition-opacity duration-200 pointer-events-none z-10">
                                                {{ $tracking->entry_time_1_observation }}
                                                <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--color-text)] group-hover:text-gray-900 dark:group-hover:text-gray-100">
                                <div class="flex items-center gap-2">
                                    <span>{{ $tracking->return_time_1 ? \Carbon\Carbon::parse($tracking->return_time_1)->format('H:i') : '-' }}</span>
                                    @if($tracking->return_time_1_observation)
                                        <div class="relative group/tooltip">
                                            <i class="fa-solid fa-info-circle text-blue-500 text-xs cursor-help"></i>
                                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 text-white text-xs rounded whitespace-nowrap opacity-0 group-hover/tooltip:opacity-100 transition-opacity duration-200 pointer-events-none z-10">
                                                {{ $tracking->return_time_1_observation }}
                                                <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--color-text)] group-hover:text-gray-900 dark:group-hover:text-gray-100">
                                <div class="flex items-center gap-2">
                                    <span>{{ $tracking->entry_time_2 ? \Carbon\Carbon::parse($tracking->entry_time_2)->format('H:i') : '-' }}</span>
                                    @if($tracking->entry_time_2_observation)
                                        <div class="relative group/tooltip">
                                            <i class="fa-solid fa-info-circle text-blue-500 text-xs cursor-help"></i>
                                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 text-white text-xs rounded whitespace-nowrap opacity-0 group-hover/tooltip:opacity-100 transition-opacity duration-200 pointer-events-none z-10">
                                                {{ $tracking->entry_time_2_observation }}
                                                <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--color-text)] group-hover:text-gray-900 dark:group-hover:text-gray-100">
                                <div class="flex items-center gap-2">
                                    <span>{{ $tracking->return_time_2 ? \Carbon\Carbon::parse($tracking->return_time_2)->format('H:i') : '-' }}</span>
                                    @if($tracking->return_time_2_observation)
                                        <div class="relative group/tooltip">
                                            <i class="fa-solid fa-info-circle text-blue-500 text-xs cursor-help"></i>
                                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 text-white text-xs rounded whitespace-nowrap opacity-0 group-hover/tooltip:opacity-100 transition-opacity duration-200 pointer-events-none z-10">
                                                {{ $tracking->return_time_2_observation }}
                                                <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($tracking->status->value === 'completo')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                        <i class="fa-solid fa-check mr-1"></i>
                                        Completo
                                    </span>
                                @elseif($tracking->status->value === 'incompleto')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                        <i class="fa-solid fa-clock mr-1"></i>
                                        Incompleto
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                        <i class="fa-solid fa-times mr-1"></i>
                                        Ausente
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button"
                                        class="text-blue-600 hover:text-blue-800 transition-colors duration-200 edit-tracking-btn"
                                        title="Editar"
                                        data-tracking-id="{{ $tracking->id }}"
                                        data-tracking-collaborator="{{ $tracking->collaborator->name }}">
                                        <i class="fa-solid fa-edit"></i>
                                    </button>
                                    <button type="button"
                                        class="text-red-600 hover:text-red-800 transition-colors duration-200 delete-tracking-btn"
                                        title="Excluir"
                                        data-tracking-id="{{ $tracking->id }}"
                                        data-tracking-collaborator="{{ $tracking->collaborator->name }}"
                                        data-tracking-date="{{ \Carbon\Carbon::parse($tracking->date)->format('d/m/Y') }}">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden space-y-4 p-4">
            @foreach($timeTrackings as $tracking)
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 space-y-3">
                    <div class="flex items-start justify-between">
                        <div>
                            <h4 class="font-medium text-[var(--color-text)]">{{ $tracking->collaborator->name }}</h4>
                            <p class="text-sm text-[var(--color-text)] opacity-70">{{ \Carbon\Carbon::parse($tracking->date)->format('d/m/Y') }}</p>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" class="text-blue-600 p-1 edit-tracking-btn"
                                    data-tracking-id="{{ $tracking->id }}"
                                    data-tracking-collaborator="{{ $tracking->collaborator->name }}">
                                <i class="fa-solid fa-edit text-sm"></i>
                            </button>
                            <button type="button" class="text-red-600 p-1 delete-tracking-btn"
                                    data-tracking-id="{{ $tracking->id }}"
                                    data-tracking-collaborator="{{ $tracking->collaborator->name }}"
                                    data-tracking-date="{{ \Carbon\Carbon::parse($tracking->date)->format('d/m/Y') }}">
                                <i class="fa-solid fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-[var(--color-text)] opacity-70">Entrada:</span>
                            <div class="mt-1 text-[var(--color-text)] flex items-center gap-2">
                                <span>{{ $tracking->entry_time_1 ? \Carbon\Carbon::parse($tracking->entry_time_1)->format('H:i') : '-' }}</span>
                                @if($tracking->entry_time_1_observation)
                                    <div class="relative group/tooltip">
                                        <i class="fa-solid fa-info-circle text-blue-500 text-xs cursor-help"></i>
                                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 text-white text-xs rounded whitespace-nowrap opacity-0 group-hover/tooltip:opacity-100 transition-opacity duration-200 pointer-events-none z-10">
                                            {{ $tracking->entry_time_1_observation }}
                                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div>
                            <span class="text-[var(--color-text)] opacity-70">Saída Almoço:</span>
                            <div class="mt-1 text-[var(--color-text)] flex items-center gap-2">
                                <span>{{ $tracking->return_time_1 ? \Carbon\Carbon::parse($tracking->return_time_1)->format('H:i') : '-' }}</span>
                                @if($tracking->return_time_1_observation)
                                    <div class="relative group/tooltip">
                                        <i class="fa-solid fa-info-circle text-blue-500 text-xs cursor-help"></i>
                                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 text-white text-xs rounded whitespace-nowrap opacity-0 group-hover/tooltip:opacity-100 transition-opacity duration-200 pointer-events-none z-10">
                                            {{ $tracking->return_time_1_observation }}
                                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div>
                            <span class="text-[var(--color-text)] opacity-70">Volta Almoço:</span>
                            <div class="mt-1 text-[var(--color-text)] flex items-center gap-2">
                                <span>{{ $tracking->entry_time_2 ? \Carbon\Carbon::parse($tracking->entry_time_2)->format('H:i') : '-' }}</span>
                                @if($tracking->entry_time_2_observation)
                                    <div class="relative group/tooltip">
                                        <i class="fa-solid fa-info-circle text-blue-500 text-xs cursor-help"></i>
                                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 text-white text-xs rounded whitespace-nowrap opacity-0 group-hover/tooltip:opacity-100 transition-opacity duration-200 pointer-events-none z-10">
                                            {{ $tracking->entry_time_2_observation }}
                                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div>
                            <span class="text-[var(--color-text)] opacity-70">Saída:</span>
                            <div class="mt-1 text-[var(--color-text)] flex items-center gap-2">
                                <span>{{ $tracking->return_time_2 ? \Carbon\Carbon::parse($tracking->return_time_2)->format('H:i') : '-' }}</span>
                                @if($tracking->return_time_2_observation)
                                    <div class="relative group/tooltip">
                                        <i class="fa-solid fa-info-circle text-blue-500 text-xs cursor-help"></i>
                                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 text-white text-xs rounded whitespace-nowrap opacity-0 group-hover/tooltip:opacity-100 transition-opacity duration-200 pointer-events-none z-10">
                                            {{ $tracking->return_time_2_observation }}
                                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        @if($tracking->status->value === 'completo')
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                <i class="fa-solid fa-check mr-1"></i>
                                Completo
                            </span>
                        @elseif($tracking->status->value === 'incompleto')
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                <i class="fa-solid fa-clock mr-1"></i>
                                Incompleto
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                <i class="fa-solid fa-times mr-1"></i>
                                Ausente
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Estado Vazio -->
        <div class="px-6 py-12 text-center">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-clock text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-[var(--color-text)] mb-2">Nenhum registro de ponto encontrado</h3>
            <p class="text-[var(--color-text)] opacity-70 mb-4">
                @if(request('search') || request('collaborator_id'))
                    Não foram encontrados registros com os filtros aplicados.
                @else
                    Ainda não há registros de ponto no sistema.
                @endif
            </p>
            @if(request('search') || request('collaborator_id'))
                <button
                    id="btn-clear-filters"
                    class="inline-flex items-center gap-2 text-[var(--color-main)] hover:text-[var(--color-main-dark)] font-medium">
                    <i class="fa-solid fa-times"></i>
                    Limpar filtros
                </button>
            @endif
        </div>
    @endif
</div>
