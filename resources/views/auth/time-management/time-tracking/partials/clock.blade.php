<div class="bg-[var(--color-background)] border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
    <div class="p-8">
        <!-- Relógio Digital -->
        <div class="text-center mb-8">
            <div class="clock-container mx-auto">
                <div class="digital-clock">
                    <div id="current-time" class="time-display">00:00:00</div>
                    <div id="current-date" class="date-display">Carregando...</div>
                </div>
            </div>
        </div>

        <!-- Formulário de Registro de Ponto -->
        <div class="max-w-md mx-auto">
            <form method="POST" action="{{ route('time-tracking.store') }}" id="time-tracking-form">
                @csrf

                <!-- Seleção do Colaborador -->
                <div class="mb-6">
                    <label for="collaborator_id" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                        <i class="fa-solid fa-user mr-2"></i>
                        Selecionar Colaborador
                    </label>
                    <select id="collaborator_id"
                            name="collaborator_id"
                            required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)]">
                        <option value="">-- Selecione um colaborador --</option>
                        @foreach($collaborators as $collaborator)
                            <option value="{{ $collaborator->id }}">
                                {{ $collaborator->name }} - {{ $collaborator->position->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Próximo Registro -->
                <div class="mb-6" id="next-tracking-info">
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fa-solid fa-info-circle text-blue-500 mr-2"></i>
                            <span class="text-sm font-medium text-blue-700 dark:text-blue-300">
                                Próximo registro: <span id="next-tracking-type">Selecione um colaborador</span>
                            </span>
                        </div>
                    </div>
                </div>                <!-- Data e Hora (opcionais para ajustes) -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="date" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                            Data
                        </label>
                        <input type="date"
                               id="date"
                               name="date"
                               value="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)]">
                    </div>
                    <div>
                        <label for="time" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                            Horário
                        </label>
                        <input type="time"
                               id="time"
                               name="time"
                               value="{{ date('H:i') }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)]">
                    </div>
                </div>

                <!-- Observação do Horário -->
                <div class="mb-6">
                    <label for="time_observation" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                        <i class="fa-solid fa-note-sticky mr-2"></i>
                        Observação para este horário (opcional)
                    </label>
                    <div class="relative">
                        <input type="text"
                               id="time_observation"
                               name="time_observation"
                               maxlength="30"
                               placeholder="Observação sobre este registro específico..."
                               class="w-full px-4 py-3 pr-16 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[var(--color-main)] focus:border-transparent bg-[var(--color-background)] text-[var(--color-text)]">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <span id="char-counter" class="text-xs text-gray-500">0/30</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Máximo de 30 caracteres</p>
                </div>
                <button type="submit"
                        id="submit-btn"
                        class="w-full bg-[var(--color-main)] hover:bg-[var(--color-main-dark)] text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-clock"></i>
                    Registrar Ponto
                </button>
            </form>
        </div>
    </div>
</div>
