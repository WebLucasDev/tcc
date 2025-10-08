<div class="bg-[var(--color-background)] rounded-xl shadow-lg border border-[var(--color-text)]/10 overflow-hidden">
    <!-- Cabeçalho -->
    <div class="px-6 py-5 border-b border-[var(--color-text)]/10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-[var(--color-main)] to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fa-solid fa-list text-white"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-[var(--color-text)]">
                    Detalhamento do Banco de Horas
                </h3>
                <p class="text-sm text-[var(--color-text)]/60">
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
                <h4 class="text-lg font-bold text-[var(--color-text)] mb-4 flex items-center">
                    <i class="fa-solid fa-calendar-days text-[var(--color-main)] mr-2"></i>
                    {{ count($bankHoursData['work_days']) }} dia{{ count($bankHoursData['work_days']) != 1 ? 's' : '' }} registrado{{ count($bankHoursData['work_days']) != 1 ? 's' : '' }}
                </h4>

                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @foreach($bankHoursData['work_days'] as $day)
                        <div class="flex items-center justify-between p-3 bg-[var(--color-text)]/5 rounded-lg border border-[var(--color-text)]/10 hover:bg-[var(--color-text)]/10 transition-colors">
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
                            <div class="text-right">
                                <p class="text-base font-bold {{ $day['difference_minutes'] >= 0 ? 'text-green-500' : 'text-red-500' }}">
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
