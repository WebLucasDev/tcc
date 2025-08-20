<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Estilos customizados -->
    <style>
        .sidebar-transition {
            transition: all 0.3s ease-in-out;
        }

        .menu-item {
            transition: all 0.2s ease-in-out;
        }

        .menu-item:hover {
            background-color: rgba(59, 130, 246, 0.1);
            border-left: 4px solid #3b82f6;
        }

        .menu-item.active {
            background-color: rgba(59, 130, 246, 0.2);
            border-left: 4px solid #3b82f6;
            color: #3b82f6;
        }

        .breadcrumb-item::after {
            content: '/';
            margin: 0 8px;
            color: #6b7280;
        }

        .breadcrumb-item:last-child::after {
            display: none;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar-transition bg-white shadow-lg w-64 min-h-screen flex flex-col">
            <!-- Logo/Header da Sidebar -->
            <div class="flex items-center justify-center p-4">
                <img src="/imgs/logo.svg" alt="Logo Metre Sistemas">
            </div>

            <!-- Menu de Navegação -->
            <nav class="flex-1 px-4 py-6 overflow-y-auto">
                <ul class="space-y-2">
                    <!-- Dashboard -->
                    <li>
                        <a href="#"
                           class="menu-item flex items-center px-4 py-3 rounded-lg text-gray-700 hover:text-blue-600">
                            <i class="fas fa-home w-5 h-5 mr-3"></i>
                            <span class="sidebar-text">Dashboard</span>
                        </a>
                    </li>

                    <!-- Usuários -->
                    <li>
                        <a href="#"
                           class="menu-item flex items-center px-4 py-3 rounded-lg text-gray-700 hover:text-blue-600">
                            <i class="fas fa-users w-5 h-5 mr-3"></i>
                            <span class="sidebar-text">Usuários</span>
                        </a>
                    </li>

                    <!-- Produtos -->
                    <li>
                        <a href="#"
                           class="menu-item flex items-center px-4 py-3 rounded-lg text-gray-700 hover:text-blue-600">
                            <i class="fas fa-box w-5 h-5 mr-3"></i>
                            <span class="sidebar-text">Produtos</span>
                        </a>
                    </li>

                    <!-- Pedidos -->
                    <li>
                        <a href="#"
                           class="menu-item flex items-center px-4 py-3 rounded-lg text-gray-700 hover:text-blue-600">
                            <i class="fas fa-shopping-cart w-5 h-5 mr-3"></i>
                            <span class="sidebar-text">Pedidos</span>
                        </a>
                    </li>

                    <!-- Relatórios -->
                    <li>
                        <a href="#"
                           class="menu-item flex items-center px-4 py-3 rounded-lg text-gray-700 hover:text-blue-600">
                            <i class="fas fa-chart-bar w-5 h-5 mr-3"></i>
                            <span class="sidebar-text">Relatórios</span>
                        </a>
                    </li>

                    <!-- Configurações -->
                    <li>
                        <a href="#"
                           class="menu-item flex items-center px-4 py-3 rounded-lg text-gray-700 hover:text-blue-600">
                            <i class="fas fa-cog w-5 h-5 mr-3"></i>
                            <span class="sidebar-text">Configurações</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Footer da Sidebar -->
            <div class="p-4 border-t border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-user text-gray-600"></i>
                    </div>
                    <div id="user-info" class="sidebar-transition">
                        <p class="text-sm font-medium text-gray-800">{{ auth()->user()->name ?? 'Usuário' }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()->email ?? 'usuario@email.com' }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Conteúdo Principal -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <!-- Botão de Toggle da Sidebar e Breadcrumb -->
                    <div class="flex items-center">
                        <button id="sidebar-toggle" class="mr-4 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-bars text-gray-600"></i>
                        </button>

                        <!-- Breadcrumb -->
                        <nav class="flex items-center text-sm text-gray-600">
                            @if(isset($breadcrumbs))
                                @foreach($breadcrumbs as $breadcrumb)
                                    <span class="breadcrumb-item {{ $loop->last ? 'text-gray-900 font-medium' : '' }}">
                                        @if($loop->last || !isset($breadcrumb['url']))
                                            {{ $breadcrumb['name'] }}
                                        @else
                                            <a href="{{ $breadcrumb['url'] }}" class="hover:text-blue-600 transition-colors">
                                                {{ $breadcrumb['name'] }}
                                            </a>
                                        @endif
                                    </span>
                                @endforeach
                            @else
                                <span class="breadcrumb-item">
                                    <a href="#" class="hover:text-blue-600 transition-colors">
                                        <i class="fas fa-home mr-1"></i>Início
                                    </a>
                                </span>
                                <span class="breadcrumb-item text-gray-900 font-medium">
                                    @yield('page-title', 'Página')
                                </span>
                            @endif
                        </nav>
                    </div>

                    <!-- Ações do Header -->
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <button class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                    <i class="fa-solid fa-moon text-gray-600 text-sm"></i>
                                </div>
                            </button>
                        </div>

                        <form method="POST" action="{{ route('logout') ?? '#' }}" class="inline">
                            @csrf
                            <button type="submit" class="p-2 rounded-lg hover:bg-red-100 transition-colors text-red-600">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Área de Conteúdo -->
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebar-toggle');
            const logoText = document.getElementById('logo-text');
            const userInfo = document.getElementById('user-info');
            const sidebarTexts = document.querySelectorAll('.sidebar-text');

            let isCollapsed = false;

            toggleBtn.addEventListener('click', function() {
                isCollapsed = !isCollapsed;

                if (isCollapsed) {
                    sidebar.classList.remove('w-64');
                    sidebar.classList.add('w-20');

                    logoText.style.display = 'none';
                    userInfo.style.display = 'none';

                    sidebarTexts.forEach(text => {
                        text.style.display = 'none';
                    });

                    // Centralizar ícones
                    const menuItems = document.querySelectorAll('.menu-item');
                    menuItems.forEach(item => {
                        item.classList.add('justify-center');
                    });
                } else {
                    sidebar.classList.remove('w-20');
                    sidebar.classList.add('w-64');

                    logoText.style.display = 'block';
                    userInfo.style.display = 'block';

                    sidebarTexts.forEach(text => {
                        text.style.display = 'inline';
                    });

                    // Voltar ao alinhamento normal
                    const menuItems = document.querySelectorAll('.menu-item');
                    menuItems.forEach(item => {
                        item.classList.remove('justify-center');
                    });
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
