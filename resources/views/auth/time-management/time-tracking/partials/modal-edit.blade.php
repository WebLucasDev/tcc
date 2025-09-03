<!-- Modal de Seleção de Horário para Edição -->
<div id="editTimeSelectModal" class="hidden" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.85); z-index: 9999; align-items: center; justify-content: center; padding: 16px;" onclick="closeEditTimeSelectModal()">
    <div class="bg-[var(--color-background)] rounded-lg shadow-xl w-11/12 sm:w-10/12 md:w-8/12 lg:w-6/12 xl:w-4/12 max-w-lg max-h-[95vh] sm:max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <!-- Header do Modal -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-[var(--color-text)]">
                <i class="fa-solid fa-clock text-[var(--color-main)] mr-2"></i>
                <span class="hidden sm:inline">Selecionar Horário para Editar</span>
                <span class="sm:hidden">Selecionar Horário</span>
            </h3>
        </div>

        <!-- Corpo do Modal -->
        <div class="px-6 py-4">
            <p class="text-[var(--color-text)] mb-4">
                Selecione qual horário você deseja editar para o colaborador:
            </p>
            <p class="font-semibold text-[var(--color-text)] mb-4" id="selectedCollaboratorName">
                <!-- Nome do colaborador será inserido aqui -->
            </p>

            <!-- Lista de Horários Disponíveis -->
            <div class="space-y-3" id="timeSlotsList">
                <!-- Os horários serão inseridos dinamicamente aqui -->
            </div>
        </div>

        <!-- Footer do Modal -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
           <button type="button"
                    onclick="closeEditTimeSelectModal()"
                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors duration-200">
                <i class="fa-solid fa-times mr-1"></i>
                Cancelar
            </button>
        </div>
    </div>
</div>
