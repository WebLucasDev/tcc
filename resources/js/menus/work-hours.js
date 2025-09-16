// Função para alternar a exibição dos campos de horário por dia
function toggleDayInputs(day) {
    const checkbox = document.getElementById(day + '_active');
    const inputs = document.getElementById(day + '_inputs');

    if (checkbox.checked) {
        inputs.classList.remove('hidden');
    } else {
        inputs.classList.add('hidden');
        // Limpar os valores dos campos quando desativado
        const dayInputs = inputs.querySelectorAll('input[type="time"]');
        dayInputs.forEach(input => input.value = '');
    }

    calculateWeeklyHours();
}

// Função para calcular a carga horária semanal total
function calculateWeeklyHours() {
    let totalMinutes = 0;
    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    days.forEach(day => {
        const activeCheckbox = document.getElementById(day + '_active');
        if (activeCheckbox && activeCheckbox.checked) {
            // Período 1
            const entry1 = document.querySelector(`input[name="${day}_entry_1"]`);
            const exit1 = document.querySelector(`input[name="${day}_exit_1"]`);

            if (entry1.value && exit1.value) {
                totalMinutes += calculateMinutesDiff(entry1.value, exit1.value);
            }

            // Período 2
            const entry2 = document.querySelector(`input[name="${day}_entry_2"]`);
            const exit2 = document.querySelector(`input[name="${day}_exit_2"]`);

            if (entry2.value && exit2.value) {
                totalMinutes += calculateMinutesDiff(entry2.value, exit2.value);
            }
        }
    });

    const hours = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;

    document.getElementById('weekly-hours').textContent =
        `Total: ${hours}h${minutes > 0 ? ` ${minutes}min` : ''} semanais`;
}

// Função para calcular a diferença em minutos entre dois horários
function calculateMinutesDiff(startTime, endTime) {
    const start = new Date('1970-01-01T' + startTime);
    let end = new Date('1970-01-01T' + endTime);

    // Se o horário de saída for menor que entrada, assume que é no dia seguinte
    if (end < start) {
        end = new Date(end.getTime() + 24 * 60 * 60 * 1000);
    }

    return (end - start) / (1000 * 60);
}

// Inicialização quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    // Adicionar event listeners para todos os campos de tempo
    const timeInputs = document.querySelectorAll('input[type="time"]');
    timeInputs.forEach(input => {
        input.addEventListener('change', calculateWeeklyHours);
    });

    // Calcular na inicialização se houver dados
    calculateWeeklyHours();
});
