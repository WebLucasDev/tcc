<div class="bg-[var(--color-background)] rounded-xl shadow-lg p-6 border border-[var(--color-text)]/10">
    <!-- Cabeçalho -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-[var(--color-main)] rounded-xl flex items-center justify-center shadow-lg">
                <i class="fa-solid fa-filter text-white"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-[var(--color-text)]">Filtrar Período</h3>
                <p class="text-sm text-[var(--color-text)]/60">Selecione o mês para visualizar seu banco de horas</p>
            </div>
        </div>
    </div>

    <!-- Filtro de Mês -->
    <div class="max-w-md">
        <label for="month_filter" class="block text-sm font-bold text-[var(--color-text)] mb-2">
            <i class="fa-solid fa-calendar mr-1"></i>
            Mês/Ano
        </label>
        <input
            type="month"
            name="month"
            id="month_filter"
            value="{{ $month }}"
            max="{{ now()->format('Y-m') }}"
            class="w-full px-4 py-3 bg-[var(--color-background)] text-[var(--color-text)] border border-[var(--color-text)]/20 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all">
    </div>
</div>
