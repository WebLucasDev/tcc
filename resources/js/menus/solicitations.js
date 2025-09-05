// Solicitations page functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('Solicitations JS carregado');

    // Cache dos elementos
    const searchInput = document.querySelector('input[name="search"]');
    const statusSelect = document.querySelector('select[name="status"]');
    const sortBySelect = document.querySelector('select[name="sort_by"]');
    const sortDirectionBtn = document.querySelector('[name="sort_direction"]');
    const tableContainer = document.getElementById('table-container');
    const paginationContainer = document.getElementById('pagination-container');
    const statisticsContainer = document.getElementById('statistics-container');
    const resultsSummary = document.getElementById('results-summary');

    // Elementos do modal
    const actionModal = document.getElementById('actionModal');
    const actionForm = document.getElementById('actionForm');
    const actionContent = document.getElementById('actionContent');
    const commentSection = document.getElementById('commentSection');
    const confirmBtn = document.getElementById('confirmActionBtn');
    const modalTitle = document.getElementById('actionModalTitle');
    const commentTextarea = document.getElementById('admin_comment');
    const commentError = document.getElementById('commentError');

    // Debug: Verificar se os elementos foram encontrados
    console.log('Elementos encontrados:', {
        searchInput: !!searchInput,
        statusSelect: !!statusSelect,
        sortBySelect: !!sortBySelect,
        sortDirectionBtn: !!sortDirectionBtn,
        tableContainer: !!tableContainer,
        paginationContainer: !!paginationContainer,
        actionModal: !!actionModal,
        actionForm: !!actionForm,
        actionContent: !!actionContent,
        commentSection: !!commentSection,
        confirmBtn: !!confirmBtn,
        modalTitle: !!modalTitle,
        commentTextarea: !!commentTextarea,
        commentError: !!commentError
    });

    // Estado atual dos filtros
    let currentFilters = {
        search: searchInput ? searchInput.value : '',
        status: statusSelect ? statusSelect.value : '',
        sort_by: sortBySelect ? sortBySelect.value : 'created_at',
        sort_direction: getSortDirection()
    };

    // Auto-submit do filtro de busca com debounce
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);

            // Mostrar indicador de loading no campo
            const searchIcon = searchInput.parentElement.querySelector('i');
            if (searchIcon) {
                searchIcon.className = 'fa-solid fa-spinner fa-spin text-[var(--color-text)] opacity-50';
            }

            searchTimeout = setTimeout(() => {
                currentFilters.search = this.value;
                // Resetar página ao fazer nova busca
                delete currentFilters.page;
                performAjaxSearch();

                // Restaurar ícone de busca
                if (searchIcon) {
                    searchIcon.className = 'fa-solid fa-search text-[var(--color-text)] opacity-50';
                }
            }, 500);
        });
    }

    // Event listener para filtro de status
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            currentFilters.status = this.value;
            // Resetar página ao mudar filtro
            delete currentFilters.page;
            performAjaxSearch();
        });
    }

    // Event listener para ordenação
    if (sortBySelect) {
        sortBySelect.addEventListener('change', function() {
            currentFilters.sort_by = this.value;
            // Resetar página ao mudar ordenação
            delete currentFilters.page;
            performAjaxSearch();
        });
    }

    if (sortDirectionBtn) {
        sortDirectionBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const currentDirection = this.value;
            const newDirection = currentDirection === 'asc' ? 'desc' : 'asc';
            this.value = newDirection;
            currentFilters.sort_direction = newDirection;
            // Resetar página ao mudar direção
            delete currentFilters.page;
            updateSortButton();
            performAjaxSearch();
        });
    }

    // Event listeners para o modal
    if (actionForm) {
        actionForm.addEventListener('submit', handleFormSubmit);
    }

    if (commentTextarea) {
        commentTextarea.addEventListener('input', handleCommentInput);
    }

    // Auto-hide success/error messages
    autoHideMessages();

    // Função para obter direção atual de ordenação
    function getSortDirection() {
        if (sortDirectionBtn) {
            return sortDirectionBtn.value || 'desc';
        }
        return 'desc';
    }

    // Função principal para realizar busca AJAX
    function performAjaxSearch() {
        // Usar o sistema de loading global se disponível
        if (window.GlobalLoading) {
            window.GlobalLoading.show('Buscando solicitações...');
        }

        // Construir parâmetros da URL
        const params = new URLSearchParams();
        Object.keys(currentFilters).forEach(key => {
            if (currentFilters[key] && currentFilters[key] !== '') {
                params.append(key, currentFilters[key]);
            }
        });

        // Atualizar URL sem recarregar a página
        const newUrl = '/gestao-ponto/solicitacoes' + (params.toString() ? '?' + params.toString() : '');
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
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
            updateContent(data);

            // Verificar se a página atual é válida após aplicar filtros
            if (data.resetPage || (data.currentPage > data.lastPage && data.lastPage > 0)) {
                // Se a página atual não é válida, ir para a última página disponível
                if (data.lastPage > 0) {
                    currentFilters.page = data.lastPage;
                    // Fazer nova requisição com a página correta
                    performAjaxSearch();
                    return;
                } else {
                    // Se não há páginas, remover parâmetro de página
                    delete currentFilters.page;
                }
            }

            // Esconder loading
            if (window.GlobalLoading) {
                window.GlobalLoading.hide();
            }
        })
            .catch(error => {
                console.error('Erro na busca:', error);
                showError('Erro ao buscar solicitações. Tente novamente.');

                // Esconder loading em caso de erro
                if (window.GlobalLoading) {
                    window.GlobalLoading.hide();
                }
            });
    }

    // Função para atualizar o conteúdo da página
    function updateContent(data) {
        if (tableContainer && data.table) {
            tableContainer.innerHTML = data.table;
        }

        if (paginationContainer) {
            if (data.pagination) {
                paginationContainer.innerHTML = data.pagination;
                paginationContainer.style.display = '';
            } else {
                paginationContainer.innerHTML = '';
                paginationContainer.style.display = 'none';
            }
        }

        // Atualizar estatísticas
        if (data.statistics && statisticsContainer) {
            updateStatistics(data.statistics);
        }

        // Reanexar eventos após atualizar o conteúdo
        reattachEvents();

        // Sincronizar controles com filtros atuais
        syncControlsWithFilters();

        // Resetar página se necessário (quando os filtros resultam em menos páginas)
        if (data.resetPage && currentFilters.page) {
            delete currentFilters.page;
        }
    }

    // Função para reanexar todos os eventos após atualização de conteúdo
    function reattachEvents() {
        attachPaginationEvents();
        attachSortingEvents();

        // Reattach modal events
        const newActionButtons = document.querySelectorAll('[onclick*="openActionModal"]');
        // Os eventos onclick já estão definidos inline, não precisamos reanexar
    }

    // Função para anexar eventos de ordenação
    function attachSortingEvents() {
        const newSortBySelect = document.querySelector('select[name="sort_by"]');
        const newSortDirectionBtn = document.querySelector('[name="sort_direction"]');

        if (newSortBySelect) {
            newSortBySelect.addEventListener('change', function() {
                currentFilters.sort_by = this.value;
                delete currentFilters.page;
                performAjaxSearch();
            });
        }

        if (newSortDirectionBtn) {
            newSortDirectionBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const currentDirection = this.value;
                const newDirection = currentDirection === 'asc' ? 'desc' : 'asc';
                this.value = newDirection;
                currentFilters.sort_direction = newDirection;
                delete currentFilters.page;
                updateSortButton();
                performAjaxSearch();
            });
        }
    }

    // Função para sincronizar controles com filtros atuais
    function syncControlsWithFilters() {
        // Sincronizar select de ordenação
        const currentSortBySelect = document.querySelector('select[name="sort_by"]');
        if (currentSortBySelect && currentFilters.sort_by) {
            currentSortBySelect.value = currentFilters.sort_by;
        }

        // Sincronizar botão de direção
        const currentSortDirectionBtn = document.querySelector('[name="sort_direction"]');
        if (currentSortDirectionBtn && currentFilters.sort_direction) {
            currentSortDirectionBtn.value = currentFilters.sort_direction;
            updateSortButton();
        }

        // Sincronizar filtro de status
        const currentStatusSelect = document.querySelector('select[name="status"]');
        if (currentStatusSelect && currentFilters.status) {
            currentStatusSelect.value = currentFilters.status;
        }

        // Sincronizar campo de busca
        const currentSearchInput = document.querySelector('input[name="search"]');
        if (currentSearchInput && currentFilters.search) {
            currentSearchInput.value = currentFilters.search;
        }
    }

    // Função para atualizar estatísticas
    function updateStatistics(stats) {
        if (stats.pending !== undefined) {
            const pendingEl = document.getElementById('pending-count');
            if (pendingEl) pendingEl.textContent = stats.pending;
        }
        if (stats.approved !== undefined) {
            const approvedEl = document.getElementById('approved-count');
            if (approvedEl) approvedEl.textContent = stats.approved;
        }
        if (stats.rejected !== undefined) {
            const rejectedEl = document.getElementById('rejected-count');
            if (rejectedEl) rejectedEl.textContent = stats.rejected;
        }
        if (stats.cancelled !== undefined) {
            const cancelledEl = document.getElementById('cancelled-count');
            if (cancelledEl) cancelledEl.textContent = stats.cancelled;
        }
    }

    // Função para atualizar o botão de ordenação
    function updateSortButton() {
        const currentSortDirectionBtn = document.querySelector('[name="sort_direction"]');
        if (currentSortDirectionBtn) {
            const icon = currentSortDirectionBtn.querySelector('i');
            if (icon) {
                const direction = currentSortDirectionBtn.value;
                icon.className = `fa-solid fa-sort-${direction === 'asc' ? 'up' : 'down'}`;
            }
        }
    }

    // Função para anexar eventos de paginação
    function attachPaginationEvents() {
        const paginationLinks = document.querySelectorAll('.pagination-links a, .pagination a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();

                // Verificar se é um link de página válido
                const url = new URL(this.href);
                const page = url.searchParams.get('page');

                if (page && !this.classList.contains('disabled')) {
                    performAjaxPagination(page);
                }
            });
        });
    }

    // Função para paginação AJAX
    function performAjaxPagination(page) {
        // Verificar se a página é válida antes de fazer a requisição
        const currentPageElement = document.querySelector('.pagination .page-item.active .page-link');
        const lastPageElement = document.querySelector('.pagination .page-item:last-child .page-link');

        if (lastPageElement) {
            const lastPageText = lastPageElement.textContent.trim();
            const lastPageNumber = parseInt(lastPageText);

            // Se a página solicitada é maior que a última página disponível, ir para a última
            if (!isNaN(lastPageNumber) && parseInt(page) > lastPageNumber) {
                page = lastPageNumber;
            }
        }

        currentFilters.page = page;
        performAjaxSearch();
    }

    // Função para mostrar erro
    function showError(message) {
        // Implementar sistema de notificação se necessário
        console.error(message);
    }

    /**
     * Manipula o envio do formulário
     */
    function handleFormSubmit(e) {
        console.log('Form submit iniciado');

        // Validar comentário se for obrigatório
        if (commentTextarea.required && !commentTextarea.value.trim()) {
            e.preventDefault();
            showCommentError('Este campo é obrigatório');
            commentTextarea.focus();
            return false;
        }

        // Desabilitar botão e mostrar loading
        if (confirmBtn) {
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i>Processando...';
        }
    }

    /**
     * Manipula a entrada de texto no comentário
     */
    function handleCommentInput() {
        hideCommentError();
        commentTextarea.classList.remove('border-red-500');
    }

    /**
     * Mostra erro no comentário
     */
    function showCommentError(message) {
        if (commentError) {
            commentError.textContent = message;
            commentError.classList.remove('hidden');
        }
        if (commentTextarea) {
            commentTextarea.classList.add('border-red-500');
        }
    }

    /**
     * Esconde erro no comentário
     */
    function hideCommentError() {
        if (commentError) {
            commentError.classList.add('hidden');
        }
    }

    /**
     * Auto-hide de mensagens de sucesso/erro
     */
    function autoHideMessages() {
        const messages = document.querySelectorAll('[id="success"], [id="error"]');
        messages.forEach(function(message) {
            setTimeout(function() {
                if (message && message.parentNode) {
                    message.style.transition = 'opacity 0.5s ease-out';
                    message.style.opacity = '0';
                    setTimeout(function() {
                        if (message.parentNode) {
                            message.parentNode.removeChild(message);
                        }
                    }, 500);
                }
            }, 5000);
        });
    }

    // Anexar eventos iniciais
    attachPaginationEvents();
    attachSortingEvents();

    // Expor funções globalmente para uso nos botões
    window.openActionModal = openActionModal;
    window.closeActionModal = closeActionModal;
    window.toggleSolicitationDetails = toggleSolicitationDetails;
    window.clearSearch = clearSearch;
});

/**
 * Limpa o filtro de busca
 */
function clearSearch() {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.value = '';
        // Trigger the input event to update filters and reset page
        searchInput.dispatchEvent(new Event('input'));
    }
}

/**
 * Alterna a exibição dos detalhes de uma solicitação
 */
function toggleSolicitationDetails(solicitationId) {
    console.log('Alternando detalhes da solicitação:', solicitationId);

    const detailsElement = document.getElementById(`details-${solicitationId}`);
    const arrowElement = document.getElementById(`arrow-${solicitationId}`);

    if (!detailsElement || !arrowElement) {
        console.error('Elementos não encontrados para a solicitação:', solicitationId);
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

/**
 * Abre o modal de confirmação para ações nas solicitações
 */
function openActionModal(action, solicitationId, userName) {
    console.log('Abrindo modal:', { action, solicitationId, userName });

    const modal = document.getElementById('actionModal');
    const form = document.getElementById('actionForm');
    const actionContent = document.getElementById('actionContent');
    const commentSection = document.getElementById('commentSection');
    const confirmBtn = document.getElementById('confirmActionBtn');
    const modalTitle = document.getElementById('actionModalTitle');
    const commentTextarea = document.getElementById('admin_comment');

    if (!modal || !form || !actionContent || !commentSection || !confirmBtn || !modalTitle || !commentTextarea) {
        console.error('Elementos do modal não encontrados');
        return;
    }

    // Resetar o modal
    commentTextarea.value = '';
    commentTextarea.required = false;
    commentTextarea.classList.remove('border-red-500');

    // Configurar a ação baseada no tipo
    switch(action) {
        case 'approve':
            modalTitle.textContent = 'Aprovar Solicitação';
            actionContent.innerHTML = `
                <div class="flex items-start p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                    <i class="fa-solid fa-check-circle text-green-600 dark:text-green-400 text-2xl mr-3 mt-1"></i>
                    <div>
                        <h4 class="font-medium text-green-800 dark:text-green-200 mb-1">Aprovar Solicitação</h4>
                        <p class="text-green-700 dark:text-green-300 mb-2">Você está prestes a aprovar a solicitação de <strong>${userName}</strong>.</p>
                        <p class="text-green-600 dark:text-green-400 text-sm">Esta ação irá aplicar as alterações solicitadas no registro de ponto.</p>
                    </div>
                </div>
            `;
            confirmBtn.className = 'px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors duration-200';
            confirmBtn.innerHTML = '<i class="fa-solid fa-check mr-1"></i>Aprovar';
            commentSection.classList.remove('hidden');
            form.action = `/gestao-ponto/solicitacoes/${solicitationId}/approve`;
            break;

        case 'reject':
            modalTitle.textContent = 'Rejeitar Solicitação';
            actionContent.innerHTML = `
                <div class="flex items-start p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <i class="fa-solid fa-times-circle text-red-600 dark:text-red-400 text-2xl mr-3 mt-1"></i>
                    <div>
                        <h4 class="font-medium text-red-800 dark:text-red-200 mb-1">Rejeitar Solicitação</h4>
                        <p class="text-red-700 dark:text-red-300 mb-2">Você está prestes a rejeitar a solicitação de <strong>${userName}</strong>.</p>
                        <p class="text-red-600 dark:text-red-400 text-sm">É obrigatório informar o motivo da rejeição.</p>
                    </div>
                </div>
            `;
            confirmBtn.className = 'px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors duration-200';
            confirmBtn.innerHTML = '<i class="fa-solid fa-times mr-1"></i>Rejeitar';
            commentSection.classList.remove('hidden');
            commentTextarea.required = true;
            commentTextarea.placeholder = 'O motivo da rejeição deve ter pelo menos 10 caracteres (obrigatório)...';
            form.action = `/gestao-ponto/solicitacoes/${solicitationId}/reject`;
            break;

        case 'cancel':
            modalTitle.textContent = 'Cancelar Solicitação';
            actionContent.innerHTML = `
                <div class="flex items-start p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                    <i class="fa-solid fa-ban text-yellow-600 dark:text-yellow-400 text-2xl mr-3 mt-1"></i>
                    <div>
                        <h4 class="font-medium text-yellow-800 dark:text-yellow-200 mb-1">Cancelar Solicitação</h4>
                        <p class="text-yellow-700 dark:text-yellow-300 mb-2">Você está prestes a cancelar a solicitação de <strong>${userName}</strong>.</p>
                        <p class="text-yellow-600 dark:text-yellow-400 text-sm">Esta ação não pode ser desfeita.</p>
                    </div>
                </div>
            `;
            confirmBtn.className = 'px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors duration-200';
            confirmBtn.innerHTML = '<i class="fa-solid fa-ban mr-1"></i>Cancelar';
            commentSection.classList.add('hidden');
            form.action = `/gestao-ponto/solicitacoes/${solicitationId}/cancel`;
            break;

        default:
            console.error('Ação não reconhecida:', action);
            return;
    }

    // Mostrar o modal
    modal.classList.remove('hidden');

    // Focus no primeiro elemento interativo
    setTimeout(() => {
        if (commentTextarea.required) {
            commentTextarea.focus();
        } else {
            confirmBtn.focus();
        }
    }, 100);
}

/**
 * Fecha o modal de ação
 */
function closeActionModal() {
    console.log('Fechando modal');

    const modal = document.getElementById('actionModal');
    const commentTextarea = document.getElementById('admin_comment');
    const confirmBtn = document.getElementById('confirmActionBtn');
    const commentError = document.getElementById('commentError');

    if (modal) {
        modal.classList.add('hidden');
    }

    // Resetar estado
    if (commentTextarea) {
        commentTextarea.value = '';
        commentTextarea.required = false;
        commentTextarea.classList.remove('border-red-500');
    }

    if (confirmBtn) {
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = '<i class="fa-solid fa-check mr-1"></i>Confirmar';
    }

    if (commentError) {
        commentError.classList.add('hidden');
    }
}
