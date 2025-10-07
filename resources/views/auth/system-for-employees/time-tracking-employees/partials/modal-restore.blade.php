<div id="restore-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-[var(--color-background)] rounded-xl shadow-2xl max-w-md w-full mx-4">
        <div class="p-6 border-b border-[var(--color-text)]/10">
            <h3 class="text-xl font-semibold text-green-600 dark:text-green-400">
                <i class="fa-solid fa-rotate-left mr-2"></i>
                Restaurar Registro
            </h3>
        </div>

        <div class="p-6">
            <p class="text-[var(--color-text)]">
                Tem certeza que deseja restaurar este registro de ponto?
            </p>
        </div>

        <div class="p-6 border-t border-[var(--color-text)]/10 flex justify-end gap-3">
            <button type="button" onclick="window.closeRestoreModal()"
                class="px-6 py-2 border border-[var(--color-text)]/20 rounded-lg hover:bg-[var(--color-text)]/5 text-[var(--color-text)] transition-colors">
                Cancelar
            </button>
            <form method="POST" id="restore-form" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit"
                    class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    <i class="fa-solid fa-check mr-2"></i>
                    Sim, Restaurar
                </button>
            </form>
        </div>
    </div>
</div>
