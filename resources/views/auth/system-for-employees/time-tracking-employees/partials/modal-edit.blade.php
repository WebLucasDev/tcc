<div id="edit-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-[var(--color-background)] rounded-xl shadow-2xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-[var(--color-text)]/10">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-[var(--color-main)]">
                    <i class="fa-solid fa-edit mr-2"></i>
                    Editar Registro de Ponto
                </h3>
                <button onclick="window.closeEditModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <form method="POST" action="{{ route('system-for-employees.time-tracking.update') }}" id="edit-form">
            @csrf
            @method('PATCH')
            <input type="hidden" name="id" id="edit-id">

            <div class="p-6 space-y-6">
                <!-- Data (somente leitura) -->
                <div>
                    <label class="block text-sm font-medium text-[var(--color-text)] mb-2">Data</label>
                    <input type="text" id="edit-date-display" readonly
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-800 text-[var(--color-text)] cursor-not-allowed">
                </div>

                <!-- Entrada (Manhã) -->
                <div class="border border-[var(--color-text)]/10 rounded-lg p-4">
                    <h4 class="font-semibold text-[var(--color-text)] mb-4">
                        <i class="fa-solid fa-sunrise text-orange-500 mr-2"></i>
                        Entrada (Manhã)
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="edit-entry-time-1"
                                class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                Horário *
                            </label>
                            <input type="time" name="entry_time_1" id="edit-entry-time-1" required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)]">
                        </div>
                        <div>
                            <label for="edit-entry-time-1-observation"
                                class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                Observação
                            </label>
                            <div class="relative">
                                <input type="text" name="entry_time_1_observation" id="edit-entry-time-1-observation"
                                    maxlength="30"
                                    class="w-full px-4 py-2 pr-16 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)]">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span id="edit-entry-1-char-counter" class="text-xs text-gray-500">0/30</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Saída para Almoço -->
                <div class="border border-[var(--color-text)]/10 rounded-lg p-4">
                    <h4 class="font-semibold text-[var(--color-text)] mb-4">
                        <i class="fa-solid fa-utensils text-blue-500 mr-2"></i>
                        Saída para Almoço
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="edit-return-time-1"
                                class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                Horário
                            </label>
                            <input type="time" name="return_time_1" id="edit-return-time-1"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)]">
                        </div>
                        <div>
                            <label for="edit-return-time-1-observation"
                                class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                Observação
                            </label>
                            <div class="relative">
                                <input type="text" name="return_time_1_observation"
                                    id="edit-return-time-1-observation" maxlength="30"
                                    class="w-full px-4 py-2 pr-16 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)]">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span id="edit-return-1-char-counter" class="text-xs text-gray-500">0/30</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Retorno do Almoço -->
                <div class="border border-[var(--color-text)]/10 rounded-lg p-4">
                    <h4 class="font-semibold text-[var(--color-text)] mb-4">
                        <i class="fa-solid fa-clock text-green-500 mr-2"></i>
                        Retorno do Almoço
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="edit-entry-time-2"
                                class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                Horário
                            </label>
                            <input type="time" name="entry_time_2" id="edit-entry-time-2"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)]">
                        </div>
                        <div>
                            <label for="edit-entry-time-2-observation"
                                class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                Observação
                            </label>
                            <div class="relative">
                                <input type="text" name="entry_time_2_observation" id="edit-entry-time-2-observation"
                                    maxlength="30"
                                    class="w-full px-4 py-2 pr-16 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)]">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span id="edit-entry-2-char-counter" class="text-xs text-gray-500">0/30</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Saída (Tarde) -->
                <div class="border border-[var(--color-text)]/10 rounded-lg p-4">
                    <h4 class="font-semibold text-[var(--color-text)] mb-4">
                        <i class="fa-solid fa-sunset text-purple-500 mr-2"></i>
                        Saída (Tarde)
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="edit-return-time-2"
                                class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                Horário
                            </label>
                            <input type="time" name="return_time_2" id="edit-return-time-2"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)]">
                        </div>
                        <div>
                            <label for="edit-return-time-2-observation"
                                class="block text-sm font-medium text-[var(--color-text)] mb-2">
                                Observação
                            </label>
                            <div class="relative">
                                <input type="text" name="return_time_2_observation"
                                    id="edit-return-time-2-observation" maxlength="30"
                                    class="w-full px-4 py-2 pr-16 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)]">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span id="edit-return-2-char-counter" class="text-xs text-gray-500">0/30</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-[var(--color-text)]/10 flex justify-end gap-3">
                <button type="button" onclick="window.closeEditModal()"
                    class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-[var(--color-text)] transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-6 py-2 bg-[var(--color-main)] hover:opacity-90 text-white rounded-lg transition-all">
                    <i class="fa-solid fa-save mr-2"></i>
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>
