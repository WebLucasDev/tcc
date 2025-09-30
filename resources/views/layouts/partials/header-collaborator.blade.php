<header class="bg-[var(--color-background)] shadow-2xl/25 px-6 py-4 flex items-center justify-between">

    <div class="flex items-center">
        <button class="mr-4" id="toggle-sidebar">
            <i class="fa-solid fa-bars text-[var(--color-text)] hover:text-[var(--color-main)]"></i>
        </button>

        <nav class="flex items-center space-x-2 text-sm">
            <div class="flex items-center space-x-2">
                @if (isset($breadcrumbs) && count($breadcrumbs) > 0)
                    @foreach ($breadcrumbs as $index => $breadcrumb)
                        @if ($index > 0)
                            <i class="fa-solid fa-chevron-right text-[var(--color-text)] text-xs"></i>
                        @endif

                        @if ($loop->last)
                            <span class="text-[var(--color-main)] font-medium">
                                {{ $breadcrumb['label'] }}
                            </span>
                        @else
                            @if (isset($breadcrumb['url']) && $breadcrumb['url'])
                                <a href="{{ $breadcrumb['url'] }}"
                                    class="text-[var(--color-text)] hover:text-[var(--color-main)] transition-colors duration-200">
                                    {{ $breadcrumb['label'] }}
                                </a>
                            @else
                                <span class="text-[var(--color-text)]">
                                    {{ $breadcrumb['label'] }}
                                </span>
                            @endif
                        @endif
                    @endforeach
                @else
                    <span class="text-[var(--color-text)] font-medium">
                        <i class="fa-solid fa-user-circle mr-2"></i>
                        Área do Colaborador
                    </span>
                @endif
            </div>
        </nav>
    </div>

    <div class="flex items-center space-x-5">
        <!-- Botão de bater ponto rápido -->
        <button
            class="bg-[var(--color-main)] text-white px-4 py-2 rounded-lg hover:bg-[var(--color-main)]/80 transition-all duration-300 flex items-center">
            <i class="fa-solid fa-clock mr-2"></i>
            Bater Ponto
        </button>

        <button
            onclick="toggleTheme()"
            class="p-2 hover:bg-[var(--color-text)] text-[var(--color-text)] hover:text-[var(--color-main)] rounded-full transition-all duration-300">
            <i class="fa-solid fa-moon"></i>
        </button>

        <form method="POST" action="{{ route('logout') ?? '#' }}">
            @csrf
            <button
                class="p-2 hover:bg-[var(--color-text)] text-[var(--color-text)] hover:text-[var(--color-main)] rounded-full transition-all duration-300">
                <i class="fa-solid fa-sign-out-alt"></i>
            </button>
        </form>
    </div>
</header>
