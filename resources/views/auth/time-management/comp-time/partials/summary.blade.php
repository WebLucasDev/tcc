<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Total de Colaboradores -->
    <div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow duration-300">
        <div class="flex items-center">
            <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                <i class="fa-solid fa-users text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-[var(--color-text)] opacity-70">Colaboradores</p>
                <p class="text-2xl font-bold text-[var(--color-text)]">{{ $summary['total_collaborators'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Saldo Positivo -->
    <div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow duration-300">
        <div class="flex items-center">
            <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                <i class="fa-solid fa-plus text-green-600 dark:text-green-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-[var(--color-text)] opacity-70">Horas Positivas</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                    {{ $summary['total_positive_minutes'] ? sprintf('%02d:%02d', intval($summary['total_positive_minutes'] / 60), $summary['total_positive_minutes'] % 60) : '00:00' }}
                </p>
                <p class="text-xs text-[var(--color-text)] opacity-50">
                    {{ $summary['collaborators_with_positive_bank'] ?? 0 }} colaborador{{ ($summary['collaborators_with_positive_bank'] ?? 0) != 1 ? 'es' : '' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Saldo Negativo -->
    <div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow duration-300">
        <div class="flex items-center">
            <div class="p-3 bg-red-100 dark:bg-red-900 rounded-lg">
                <i class="fa-solid fa-minus text-red-600 dark:text-red-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-[var(--color-text)] opacity-70">Horas Negativas</p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                    {{ $summary['total_negative_minutes'] ? sprintf('%02d:%02d', intval($summary['total_negative_minutes'] / 60), $summary['total_negative_minutes'] % 60) : '00:00' }}
                </p>
                <p class="text-xs text-[var(--color-text)] opacity-50">
                    {{ $summary['collaborators_with_negative_bank'] ?? 0 }} colaborador{{ ($summary['collaborators_with_negative_bank'] ?? 0) != 1 ? 'es' : '' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Saldo Líquido -->
    <div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow duration-300">
        <div class="flex items-center">
            <div class="p-3 {{ ($summary['net_balance_minutes'] ?? 0) >= 0 ? 'bg-indigo-100 dark:bg-indigo-900' : 'bg-orange-100 dark:bg-orange-900' }} rounded-lg">
                <i class="fa-solid fa-balance-scale {{ ($summary['net_balance_minutes'] ?? 0) >= 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-orange-600 dark:text-orange-400' }} text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-[var(--color-text)] opacity-70">Saldo Líquido</p>
                <p class="text-2xl font-bold {{ ($summary['net_balance_minutes'] ?? 0) >= 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-orange-600 dark:text-orange-400' }}">
                    @if(($summary['net_balance_minutes'] ?? 0) < 0)
                        -{{ sprintf('%02d:%02d', intval(abs($summary['net_balance_minutes']) / 60), abs($summary['net_balance_minutes']) % 60) }}
                    @else
                        {{ sprintf('%02d:%02d', intval($summary['net_balance_minutes'] / 60), $summary['net_balance_minutes'] % 60) }}
                    @endif
                </p>
                <p class="text-xs text-[var(--color-text)] opacity-50">
                    {{ ($summary['net_balance_minutes'] ?? 0) >= 0 ? 'Superávit' : 'Déficit' }} geral
                </p>
            </div>
        </div>
    </div>
</div>
