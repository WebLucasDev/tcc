/**
 * comp-time.js (Employee Version)
 * Funcionalidades para o banco de horas do colaborador
 */

/**
 * Toggle dos detalhes do dia
 * Esta função precisa estar no escopo global para ser chamada pelo onclick
 */
window.toggleDayDetails = function(index) {
    const detailsDiv = document.getElementById('day-details-' + index);
    const chevronIcon = document.querySelector('.chevron-icon-' + index);

    if (detailsDiv) {
        if (detailsDiv.classList.contains('hidden')) {
            // Mostrar detalhes
            detailsDiv.classList.remove('hidden');

            // Animar entrada
            detailsDiv.style.maxHeight = '0';
            detailsDiv.style.opacity = '0';
            detailsDiv.style.transition = 'max-height 0.3s ease-out, opacity 0.3s ease-out';

            setTimeout(() => {
                detailsDiv.style.maxHeight = detailsDiv.scrollHeight + 'px';
                detailsDiv.style.opacity = '1';
            }, 10);

            // Rotacionar chevron
            if (chevronIcon) {
                chevronIcon.style.transform = 'rotate(180deg)';
            }
        } else {
            // Esconder detalhes
            detailsDiv.style.maxHeight = '0';
            detailsDiv.style.opacity = '0';

            setTimeout(() => {
                detailsDiv.classList.add('hidden');
                detailsDiv.style.maxHeight = '';
                detailsDiv.style.opacity = '';
            }, 300);

            // Rotacionar chevron de volta
            if (chevronIcon) {
                chevronIcon.style.transform = 'rotate(0deg)';
            }
        }
    }
};

document.addEventListener('DOMContentLoaded', function() {
    console.log('Comp Time (Employee) JS carregado');

    // Cache dos elementos
    const monthFilter = document.getElementById('month_filter');
    const summaryContainer = document.getElementById('summary-container');
    const detailsContainer = document.getElementById('details-container');

    // Debug: Verificar se os elementos foram encontrados
    console.log('Elementos encontrados:', {
        monthFilter: !!monthFilter,
        summaryContainer: !!summaryContainer,
        detailsContainer: !!detailsContainer
    });

    // Estado atual dos filtros
    let currentFilters = {
        month: monthFilter ? monthFilter.value : ''
    };

    // Event listener para o filtro de mês
    if (monthFilter) {
        monthFilter.addEventListener('change', function() {
            updateFilters();
            performAjaxSearch();
        });
    }

    // Auto-hide success/error messages
    autoHideMessages();

    // Função para atualizar filtros
    function updateFilters() {
        currentFilters.month = monthFilter ? monthFilter.value : '';
    }

    // Função principal para realizar busca AJAX
    function performAjaxSearch() {
        // Mostrar loading
        if (summaryContainer) {
            summaryContainer.innerHTML = '<div class="text-center py-8"><i class="fa-solid fa-spinner fa-spin text-3xl text-[var(--color-main)]"></i><p class="mt-2 text-[var(--color-text)]">Calculando banco de horas...</p></div>';
        }

        if (detailsContainer) {
            detailsContainer.innerHTML = '<div class="text-center py-8"><i class="fa-solid fa-spinner fa-spin text-3xl text-[var(--color-main)]"></i></div>';
        }

        // Construir parâmetros da URL
        const params = new URLSearchParams();
        if (currentFilters.month && currentFilters.month !== '') {
            params.append('month', currentFilters.month);
        }

        // Atualizar URL sem recarregar a página
        const newUrl = '/sistema-colaboradores/banco-horas' + (params.toString() ? '?' + params.toString() : '');
        window.history.pushState({}, '', newUrl);

        // Fazer requisição AJAX
        fetch(newUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na resposta do servidor');
            }
            return response.json();
        })
        .then(data => {
            updateContent(data);
        })
        .catch(error => {
            console.error('Erro na busca:', error);
            showError('Erro ao calcular banco de horas. Tente novamente.');

            // Restaurar conteúdo em caso de erro
            if (summaryContainer) {
                summaryContainer.innerHTML = '<div class="text-center py-8 text-red-500"><i class="fa-solid fa-exclamation-triangle text-3xl mb-2"></i><p>Erro ao carregar dados</p></div>';
            }
            if (detailsContainer) {
                detailsContainer.innerHTML = '';
            }
        });
    }

    // Função para atualizar o conteúdo da página
    function updateContent(data) {
        if (summaryContainer && data.html && data.html.summary) {
            summaryContainer.innerHTML = data.html.summary;
        }

        if (detailsContainer && data.html && data.html.details) {
            detailsContainer.innerHTML = data.html.details;
        }
    }

    // Função para mostrar erro
    function showError(message) {
        console.error(message);

        // Criar elemento de erro temporário
        const errorDiv = document.createElement('div');
        errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
        errorDiv.innerHTML = `
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-exclamation-circle"></i>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(errorDiv);

        // Remover após 5 segundos
        setTimeout(() => {
            errorDiv.style.opacity = '0';
            errorDiv.style.transition = 'opacity 0.5s ease-out';
            setTimeout(() => {
                errorDiv.remove();
            }, 500);
        }, 5000);
    }

    /**
     * Auto-hide de mensagens de sucesso/erro
     */
    function autoHideMessages() {
        const successMessages = document.querySelectorAll('.success-message');
        const errorMessages = document.querySelectorAll('.error-message');

        [...successMessages, ...errorMessages].forEach(function(message) {
            setTimeout(function() {
                message.style.transition = 'opacity 0.5s ease-out';
                message.style.opacity = '0';
                setTimeout(function() {
                    message.remove();
                }, 500);
            }, 5000);
        });
    }
});
