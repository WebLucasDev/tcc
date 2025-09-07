// Compensatory Time page functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('Compensatory Time JS carregado');

    // Cache dos elementos
    const collaboratorFilter = document.getElementById('collaborator_filter');
    const monthFilter = document.getElementById('month_filter');
    const compTimeContainer = document.getElementById('comp-time-container');
    const summaryContainer = document.getElementById('summary-container');

    // Debug: Verificar se os elementos foram encontrados
    console.log('Elementos encontrados:', {
        collaboratorFilter: !!collaboratorFilter,
        monthFilter: !!monthFilter,
        compTimeContainer: !!compTimeContainer,
        summaryContainer: !!summaryContainer
    });

    // Estado atual dos filtros
    let currentFilters = {
        collaborator_id: collaboratorFilter ? collaboratorFilter.value : '',
        month: monthFilter ? monthFilter.value : ''
    };

    // Event listeners para os filtros
    if (collaboratorFilter) {
        collaboratorFilter.addEventListener('change', function() {
            updateFilters();
            performAjaxSearch();
        });
    }

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
        currentFilters.collaborator_id = collaboratorFilter ? collaboratorFilter.value : '';
        currentFilters.month = monthFilter ? monthFilter.value : '';
    }

    // Função principal para realizar busca AJAX
    function performAjaxSearch() {
        // Usar o sistema de loading global se disponível
        if (window.GlobalLoading) {
            window.GlobalLoading.show('Calculando banco de horas...');
        }

        // Construir parâmetros da URL
        const params = new URLSearchParams();
        Object.keys(currentFilters).forEach(key => {
            if (currentFilters[key] && currentFilters[key] !== '') {
                params.append(key, currentFilters[key]);
            }
        });

        // Atualizar URL sem recarregar a página
        const newUrl = '/gestao-ponto/banco-horas' + (params.toString() ? '?' + params.toString() : '');
        window.history.pushState({}, '', newUrl);

        // Fazer requisição AJAX
        const fetchPromise = window.GlobalLoading ?
            window.GlobalLoading.fetch(newUrl, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            }) :
            fetch(newUrl, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

        fetchPromise
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na resposta do servidor');
                }
                return response.json();
            })
            .then(data => {
                updateContent(data);

                // Esconder loading
                if (window.GlobalLoading) {
                    window.GlobalLoading.hide();
                }
            })
            .catch(error => {
                console.error('Erro na busca:', error);
                showError('Erro ao calcular banco de horas. Tente novamente.');

                // Esconder loading em caso de erro
                if (window.GlobalLoading) {
                    window.GlobalLoading.hide();
                }
            });
    }

    // Função para atualizar o conteúdo da página
    function updateContent(data) {
        if (compTimeContainer && data.html && data.html.table) {
            compTimeContainer.innerHTML = data.html.table;
        }

        if (summaryContainer && data.html && data.html.summary) {
            summaryContainer.innerHTML = data.html.summary;
        }

        // Reanexar eventos após atualizar o conteúdo
        reattachEvents();
    }

    // Função para reanexar todos os eventos após atualização de conteúdo
    function reattachEvents() {
        // Os eventos de expansão são definidos inline (onclick), não precisamos reanexar
        console.log('Eventos reanexados após atualização de conteúdo');
    }

    // Função para mostrar erro
    function showError(message) {
        // Implementar sistema de notificação se necessário
        console.error(message);
        alert(message); // Temporário - substituir por sistema de notificação adequado
    }

    /**
     * Auto-hide de mensagens de sucesso/erro
     */
    function autoHideMessages() {
        const messages = document.querySelectorAll('[id="success"], [id="error"]');
        messages.forEach(function(message) {
            setTimeout(function() {
                message.style.transition = 'opacity 0.5s ease-out';
                message.style.opacity = '0';
                setTimeout(function() {
                    message.remove();
                }, 500);
            }, 5000);
        });
    }

    // Expor funções globalmente para uso nos botões
    window.toggleCollaboratorDetails = toggleCollaboratorDetails;
});

/**
 * Alterna a exibição dos detalhes de um colaborador
 */
function toggleCollaboratorDetails(collaboratorId) {
    console.log('Alternando detalhes do colaborador:', collaboratorId);

    const detailsElement = document.getElementById(`details-${collaboratorId}`);
    const arrowElement = document.getElementById(`arrow-${collaboratorId}`);

    if (!detailsElement || !arrowElement) {
        console.error('Elementos não encontrados para o colaborador:', collaboratorId);
        return;
    }

    const isHidden = detailsElement.classList.contains('hidden');

    if (isHidden) {
        // Expandir
        detailsElement.classList.remove('hidden');
        arrowElement.style.transform = 'rotate(90deg)';

        // Animação suave
        detailsElement.style.maxHeight = '0px';
        detailsElement.style.overflow = 'hidden';
        detailsElement.style.transition = 'max-height 0.3s ease-out';

        // Calcular altura necessária
        requestAnimationFrame(() => {
            const scrollHeight = detailsElement.scrollHeight;
            detailsElement.style.maxHeight = scrollHeight + 'px';

            // Remover restrições após animação
            setTimeout(() => {
                detailsElement.style.maxHeight = '';
                detailsElement.style.overflow = '';
                detailsElement.style.transition = '';
            }, 300);
        });
    } else {
        // Contrair
        detailsElement.style.maxHeight = detailsElement.scrollHeight + 'px';
        detailsElement.style.overflow = 'hidden';
        detailsElement.style.transition = 'max-height 0.3s ease-in';

        requestAnimationFrame(() => {
            detailsElement.style.maxHeight = '0px';
            arrowElement.style.transform = 'rotate(0deg)';

            setTimeout(() => {
                detailsElement.classList.add('hidden');
                detailsElement.style.maxHeight = '';
                detailsElement.style.overflow = '';
                detailsElement.style.transition = '';
            }, 300);
        });
    }
}
