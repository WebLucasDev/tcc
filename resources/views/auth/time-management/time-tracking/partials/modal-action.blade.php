<!-- Modal de Confirmação de Ação -->
<div id="actionConfirmModal" class="hidden" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.85); z-index: 9999; align-items: center; justify-content: center; padding: 16px;" onclick="closeActionConfirmModal()">
    <div class="bg-[var(--color-background)] rounded-lg shadow-xl w-11/12 sm:w-10/12 md:w-8/12 lg:w-6/12 xl:w-4/12 max-w-lg max-h-[95vh] sm:max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <!-- Header do Modal -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-[var(--color-text)]">
                <i id="actionModalIcon" class="fa-solid fa-question-circle text-[var(--color-main)] mr-2"></i>
                <span id="actionModalTitle">Confirmar Ação</span>
            </h3>
        </div>

        <!-- Corpo do Modal -->
        <div class="px-6 py-4">
            <!-- Informações do Registro -->
            <div class="mb-4">
                <p class="text-sm text-[var(--color-text)] opacity-70">Colaborador:</p>
                <p class="font-medium text-[var(--color-text)]" id="actionCollaboratorName">
                    <!-- Nome do colaborador -->
                </p>
            </div>

            <div class="mb-4">
                <p class="text-sm text-[var(--color-text)] opacity-70">Data:</p>
                <p class="font-medium text-[var(--color-text)]" id="actionDate">
                    <!-- Data do registro -->
                </p>
            </div>

            <!-- Mensagem de Confirmação -->
            <div class="mb-6 p-4 rounded-lg border" id="actionMessageContainer">
                <div class="flex items-start gap-3">
                    <i id="actionWarningIcon" class="fa-solid fa-exclamation-triangle text-xl mt-1"></i>
                    <div>
                        <p class="font-medium text-[var(--color-text)] mb-2" id="actionQuestion">
                            <!-- Pergunta de confirmação -->
                        </p>
                        <p class="text-sm text-[var(--color-text)] opacity-70" id="actionDescription">
                            <!-- Descrição da ação -->
                        </p>
                    </div>
                </div>
            </div>

            <!-- Status Atual -->
            <div class="mb-4">
                <p class="text-sm text-[var(--color-text)] opacity-70">Status Atual:</p>
                <div class="mt-1" id="currentActionBadge">
                    <!-- Badge do status atual -->
                </div>
            </div>
        </div>

        <!-- Footer do Modal -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
            <button type="button"
                    onclick="closeActionConfirmModal()"
                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors duration-200">
                <i class="fa-solid fa-times mr-1"></i>
                Cancelar
            </button>
            <button type="button"
                    id="confirmActionButton"
                    onclick="executeAction()"
                    class="px-4 py-2 text-white rounded-lg font-medium transition-colors duration-200">
                <i id="confirmActionIcon" class="fa-solid fa-check mr-1"></i>
                <span id="confirmActionText">Confirmar</span>
            </button>
        </div>
    </div>
</div>

<!-- Formulário Oculto para Envio da Ação -->
<form id="actionForm" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
</form>