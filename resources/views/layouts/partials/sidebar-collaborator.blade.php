<aside id="sidebar" class="bg-[var(--color-background)] shadow-2xl/55 w-64 min-h-screen flex flex-col transition-all duration-300 ease-in-out fixed md:relative z-40 -translate-x-full md:translate-x-0 left-0 top-0">

    <div class="flex items-center justify-center p-4 pt-6">
        <img src="/imgs/logo.svg" alt="Logo Metre Sistemas">
    </div>

    <nav class="flex-1 px-4 py-6">
        <ul class="space-y-2">

            <li>
                <a href="{{ route('system-for-employees.dashboard.index') }}"
                    class="menu-item flex items-center px-4 py-3 rounded-lg text-[var(--color-text)] hover:text-[var(--color-main)]">
                    <i class="fa-solid fa-chart-line w-5 h-5 mr-3"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            </li>

            <li>
                <a href="{{ route('system-for-employees.time-tracking.index') }}"
                    class="menu-item flex items-center px-4 py-3 rounded-lg text-[var(--color-text)] hover:text-[var(--color-main)]">
                    <i class="fa-solid fa-clock w-5 h-5 mr-3"></i>
                    <span class="sidebar-text">Bater Ponto</span>
                </a>
            </li>

            <li>
                <a href="{{ route('system-for-employees.solicitation.index') }}"
                    class="menu-item flex items-center px-4 py-3 rounded-lg text-[var(--color-text)] hover:text-[var(--color-main)]">
                    <i class="fa-solid fa-file-alt w-5 h-5 mr-3"></i>
                    <span class="sidebar-text">Solicitações</span>
                </a>
            </li>

            <li>
                <a href="{{ route('system-for-employees.comp-time.index') }}"
                    class="menu-item flex items-center px-4 py-3 rounded-lg text-[var(--color-text)] hover:text-[var(--color-main)]">
                    <i class="fa-solid fa-calendar-check w-5 h-5 mr-3"></i>
                    <span class="sidebar-text">Banco de Horas</span>
                </a>
            </li>

            <li>
                <a href="{{ route('system-for-employees.registrations.index') }}"
                    class="menu-item flex items-center px-4 py-3 rounded-lg text-[var(--color-text)] hover:text-[var(--color-main)]">
                    <i class="fa-solid fa-user-edit w-5 h-5 mr-3"></i>
                    <span class="sidebar-text">Meus Dados</span>
                </a>
            </li>

        </ul>
    </nav>

    <div class="p-4 border-t border-[var(--color-text)]">
        <div class="flex items-center">
            <div class="w-10 h-10 border-[var(--color-text)] rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-user text-[var(--color-main)]"></i>
            </div>
            <div id="user-info">
                <p class="text-sm font-medium text-[var(--color-main)]">{{ auth('collaborator')->user()->name ?? 'Colaborador' }}</p>
                <p class="text-xs text-[var(--color-main)]">{{ auth('collaborator')->user()->email ?? 'colaborador@email.com' }}</p>
            </div>
        </div>
    </div>
</aside>
