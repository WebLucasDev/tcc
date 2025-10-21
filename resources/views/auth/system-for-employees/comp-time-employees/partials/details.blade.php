<div class="bg-[var(--color-background)] rounded-xl shadow-lg border border-[var(--color-text)]/10 overflow-hidden">
    <!-- Cabeçalho -->
    <div class="px-6 py-5 border-b border-[var(--color-text)]/10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-[var(--color-main)] to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fa-solid fa-list text-white"></i>
            </div>
            <div>
                <h3 class="text-sm sm:text-lg font-bold text-[var(--color-text)]">
                    Detalhamento do Banco de Horas
                </h3>
                <p class="text-sm sm:text-lg text-[var(--color-text)]/60">
                    Análise diária do período selecionado
                </p>
            </div>
        </div>
    </div>

    <!-- Conteúdo -->
    <div class="p-6">
        <!-- Registros Diários -->
        @if(count($bankHoursData['work_days']) > 0)
            <div>
                <h4 class="text-sm sm:text-lg font-bold text-[var(--color-text)] mb-4 flex items-center">
                    <i class="fa-solid fa-calendar-days text-[var(--color-main)] mr-2"></i>
                    {{ count($bankHoursData['work_days']) }} dia{{ count($bankHoursData['work_days']) != 1 ? 's' : '' }} registrado{{ count($bankHoursData['work_days']) != 1 ? 's' : '' }}
                </h4>

                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @foreach($bankHoursData['work_days'] as $index => $day)
                        <div class="bg-[var(--color-text)]/5 rounded-lg border border-[var(--color-text)]/10 overflow-hidden">
                            <!-- Cabeçalho do dia (clicável) -->
                            <div
                                class="flex items-center justify-between p-3 hover:bg-[var(--color-text)]/10 transition-colors cursor-pointer day-header"
                                onclick="toggleDayDetails({{ $index }})">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 {{ $day['difference_minutes'] >= 0 ? 'bg-green-500/10 text-green-500' : ($day['tracking'] ? 'bg-red-500/10 text-red-500' : 'bg-gray-500/10 text-gray-500') }} rounded-lg flex items-center justify-center border {{ $day['difference_minutes'] >= 0 ? 'border-green-500/30' : ($day['tracking'] ? 'border-red-500/30' : 'border-gray-500/30') }}">
                                        <i class="fa-solid fa-{{ $day['tracking'] ? ($day['difference_minutes'] >= 0 ? 'plus' : 'minus') : 'ban' }}"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-[var(--color-text)]">
                                            {{ $day['date']->format('d/m/Y') }} - {{ $day['date']->locale('pt_BR')->dayName }}
                                        </p>
                                        <p class="text-xs text-[var(--color-text)]/60">
                                            @if($day['tracking'])
                                                {{ sprintf('%02d:%02d', intval($day['worked_minutes'] / 60), $day['worked_minutes'] % 60) }} trabalhadas
                                                / {{ sprintf('%02d:%02d', intval($day['expected_minutes'] / 60), $day['expected_minutes'] % 60) }} esperadas
                                            @else
                                                Ausente
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <p class="text-base font-bold {{ $day['difference_minutes'] >= 0 ? 'text-green-500' : 'text-red-500' }}">
                                        @if($day['difference_minutes'] < 0)
                                            -{{ sprintf('%02d:%02d', intval(abs($day['difference_minutes']) / 60), abs($day['difference_minutes']) % 60) }}
                                        @else
                                            +{{ sprintf('%02d:%02d', intval($day['difference_minutes'] / 60), $day['difference_minutes'] % 60) }}
                                        @endif
                                    </p>
                                    <i class="fa-solid fa-chevron-down text-[var(--color-text)]/40 transition-transform duration-300 chevron-icon-{{ $index }}"></i>
                                </div>
                            </div>

                            <!-- Detalhes do dia (dropdown) -->
                            <div id="day-details-{{ $index }}" class="hidden border-t border-[var(--color-text)]/10 bg-[var(--color-text)]/3 p-4">
                                @if($day['tracking'])
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Período Manhã -->
                                        <div class="space-y-2">
                                            <h5 class="text-xs font-bold text-[var(--color-text)]/70 uppercase tracking-wider flex items-center gap-2">
                                                <i class="fa-solid fa-sun text-yellow-500"></i>
                                                Período Manhã
                                            </h5>
                                            <div class="bg-[var(--color-background)] rounded-lg p-3 border border-[var(--color-text)]/10">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="text-xs text-[var(--color-text)]/60">Entrada</span>
                                                    <span class="text-sm font-bold text-[var(--color-text)]">
                                                        @if($day['tracking']->entry_time_1)
                                                            {{ \Carbon\Carbon::parse($day['tracking']->entry_time_1)->format('H:i') }}
                                                        @else
                                                            <span class="text-[var(--color-text)]/40">--:--</span>
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="flex items-center justify-between">
                                                    <span class="text-xs text-[var(--color-text)]/60">Saída</span>
                                                    <span class="text-sm font-bold text-[var(--color-text)]">
                                                        @if($day['tracking']->return_time_1)
                                                            {{ \Carbon\Carbon::parse($day['tracking']->return_time_1)->format('H:i') }}
                                                        @else
                                                            <span class="text-[var(--color-text)]/40">--:--</span>
                                                        @endif
                                                    </span>
                                                </div>
                                                @if($day['tracking']->entry_time_1 && $day['tracking']->return_time_1)
                                                    <div class="mt-2 pt-2 border-t border-[var(--color-text)]/10">
                                                        <div class="flex items-center justify-between">
                                                            <span class="text-xs text-[var(--color-text)]/60">Total</span>
                                                            <span class="text-sm font-bold text-blue-500">
                                                                {{ sprintf('%02d:%02d',
                                                                    intval(\Carbon\Carbon::parse($day['tracking']->entry_time_1)->diffInMinutes(\Carbon\Carbon::parse($day['tracking']->return_time_1)) / 60),
                                                                    \Carbon\Carbon::parse($day['tracking']->entry_time_1)->diffInMinutes(\Carbon\Carbon::parse($day['tracking']->return_time_1)) % 60
                                                                ) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Período Tarde -->
                                        <div class="space-y-2">
                                            <h5 class="text-xs font-bold text-[var(--color-text)]/70 uppercase tracking-wider flex items-center gap-2">
                                                <i class="fa-solid fa-cloud-sun text-orange-500"></i>
                                                Período Tarde
                                            </h5>
                                            <div class="bg-[var(--color-background)] rounded-lg p-3 border border-[var(--color-text)]/10">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="text-xs text-[var(--color-text)]/60">Entrada</span>
                                                    <span class="text-sm font-bold text-[var(--color-text)]">
                                                        @if($day['tracking']->entry_time_2)
                                                            {{ \Carbon\Carbon::parse($day['tracking']->entry_time_2)->format('H:i') }}
                                                        @else
                                                            <span class="text-[var(--color-text)]/40">--:--</span>
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="flex items-center justify-between">
                                                    <span class="text-xs text-[var(--color-text)]/60">Saída</span>
                                                    <span class="text-sm font-bold text-[var(--color-text)]">
                                                        @if($day['tracking']->return_time_2)
                                                            {{ \Carbon\Carbon::parse($day['tracking']->return_time_2)->format('H:i') }}
                                                        @else
                                                            <span class="text-[var(--color-text)]/40">--:--</span>
                                                        @endif
                                                    </span>
                                                </div>
                                                @if($day['tracking']->entry_time_2 && $day['tracking']->return_time_2)
                                                    <div class="mt-2 pt-2 border-t border-[var(--color-text)]/10">
                                                        <div class="flex items-center justify-between">
                                                            <span class="text-xs text-[var(--color-text)]/60">Total</span>
                                                            <span class="text-sm font-bold text-blue-500">
                                                                {{ sprintf('%02d:%02d',
                                                                    intval(\Carbon\Carbon::parse($day['tracking']->entry_time_2)->diffInMinutes(\Carbon\Carbon::parse($day['tracking']->return_time_2)) / 60),
                                                                    \Carbon\Carbon::parse($day['tracking']->entry_time_2)->diffInMinutes(\Carbon\Carbon::parse($day['tracking']->return_time_2)) % 60
                                                                ) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Observações (se houver) -->
                                    @if($day['tracking']->individual_observations)
                                        <div class="mt-4 pt-4 border-t border-[var(--color-text)]/10">
                                            <h5 class="text-xs font-bold text-[var(--color-text)]/70 uppercase tracking-wider mb-2 flex items-center gap-2">
                                                <i class="fa-solid fa-comment-dots text-[var(--color-main)]"></i>
                                                Observações
                                            </h5>
                                            <div class="bg-[var(--color-background)] rounded-lg p-3 border border-[var(--color-text)]/10">
                                                <p class="text-sm text-[var(--color-text)]/80">{{ $day['tracking']->individual_observations }}</p>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <!-- Mensagem para dias ausentes -->
                                    <div class="text-center py-8">
                                        <div class="w-16 h-16 mx-auto mb-3 bg-gray-500/10 rounded-full flex items-center justify-center border border-gray-500/30">
                                            <i class="fa-solid fa-ban text-2xl text-gray-500"></i>
                                        </div>
                                        <h5 class="text-sm font-bold text-[var(--color-text)] mb-1">Nenhum registro de ponto</h5>
                                        <p class="text-xs text-[var(--color-text)]/60">
                                            Não há horários registrados para este dia.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <!-- Estado Vazio -->
            <div class="text-center py-12">
                <div class="w-24 h-24 mx-auto mb-4 bg-[var(--color-text)]/5 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-inbox text-4xl text-[var(--color-text)]/30"></i>
                </div>
                <h3 class="text-lg font-bold text-[var(--color-text)] mb-2">Nenhum registro encontrado</h3>
                <p class="text-[var(--color-text)]/60">
                    Não há registros de ponto para o período selecionado.
                </p>
            </div>
        @endif
    </div>
</div>
