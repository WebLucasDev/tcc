<div id="cancel-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-[var(--color-background)] rounded-xl shadow-2xl max-w-md w-full mx-4">
        <div class="p-6 border-b border-[var(--color-text)]/10">
            <h3 class="text-xl font-semibold text-orange-600 dark:text-orange-400">
                <i class="fa-solid fa-rotate-left mr-2"></i>
                Cancelar Último Registro
            </h3>
        </div>

        <div class="p-6">
            <p class="text-[var(--color-text)] mb-4">
                Tem certeza que deseja <strong>cancelar o último registro de ponto</strong> deste dia?
            </p>
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-3">
                <p class="text-sm text-blue-700 dark:text-blue-300">
                    <i class="fa-solid fa-info-circle mr-2"></i>
                    O sistema irá remover automaticamente o último horário registrado.
                </p>
            </div>
        </div>

        <div class="p-6 border-t border-[var(--color-text)]/10 flex justify-end gap-3">
            <button type="button" onclick="window.closeCancelModal()"
                class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-[var(--color-text)] transition-colors">
                Não, Voltar
            </button>
            <form method="POST" id="cancel-form" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit"
                    class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition-colors">
                    <i class="fa-solid fa-rotate-left mr-2"></i>
                    Sim, Cancelar
                </button>
            </form>
        </div>
    </div>
</div>
