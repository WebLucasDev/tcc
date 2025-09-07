<div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg p-4">
    <!-- Formulário de Filtros -->
    <div class="flex flex-col lg:flex-row gap-4 justify-center items-end">
        <!-- Filtro por Colaborador -->
        <div class="w-full sm:w-80">
            <label for="collaborator_filter" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                Colaborador
            </label>
            <select name="collaborator_id" id="collaborator_filter"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200">
                <option value="">Todos os colaboradores</option>
                @foreach($allCollaborators as $collab)
                    <option value="{{ $collab->id }}" {{ $collaboratorId == $collab->id ? 'selected' : '' }}>
                        {{ $collab->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Filtro por Mês -->
        <div class="w-full sm:w-64">
            <label for="month_filter" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                Mês/Ano
            </label>
            <input type="month" name="month" id="month_filter" value="{{ $month }}"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200">
        </div>
    </div>
</div>
