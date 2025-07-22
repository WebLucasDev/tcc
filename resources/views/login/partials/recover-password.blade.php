<div id="modal-forgot" class="fixed inset-0 flex items-start justify-end z-50 hidden">
    <!-- Overlay escuro -->
    <div id="modal-overlay" class="absolute inset-0 bg-black opacity-55"></div>
    <!-- Modal container -->
    <div id="modal-container"
        class="relative bg-white rounded-md p-6 max-w-sm w-full min-h-full shadow-lg transform translate-x-full transition-all duration-300">
        <div id="close-modal" class="flex items-center gap-2 cursor-pointer mb-8 text-sm text-[var(--color-text)] hover:text-[var(--color-main)]">
            <i class="fa-solid fa-arrow-left"></i>
            <p>Voltar</p>
        </div>
        <div class="flex flex-col justify-center">
            <h2 class="text-[var(--color-text)] text-lg font-semibold mb-4">Redefinir senha</h2>
            <p class="text-[var(--color-text)] text-sm text-justify font-light mb-10">Informe o endereço de e-mail correspondente a sua conta. Iremos lhe encaminhar as instruções para alterar sua senha.</p>
            <form>
                <label for="email" class="text-xs text-black">email <span>*</span></label>
                <input type="email" id="email" placeholder="Seu e-mail cadastrado" class="w-full border rounded px-3 py-2 mb-4"
                    required>
                <div class="flex justify-end gap-2">
                    <button type="submit" class="px-4 py-2 rounded bg-[var(--color-main)] hover:outline-double outline-orange-700 cursor-pointer text-white">Redefinir</button>
                </div>
            </form>
        </div>
    </div>
</div>
