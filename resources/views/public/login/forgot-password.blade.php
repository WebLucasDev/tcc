<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="shortcut icon" href="/imgs/favicon.ico" type="image/x-icon">
    <title>Recuperar Senha</title>
</head>
<body>
    <div class="min-w-screen min-h-screen flex flex-col items-center justify-center bg-[var(--color-background)]">
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full">
            <h2 class="text-2xl font-bold mb-6 text-center text-[var(--color-text)]">Redefinir Senha</h2>

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('forgot-password.process-reset') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                        Nova Senha <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[var(--color-main)]">
                        <button
                            type="button"
                            onclick="togglePassword('password')"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-[var(--color-main)] focus:outline-none">
                            <i class="fa-solid fa-eye-slash" id="password-icon"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                        Confirmar Senha <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            required
                            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[var(--color-main)]">
                        <button
                            type="button"
                            onclick="togglePassword('password_confirmation')"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-[var(--color-main)] focus:outline-none">
                            <i class="fa-solid fa-eye-slash" id="password_confirmation-icon"></i>
                        </button>
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full bg-[var(--color-main)] text-white py-2 px-4 rounded-md hover:outline-double outline-orange-700 focus:outline-none focus:ring-2 focus:ring-[var(--color-main)] transition-all">
                    Atualizar Senha
                </button>

                <div class="mt-4 text-center">
                    <a href="{{ route('login.index') }}" class="text-sm text-[var(--color-main)] hover:underline">
                        Voltar para o login
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-icon');

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>
</html>
