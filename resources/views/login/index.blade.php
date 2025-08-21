<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css', 'resources/js/login.js', 'resources/js/alerts.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Login</title>
</head>
<body>
    <div class="min-w-screen min-h-screen flex flex-col items-center justify-center bg-[var(--color-background)]">

        <x-error/>
        <x-success/>

        @include('login.partials.message-recover-password')

        <div class="flex flex-col max-w-md p-6 sm:p-10 border rounded-md border-[var(--color-main)] shadow-2xl/55">
            <div class="flex flex-col justify-center items-center mb-8">
                <img src="/imgs/logo.svg" alt="Logo Metre Sistemas" class="mb-4">
                <h1 class="text-2xl text-[var(--color-text)]">Acesse sua conta</h1>
            </div>

            <form action="{{ route('login.auth') }}" method="POST" class="space-y-12">
                @csrf
                <div class="space-y-4">
                    <div>
                        <input type="email" name="email" placeholder="Email" required
                            value="{{ old('email') }}"
                            class="w-full px-4 py-2 text-start border rounded-md border-[var(--color-main)]">
                    </div>
                    <div class="relative">
                        <input type="password" name="password" id="password" placeholder="Senha" required
                            class="w-full px-4 py-2 pr-10 text-start border rounded-md border-[var(--color-main)]">
                        <button type="button" onclick="showPasswordFunc()"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 md:text-[var(--color-text)] sm:text-[var(--color-main)] hover:text-[var(--color-main)] focus:outline-none">
                            <i class="fa-solid fa-eye-slash" id="eye-icon"></i>
                        </button>
                    </div>
                    <div class="flex items-center justify-between">
                        <button type="button" class="text-xs cursor-pointer hover:underline text-[var(--color-main)]" onclick="openModalFunc()">Esqueci minha senha</button>
                    </div>
                    <div>
                        <button type="submit"
                            class="w-full px-8 py-3 font-semibold rounded-md bg-[var(--color-main)] text-white hover:outline-double outline-orange-700 cursor-pointer">Entrar</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
    @include('login.partials.modal-forgot-password')

</body>

</html>
