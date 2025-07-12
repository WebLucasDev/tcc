<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <div class="min-w-screen min-h-screen flex flex-col items-center justify-center bg-[var(--color-background)]">

        <div class="flex flex-col max-w-md p-6 sm:p-10 border rounded-md border-[var(--color-main)] shadow-2xl/55">
            <div class="flex flex-col justify-center items-center mb-8">
                <img src="/imgs/logo.svg" alt="Logo Metre Sistemas" class="mb-4">
                <h1 class="text-2xl text-(--color-text)">Acesse sua conta</h1>
            </div>
            <form novalidate="" action="" class="space-y-12">
                <div class="space-y-4">
                    <div>
                        <input type="email" name="email" id="email" placeholder="Email" required
                            pattern="^[a-zA-Z0-9._%+-]+@metresistemas\.com\.br$"
                            class="w-full px-4 py-2 text-start border rounded-md border-[var(--color-main)]">
                    </div>
                    <div>
                        <input type="password" name="password" id="password" placeholder="Senha" required
                            class="w-full px-4 py-2 text-start border rounded-md border-[var(--color-main)]">
                    </div>
                    <div>
                        <button class="text-xs hover:underline text-[var(--color-main)]" id="forgot-password">Esqueci
                            minha senha</button>
                    </div>
                    <div>
                        <div>
                            <button type="submit"
                                class="w-full px-8 py-3 font-semibold rounded-md bg-[var(--color-main)] text-white hover:outline-double outline-orange-700 cursor-pointer">Entrar</button>
                        </div>
            </form>
        </div>

    </div>
    @include('login.partials.recover-password')

</body>

</html>
