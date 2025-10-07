<!-- Tabela de Solicitações -->
<div class="bg-[var(--color-background)] rounded-xl shadow-lg border border-[var(--color-text)]/10 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-[var(--color-text)]/10">
            <thead class="bg-[var(--color-main)]/5">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-[var(--color-text)] uppercase tracking-wider">
                        Data do Registro
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-[var(--color-text)] uppercase tracking-wider">
                        Horário Antigo
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-[var(--color-text)] uppercase tracking-wider">
                        Horário Solicitado
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-[var(--color-text)] uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-[var(--color-text)] uppercase tracking-wider">
                        Data da Solicitação
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-[var(--color-text)] uppercase tracking-wider">
                        Ações
                    </th>
                </tr>
            </thead>
            <tbody class="bg-[var(--color-background)] divide-y divide-[var(--color-text)]/10">
                @forelse($solicitations as $solicitation)
                    <tr class="hover:bg-[var(--color-main)]/5 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--color-text)]">
                            {{ \Carbon\Carbon::parse($solicitation->timeTracking->date)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--color-text)]">
                            @if($solicitation->old_time_start && $solicitation->old_time_finish)
                                {{ \Carbon\Carbon::parse($solicitation->old_time_start)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($solicitation->old_time_finish)->format('H:i') }}
                            @else
                                <span class="text-gray-400">Não informado</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-[var(--color-main)]">
                            {{ \Carbon\Carbon::parse($solicitation->new_time_start)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($solicitation->new_time_finish)->format('H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $solicitation->status->color() }}">
                                <i class="{{ $solicitation->status->icon() }} mr-1"></i>
                                {{ $solicitation->status->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--color-text)]">
                            {{ $solicitation->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-center gap-2">
                                <!-- Botão Ver Detalhes -->
                                <button
                                    onclick="showDetails({{ $solicitation->id }})"
                                    class="text-blue-600 hover:text-blue-900 transition-colors p-2 hover:bg-blue-50 rounded-lg"
                                    title="Ver detalhes">
                                    <i class="fa-solid fa-eye text-lg"></i>
                                </button>

                                <!-- Botão Cancelar (apenas se estiver pendente) -->
                                @if($solicitation->status === App\Enums\SolicitationStatusEnum::PENDING)
                                    <button
                                        onclick="openCancelModal({{ $solicitation->id }})"
                                        class="text-orange-600 hover:text-orange-900 transition-colors p-2 hover:bg-orange-50 rounded-lg"
                                        title="Cancelar solicitação">
                                        <i class="fa-solid fa-ban text-lg"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-[var(--color-text)]/50">
                                <i class="fa-solid fa-inbox text-6xl mb-4"></i>
                                <p class="text-lg font-semibold">Nenhuma solicitação encontrada</p>
                                <p class="text-sm mt-2">Clique em "Nova Solicitação" para criar uma</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
