<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css', 'resources/css/welcome.css', 'resources/js/welcome.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="/imgs/favicon.ico" type="image/x-icon">
    <title>Metre Ponto - Sistema de Registro de Ponto Eletrônico</title>
</head>
<body class="bg-gray-50 overflow-x-hidden">
    <!-- Splash Screen -->
    <div id="splash-screen" class="fixed inset-0 z-50 flex items-center justify-center bg-gradient-to-br from-orange-600 via-orange-500 to-orange-700">
        <div class="text-center">
            <img src="/imgs/logo.svg" alt="Logo Metre Sistemas">
            <h1 class="text-4xl md:text-6xl font-bold text-white mb-4 opacity-0" id="splash-title">Metre Ponto</h1>
            <p class="text-xl text-orange-100 opacity-0" id="splash-subtitle">Sistema de Registro de Ponto Eletrônico</p>
            <div class="mt-8 opacity-0" id="splash-loader">
                <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-current border-r-transparent text-white"></div>
            </div>
        </div>
    </div>

    <!-- Canvas para efeitos de mouse -->
    <canvas id="canvas-effects" class="fixed inset-0 z-0 pointer-events-none"></canvas>

    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-40 bg-white/90 backdrop-blur-md shadow-lg transition-all duration-300" id="navbar">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-clock text-3xl text-orange-600"></i>
                    <span class="text-2xl font-bold text-gray-800">Metre Ponto</span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#sobre" class="nav-link text-gray-700 hover:text-orange-600 transition-colors duration-300 smooth-scroll" data-section="sobre">Sobre</a>
                    <a href="#aplicativo" class="nav-link text-gray-700 hover:text-orange-600 transition-colors duration-300 smooth-scroll" data-section="aplicativo">Aplicativo</a>
                    <a href="#painel" class="nav-link text-gray-700 hover:text-orange-600 transition-colors duration-300 smooth-scroll" data-section="painel">Painel Admin</a>
                    <a href="#acesso" class="bg-orange-600 text-white px-6 py-2 rounded-full hover:bg-orange-700 transition-all duration-300 transform hover:scale-105 smooth-scroll">Acesse Agora</a>
                </div>

                <!-- Mobile Menu Button -->
                <button class="md:hidden text-gray-800 text-2xl" id="mobile-menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div class="md:hidden mt-4 pb-4 hidden" id="mobile-menu">
                <div class="flex flex-col space-y-4">
                    <a href="#sobre" class="nav-link text-gray-700 hover:text-orange-600 transition-colors duration-300 smooth-scroll" data-section="sobre">Sobre</a>
                    <a href="#aplicativo" class="nav-link text-gray-700 hover:text-orange-600 transition-colors duration-300 smooth-scroll" data-section="aplicativo">Aplicativo</a>
                    <a href="#painel" class="nav-link text-gray-700 hover:text-orange-600 transition-colors duration-300 smooth-scroll" data-section="painel">Painel Admin</a>
                    <a href="#acesso" class="bg-orange-600 text-white px-6 py-2 rounded-full hover:bg-orange-700 transition-all duration-300 text-center smooth-scroll">Acesse Agora</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden parallax-container">
        <div class="absolute inset-0 bg-gradient-to-br from-orange-600 via-orange-500 to-orange-700 opacity-90"></div>
        <div class="absolute inset-0 bg-black/20"></div>

        <!-- Parallax Background Elements -->
        <div class="absolute inset-0 parallax-bg" data-speed="0.5">
            <div class="absolute top-20 left-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-20 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 left-1/4 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        </div>

        <div class="relative z-10 text-center text-white px-6 max-w-4xl">
            <h1 class="text-5xl md:text-7xl font-bold mb-6 opacity-0 transform translate-y-10" id="hero-title">
                Metre Ponto
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-orange-100 opacity-0 transform translate-y-10" id="hero-subtitle">
                O sistema mais moderno de controle de ponto eletrônico
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center opacity-0 transform translate-y-10" id="hero-buttons">
                <button class="bg-white text-orange-600 px-8 py-4 rounded-full font-semibold hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-play mr-2"></i>Começar Agora
                </button>
                <button class="border-2 border-white text-white px-8 py-4 rounded-full font-semibold hover:bg-white hover:text-orange-600 transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-info-circle mr-2"></i>Saiba Mais
                </button>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white animate-bounce cursor-pointer smooth-scroll" onclick="document.getElementById('sobre').scrollIntoView({behavior: 'smooth', block: 'start'});">
            <i class="fas fa-chevron-down text-2xl"></i>
        </div>
    </section>

    <!-- Sobre Section -->
    <section id="sobre" class="py-20 bg-white relative overflow-hidden">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-6 fade-in">Sobre o Metre Ponto</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto fade-in">
                    Uma solução completa e moderna para o controle de ponto eletrônico,
                    oferecendo praticidade tanto para colaboradores quanto para gestores.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center p-8 rounded-2xl bg-gradient-to-br from-gray-50 to-gray-100 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-2 fade-in">
                    <div class="w-16 h-16 bg-orange-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-mobile-alt text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">App Mobile</h3>
                    <p class="text-gray-600">
                        Aplicativo intuitivo para colaboradores registrarem seus horários de forma rápida e segura.
                    </p>
                </div>

                <div class="text-center p-8 rounded-2xl bg-gradient-to-br from-gray-50 to-gray-100 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-2 fade-in">
                    <div class="w-16 h-16 bg-orange-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-chart-line text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Relatórios</h3>
                    <p class="text-gray-600">
                        Relatórios detalhados e análises em tempo real para uma gestão eficiente de recursos humanos.
                    </p>
                </div>

                <div class="text-center p-8 rounded-2xl bg-gradient-to-br from-gray-50 to-gray-100 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-2 fade-in">
                    <div class="w-16 h-16 bg-orange-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-shield-alt text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Segurança</h3>
                    <p class="text-gray-600">
                        Máxima segurança dos dados com criptografia avançada e conformidade com a LGPD.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Aplicativo Section -->
    <section id="aplicativo" class="py-20 bg-gradient-to-br from-gray-900 to-gray-800 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="container mx-auto px-6 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="fade-in">
                    <h2 class="text-4xl md:text-5xl font-bold mb-6">Aplicativo Mobile</h2>
                    <p class="text-xl text-gray-300 mb-8">
                        Developed with cutting-edge technology, our mobile app provides a seamless experience
                        for employees to clock in and out with just a few taps.
                    </p>

                    <div class="space-y-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-orange-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-fingerprint text-white"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold">Autenticação Biométrica</h4>
                                <p class="text-gray-400">Segurança máxima com reconhecimento biométrico</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-orange-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-map-marker-alt text-white"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold">Geolocalização</h4>
                                <p class="text-gray-400">Registro de ponto baseado em localização</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-orange-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-sync text-white"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold">Sincronização em Tempo Real</h4>
                                <p class="text-gray-400">Dados sempre atualizados e sincronizados</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex space-x-4 mt-8">
                        <a href="#" class="bg-black rounded-lg px-6 py-3 flex items-center space-x-3 hover:bg-gray-900 transition-colors">
                            <i class="fab fa-apple text-2xl"></i>
                            <div>
                                <div class="text-xs">Download na</div>
                                <div class="text-lg font-semibold">App Store</div>
                            </div>
                        </a>
                        <a href="#" class="bg-black rounded-lg px-6 py-3 flex items-center space-x-3 hover:bg-gray-900 transition-colors">
                            <i class="fab fa-google-play text-2xl"></i>
                            <div>
                                <div class="text-xs">Baixar no</div>
                                <div class="text-lg font-semibold">Google Play</div>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="relative fade-in">
                    <div class="relative mx-auto w-80 h-96 bg-gray-900 rounded-3xl p-2 shadow-2xl">
                        <div class="w-full h-full bg-gradient-to-br from-orange-600 to-orange-700 rounded-2xl flex items-center justify-center">
                            <div class="text-center">
                                <i class="fas fa-mobile-alt text-6xl text-white mb-4"></i>
                                <p class="text-white text-lg">Interface do App</p>
                            </div>
                        </div>
                    </div>
                    <div class="absolute -top-4 -right-4 w-24 h-24 bg-orange-600 rounded-full opacity-20 animate-ping"></div>
                    <div class="absolute -bottom-4 -left-4 w-16 h-16 bg-orange-600 rounded-full opacity-30"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Painel Administrativo Section -->
    <section id="painel" class="py-20 bg-white relative overflow-hidden">
        <div class="container mx-auto px-6">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="relative fade-in">
                    <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl p-8 shadow-2xl">
                        <div class="flex items-center justify-between mb-6">
                            <h4 class="text-white text-lg font-semibold">Dashboard Admin</h4>
                            <div class="flex space-x-2">
                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="bg-gray-800 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-gray-300">Colaboradores Online</span>
                                    <span class="text-green-400 font-bold">127</span>
                                </div>
                                <div class="w-full bg-gray-700 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full w-3/4"></div>
                                </div>
                            </div>

                            <div class="bg-gray-800 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-gray-300">Pontos Hoje</span>
                                    <span class="text-blue-400 font-bold">1,247</span>
                                </div>
                                <div class="w-full bg-gray-700 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full w-5/6"></div>
                                </div>
                            </div>

                            <div class="bg-gray-800 rounded-lg p-4">
                                <div class="grid grid-cols-3 gap-4 text-center">
                                    <div>
                                        <div class="text-2xl font-bold text-white">98%</div>
                                        <div class="text-gray-400 text-sm">Precisão</div>
                                    </div>
                                    <div>
                                        <div class="text-2xl font-bold text-white">24/7</div>
                                        <div class="text-gray-400 text-sm">Uptime</div>
                                    </div>
                                    <div>
                                        <div class="text-2xl font-bold text-white">5ms</div>
                                        <div class="text-gray-400 text-sm">Latência</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fade-in">
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-6">Painel Administrativo</h2>
                    <p class="text-xl text-gray-600 mb-8">
                        Interface web completa para gestores controlarem e analisarem todos os aspectos
                        do controle de ponto da empresa.
                    </p>

                    <div class="space-y-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-orange-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-white"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-800">Gestão de Colaboradores</h4>
                                <p class="text-gray-600">Cadastro, edição e controle completo de funcionários</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-orange-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-bar text-white"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-800">Relatórios Avançados</h4>
                                <p class="text-gray-600">Dashboards interativos e relatórios personalizados</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-orange-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-cog text-white"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-800">Configurações Flexíveis</h4>
                                <p class="text-gray-600">Personalização completa de horários e políticas</p>
                            </div>
                        </div>
                    </div>

                    <button class="bg-orange-600 text-white px-8 py-4 rounded-full font-semibold hover:bg-orange-700 transition-all duration-300 transform hover:scale-105 shadow-lg mt-8">
                        <i class="fas fa-external-link-alt mr-2"></i>Acessar Painel
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Acesse Agora Section -->
    <section id="acesso" class="py-20 bg-gradient-to-br from-orange-600 via-orange-500 to-orange-700 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="container mx-auto px-6 text-center relative z-10">
            <div class="max-w-4xl mx-auto fade-in">
                <h2 class="text-4xl md:text-6xl font-bold mb-6">Pronto para Começar?</h2>
                <p class="text-xl md:text-2xl text-orange-100 mb-12">
                    Transforme a gestão de ponto da sua empresa hoje mesmo.
                    Teste gratuitamente por 30 dias, sem compromisso.
                </p>

                <div class="grid md:grid-cols-3 gap-8 mb-12">
                    <div class="text-center">
                        <div class="text-4xl font-bold mb-2">30</div>
                        <div class="text-orange-200">Dias Grátis</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold mb-2">24/7</div>
                        <div class="text-orange-200">Suporte</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold mb-2">99.9%</div>
                        <div class="text-orange-200">Disponibilidade</div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button class="bg-white text-orange-600 px-8 py-4 rounded-full font-semibold hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-rocket mr-2"></i>Começar Teste Grátis
                    </button>
                    <button class="border-2 border-white text-white px-8 py-4 rounded-full font-semibold hover:bg-white hover:text-orange-600 transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-phone mr-2"></i>Falar com Vendas
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <i class="fas fa-clock text-3xl text-orange-600"></i>
                        <span class="text-2xl font-bold">Metre Ponto</span>
                    </div>
                    <p class="text-gray-400 mb-6 max-w-md">
                        Sistema completo de controle de ponto eletrônico, desenvolvido para oferecer
                        praticidade e segurança na gestão de recursos humanos.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook-f text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-linkedin-in text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="text-lg font-semibold mb-4">Produto</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Aplicativo Mobile</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Painel Admin</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Relatórios</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Integrações</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-semibold mb-4">Suporte</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Central de Ajuda</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Documentação</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Contato</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Status</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-12 pt-8 text-center">
                <p class="text-gray-400">
                    © 2025 Metre Ponto. Todos os direitos reservados.
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
