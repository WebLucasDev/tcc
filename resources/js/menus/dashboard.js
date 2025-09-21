/**
 * Dashboard JavaScript
 * Funcionalidades de interatividade do dashboard
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
});

function initializeDashboard() {
    // Animação dos cards de métricas
    animateMetricsCards();

    // Inicializar tooltips
    initializeTooltips();

    // Auto-atualização de dados (opcional)
    // setInterval(refreshRecentRecords, 300000); // 5 minutos
}

/**
 * Anima os cards de métricas na entrada
 */
function animateMetricsCards() {
    const cards = document.querySelectorAll('.grid .bg-\\[var\\(--color-background\\)\\]');

    cards.forEach((card, index) => {
        // Pequeno atraso para efeito cascata
        setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s ease-out';

            // Trigger da animação
            requestAnimationFrame(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            });
        }, index * 100);
    });
}

/**
 * Inicializa tooltips para elementos com dados adicionais
 */
function initializeTooltips() {
    // Adicionar tooltips aos elementos de progresso
    const progressBars = document.querySelectorAll('[style*="width:"]');

    progressBars.forEach(bar => {
        if (bar.style.width) {
            bar.title = `Progresso: ${bar.style.width}`;
        }
    });
}

/**
 * Formata números para exibição
 */
function formatNumber(number) {
    return new Intl.NumberFormat('pt-BR').format(number);
}

/**
 * Formata horas para exibição (opcional para uso futuro)
 */
function formatHours(minutes) {
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    return `${hours}h${mins.toString().padStart(2, '0')}m`;
}

/**
 * Adiciona efeito hover aos cards
 */
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.bg-\\[var\\(--color-background\\)\\]');

    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.transition = 'transform 0.2s ease-out';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});

/**
 * Função para destacar alertas importantes
 */
function highlightCriticalAlerts() {
    const alertElements = document.querySelectorAll('.bg-red-50, .bg-yellow-50');

    alertElements.forEach(alert => {
        const value = parseInt(alert.querySelector('.font-medium').textContent);

        if (value > 0) {
            alert.style.animation = 'pulse 2s infinite';
        }
    });
}

// Executar highlight de alertas após carregamento
setTimeout(highlightCriticalAlerts, 1000);
