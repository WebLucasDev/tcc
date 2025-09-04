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
                    placeholder="Buscar por nome, email, departamento ou cargo..."
                    autocomplete="off"
                    spellcheck="false"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200">
            </div>
        </div>

        <!-- Filtro por Departamento -->
        <div class="lg:w-64">
            <select name="department_id"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200">
                <option value="">Todos os departamentos</option>
                @if(isset($departments))
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}"
                            {{ request('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>

        <!-- Filtro por Cargo -->
        <div class="lg:w-64">
            <select name="position_id"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200">
                <option value="">Todos os cargos</option>
                @if(isset($positions))
                    @foreach ($positions as $position)
                        <option value="{{ $position->id }}"
                            {{ request('position_id') == $position->id ? 'selected' : '' }}>
                            {{ $position->name }}
                        </option>
                    @endforeach
                @endif
            </select>
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
                    <i class="fa-solid fa-users text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-[var(--color-text)] opacity-70">Total de Colaboradores</p>
                    <p id="total-collaborators" class="text-2xl font-bold text-[var(--color-text)]">
                        {{ isset($collaborators) ? $collaborators->total() : 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                    <i class="fa-solid fa-building text-green-600 dark:text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-[var(--color-text)] opacity-70">Com Departamento</p>
                    <p id="with-department" class="text-2xl font-bold text-[var(--color-text)]">
                        {{ $withDepartment ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                    <i class="fa-solid fa-briefcase text-purple-600 dark:text-purple-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-[var(--color-text)] opacity-70">Com Cargo</p>
                    <p id="with-position" class="text-2xl font-bold text-[var(--color-text)]">
                        {{ $withPosition ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
