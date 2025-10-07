document.addEventListener('DOMContentLoaded', function() {
    // ==================== PREVENIR MÚLTIPLAS SUBMISSÕES ====================
    const timeTrackingForm = document.getElementById('time-tracking-form');
    const submitBtn = document.getElementById('submit-btn');

    if (timeTrackingForm && submitBtn) {
        timeTrackingForm.addEventListener('submit', function(e) {
            // Verificar se o botão já está desabilitado
            if (submitBtn.disabled) {
                e.preventDefault();
                return false;
            }

            // Desabilitar o botão e mudar o visual
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');

            // Salvar HTML original
            const originalHTML = submitBtn.innerHTML;

            // Mostrar indicador de carregamento
            submitBtn.innerHTML = `
                <i class="fa-solid fa-spinner fa-spin"></i>
                Registrando...
            `;

            // Prevenir múltiplos cliques
            submitBtn.style.pointerEvents = 'none';
        });
    }

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

    // Modal de Cancelamento
    window.openCancelModal = function(trackingId) {
        const modal = document.getElementById('cancel-modal');
        const form = document.getElementById('cancel-form');
        if (!modal || !form) return;

        form.action = `/sistema-colaboradores/bater-ponto/${trackingId}/cancel`;
        modal.classList.remove('hidden');

        // Resetar o botão de submit (caso tenha sido desabilitado antes)
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            submitBtn.style.pointerEvents = '';
            submitBtn.innerHTML = `
                <i class="fa-solid fa-rotate-left mr-2"></i>
                Sim, Cancelar
            `;
        }
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

        // Resetar o botão de submit (caso tenha sido desabilitado antes)
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            submitBtn.style.pointerEvents = '';
            submitBtn.innerHTML = `
                <i class="fa-solid fa-check mr-2"></i>
                Sim, Restaurar
            `;
        }
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
            window.closeCancelModal();
            window.closeRestoreModal();
        }
    });

    // Fechar modais clicando fora
    const modals = ['cancel-modal', 'restore-modal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    if (modalId === 'cancel-modal') window.closeCancelModal();
                    if (modalId === 'restore-modal') window.closeRestoreModal();
                }
            });
        }
    });

    // ==================== PREVENIR MÚLTIPLAS SUBMISSÕES NOS MODAIS ====================
    const cancelForm = document.getElementById('cancel-form');
    if (cancelForm) {
        cancelForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');

            if (!submitBtn) return;

            // Verificar se o botão já está desabilitado
            if (submitBtn.disabled) {
                e.preventDefault();
                return false;
            }

            // Desabilitar o botão e mudar o visual
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitBtn.style.pointerEvents = 'none';

            // Mostrar indicador de carregamento
            submitBtn.innerHTML = `
                <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                Cancelando...
            `;
        });
    }

    const restoreForm = document.getElementById('restore-form');
    if (restoreForm) {
        restoreForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');

            if (!submitBtn) return;

            // Verificar se o botão já está desabilitado
            if (submitBtn.disabled) {
                e.preventDefault();
                return false;
            }

            // Desabilitar o botão e mudar o visual
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitBtn.style.pointerEvents = 'none';

            // Mostrar indicador de carregamento
            submitBtn.innerHTML = `
                <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                Restaurando...
            `;
        });
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
