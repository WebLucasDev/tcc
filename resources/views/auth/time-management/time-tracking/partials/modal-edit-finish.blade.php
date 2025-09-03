<!-- Modal Final de Edição de Horário -->
<div id="editTimeFinishModal" class="hidden" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.85); z-index: 9999; align-items: center; justify-content: center; padding: 16px;" onclick="closeEditTimeFinishModal()">
    <div class="bg-[var(--color-background)] rounded-lg shadow-xl w-11/12 sm:w-10/12 md:w-8/12 lg:w-6/12 xl:w-4/12 max-w-lg max-h-[95vh] sm:max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <!-- Header do Modal -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-[var(--color-text)]">
                <i class="fa-solid fa-edit text-[var(--color-main)] mr-2"></i>
                Editar Horário
            </h3>
        </div>

        <!-- Corpo do Modal -->
        <div class="px-6 py-4">
            <!-- Informações do Registro -->
            <div class="mb-4">
                <p class="text-sm text-[var(--color-text)] opacity-70">Colaborador:</p>
                <p class="font-medium text-[var(--color-text)]" id="editCollaboratorName">
                    <!-- Nome do colaborador -->
                </p>
            </div>

            <div class="mb-4">
                <p class="text-sm text-[var(--color-text)] opacity-70">Editando:</p>
                <p class="font-semibold text-[var(--color-main)]" id="editTimeSlotName">
                    <!-- Nome do horário sendo editado -->
                </p>
            </div>

            <!-- Formulário de Edição -->
            <form id="editTimeForm">
                <!-- Campo de Horário -->
                <div class="mb-4">
                    <label for="editTimeInput" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                        Novo Horário
                    </label>
                    <input type="time"
                           id="editTimeInput"
                           name="time"
                           class="w-full px-3 py-2 text-base border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent"
                           required>
                </div>

                <!-- Campo de Observação -->
                <div class="mb-4">
                    <label for="editObservationInput" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                        Observação
                        <span class="text-xs text-gray-500">(opcional, máx. 30 caracteres)</span>
                    </label>
                    <textarea id="editObservationInput"
                              name="observation"
                              maxlength="30"
                              rows="3"
                              placeholder="Digite uma observação opcional..."
                              class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-[var(--color-background)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent resize-none"></textarea>
                    <div class="flex justify-between items-center mt-1">
                        <span id="editCharCounter" class="text-xs text-gray-500">0/30</span>
                    </div>
                </div>

                <!-- Campos ocultos para dados -->
                <input type="hidden" id="editTrackingId" name="tracking_id">
                <input type="hidden" id="editTimeSlotType" name="time_slot_type">
            </form>
        </div>

        <!-- Footer do Modal -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
            <button type="button"
                    onclick="closeEditTimeFinishModal()"
                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors duration-200">
                <i class="fa-solid fa-times mr-1"></i>
                Cancelar
            </button>
            <button type="button"
                    onclick="submitTimeEdit()"
                    class="px-4 py-2 bg-[var(--color-main)] hover:bg-[var(--color-main)] text-white rounded-lg font-medium transition-colors duration-200">
                <i class="fa-solid fa-save mr-1"></i>
                Salvar Alterações
            </button>
        </div>
    </div>
</div>
