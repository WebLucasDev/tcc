<div id="modal-forgot-password" class="fixed inset-0 flex items-start justify-end z-50 hidden">

    <div class="absolute inset-0 bg-black opacity-55" onclick="closeModalFunc()"></div>

    <div id="modal-container"
        class="relative bg-[var(--color-background)] rounded-md p-6 max-w-sm w-full min-h-full shadow-lg transform translate-x-full transition-all duration-300">

        <div onclick="closeModalFunc()" class="flex items-center gap-2 cursor-pointer mb-8 text-sm text-[var(--color-text)] hover:text-[var(--color-main)]">
            <i class="fa-solid fa-arrow-left"></i>
            <p>Voltar</p>
        </div>

        <div class="flex flex-col justify-center">
            <h2 class="text-[var(--color-text)] text-lg font-semibold mb-4">Redefinir senha</h2>
            <p class="text-[var(--color-text)] text-sm text-justify font-light mb-10">
                Informe o endereço de e-mail correspondente a sua conta. Iremos lhe encaminhar as instruções para alterar sua senha.
            </p>

            <form action="{{ route('forgot-password.send') }}" method="POST">
                @csrf
                <label for="email" class="text-xs text-[var(--color-text)]">E-mail <span class="text-red-500">*</span></label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    placeholder="Seu e-mail cadastrado"
                    class="w-full border rounded px-3 py-2 mb-4 focus:outline-none focus:ring-2 focus:ring-[var(--color-main)]"
                    value="{{ old('email') }}"
                    required>

                @error('email')
                    <p class="text-red-500 text-xs mb-4">{{ $message }}</p>
                @enderror

                <div class="flex justify-start gap-2">
                    <button
                        type="submit"
                        class="px-8 py-2 rounded bg-[var(--color-main)] hover:outline-double outline-orange-700 cursor-pointer text-white transition-all">
                        Redefinir
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
