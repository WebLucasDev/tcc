<aside class="bg-[var(--color-background)] shadow-2xl/55 w-64 min-h-screen flex flex-col transition-all duration-300 ease-in-out">

    <div class="flex items-center justify-center p-4">
        <img src="/imgs/logo.svg" alt="Logo Metre Sistemas">
    </div>

    <nav class="flex-1 px-4 py-6">
        <ul class="space-y-2">

            <li>
                <a href="{{ route('dashboard.index') }}"
                    class="menu-item flex items-center px-4 py-3 rounded-lg text-[var(--color-text)] hover:text-[var(--color-main)]">
                    <i class="fa-solid fa-chart-line w-5 h-5 mr-3"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            </li>

            <li>
                <button class="dropdown-btn menu-item flex items-center justify-between w-full px-4 py-3 rounded-lg text-[var(--color-text)] hover:text-[var(--color-main)] focus:outline-none"
                    data-target="register-menu">
                    <div class="flex items-center">
                        <i class="fa-solid fa-book w-5 h-5 mr-3"></i>
                        <span class="sidebar-text">Cadastros</span>
                    </div>
                    <i class="fa-solid fa-chevron-down dropdown-icon transition-transform duration-300 ease-in-out sidebar-text"></i>
                </button>
                <ul class="dropdown-content ml-6 mt-2 space-y-1 overflow-hidden transition-all duration-300 ease-in-out max-h-0"
                    id="register-menu">
                    <li>
                        <a href="{{ route('collaborator.index') }}"
                            class="flex items-center px-4 py-2 rounded-lg text-sm text-[var(--color-text)] hover:text-[var(--color-main)] hover:bg-gray-100/10">
                            <i class="fa-solid fa-users w-4 h-4 mr-3"></i>
                            Colaboradores
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('department.index') }}"
                            class="flex items-center px-4 py-2 rounded-lg text-sm text-[var(--color-text)] hover:text-[var(--color-main)] hover:bg-gray-100/10">
                            <i class="fa-solid fa-building w-4 h-4 mr-3"></i>
                            Departamentos
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('position.index') }}"
                            class="flex items-center px-4 py-2 rounded-lg text-sm text-[var(--color-text)] hover:text-[var(--color-main)] hover:bg-gray-100/10">
                            <i class="fa-solid fa-briefcase w-4 h-4 mr-3"></i>
                            Cargos
                        </a>
                    </li>
                </ul>
            </li>

            <li>
                <button
                    class="dropdown-btn menu-item flex items-center justify-between w-full px-4 py-3 rounded-lg text-[var(--color-text)] hover:text-[var(--color-main)] focus:outline-none"
                    data-target="timecard-menu">
                    <div class="flex items-center">
                        <i class="fa-solid fa-clock w-5 h-5 mr-3"></i>
                        <span class="sidebar-text">Gestão de Ponto</span>
                    </div>
                    <i class="fa-solid fa-chevron-down dropdown-icon transition-transform duration-300 ease-in-out sidebar-text"></i>
                </button>
                <ul class="dropdown-content ml-6 mt-2 space-y-1 overflow-hidden transition-all duration-300 ease-in-out max-h-0"
                    id="timecard-menu">
                    <li>
                        <a href="#"
                            class="flex items-center px-4 py-2 rounded-lg text-sm text-[var(--color-text)] hover:text-[var(--color-main)] hover:bg-gray-100/10">
                            <i class="fa-solid fa-clock-four w-4 h-4 mr-3"></i>
                            Registro de Ponto
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center px-4 py-2 rounded-lg text-sm text-[var(--color-text)] hover:text-[var(--color-main)] hover:bg-gray-100/10">
                            <i class="fa-solid fa-circle-exclamation w-4 h-4 mr-3"></i>
                            Solicitações
                        </a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="#"
                    class="menu-item flex items-center px-4 py-3 rounded-lg text-[var(--color-text)] hover:text-[var(--color-main)]">
                    <i class="fa-solid fa-file-invoice-dollar w-5 h-5 mr-3"></i>
                    <span class="sidebar-text">Folha de Pagamento</span>
                </a>
            </li>

            <li>
                <a href="#"
                    class="menu-item flex items-center px-4 py-3 rounded-lg text-[var(--color-text)] hover:text-[var(--color-main)]">
                    <i class="fa-solid fa-table-list w-5 h-5 mr-3"></i>
                    <span class="sidebar-text">Relatórios</span>
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
                <p class="text-sm font-medium text-[var(--color-main)]">{{ auth()->user()->name ?? 'Usuário' }}</p>
                <p class="text-xs text-[var(--color-main)]">{{ auth()->user()->email ?? 'usuario@email.com' }}</p>
            </div>
        </div>
    </div>
</aside>
