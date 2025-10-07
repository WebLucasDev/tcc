<!-- Tabela de Registros -->
<div class="bg-[var(--color-background)] rounded-xl shadow-lg overflow-hidden border border-[var(--color-text)]/10">
    <div class="p-6 border-b border-[var(--color-text)]/10">
        <h2 class="text-xl font-semibold text-[var(--color-main)]">
            <i class="fa-solid fa-list mr-2"></i>
            Meus Registros (Últimos 30 dias)
        </h2>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-[var(--color-text)]/5">
                <tr>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text)] uppercase tracking-wider">
                        Data</th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text)] uppercase tracking-wider">
                        Entrada</th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text)] uppercase tracking-wider">
                        Saída Almoço</th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text)] uppercase tracking-wider">
                        Retorno Almoço</th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text)] uppercase tracking-wider">
                        Saída</th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text)] uppercase tracking-wider">
                        Status</th>
                    <th
                        class="px-6 py-3 text-center text-xs font-medium text-[var(--color-text)] uppercase tracking-wider">
                        Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--color-text)]/10">
                @forelse($timeTrackings as $tracking)
                    <tr class="hover:bg-[var(--color-text)]/5 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--color-text)]">
                            {{ $tracking->date->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if ($tracking->entry_time_1)
                                <div class="flex flex-col">
                                    <span
                                        class="text-[var(--color-text)] font-medium">{{ $tracking->entry_time_1 }}</span>
                                    @if ($tracking->entry_time_1_observation)
                                        <span
                                            class="text-xs text-gray-500 italic">{{ $tracking->entry_time_1_observation }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if ($tracking->return_time_1)
                                <div class="flex flex-col">
                                    <span
                                        class="text-[var(--color-text)] font-medium">{{ $tracking->return_time_1 }}</span>
                                    @if ($tracking->return_time_1_observation)
                                        <span
                                            class="text-xs text-gray-500 italic">{{ $tracking->return_time_1_observation }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if ($tracking->entry_time_2)
                                <div class="flex flex-col">
                                    <span
                                        class="text-[var(--color-text)] font-medium">{{ $tracking->entry_time_2 }}</span>
                                    @if ($tracking->entry_time_2_observation)
                                        <span
                                            class="text-xs text-gray-500 italic">{{ $tracking->entry_time_2_observation }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if ($tracking->return_time_2)
                                <div class="flex flex-col">
                                    <span
                                        class="text-[var(--color-text)] font-medium">{{ $tracking->return_time_2 }}</span>
                                    @if ($tracking->return_time_2_observation)
                                        <span
                                            class="text-xs text-gray-500 italic">{{ $tracking->return_time_2_observation }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusClass = match ($tracking->status) {
                                    'completo'
                                        => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                    'incompleto'
                                        => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                    'ausente' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
                                };
                                $statusLabel = match ($tracking->status) {
                                    'completo' => 'Completo',
                                    'incompleto' => 'Incompleto',
                                    'ausente' => 'Ausente',
                                    default => $tracking->status,
                                };
                            @endphp
                            <span class="px-3 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-2">
                                @if ($tracking->status !== 'ausente')
                                    @if ($tracking->entry_time_1 || $tracking->return_time_1 || $tracking->entry_time_2 || $tracking->return_time_2)
                                        <button onclick="window.openCancelModal({{ $tracking->id }})"
                                            class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                            title="Cancelar Último Registro">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </button>
                                    @endif
                                @else
                                    <button onclick="window.openRestoreModal({{ $tracking->id }})"
                                        class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300"
                                        title="Restaurar">
                                        <i class="fa-solid fa-undo"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-[var(--color-text)] opacity-50">
                                <i class="fa-solid fa-inbox text-4xl mb-3"></i>
                                <p class="text-sm">Nenhum registro encontrado</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
