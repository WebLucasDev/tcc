document.addEventListener('DOMContentLoaded', function() {
    // ==================== RELÓGIO DIGITAL ====================
    function updateClock() {
        const now = new Date();

        // Atualizar horário
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const timeDisplay = document.getElementById('current-time');
        if (timeDisplay) {
            timeDisplay.textContent = `${hours}:${minutes}:${seconds}`;
        }

        // Atualizar data
        const days = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
        const months = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];

        const dayName = days[now.getDay()];
        const day = String(now.getDate()).padStart(2, '0');
        const month = months[now.getMonth()];
        const year = now.getFullYear();

        const dateDisplay = document.getElementById('current-date');
        if (dateDisplay) {
            dateDisplay.textContent = `${dayName}, ${day} de ${month} de ${year}`;
        }
    }

    // Atualizar relógio a cada segundo
    updateClock();
    setInterval(updateClock, 1000);

    // ==================== PRÓXIMO TIPO DE REGISTRO ====================
    function loadNextTrackingInfo() {
        const nextTrackingType = document.getElementById('next-tracking-type');

        if (nextTrackingType) {
            nextTrackingType.textContent = 'Carregando...';
        }

        fetch(`/sistema-colaboradores/bater-ponto/next-tracking-info`)
            .then(response => response.json())
            .then(data => {
                if (nextTrackingType) {
                    nextTrackingType.textContent = data.next_type;
                }

                // Atualizar o botão
                const submitBtn = document.getElementById('submit-btn');
                if (submitBtn) {
                    let icon = 'fa-clock';

                    // Definir ícone baseado no tipo
                    if (data.next_type === 'Entrada') {
                        icon = 'fa-arrow-right-to-bracket';
                    } else if (data.next_type === 'Saída para Almoço') {
                        icon = 'fa-utensils';
                    } else if (data.next_type === 'Retorno do Almoço') {
                        icon = 'fa-clock-rotate-left';
                    } else if (data.next_type === 'Saída') {
                        icon = 'fa-arrow-right-from-bracket';
                    } else if (data.next_type === 'Completo') {
                        icon = 'fa-check-circle';
                    }

                    submitBtn.innerHTML = `
                        <i class="fa-solid ${icon}"></i>
                        Registrar ${data.next_type}
                    `;

                    // Desabilitar botão se já estiver completo
                    if (data.next_type === 'Completo') {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        submitBtn.classList.remove('hover:opacity-90');
                    } else {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        submitBtn.classList.add('hover:opacity-90');
                    }
                }
            })
            .catch(error => {
                console.error('Erro ao carregar próximo tipo:', error);
                // Não mostrar mensagem de erro, apenas manter "Carregando..."
            });
    }

    // Carregar info inicial
    loadNextTrackingInfo();

    // Recarregar info a cada 30 segundos
    setInterval(loadNextTrackingInfo, 30000);

    // ==================== CONTADOR DE CARACTERES ====================
    const observationInput = document.getElementById('time_observation');
    const charCounter = document.getElementById('char-counter');

    if (observationInput && charCounter) {
        observationInput.addEventListener('input', function() {
            const currentLength = this.value.length;
            charCounter.textContent = `${currentLength}/30`;

            // Mudar cor quando próximo do limite
            if (currentLength >= 25) {
                charCounter.classList.add('text-orange-500');
            } else {
                charCounter.classList.remove('text-orange-500');
            }
        });
    }

    // ==================== TIMEOUT DE MENSAGENS ====================
    const successMessage = document.querySelector('.success-message');
    const errorMessage = document.querySelector('.error-message');

    if (successMessage) {
        setTimeout(() => {
            successMessage.style.opacity = '0';
            successMessage.style.transition = 'opacity 0.5s ease-out';
            setTimeout(() => {
                successMessage.remove();
            }, 500);
        }, 5000);

        successMessage.addEventListener('click', function() {
            this.style.opacity = '0';
            this.style.transition = 'opacity 0.5s ease-out';
            setTimeout(() => {
                this.remove();
            }, 500);
        });
    }

    if (errorMessage) {
        setTimeout(() => {
            errorMessage.style.opacity = '0';
            errorMessage.style.transition = 'opacity 0.5s ease-out';
            setTimeout(() => {
                errorMessage.remove();
            }, 500);
        }, 5000);

        errorMessage.addEventListener('click', function() {
            this.style.opacity = '0';
            this.style.transition = 'opacity 0.5s ease-out';
            setTimeout(() => {
                this.remove();
            }, 500);
        });
    }

    // ==================== FUNÇÕES DE MODAL ====================

    // Modal de Edição
    window.openEditModal = function(trackingId) {
        const modal = document.getElementById('edit-modal');
        if (!modal) return;

        // Buscar dados do registro
        fetch(`/sistema-colaboradores/bater-ponto/${trackingId}`)
            .then(response => response.json())
            .then(data => {
                // Preencher o formulário
                document.getElementById('edit-id').value = data.id;
                document.getElementById('edit-date-display').value = formatDate(data.date);

                // Preencher todos os 4 horários
                document.getElementById('edit-entry-time-1').value = data.entry_time_1 || '';
                document.getElementById('edit-entry-time-1-observation').value = data.entry_time_1_observation || '';

                document.getElementById('edit-return-time-1').value = data.return_time_1 || '';
                document.getElementById('edit-return-time-1-observation').value = data.return_time_1_observation || '';

                document.getElementById('edit-entry-time-2').value = data.entry_time_2 || '';
                document.getElementById('edit-entry-time-2-observation').value = data.entry_time_2_observation || '';

                document.getElementById('edit-return-time-2').value = data.return_time_2 || '';
                document.getElementById('edit-return-time-2-observation').value = data.return_time_2_observation || '';

                // Atualizar todos os contadores
                updateCharCounter('edit-entry-time-1-observation', 'edit-entry-1-char-counter');
                updateCharCounter('edit-return-time-1-observation', 'edit-return-1-char-counter');
                updateCharCounter('edit-entry-time-2-observation', 'edit-entry-2-char-counter');
                updateCharCounter('edit-return-time-2-observation', 'edit-return-2-char-counter');

                // Mostrar modal
                modal.classList.remove('hidden');
            })
            .catch(error => {
                console.error('Erro ao carregar registro:', error);
                alert('Erro ao carregar os dados do registro.');
            });
    };

    window.closeEditModal = function() {
        const modal = document.getElementById('edit-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
    };

    // Modal de Cancelamento
    window.openCancelModal = function(trackingId) {
        const modal = document.getElementById('cancel-modal');
        const form = document.getElementById('cancel-form');
        if (!modal || !form) return;

        form.action = `/sistema-colaboradores/bater-ponto/${trackingId}/cancel`;
        modal.classList.remove('hidden');
    };

    window.closeCancelModal = function() {
        const modal = document.getElementById('cancel-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
    };

    // Modal de Restauração
    window.openRestoreModal = function(trackingId) {
        const modal = document.getElementById('restore-modal');
        const form = document.getElementById('restore-form');
        if (!modal || !form) return;

        form.action = `/sistema-colaboradores/bater-ponto/${trackingId}/restore`;
        modal.classList.remove('hidden');
    };

    window.closeRestoreModal = function() {
        const modal = document.getElementById('restore-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
    };

    // Fechar modais com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.closeEditModal();
            window.closeCancelModal();
            window.closeRestoreModal();
        }
    });

    // Fechar modais clicando fora
    const modals = ['edit-modal', 'cancel-modal', 'restore-modal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    if (modalId === 'edit-modal') window.closeEditModal();
                    if (modalId === 'cancel-modal') window.closeCancelModal();
                    if (modalId === 'restore-modal') window.closeRestoreModal();
                }
            });
        }
    });

    // ==================== CONTADORES DE CARACTERES DO MODAL ====================
    const editInputs = [
        { input: 'edit-entry-time-1-observation', counter: 'edit-entry-1-char-counter' },
        { input: 'edit-return-time-1-observation', counter: 'edit-return-1-char-counter' },
        { input: 'edit-entry-time-2-observation', counter: 'edit-entry-2-char-counter' },
        { input: 'edit-return-time-2-observation', counter: 'edit-return-2-char-counter' }
    ];

    editInputs.forEach(({ input, counter }) => {
        const inputElement = document.getElementById(input);
        if (inputElement) {
            inputElement.addEventListener('input', function() {
                updateCharCounter(input, counter);
            });
        }
    });

    function updateCharCounter(inputId, counterId) {
        const input = document.getElementById(inputId);
        const counter = document.getElementById(counterId);

        if (input && counter) {
            const currentLength = input.value.length;
            counter.textContent = `${currentLength}/30`;

            if (currentLength >= 25) {
                counter.classList.add('text-orange-500');
            } else {
                counter.classList.remove('text-orange-500');
            }
        }
    }

    // ==================== FUNÇÕES AUXILIARES ====================
    function formatDate(dateString) {
        const date = new Date(dateString + 'T00:00:00');
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    }
});
