@if($paginator->hasPages())
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
        <!-- Informações da paginação -->
        <div class="text-sm text-[var(--color-text)] opacity-70">
            Mostrando {{ $paginator->firstItem() }} a {{ $paginator->lastItem() }} de {{ $paginator->total() }} resultados
        </div>

        <!-- Controles de paginação -->
        <div class="flex items-center justify-center gap-1">
            {{-- Botão: Ir para o início --}}
            @if($paginator->currentPage() > 1)
                <button type="button" data-page="1"
                        class="pagination-btn inline-flex items-center px-3 py-2 text-sm bg-[var(--color-background)] border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 text-[var(--color-text)] transition-colors duration-200"
                        title="Primeira página">
                    <i class="fa-solid fa-angles-left"></i>
                </button>
            @else
                <span class="inline-flex items-center px-3 py-2 text-sm bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-400 cursor-not-allowed">
                    <i class="fa-solid fa-angles-left"></i>
                </span>
            @endif

            {{-- Botão: Página anterior --}}
            @if($paginator->currentPage() > 1)
                <button type="button" data-page="{{ $paginator->currentPage() - 1 }}"
                        class="pagination-btn inline-flex items-center px-3 py-2 text-sm bg-[var(--color-background)] border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 text-[var(--color-text)] transition-colors duration-200"
                        title="Página anterior">
                    <i class="fa-solid fa-angle-left"></i>
                </button>
            @else
                <span class="inline-flex items-center px-3 py-2 text-sm bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-400 cursor-not-allowed">
                    <i class="fa-solid fa-angle-left"></i>
                </span>
            @endif

            {{-- Números das páginas --}}
            @if($paginationInfo['start'] > 1)
                <button type="button" data-page="1"
                        class="pagination-btn inline-flex items-center px-3 py-2 text-sm bg-[var(--color-background)] border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 text-[var(--color-text)] transition-colors duration-200">
                    1
                </button>
                @if($paginationInfo['start'] > 2)
                    <span class="inline-flex items-center px-3 py-2 text-sm text-gray-400">...</span>
                @endif
            @endif

            @for($page = $paginationInfo['start']; $page <= $paginationInfo['end']; $page++)
                @if($page == $paginator->currentPage())
                    <span class="inline-flex items-center px-3 py-2 text-sm bg-[var(--color-main)] border border-[var(--color-main)] rounded-lg text-white font-medium">
                        {{ $page }}
                    </span>
                @else
                    <button type="button" data-page="{{ $page }}"
                            class="pagination-btn inline-flex items-center px-3 py-2 text-sm bg-[var(--color-background)] border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 text-[var(--color-text)] transition-colors duration-200">
                        {{ $page }}
                    </button>
                @endif
            @endfor

            @if($paginationInfo['end'] < $paginator->lastPage())
                @if($paginationInfo['end'] < $paginator->lastPage() - 1)
                    <span class="inline-flex items-center px-3 py-2 text-sm text-gray-400">...</span>
                @endif
                <button type="button" data-page="{{ $paginator->lastPage() }}"
                        class="pagination-btn inline-flex items-center px-3 py-2 text-sm bg-[var(--color-background)] border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 text-[var(--color-text)] transition-colors duration-200">
                    {{ $paginator->lastPage() }}
                </button>
            @endif

            {{-- Botão: Próxima página --}}
            @if($paginator->hasMorePages())
                <button type="button" data-page="{{ $paginator->currentPage() + 1 }}"
                        class="pagination-btn inline-flex items-center px-3 py-2 text-sm bg-[var(--color-background)] border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 text-[var(--color-text)] transition-colors duration-200"
                        title="Próxima página">
                    <i class="fa-solid fa-angle-right"></i>
                </button>
            @else
                <span class="inline-flex items-center px-3 py-2 text-sm bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-400 cursor-not-allowed">
                    <i class="fa-solid fa-angle-right"></i>
                </span>
            @endif

            {{-- Botão: Ir para o final --}}
            @if($paginator->currentPage() < $paginator->lastPage())
                <button type="button" data-page="{{ $paginator->lastPage() }}"
                        class="pagination-btn inline-flex items-center px-3 py-2 text-sm bg-[var(--color-background)] border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 text-[var(--color-text)] transition-colors duration-200"
                        title="Última página">
                    <i class="fa-solid fa-angles-right"></i>
                </button>
            @else
                <span class="inline-flex items-center px-3 py-2 text-sm bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-400 cursor-not-allowed">
                    <i class="fa-solid fa-angles-right"></i>
                </span>
            @endif
        </div>
    </div>
@endif
