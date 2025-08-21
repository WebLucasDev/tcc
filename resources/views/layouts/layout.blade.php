<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css', 'resources/js/alerts.js', 'resources/js/layout.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>@yield('title') - Metre Ponto</title>
</head>
    <body>
        <div class="flex min-h-screen bg-[var(--color-background)]">

            <aside class="bg-[var(--color-background)] shadow-2xl/55 w-64 min-h-screen flex flex-col transition-all duration-300 ease-in-out">

                <div class="flex items-center justify-center p-4">
                    <img src="/imgs/logo.svg" alt="Logo Metre Sistemas">
                </div>

                <nav class="flex-1 px-4 py-6">
                    <ul class="space-y-2">

                        <li>
                            <a href="#"
                            class="menu-item flex items-center px-4 py-3 rounded-lg text-[var(--color-text)] hover:text-[var(--color-main)]">
                                <i class="fa-solid fa-chart-line w-5 h-5 mr-3"></i>
                                <span class="sidebar-text">Dashboard</span>
                            </a>
                        </li>

                        <li>
                            <button
                                class="dropdown-btn menu-item flex items-center justify-between w-full px-4 py-3 rounded-lg text-[var(--color-text)] hover:text-[var(--color-main)] focus:outline-none"
                                data-target="register-menu">
                                <div class="flex items-center">
                                    <i class="fa-solid fa-book w-5 h-5 mr-3"></i>
                                    <span class="sidebar-text">Cadastros</span>
                                </div>
                                <i class="fa-solid fa-chevron-down dropdown-icon transition-transform duration-300 ease-in-out sidebar-text"></i>
                            </button>
                            <ul class="dropdown-content ml-6 mt-2 space-y-1 overflow-hidden transition-all duration-300 ease-in-out max-h-0" id="register-menu">
                                <li>
                                    <a href="#" class="flex items-center px-4 py-2 rounded-lg text-sm text-[var(--color-text)] hover:text-[var(--color-main)] hover:bg-gray-100/10">
                                        <i class="fa-solid fa-users w-4 h-4 mr-3"></i>
                                        Funcionários
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="flex items-center px-4 py-2 rounded-lg text-sm text-[var(--color-text)] hover:text-[var(--color-main)] hover:bg-gray-100/10">
                                        <i class="fa-solid fa-building w-4 h-4 mr-3"></i>
                                        Departamentos
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="flex items-center px-4 py-2 rounded-lg text-sm text-[var(--color-text)] hover:text-[var(--color-main)] hover:bg-gray-100/10">
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
                            <ul class="dropdown-content ml-6 mt-2 space-y-1 overflow-hidden transition-all duration-300 ease-in-out max-h-0" id="timecard-menu">
                                <li>
                                    <a href="#" class="flex items-center px-4 py-2 rounded-lg text-sm text-[var(--color-text)] hover:text-[var(--color-main)] hover:bg-gray-100/10">
                                        <i class="fa-solid fa-clock-four w-4 h-4 mr-3"></i>
                                        Registro de Ponto
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="flex items-center px-4 py-2 rounded-lg text-sm text-[var(--color-text)] hover:text-[var(--color-main)] hover:bg-gray-100/10">
                                        <i class="fa-solid fa-calendar-days w-4 h-4 mr-3"></i>
                                        Espelho de Ponto
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="flex items-center px-4 py-2 rounded-lg text-sm text-[var(--color-text)] hover:text-[var(--color-main)] hover:bg-gray-100/10">
                                        <i class="fa-solid fa-user-clock w-4 h-4 mr-3"></i>
                                        Horas Extras
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="flex items-center px-4 py-2 rounded-lg text-sm text-[var(--color-text)] hover:text-[var(--color-main)] hover:bg-gray-100/10">
                                        <i class="fa-solid fa-calendar-xmark w-4 h-4 mr-3"></i>
                                        Faltas e Atrasos
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <button
                                class="dropdown-btn menu-item flex items-center justify-between w-full px-4 py-3 rounded-lg text-[var(--color-text)] hover:text-[var(--color-main)] focus:outline-none whitespace-nowrap"
                                data-target="payroll-menu">
                                <div class="flex items-center whitespace-nowrap">
                                    <i class="fa-solid fa-file-invoice-dollar w-5 h-5 mr-3"></i>
                                    <span class="sidebar-text">Folha de Pagamento</span>
                                </div>
                                <i class="fa-solid fa-chevron-down dropdown-icon transition-transform duration-300 ease-in-out sidebar-text"></i>
                            </button>
                            <ul class="dropdown-content ml-6 mt-2 space-y-1 overflow-hidden transition-all duration-300 ease-in-out max-h-0" id="payroll-menu">
                                <li>
                                    <a href="#" class="flex items-center px-4 py-2 rounded-lg text-sm text-[var(--color-text)] hover:text-[var(--color-main)] hover:bg-gray-100/10">
                                        <i class="fa-solid fa-calculator w-4 h-4 mr-3"></i>
                                        Cálculo da Folha
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="flex items-center px-4 py-2 rounded-lg text-sm text-[var(--color-text)] hover:text-[var(--color-main)] hover:bg-gray-100/10">
                                        <i class="fa-solid fa-money-bill w-4 h-4 mr-3"></i>
                                        Holerites
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="flex items-center px-4 py-2 rounded-lg text-sm text-[var(--color-text)] hover:text-[var(--color-main)] hover:bg-gray-100/10">
                                        <i class="fa-solid fa-percent w-4 h-4 mr-3"></i>
                                        Descontos e Benefícios
                                    </a>
                                </li>
                            </ul>
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

            <div class="flex-1 flex flex-col overflow-hidden">
                <header class="bg-[var(--color-background)] shadow-2xl/25 px-6 py-4 flex items-center justify-between">

                    <div class="flex items-center">
                        <button class="mr-4" id="toggle-sidebar">
                            <i class="fa-solid fa-bars text-[var(--color-text)] hover:text-[var(--color-main)]"></i>
                        </button>

                        <!-- Breadcrumb -->
                        <nav class="flex items-center space-x-2 text-sm">
                            <div class="flex items-center space-x-2">
                                @if(isset($breadcrumbs) && count($breadcrumbs) > 0)
                                    @foreach($breadcrumbs as $index => $breadcrumb)
                                        @if($index > 0)
                                            <i class="fa-solid fa-chevron-right text-[var(--color-text)] text-xs"></i>
                                        @endif

                                        @if($loop->last)
                                            <span class="text-[var(--color-main)] font-medium">
                                                {{ $breadcrumb['label'] }}
                                            </span>
                                        @else
                                            @if(isset($breadcrumb['url']) && $breadcrumb['url'])
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
                                    <span class="text-[var(--color-text)] font-medium">Dashboard</span>
                                @endif
                            </div>
                        </nav>
                    </div>

                    <div class="flex items-center space-x-5">
                        <button class="p-2 hover:bg-[var(--color-text)] text-[var(--color-text)] hover:text-[var(--color-main)] rounded-full transition-all duration-300">
                            <i class="fa-solid fa-moon"></i>
                        </button>

                        <form method="POST" action="{{ route('logout') ?? '#' }}">
                            @csrf
                           <button class="p-2 hover:bg-[var(--color-text)] text-[var(--color-text)] hover:text-[var(--color-main)] rounded-full transition-all duration-300">
                                <i class="fa-solid fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto p-6">
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
