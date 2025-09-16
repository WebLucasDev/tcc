<div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg p-4">
    <!-- Formulário de Filtros -->
    <div class="flex flex-col lg:flex-row gap-4 mb-4">
        <!-- Campo de Busca -->
        <div class="flex-1">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-search text-[var(--color-text)] opacity-50"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Buscar por nome da jornada ou descrição..."
                    autocomplete="off"
                    spellcheck="false"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200">
            </div>
        </div>

        <!-- Filtro por Status -->
        <div class="lg:w-48">
            <select name="status"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200">
                <option value="">Todos os status</option>
                <option value="ativo" {{ request('status') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                <option value="inativo" {{ request('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
            </select>
        </div>
    </div>

    <!-- Estatísticas -->
    <div id="statistics-container" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <i class="fa-solid fa-business-time text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-[var(--color-text)] opacity-70">Total de Jornadas</p>
                    <p id="total-work-hours" class="text-2xl font-bold text-[var(--color-text)]">
                        {{ $workHours->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                    <i class="fa-solid fa-check-circle text-green-600 dark:text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-[var(--color-text)] opacity-70">Jornadas Ativas</p>
                    <p id="active-work-hours" class="text-2xl font-bold text-[var(--color-text)]">
                        {{ $activeCount ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg">
                    <i class="fa-solid fa-times-circle text-red-600 dark:text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-[var(--color-text)] opacity-70">Jornadas Inativas</p>
                    <p id="inactive-work-hours" class="text-2xl font-bold text-[var(--color-text)]">
                        {{ $inactiveCount ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
