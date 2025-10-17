<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css','resources/js/alerts.js','resources/js/layout.js','resources/js/loading.js','resources/js/theme.js'])
    <link rel="shortcut icon" href="/imgs/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>@yield('title') - Metre Ponto</title>
</head>
    <body>

        @include('layouts.partials.loading')

        <!-- Overlay para mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/30 z-30 hidden md:hidden"></div>

        <div class="flex min-h-screen bg-[var(--color-background)]">
            @if(Auth::guard('collaborator')->check())
                @include('layouts.partials.sidebar-collaborator')
            @else
                @include('layouts.partials.sidebar-user')
            @endif

            <div class="flex-1 flex flex-col overflow-hidden main-content transition-all duration-300 ease-in-out w-full md:w-auto">
                @if(Auth::guard('collaborator')->check())
                    @include('layouts.partials.header-collaborator')
                @else
                    @include('layouts.partials.header-user')
                @endif

                <main class="flex-1 overflow-y-auto p-6">
                    @yield('content')
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
