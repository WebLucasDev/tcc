/**
 * solicitation-create.js
 * Funcionalidades para criação de solicitação
 */

// Variável global para armazenar dados do tracking
let currentTrackingData = null;

document.addEventListener('DOMContentLoaded', function() {
    // ==================== PREVENIR MÚLTIPLAS SUBMISSÕES ====================
    const solicitationForm = document.querySelector('form[action*="solicitations"]');

    if (solicitationForm) {
        solicitationForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');

            if (!submitBtn) return;

            // Verificar se o botão já está desabilitado
            if (submitBtn.disabled) {
                e.preventDefault();
                return false;
            }

            // Validação básica antes de desabilitar
            const timeTrackingId = document.getElementById('time_tracking_id');
            const period = document.getElementById('period');
            const newTimeStart = document.getElementById('new_time_start');
            const newTimeFinish = document.getElementById('new_time_finish');
            const reason = document.getElementById('reason');

            if (!timeTrackingId?.value || !period?.value || !newTimeStart?.value ||
                !newTimeFinish?.value || !reason?.value) {
                // Deixar a validação HTML5 funcionar
                return;
            }

            // Desabilitar o botão e mudar o visual
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitBtn.classList.remove('hover:bg-[var(--color-main)]/90', 'hover:shadow-xl');

            // Mostrar indicador de carregamento
            submitBtn.innerHTML = `
                <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                Enviando Solicitação...
            `;

            // Prevenir múltiplos cliques
            submitBtn.style.pointerEvents = 'none';
        });
    }

    // ==================== CONTADOR DE CARACTERES ====================
    const reasonTextarea = document.getElementById('reason');
    const charCount = document.getElementById('char-count');

    if (reasonTextarea && charCount) {
        reasonTextarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });

        // Inicializar contador
        charCount.textContent = reasonTextarea.value.length;
    }
});

// Função para carregar dados do registro de ponto selecionado
function loadTimeTrackingData(id) {
    const select = document.getElementById('time_tracking_id');
    const selectedOption = select.options[select.selectedIndex];

    // Resetar seleções
    document.getElementById('period').value = '';
    document.getElementById('period-selection').classList.add('hidden');
    document.getElementById('current-times').classList.add('hidden');
    document.getElementById('new-time-section').classList.add('hidden');

    if (!id || !selectedOption) {
        currentTrackingData = null;
        return;
    }

    try {
        currentTrackingData = JSON.parse(selectedOption.getAttribute('data-tracking'));

        // Mostrar seleção de período
        document.getElementById('period-selection').classList.remove('hidden');

    } catch (error) {
        console.error('Erro ao carregar dados do registro:', error);
        currentTrackingData = null;
    }
}

// Função para carregar dados do período selecionado
function loadPeriodData(period) {
    if (!period || !currentTrackingData) {
        document.getElementById('current-times').classList.add('hidden');
        document.getElementById('new-time-section').classList.add('hidden');
        return;
    }

    let entryTime, exitTime;

    if (period === 'morning') {
        // Período da Manhã: entry_time_1 → return_time_1
        entryTime = currentTrackingData.entry_time_1;
        exitTime = currentTrackingData.return_time_1;
    } else if (period === 'afternoon') {
        // Período da Tarde: entry_time_2 → return_time_2
        entryTime = currentTrackingData.entry_time_2;
        exitTime = currentTrackingData.return_time_2;
    }

    // Atualizar visualização do horário atual
    const entryDisplay = document.getElementById('current-entry');
    const exitDisplay = document.getElementById('current-exit');

    entryDisplay.innerHTML = entryTime
        ? `<span class="text-[var(--color-main)]">${entryTime}</span>`
        : `<span class="text-red-500">Ausente</span>`;

    exitDisplay.innerHTML = exitTime
        ? `<span class="text-[var(--color-main)]">${exitTime}</span>`
        : `<span class="text-red-500">Ausente</span>`;

    // Preencher campos hidden de horário antigo
    document.getElementById('old_time_start').value = entryTime || '';
    document.getElementById('old_time_finish').value = exitTime || '';

    // Sugerir os mesmos horários nos campos de novo horário (editáveis)
    document.getElementById('new_time_start').value = entryTime || '';
    document.getElementById('new_time_finish').value = exitTime || '';

    // Mostrar seções
    document.getElementById('current-times').classList.remove('hidden');
    document.getElementById('new-time-section').classList.remove('hidden');
}

// Validação de horários antes do submit
document.querySelector('form')?.addEventListener('submit', function(e) {
    const period = document.getElementById('period').value;
    const newStart = document.getElementById('new_time_start').value;
    const newFinish = document.getElementById('new_time_finish').value;

    if (!period) {
        e.preventDefault();
        alert('Selecione o período que deseja ajustar.');
        return false;
    }

    if (!newStart || !newFinish) {
        return; // Deixar validação HTML5 funcionar
    }

    // Converter para minutos para comparação
    const startMinutes = timeToMinutes(newStart);
    const finishMinutes = timeToMinutes(newFinish);

    if (finishMinutes <= startMinutes) {
        e.preventDefault();
        alert('O horário de saída deve ser posterior ao horário de entrada.');
        return false;
    }

    // Verificar se os horários são diferentes dos atuais
    const oldStart = document.getElementById('old_time_start').value;
    const oldFinish = document.getElementById('old_time_finish').value;

    if (oldStart === newStart && oldFinish === newFinish) {
        e.preventDefault();
        const confirmed = confirm('Os horários informados são iguais aos atuais. Deseja continuar mesmo assim?');
        if (!confirmed) {
            return false;
        }
    }
});

// Função auxiliar para converter HH:MM em minutos
function timeToMinutes(time) {
    const [hours, minutes] = time.split(':').map(Number);
    return hours * 60 + minutes;
}

// Expor funções globalmente
window.loadTimeTrackingData = loadTimeTrackingData;
window.loadPeriodData = loadPeriodData;
