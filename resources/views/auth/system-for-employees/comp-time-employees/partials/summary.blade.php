@php
    $balance = $bankHoursData['bank_balance_minutes'];
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Horas Trabalhadas -->
    <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10 hover:shadow-xl transition-shadow duration-300">
        <div class="flex items-center">
            <div class="p-3 bg-blue-500/10 rounded-xl border border-blue-500/30">
                <i class="fa-solid fa-clock text-blue-500 text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-[var(--color-text)]/60">Horas Trabalhadas</p>
                <p class="text-2xl font-bold text-[var(--color-text)]">
                    {{ sprintf('%02d:%02d', intval($bankHoursData['total_worked_minutes'] / 60), $bankHoursData['total_worked_minutes'] % 60) }}
                </p>
            </div>
        </div>
    </div>

    <!-- Horas Esperadas -->
    <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10 hover:shadow-xl transition-shadow duration-300">
        <div class="flex items-center">
            <div class="p-3 bg-purple-500/10 rounded-xl border border-purple-500/30">
                <i class="fa-solid fa-hourglass-half text-purple-500 text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-[var(--color-text)]/60">Horas Esperadas</p>
                <p class="text-2xl font-bold text-[var(--color-text)]">
                    {{ sprintf('%02d:%02d', intval($bankHoursData['total_standard_minutes'] / 60), $bankHoursData['total_standard_minutes'] % 60) }}
                </p>
            </div>
        </div>
    </div>

    <!-- Dias Trabalhados -->
    <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10 hover:shadow-xl transition-shadow duration-300">
        <div class="flex items-center">
            <div class="p-3 bg-orange-500/10 rounded-xl border border-orange-500/30">
                <i class="fa-solid fa-calendar-check text-orange-500 text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-[var(--color-text)]/60">Dias Trabalhados</p>
                <p class="text-2xl font-bold text-[var(--color-text)]">{{ $bankHoursData['work_days_count'] }}</p>
                <p class="text-xs text-[var(--color-text)]/50">no período</p>
            </div>
        </div>
    </div>

    <!-- Saldo do Banco de Horas -->
    <div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10 hover:shadow-xl transition-shadow duration-300">
        <div class="flex items-center">
            <div class="p-3 {{ $balance >= 0 ? 'bg-green-500/10 border-green-500/30' : 'bg-red-500/10 border-red-500/30' }} rounded-xl border">
                <i class="fa-solid fa-{{ $balance >= 0 ? 'plus' : 'minus' }} {{ $balance >= 0 ? 'text-green-500' : 'text-red-500' }} text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-[var(--color-text)]/60">Saldo Atual</p>
                <p class="text-2xl font-bold {{ $balance >= 0 ? 'text-green-500' : 'text-red-500' }}">
                    @if($balance < 0)
                        -{{ sprintf('%02d:%02d', intval(abs($balance) / 60), abs($balance) % 60) }}
                    @else
                        {{ sprintf('%02d:%02d', intval($balance / 60), $balance % 60) }}
                    @endif
                </p>
                <p class="text-xs text-[var(--color-text)]/50">{{ $balance >= 0 ? 'Superávit' : 'Déficit' }}</p>
            </div>
        </div>
    </div>
</div>
