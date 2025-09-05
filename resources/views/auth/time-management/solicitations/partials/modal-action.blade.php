<!-- Modal para Ações -->
<div id="actionModal" class="fixed inset-0 z-50 hidden">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-black/85 transition-opacity"></div>

    <!-- Modal -->
    <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
        <div class="bg-[var(--color-background)] rounded-lg shadow-xl max-w-md w-full">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <h3 id="actionModalTitle" class="text-lg font-semibold text-[var(--color-text)]">Confirmar Ação</h3>
                    <button type="button" onclick="closeActionModal()" class="text-[var(--color-text)] opacity-70 hover:opacity-100 transition-opacity">
                        <i class="fa-solid fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Body -->
            <form id="actionForm" method="POST">
                @csrf
                @method('PATCH')

                <div class="px-6 py-4">
                    <div id="actionContent" class="mb-4">
                        <!-- Conteúdo dinâmico será inserido aqui -->
                    </div>

                    <div id="commentSection" class="hidden mb-4">
                        <label for="admin_comment" class="block text-sm font-medium text-[var(--color-text)] mb-2">Comentário</label>
                        <textarea id="admin_comment"
                                  name="admin_comment"
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent transition-all duration-200"
                                  placeholder="Digite um comentário sobre esta ação..."></textarea>
                        <div id="commentError" class="hidden text-[var(--color-error)] text-sm mt-1">Este campo é obrigatório</div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                    <button type="button"
                            onclick="closeActionModal()"
                            class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors duration-200">
                        <i class="fa-solid fa-times mr-1"></i>
                        Cancelar
                    </button>
                    <button type="submit"
                            id="confirmActionBtn"
                            class="px-4 py-2 text-white rounded-lg font-medium transition-colors duration-200">
                        <i class="fa-solid fa-check mr-1"></i>
                        Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
