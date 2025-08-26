@extends('layouts.layout')
@section('title', 'Cargos')

@section('content')

    <x-success/>
    <x-error/>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-4">
            <div class="flex flex-col sm:flex-row gap-2">
                <button
                    id="btn-new-position"
                    class="bg-[var(--color-main)] hover:bg-[var(--color-main-dark)] text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Novo Cargo
                </button>
            </div>
        </div>

        <div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <form class="flex flex-col lg:flex-row gap-4 mb-4">
                <!-- Campo de Busca -->
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-[var(--color-text)] opacity-50"></i>
                        </div>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Buscar por nome do cargo ou departamento..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200">
                    </div>
                </div>

                <!-- Filtro por Departamento -->
                <div class="lg:w-64">
                    <select
                        name="department_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200">
                        <option value="">Todos os departamentos</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

            <!-- Estatísticas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                            <i class="fas fa-briefcase text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-[var(--color-text)] opacity-70">Total de Cargos</p>
                            <p class="text-2xl font-bold text-[var(--color-text)]">{{ $positions->total() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                            <i class="fas fa-link text-green-600 dark:text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-[var(--color-text)] opacity-70">Com Departamento</p>
                            <p class="text-2xl font-bold text-[var(--color-text)]">{{ $positions->where('department_id', '!=', null)->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg">
                            <i class="fas fa-unlink text-red-600 dark:text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-[var(--color-text)] opacity-70">Sem Departamento</p>
                            <p class="text-2xl font-bold text-[var(--color-text)]">{{ $positions->where('department_id', null)->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Tabela de Cargos -->
        <div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <!-- Cabeçalho da Tabela -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h3 class="text-lg font-semibold text-[var(--color-text)]">
                        Lista de Cargos
                        @if(request('search') || request('department_id'))
                            <span class="text-sm font-normal opacity-70">
                                ({{ $positions->total() }} resultado{{ $positions->total() != 1 ? 's' : '' }} encontrado{{ $positions->total() != 1 ? 's' : '' }})
                            </span>
                        @endif
                    </h3>

                    <!-- Ordenação -->
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-[var(--color-text)] opacity-70">Ordenar por:</span>
                        <form method="GET" action="{{ route('cargos.index') }}" class="flex gap-2">
                            @foreach(request()->except(['sort_by', 'sort_direction']) as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach

                            <select
                                name="sort_by"
                                onchange="this.form.submit()"
                                class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded bg-[var(--color-background)] text-[var(--color-text)]">
                                <option value="name" {{ request('sort_by', 'name') == 'name' ? 'selected' : '' }}>Nome</option>
                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Data de Criação</option>
                            </select>

                            <button
                                type="submit"
                                name="sort_direction"
                                value="{{ request('sort_direction', 'asc') == 'asc' ? 'desc' : 'asc' }}"
                                class="px-2 py-1 text-sm text-[var(--color-main)] hover:text-[var(--color-main-dark)] transition-colors">
                                <i class="fas fa-sort-{{ request('sort_direction', 'asc') == 'asc' ? 'up' : 'down' }}"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Conteúdo da Tabela -->
            @if($positions->count() > 0)
                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text)] opacity-70 uppercase tracking-wider">
                                    Cargo
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text)] opacity-70 uppercase tracking-wider">
                                    Departamento
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text)] opacity-70 uppercase tracking-wider">
                                    Colaboradores
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text)] opacity-70 uppercase tracking-wider">
                                    Data de Criação
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-[var(--color-text)] opacity-70 uppercase tracking-wider">
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($positions as $position)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-[var(--color-main)] bg-opacity-10 rounded-lg flex items-center justify-center mr-3">
                                                <i class="fas fa-briefcase text-[var(--color-main)] text-sm"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-[var(--color-text)]">{{ $position->name }}</div>
                                                <div class="text-xs text-[var(--color-text)] opacity-50">ID: {{ $position->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($position->department)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                <i class="fas fa-building mr-1"></i>
                                                {{ $position->department->name }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                                <i class="fas fa-minus mr-1"></i>
                                                Sem departamento
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center text-sm text-[var(--color-text)]">
                                            <i class="fas fa-users text-[var(--color-main)] mr-2"></i>
                                            {{ $position->collaborators_count ?? 0 }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--color-text)] opacity-70">
                                        {{ $position->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <button
                                                class="text-blue-600 hover:text-blue-800 transition-colors duration-200"
                                                title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button
                                                class="text-red-600 hover:text-red-800 transition-colors duration-200"
                                                title="Excluir">
                                                <i class="fas fa-trash"></i>
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
                    @foreach($positions as $position)
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 space-y-3">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-[var(--color-main)] bg-opacity-10 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-briefcase text-[var(--color-main)] text-sm"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-[var(--color-text)]">{{ $position->name }}</h4>
                                        <p class="text-xs text-[var(--color-text)] opacity-50">ID: {{ $position->id }}</p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button class="text-[var(--color-main)] p-1"><i class="fas fa-eye text-sm"></i></button>
                                    <button class="text-blue-600 p-1"><i class="fas fa-edit text-sm"></i></button>
                                    <button class="text-red-600 p-1"><i class="fas fa-trash text-sm"></i></button>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-[var(--color-text)] opacity-70">Departamento:</span>
                                    <div class="mt-1">
                                        @if($position->department)
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                {{ $position->department->name }}
                                            </span>
                                        @else
                                            <span class="text-[var(--color-text)] opacity-50">Sem departamento</span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <span class="text-[var(--color-text)] opacity-70">Colaboradores:</span>
                                    <div class="mt-1 text-[var(--color-text)]">{{ $position->collaborators_count ?? 0 }}</div>
                                </div>
                            </div>

                            <div class="text-xs text-[var(--color-text)] opacity-50">
                                Criado em {{ $position->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Estado Vazio -->
                <div class="px-6 py-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-briefcase text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-[var(--color-text)] mb-2">Nenhum cargo encontrado</h3>
                    <p class="text-[var(--color-text)] opacity-70 mb-4">
                        @if(request('search') || request('department_id'))
                            Não foram encontrados cargos com os filtros aplicados.
                        @else
                            Comece criando o primeiro cargo do sistema.
                        @endif
                    </p>
                    @if(request('search') || request('department_id'))
                        <a
                            href="{{ route('cargos.index') }}"
                            class="inline-flex items-center gap-2 text-[var(--color-main)] hover:text-[var(--color-main-dark)] font-medium">
                            <i class="fas fa-times"></i>
                            Limpar filtros
                        </a>
                    @else
                        <button
                            id="btn-new-position-empty"
                            class="inline-flex items-center gap-2 bg-[var(--color-main)] hover:bg-[var(--color-main-dark)] text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                            <i class="fas fa-plus"></i>
                            Criar primeiro cargo
                        </button>
                    @endif
                </div>
            @endif
        </div>

        <!-- Paginação -->
        @if($positions->hasPages())
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="text-sm text-[var(--color-text)] opacity-70">
                    Mostrando {{ $positions->firstItem() }} a {{ $positions->lastItem() }} de {{ $positions->total() }} resultados
                </div>
                <div>
                    {{ $positions->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection
