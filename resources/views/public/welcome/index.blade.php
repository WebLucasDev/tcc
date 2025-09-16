<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css', 'resources/js/welcome.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="/imgs/favicon.ico" type="image/x-icon">
    <title>Metre Ponto - Sistema de Registro de Ponto Eletrônico</title>

    <style>
        :root {
            --primary-color: #E13E16;
            --primary-dark: #B82F0E;
            --primary-light: #FF6B4A;
            --gradient-start: #E13E16;
            --gradient-end: #FF6B4A;
        }

        body {
            font-family: 'Inter', sans-serif !important;
            overflow-x: hidden;
            scroll-behavior: smooth;
        }

        /* Splash Screen Styles */
        .splash-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            opacity: 1;
            visibility: visible;
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .splash-screen.fade-out {
            opacity: 0;
            visibility: hidden;
            transform: scale(1.1);
        }

        .splash-logo {
            width: 120px;
            height: auto;
            margin-bottom: 2rem;
            animation: logoFloat 3s ease-in-out infinite;
            filter: brightness(0) invert(1);
        }

        .splash-text {
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            animation: textGlow 2s ease-in-out infinite alternate;
        }

        .splash-tagline {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.2rem;
            font-weight: 300;
            text-align: center;
            max-width: 400px;
            line-height: 1.5;
            animation: fadeInUp 1s ease-out 0.5s both;
        }

        .loading-animation {
            margin-top: 3rem;
            width: 60px;
            height: 60px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        @keyframes textGlow {
            from { text-shadow: 0 0 20px rgba(255, 255, 255, 0.5); }
            to { text-shadow: 0 0 30px rgba(255, 255, 255, 0.8); }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Main Content Styles */
        .main-content {
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

        .main-content.visible {
            opacity: 1;
        }

        .hero-section {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            position: relative;
            overflow: hidden;
            will-change: transform;
        }

        .hero-bg-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.1;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23E13E16' fill-opacity='0.4'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .floating-particles {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            background: var(--primary-color);
            border-radius: 50%;
            animation: float 20s infinite linear;
            will-change: transform;
        }

        .particle:nth-child(1) { width: 4px; height: 4px; left: 10%; animation-delay: -2s; }
        .particle:nth-child(2) { width: 6px; height: 6px; left: 20%; animation-delay: -4s; }
        .particle:nth-child(3) { width: 3px; height: 3px; left: 30%; animation-delay: -6s; }
        .particle:nth-child(4) { width: 5px; height: 5px; left: 40%; animation-delay: -8s; }
        .particle:nth-child(5) { width: 2px; height: 2px; left: 50%; animation-delay: -10s; }
        .particle:nth-child(6) { width: 4px; height: 4px; left: 60%; animation-delay: -12s; }
        .particle:nth-child(7) { width: 6px; height: 6px; left: 70%; animation-delay: -14s; }
        .particle:nth-child(8) { width: 3px; height: 3px; left: 80%; animation-delay: -16s; }
        .particle:nth-child(9) { width: 5px; height: 5px; left: 90%; animation-delay: -18s; }

        @keyframes float {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100px) rotate(360deg);
                opacity: 0;
            }
        }

        .scroll-indicator {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            color: white;
            text-align: center;
            cursor: pointer;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateX(-50%) translateY(0); }
            40% { transform: translateX(-50%) translateY(-10px); }
            60% { transform: translateX(-50%) translateY(-5px); }
        }

        .section-reveal {
            opacity: 0;
            transform: translateY(60px);
            transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: opacity, transform;
        }

        .section-reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .gradient-text {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(225, 62, 22, 0.2);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }
    </style>
</head>
<body class="antialiased">
    <!-- Splash Screen -->
    <div id="splashScreen" class="splash-screen">
        <img src="/imgs/logo.svg" alt="Metre Ponto" class="splash-logo">
        <h1 class="splash-text">Metre Ponto</h1>
        <p class="splash-tagline">Sistema Inteligente de Registro de Ponto Eletrônico</p>
        <div class="loading-animation"></div>
    </div>

    <!-- Main Content -->
    <div id="mainContent" class="main-content">
        <!-- Navigation -->
        <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-300" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(229, 231, 235, 0.5);">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center space-x-3">
                        <img src="/imgs/logo.svg" alt="Metre Ponto" class="w-8 h-8 sm:w-10 sm:h-10">
                        <span class="text-xl sm:text-2xl font-bold text-gray-800">Metre Ponto</span>
                    </div>
                    <div class="hidden md:flex items-center space-x-6 lg:space-x-8">
                        <a href="#features" class="text-gray-600 hover:text-orange-600 transition-colors font-medium">Recursos</a>
                        <a href="#mobile-app" class="text-gray-600 hover:text-orange-600 transition-colors font-medium">App Mobile</a>
                        <a href="#admin-panel" class="text-gray-600 hover:text-orange-600 transition-colors font-medium">Painel Admin</a>
                        <a href="{{ route('login.index') }}" class="btn-primary text-white px-4 py-2 lg:px-6 lg:py-2 rounded-lg font-semibold transition-all hover:shadow-lg">
                            Acessar Sistema
                        </a>
                    </div>
                    <button class="md:hidden text-gray-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero-section min-h-screen flex items-center justify-center relative overflow-hidden">
            <div class="hero-bg-pattern"></div>
            <div class="floating-particles">
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white relative z-10">
                <div class="max-w-4xl mx-auto">
                    <img src="/imgs/logo.svg" alt="Metre Ponto" class="w-20 h-20 sm:w-24 sm:h-24 mx-auto mb-6 sm:mb-8 filter brightness-0 invert">
                    <h1 class="text-4xl sm:text-6xl lg:text-8xl font-black mb-4 sm:mb-6 leading-tight">
                        <span class="block">Metre</span>
                        <span class="block gradient-text">Ponto</span>
                    </h1>
                    <p class="text-lg sm:text-xl lg:text-2xl mb-6 sm:mb-8 text-gray-300 leading-relaxed max-w-3xl mx-auto px-4">
                        Revolucione o controle de ponto da sua empresa com nossa solução inteligente,
                        moderna e completamente integrada.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center px-4">
                        <a href="{{ route('login.index') }}" class="btn-primary text-white px-6 py-3 sm:px-8 sm:py-4 rounded-lg text-base sm:text-lg font-semibold transition-all hover:shadow-2xl inline-flex items-center justify-center w-full sm:w-auto">
                            <i class="fas fa-play mr-2 sm:mr-3"></i>
                            Começar Agora
                        </a>
                        <button onclick="document.getElementById('features').scrollIntoView({behavior: 'smooth'})" class="border-2 border-white text-white px-6 py-3 sm:px-8 sm:py-4 rounded-lg text-base sm:text-lg font-semibold hover:bg-white hover:text-gray-800 transition-all inline-flex items-center justify-center w-full sm:w-auto">
                            <i class="fas fa-info-circle mr-2 sm:mr-3"></i>
                            Saiba Mais
                        </button>
                    </div>
                </div>
            </div>

            <div class="scroll-indicator">
                <i class="fas fa-chevron-down text-xl sm:text-2xl mb-2"></i>
                <p class="text-xs sm:text-sm">Role para baixo</p>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-16 sm:py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 sm:mb-16 section-reveal">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-800 mb-4 sm:mb-6">
                        Por que escolher o <span class="gradient-text">Metre Ponto</span>?
                    </h2>
                    <p class="text-lg sm:text-xl text-gray-600 max-w-3xl mx-auto px-4">
                        Uma solução completa que combina tecnologia avançada com simplicidade de uso
                    </p>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 mb-12 sm:mb-16">
                    <div class="card-hover bg-white p-6 sm:p-8 rounded-2xl shadow-lg border border-gray-100 section-reveal">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-2xl flex items-center justify-center mb-4 sm:mb-6" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);">
                            <i class="fas fa-clock text-white text-xl sm:text-2xl"></i>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-3 sm:mb-4">Registro Inteligente</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Sistema automatizado de registro de ponto com geolocalização e validação em tempo real.
                        </p>
                    </div>

                    <div class="card-hover bg-white p-6 sm:p-8 rounded-2xl shadow-lg border border-gray-100 section-reveal">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-2xl flex items-center justify-center mb-4 sm:mb-6" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);">
                            <i class="fas fa-mobile-alt text-white text-xl sm:text-2xl"></i>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-3 sm:mb-4">App Mobile</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Aplicativo intuitivo para colaboradores registrarem ponto e fazerem solicitações.
                        </p>
                    </div>

                    <div class="card-hover bg-white p-6 sm:p-8 rounded-2xl shadow-lg border border-gray-100 sm:col-span-2 lg:col-span-1 section-reveal">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-2xl flex items-center justify-center mb-4 sm:mb-6" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);">
                            <i class="fas fa-chart-line text-white text-xl sm:text-2xl"></i>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-3 sm:mb-4">Dashboard Analítico</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Relatórios completos e análises avançadas para melhor gestão de recursos humanos.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mobile App Section -->
        <section id="mobile-app" class="py-16 sm:py-20 bg-gradient-to-br from-gray-50 to-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">
                    <div class="section-reveal order-2 lg:order-1">
                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-800 mb-4 sm:mb-6">
                            App Mobile <span class="gradient-text">Revolucionário</span>
                        </h2>
                        <p class="text-lg sm:text-xl text-gray-600 mb-6 sm:mb-8 leading-relaxed">
                            Desenvolvido em React Native para proporcionar a melhor experiência aos seus colaboradores.
                        </p>

                        <div class="space-y-4 sm:space-y-6">
                            <div class="flex items-start space-x-3 sm:space-x-4">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center flex-shrink-0" style="background-color: var(--primary-color);">
                                    <i class="fas fa-map-marker-alt text-white text-sm sm:text-base"></i>
                                </div>
                                <div>
                                    <h4 class="text-base sm:text-lg font-semibold text-gray-800 mb-1 sm:mb-2">Registro com Geolocalização</h4>
                                    <p class="text-sm sm:text-base text-gray-600">Validação automática da localização do colaborador no momento do registro.</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3 sm:space-x-4">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center flex-shrink-0" style="background-color: var(--primary-color);">
                                    <i class="fas fa-edit text-white text-sm sm:text-base"></i>
                                </div>
                                <div>
                                    <h4 class="text-base sm:text-lg font-semibold text-gray-800 mb-1 sm:mb-2">Solicitações de Ajuste</h4>
                                    <p class="text-sm sm:text-base text-gray-600">Facilite pedidos de correção de horários e dados cadastrais diretamente pelo app.</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3 sm:space-x-4">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center flex-shrink-0" style="background-color: var(--primary-color);">
                                    <i class="fas fa-history text-white text-sm sm:text-base"></i>
                                </div>
                                <div>
                                    <h4 class="text-base sm:text-lg font-semibold text-gray-800 mb-1 sm:mb-2">Histórico Completo</h4>
                                    <p class="text-sm sm:text-base text-gray-600">Visualização detalhada do banco de horas e histórico de registros.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-reveal order-1 lg:order-2 flex justify-center">
                        <div class="relative">
                            <div class="w-64 h-80 sm:w-80 sm:h-96 rounded-3xl p-1 shadow-2xl" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);">
                                <div class="w-full h-full bg-white rounded-3xl flex items-center justify-center">
                                    <i class="fas fa-mobile-alt text-6xl sm:text-8xl text-gray-300"></i>
                                </div>
                            </div>
                            <div class="absolute -top-2 -right-2 sm:-top-4 sm:-right-4 w-16 h-16 sm:w-24 sm:h-24 bg-white rounded-full shadow-lg flex items-center justify-center">
                                <i class="fas fa-check text-base sm:text-2xl" style="color: var(--primary-color);"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Admin Panel Section -->
        <section id="admin-panel" class="py-16 sm:py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">
                    <div class="section-reveal order-2 lg:order-1">
                        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-6 sm:p-8 shadow-2xl">
                            <div class="flex items-center justify-between mb-4 sm:mb-6">
                                <img src="/imgs/logo.svg" alt="Metre Ponto" class="w-8 h-8 sm:w-10 sm:h-10 filter brightness-0 invert">
                                <div class="flex space-x-1 sm:space-x-2">
                                    <div class="w-2 h-2 sm:w-3 sm:h-3 bg-red-500 rounded-full"></div>
                                    <div class="w-2 h-2 sm:w-3 sm:h-3 bg-yellow-500 rounded-full"></div>
                                    <div class="w-2 h-2 sm:w-3 sm:h-3 bg-green-500 rounded-full"></div>
                                </div>
                            </div>

                            <div class="space-y-3 sm:space-y-4">
                                <div class="flex items-center space-x-2 sm:space-x-3 text-white text-sm sm:text-base">
                                    <i class="fas fa-chart-pie" style="color: var(--primary-color);"></i>
                                    <span>Dashboard Executivo</span>
                                </div>
                                <div class="flex items-center space-x-2 sm:space-x-3 text-white text-sm sm:text-base">
                                    <i class="fas fa-users" style="color: var(--primary-color);"></i>
                                    <span>Gestão de Colaboradores</span>
                                </div>
                                <div class="flex items-center space-x-2 sm:space-x-3 text-white text-sm sm:text-base">
                                    <i class="fas fa-building" style="color: var(--primary-color);"></i>
                                    <span>Controle de Departamentos</span>
                                </div>
                                <div class="flex items-center space-x-2 sm:space-x-3 text-white text-sm sm:text-base">
                                    <i class="fas fa-clock" style="color: var(--primary-color);"></i>
                                    <span>Registro de Ponto</span>
                                </div>
                                <div class="flex items-center space-x-2 sm:space-x-3 text-white text-sm sm:text-base">
                                    <i class="fas fa-file-alt" style="color: var(--primary-color);"></i>
                                    <span>Solicitações</span>
                                </div>
                                <div class="flex items-center space-x-2 sm:space-x-3 text-white text-sm sm:text-base">
                                    <i class="fas fa-piggy-bank" style="color: var(--primary-color);"></i>
                                    <span>Banco de Horas</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-reveal order-1 lg:order-2">
                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-800 mb-4 sm:mb-6">
                            Painel <span class="gradient-text">Administrativo</span>
                        </h2>
                        <p class="text-lg sm:text-xl text-gray-600 mb-6 sm:mb-8 leading-relaxed">
                            Dashboard completo para gestores com todas as ferramentas necessárias para uma administração eficiente.
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div class="bg-gray-50 p-4 sm:p-6 rounded-xl">
                                <h4 class="font-semibold text-gray-800 mb-1 sm:mb-2 text-sm sm:text-base">Dashboard</h4>
                                <p class="text-gray-600 text-xs sm:text-sm">Análises completas e métricas em tempo real</p>
                            </div>
                            <div class="bg-gray-50 p-4 sm:p-6 rounded-xl">
                                <h4 class="font-semibold text-gray-800 mb-1 sm:mb-2 text-sm sm:text-base">Cadastros</h4>
                                <p class="text-gray-600 text-xs sm:text-sm">Gestão de cargos, colaboradores e departamentos</p>
                            </div>
                            <div class="bg-gray-50 p-4 sm:p-6 rounded-xl">
                                <h4 class="font-semibold text-gray-800 mb-1 sm:mb-2 text-sm sm:text-base">Gestão de Ponto</h4>
                                <p class="text-gray-600 text-xs sm:text-sm">Controle total sobre registros e solicitações</p>
                            </div>
                            <div class="bg-gray-50 p-4 sm:p-6 rounded-xl">
                                <h4 class="font-semibold text-gray-800 mb-1 sm:mb-2 text-sm sm:text-base">Relatórios</h4>
                                <p class="text-gray-600 text-xs sm:text-sm">Banco de horas e relatórios personalizados</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-16 sm:py-20" style="background: linear-gradient(to right, var(--primary-color), var(--primary-light));">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="section-reveal">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-4 sm:mb-6">
                        Pronto para revolucionar sua empresa?
                    </h2>
                    <p class="text-lg sm:text-xl text-white/80 mb-6 sm:mb-8 max-w-2xl mx-auto px-4">
                        Junte-se às empresas que já modernizaram seu controle de ponto com o Metre Ponto.
                    </p>
                    <a href="{{ route('login.index') }}" class="bg-white px-6 py-3 sm:px-8 sm:py-4 rounded-lg text-base sm:text-lg font-semibold hover:bg-gray-100 transition-all inline-flex items-center shadow-lg hover:shadow-xl" style="color: var(--primary-color);">
                        <i class="fas fa-rocket mr-2 sm:mr-3"></i>
                        Comece sua transformação digital
                    </a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-8 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-4 md:mb-0">
                        <img src="/imgs/logo.svg" alt="Metre Ponto" class="w-8 h-8 sm:w-10 sm:h-10 filter brightness-0 invert">
                        <span class="text-xl sm:text-2xl font-bold">Metre Ponto</span>
                    </div>
                    <div class="text-gray-400 text-sm sm:text-base text-center md:text-right">
                        <p>&copy; {{ date('Y') }} Metre Ponto. Todos os direitos reservados.</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
