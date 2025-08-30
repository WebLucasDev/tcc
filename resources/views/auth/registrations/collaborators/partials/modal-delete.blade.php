<!-- Modal de Confirmação de Exclusão -->
<div id="delete-modal" class="fixed inset-0 z-50 hidden">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-black/85 transition-opacity"></div>

    <!-- Modal -->
    <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
        <div class="bg-[var(--color-background)] rounded-lg shadow-xl max-w-md w-full">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-[var(--color-text)]">
                    <i class="fa-solid fa-exclamation-triangle text-[var(--color-error)] mr-2"></i>
                    Confirmar Exclusão
                </h3>
            </div>

            <!-- Body -->
            <div class="px-6 py-4">
                <p class="text-[var(--color-text)] mb-4">
                    Tem certeza que deseja excluir o colaborador <span id="collaborator-name-display" class="font-semibold text-[var(--color-error)]"></span>?
                </p>
                <p class="text-sm text-[var(--color-text)] opacity-70">
                    Esta ação não pode ser desfeita.
                </p>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                <button
                    type="button"
                    id="cancel-delete-btn"
                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors duration-200">
                    <i class="fa-solid fa-times mr-1"></i>
                    Cancelar
                </button>
                <button
                    type="button"
                    id="confirm-delete-btn"
                    class="px-4 py-2 bg-[var(--color-error)] hover:bg-[var(--color-error)] text-white rounded-lg font-medium transition-colors duration-200">
                    <i class="fa-solid fa-trash mr-1"></i>
                    Excluir
                </button>
            </div>
        </div>
    </div>
</div>