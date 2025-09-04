<!-- Tabela de Colaboradores -->
<div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
    <!-- Cabeçalho da Tabela -->
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h3 class="text-lg font-semibold text-[var(--color-text)]">
                Lista de Colaboradores
                <span id="results-summary" class="text-sm font-normal opacity-70" style="display: none;">
                    ({{ $collaborators->total() }} resultado{{ $collaborators->total() != 1 ? 's' : '' }} encontrado{{ $collaborators->total() != 1 ? 's' : '' }})
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
                        <option value="email" {{ request('sort_by') == 'email' ? 'selected' : '' }}>Email</option>
                        <option value="admission_date" {{ request('sort_by') == 'admission_date' ? 'selected' : '' }}>Data de Admissão</option>
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
    @if($collaborators->count() > 0)
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Colaborador
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Departamento
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Cargo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Data de Admissão
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($collaborators as $collaborator)
                                                <tr class="group hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-[var(--color-text)] group-hover:text-gray-900 dark:group-hover:text-gray-100">
                                        {{ $collaborator->name }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-[var(--color-text)] group-hover:text-gray-900 dark:group-hover:text-gray-100">{{ $collaborator->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($collaborator->position && $collaborator->position->department)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                        {{ $collaborator->position->department->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-sm">Sem departamento</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($collaborator->position)
                                    <span class="text-sm text-[var(--color-text)]">{{ $collaborator->position->name }}</span>
                                @else
                                    <span class="text-gray-400 text-sm">Sem cargo</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($collaborator->status)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $collaborator->status->badgeClass() }}">
                                        <i class="fa-solid {{ $collaborator->status->icon() }} mr-1"></i>
                                        {{ $collaborator->status->label() }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-sm">Indefinido</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--color-text)] opacity-70 group-hover:text-gray-900 dark:group-hover:text-gray-100 group-hover:opacity-100">
                                {{ $collaborator->admission_date ? $collaborator->admission_date->format('d/m/Y') : 'Não informado' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('collaborator.edit', $collaborator->id) }}"
                                       class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                                       title="Editar">
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                    <button
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors delete-collaborator-btn"
                                        data-collaborator-id="{{ $collaborator->id }}"
                                        data-collaborator-name="{{ $collaborator->name }}"
                                        title="Excluir">
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
            @foreach($collaborators as $collaborator)
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 space-y-3">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center">
                            <h4 class="font-medium text-[var(--color-text)] collaborator-name">{{ $collaborator->name }}</h4>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('collaborator.edit', $collaborator->id) }}" class="text-blue-600 p-1"><i class="fa-solid fa-edit text-sm"></i></a>
                            <button
                                class="text-red-600 p-1 delete-collaborator-btn"
                                data-collaborator-id="{{ $collaborator->id }}"
                                data-collaborator-name="{{ $collaborator->name }}">
                                <i class="fa-solid fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <div class="text-sm text-[var(--color-text)] opacity-70">
                        {{ $collaborator->email }}
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-[var(--color-text)] opacity-70">Departamento:</span>
                            <div class="mt-1">
                                @if($collaborator->position && $collaborator->position->department)
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                        {{ $collaborator->position->department->name }}
                                    </span>
                                @else
                                    <span class="text-[var(--color-text)] opacity-50">Sem departamento</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <span class="text-[var(--color-text)] opacity-70">Cargo:</span>
                            <div class="mt-1">
                                @if($collaborator->position)
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                                        {{ $collaborator->position->name }}
                                    </span>
                                @else
                                    <span class="text-[var(--color-text)] opacity-50">Sem cargo</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="text-xs text-[var(--color-text)] opacity-50">
                        Admitido em {{ $collaborator->admission_date ? $collaborator->admission_date->format('d/m/Y') : 'Data não informada' }}
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Estado Vazio -->
        <div class="px-6 py-12 text-center">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-users text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-[var(--color-text)] mb-2">Nenhum colaborador encontrado</h3>
            <p class="text-[var(--color-text)] opacity-70 mb-4">
                @if(request('search') || request('department_id') || request('position_id'))
                    Não foram encontrados colaboradores com os filtros aplicados.
                @else
                    Comece criando o primeiro colaborador do sistema.
                @endif
            </p>
            @if(request('search') || request('department_id') || request('position_id'))
                <button
                    id="btn-clear-filters"
                    class="inline-flex items-center gap-2 text-[var(--color-main)] hover:text-[var(--color-main-dark)] font-medium">
                    <i class="fa-solid fa-times"></i>
                    Limpar filtros
                </button>
            @else
                <button
                    id="btn-new-collaborator-empty"
                    class="inline-flex items-center gap-2 bg-[var(--color-main)] hover:bg-[var(--color-main-dark)] text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    <i class="fa-solid fa-plus"></i>
                    Criar primeiro colaborador
                </button>
            @endif
        </div>
    @endif
</div>
