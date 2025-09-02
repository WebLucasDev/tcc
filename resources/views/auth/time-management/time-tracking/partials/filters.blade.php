<!-- Filtros e Busca -->
<div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-[var(--color-text)] flex items-center gap-2">
            <i class="fa-solid fa-filter text-[var(--color-main)]"></i>
            Filtros e Busca
        </h3>
    </div>
    <div class="p-6">
        <form method="GET" action="{{ route('time-tracking.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Busca por Nome -->
                <div>
                    <label for="search" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                        Buscar por Nome do Colaborador
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Digite o nome do colaborador..."
                               class="w-full px-4 py-3 pl-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)]"
                               autocomplete="off">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-search text-gray-400"></i>
                        </div>
                        <!-- Lista de sugestões -->
                        <div id="search-suggestions" class="absolute z-10 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto hidden">
                            <!-- Preenchido via JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- Filtro por Colaborador -->
                <div>
                    <label for="collaborator_id" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                        Filtrar por Colaborador
                    </label>
                    <select id="collaborator_id" 
                            name="collaborator_id" 
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)]">
                        <option value="">-- Todos os colaboradores --</option>
                        @foreach($collaborators as $collaborator)
                            <option value="{{ $collaborator->id }}" {{ request('collaborator_id') == $collaborator->id ? 'selected' : '' }}>
                                {{ $collaborator->name }} - {{ $collaborator->position->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>
