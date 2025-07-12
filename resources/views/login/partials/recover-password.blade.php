<div id="modal-forgot" class="fixed inset-0 flex items-start justify-end z-50 hidden">
    <!-- Overlay escuro -->
    <div id="modal-overlay" class="absolute inset-0 bg-black opacity-55"></div>
    <!-- Modal container -->
    <div id="modal-container"
        class="relative bg-white rounded-lg p-6 max-w-sm w-full shadow-lg transform translate-x-full transition-all duration-300">
        <h2 class="text-lg font-semibold mb-4">Redefinir senha</h2>
        <form>
            <input type="email" placeholder="Seu e-mail cadastrado" class="w-full border rounded px-3 py-2 mb-4"
                required>
            <div class="flex justify-end gap-2">
                <button type="button" id="close-modal" class="px-4 py-2 rounded bg-gray-300">Cancelar</button>
                <button type="submit" class="px-4 py-2 rounded bg-[var(--color-main)] text-white">Enviar</button>
            </div>
        </form>
    </div>
</div>
