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
                    placeholder="Buscar por nome do colaborador, email ou motivo..."
                    autocomplete="off"
                    spellcheck="false"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200 placeholder:text-[var(--color-text)] placeholder:opacity-50">
                @if(request('search'))
                    <button type="button" onclick="clearSearch()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-[var(--color-text)] opacity-50 hover:opacity-80 transition-opacity">
                        <i class="fa-solid fa-times"></i>
                    </button>
                @endif
            </div>
        </div>

        <!-- Filtro por Status -->
        <div class="lg:w-64">
            <select name="status"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200">
                <option value="">Todos os status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendentes</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Aprovadas</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejeitadas</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Canceladas</option>
            </select>
        </div>
    </div>

    <!-- Estatísticas -->
    <div id="statistics-container" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                    <i class="fa-solid fa-clock text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-[var(--color-text)] opacity-70">Pendentes</p>
                    <p id="pending-count" class="text-2xl font-bold text-[var(--color-text)]">
                        {{ $stats['pending'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                    <i class="fa-solid fa-check text-green-600 dark:text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-[var(--color-text)] opacity-70">Aprovadas</p>
                    <p id="approved-count" class="text-2xl font-bold text-[var(--color-text)]">
                        {{ $stats['approved'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg">
                    <i class="fa-solid fa-times text-red-600 dark:text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-[var(--color-text)] opacity-70">Rejeitadas</p>
                    <p id="rejected-count" class="text-2xl font-bold text-[var(--color-text)]">
                        {{ $stats['rejected'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-2 bg-gray-100 dark:bg-gray-900 rounded-lg">
                    <i class="fa-solid fa-ban text-gray-600 dark:text-gray-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-[var(--color-text)] opacity-70">Canceladas</p>
                    <p id="cancelled-count" class="text-2xl font-bold text-[var(--color-text)]">
                        {{ $stats['cancelled'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
