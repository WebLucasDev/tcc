<!-- Tabela de Jornadas de Trabalho -->
<div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
    <!-- Cabeçalho da Tabela -->
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h3 class="text-lg font-semibold text-[var(--color-text)]">
                Lista de Jornadas de Trabalho
                <span id="results-summary" class="text-sm font-normal opacity-70" style="display: none;">
                    ({{ $workHours->total() }} resultado{{ $workHours->total() != 1 ? 's' : '' }} encontrado{{ $workHours->total() != 1 ? 's' : '' }})
                </span>
            </h3>

            <!-- Ordenação -->
            <div class="flex items-center gap-2">
                <span class="text-sm text-[var(--color-text)] opacity-70">Ordenar por:</span>
                <div class="flex gap-2">
                    <select
                        name="sort_by"
                        class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded bg-[var(--color-background)] text-[var(--color-text)]">
                        <option value="name" {{ request('sort_by', 'name') == 'name' ? 'selected' : '' }}>Nome</option>
                        <option value="total_weekly_hours" {{ request('sort_by') == 'total_weekly_hours' ? 'selected' : '' }}>Horas Semanais</option>
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Data de Criação</option>
                    </select>

                    <button
                        type="button"
                        name="sort_direction"
                        value="{{ request('sort_direction', 'asc') }}"
                        class="px-2 py-1 text-sm text-[var(--color-main)] hover:text-[var(--color-main-dark)] transition-colors">
                        <i class="fa-solid fa-sort-{{ request('sort_direction', 'asc') == 'asc' ? 'up' : 'down' }}"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Conteúdo da Tabela -->
    @if($workHours->count() > 0)
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Jornada
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Horas Semanais
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Dias Ativos
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Colaboradores
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($workHours as $workHour)
                        <tr class="group hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-[var(--color-text)] group-hover:text-gray-900 dark:group-hover:text-gray-100">
                                            {{ $workHour->name }}
                                        </div>
                                        @if($workHour->description)
                                            <div class="text-sm text-[var(--color-text)] opacity-70">
                                                {{ Str::limit($workHour->description, 40) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center text-sm text-[var(--color-text)] group-hover:text-gray-900 dark:group-hover:text-gray-100">
                                    <i class="fa-solid fa-clock text-[var(--color-main)] mr-2"></i>
                                    <div>
                                        <div class="font-medium">{{ $workHour->getFormattedWeeklyHours() }}</div>
                                        <div class="text-xs opacity-70">{{ $workHour->total_weekly_hours }}h semanais</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($workHour->getActiveDays() as $day)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                            {{ substr(\App\Models\WorkHoursModel::getDayNameInPortuguese($day), 0, 3) }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center text-sm text-[var(--color-text)] group-hover:text-gray-900 dark:group-hover:text-gray-100">
                                    <i class="fa-solid fa-users text-[var(--color-main)] mr-2"></i>
                                    {{ $workHour->collaborators_count ?? 0 }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $workHour->status->badgeClass() }}">
                                    <i class="{{ $workHour->status->icon() }} mr-1"></i>
                                    {{ $workHour->status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('work-hours.edit', $workHour->id) }}"
                                        class="text-blue-600 hover:text-blue-800 transition-colors duration-200"
                                        title="Editar">
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                    <button
                                        class="text-red-600 hover:text-red-800 transition-colors duration-200 delete-work-hour-btn"
                                        title="Excluir"
                                        data-work-hour-id="{{ $workHour->id }}"
                                        data-work-hour-name="{{ $workHour->name }}">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden space-y-4 p-4">
            @foreach($workHours as $workHour)
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 space-y-3">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center">
                            <h4 class="font-medium text-[var(--color-text)]">{{ $workHour->name }}</h4>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('work-hours.edit', $workHour->id) }}" class="text-blue-600 p-1">
                                <i class="fa-solid fa-edit text-sm"></i>
                            </a>
                            <button
                                class="text-red-600 p-1 delete-work-hour-btn"
                                data-work-hour-id="{{ $workHour->id }}"
                                data-work-hour-name="{{ $workHour->name }}">
                                <i class="fa-solid fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-[var(--color-text)] opacity-70">Horas Semanais:</span>
                            <div class="mt-1 text-[var(--color-text)] font-medium">{{ $workHour->getFormattedWeeklyHours() }}</div>
                        </div>
                        <div>
                            <span class="text-[var(--color-text)] opacity-70">Colaboradores:</span>
                            <div class="mt-1 text-[var(--color-text)]">{{ $workHour->collaborators_count ?? 0 }}</div>
                        </div>
                    </div>

                    <div>
                        <span class="text-[var(--color-text)] opacity-70 text-sm">Dias Ativos:</span>
                        <div class="mt-1 flex flex-wrap gap-1">
                            @foreach($workHour->getActiveDays() as $day)
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                    {{ substr(\App\Models\WorkHoursModel::getDayNameInPortuguese($day), 0, 3) }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $workHour->status->badgeClass() }}">
                            {{ $workHour->status->label() }}
                        </span>
                        <div class="text-xs text-[var(--color-text)] opacity-50">
                            Criado em {{ $workHour->created_at->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Estado Vazio -->
        <div class="p-12 text-center">
            <div class="mx-auto h-24 w-24 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                <i class="fa-solid fa-clock text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-lg font-medium text-[var(--color-text)] mb-2">
                Nenhuma jornada encontrada
            </h3>
            <p class="text-[var(--color-text)] opacity-70 mb-6">
                {{ request()->hasAny(['search', 'status']) ? 'Tente ajustar seus filtros ou criar uma nova jornada.' : 'Comece criando sua primeira jornada de trabalho.' }}
            </p>
            <a href="{{ route('work-hours.create') }}"
                id="btn-new-work-hour-empty"
                class="bg-[var(--color-main)] hover:bg-[var(--color-main-dark)] text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 inline-flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                Nova Jornada
            </a>
        </div>
    @endif
</div>
