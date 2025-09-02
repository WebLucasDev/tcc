// Time Tracking page functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('Time Tracking JS carregado');

    // Cache dos elementos
    const searchInput = document.querySelector('input[name="search"]');
    const collaboratorSelect = document.querySelector('select[name="collaborator_id"]');
    const sortBySelect = document.querySelector('select[name="sort_by"]');
    const sortDirectionBtn = document.querySelector('[name="sort_direction"]');
    const tableContainer = document.getElementById('table-container');
    const paginationContainer = document.getElementById('pagination-container');
    const resultsSummary = document.getElementById('results-summary');

    // Elementos do relógio
    const currentTime = document.getElementById('current-time');
    const currentDate = document.getElementById('current-date');

    // Debug: Verificar se os elementos foram encontrados
    console.log('Elementos encontrados:', {
        searchInput: !!searchInput,
        collaboratorSelect: !!collaboratorSelect,
        sortBySelect: !!sortBySelect,
        sortDirectionBtn: !!sortDirectionBtn,
        tableContainer: !!tableContainer,
        paginationContainer: !!paginationContainer,
        currentTime: !!currentTime,
        currentDate: !!currentDate
    });

    // Estado atual dos filtros
    let currentFilters = {
        search: searchInput ? searchInput.value : '',
        collaborator_id: collaboratorSelect ? collaboratorSelect.value : '',
        sort_by: sortBySelect ? sortBySelect.value : 'date',
        sort_direction: getSortDirection()
    };

    // Inicializar relógio digital
    initClock();

    // Auto-submit do filtro de busca com debounce
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentFilters.search = this.value;
                performAjaxSearch();
            }, 500);
        });
    }

    // Event listener para filtro de colaborador
    if (collaboratorSelect) {
        collaboratorSelect.addEventListener('change', function() {
            currentFilters.collaborator_id = this.value;
            performAjaxSearch();
        });
    }

    // Event listener para ordenação
    if (sortBySelect) {
        sortBySelect.addEventListener('change', function() {
            currentFilters.sort_by = this.value;
            performAjaxSearch();
        });
    }

    if (sortDirectionBtn) {
        sortDirectionBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const currentDirection = this.value;
            currentFilters.sort_direction = currentDirection === 'asc' ? 'desc' : 'asc';
            performAjaxSearch();
        });
    }

    // Função para obter direção atual de ordenação
    function getSortDirection() {
        if (sortDirectionBtn) {
            return sortDirectionBtn.value || 'desc';
        }
        return 'desc';
    }

    // Função para inicializar o relógio digital
    function initClock() {
        updateClock();
        setInterval(updateClock, 1000);
    }

    // Função para atualizar o relógio
    function updateClock() {
        if (!currentTime || !currentDate) return;

        const now = new Date();
        
        // Atualizar hora
        const timeString = now.toLocaleTimeString('pt-BR', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });
        currentTime.textContent = timeString;
        
        // Atualizar data
        const dateString = now.toLocaleDateString('pt-BR', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        currentDate.textContent = dateString.charAt(0).toUpperCase() + dateString.slice(1);
    }

    // Função principal para realizar busca AJAX
    function performAjaxSearch() {
        console.log('Executando busca AJAX com filtros:', currentFilters);
        
        // Verificar se GlobalLoading existe
        if (!window.GlobalLoading) {
            console.error('GlobalLoading não encontrado!');
            return;
        }

        // Usar o sistema de loading global
        window.GlobalLoading.show('Buscando registros...');

        // Construir parâmetros da URL
        const params = new URLSearchParams();
        Object.keys(currentFilters).forEach(key => {
            if (currentFilters[key]) {
                params.set(key, currentFilters[key]);
            }
        });

        // Atualizar URL sem recarregar a página
        const newUrl = '/cadastros/registro-ponto' + (params.toString() ? '?' + params.toString() : '');
        console.log('URL da requisição:', newUrl);
        
        window.history.pushState({}, '', newUrl);

        // Fazer requisição AJAX usando fetch normal
        fetch(newUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            console.log('Resposta recebida:', response.status, response.headers.get('content-type'));
            
            // Verificar se a resposta é JSON válida
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                throw new Error('Resposta não é JSON válida');
            }
        })
        .then(data => {
            console.log('Dados recebidos:', data);
            
            if (data.success) {
                updateContent(data);
                updateSortButton();
                updateResultsSummary();
            } else {
                showError(data.message || 'Erro ao carregar os dados');
            }
        })
        .catch(error => {
            console.error('Erro na busca:', error);
            showError('Erro ao carregar os dados. Tente novamente.');
        })
        .finally(() => {
            if (window.GlobalLoading) {
                window.GlobalLoading.hide();
            }
        });
    }

    // Função para atualizar o conteúdo da página
    function updateContent(data) {
        console.log('Atualizando conteúdo com:', data);
        
        if (tableContainer && data.html) {
            console.log('Atualizando tabela');
            tableContainer.innerHTML = data.html;
        } else {
            console.error('tableContainer não encontrado ou data.html vazio');
        }

        if (paginationContainer) {
            console.log('Atualizando paginação');
            paginationContainer.innerHTML = data.pagination || '';
        } else {
            console.error('paginationContainer não encontrado');
        }

        // Reanexar eventos após atualizar o conteúdo
        reattachEvents();
    }

    // Função para reanexar todos os eventos após atualização de conteúdo
    function reattachEvents() {
        // Reanexar eventos de ordenação
        const newSortBySelect = document.querySelector('select[name="sort_by"]');
        const newSortDirectionBtn = document.querySelector('[name="sort_direction"]');

        if (newSortBySelect) {
            newSortBySelect.addEventListener('change', function() {
                currentFilters.sort_by = this.value;
                performAjaxSearch();
            });
        }

        if (newSortDirectionBtn) {
            newSortDirectionBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const currentDirection = this.value;
                currentFilters.sort_direction = currentDirection === 'asc' ? 'desc' : 'asc';
                performAjaxSearch();
            });
        }

        // Anexar outros eventos
        attachPaginationEvents();
        attachTableEvents();
        attachClearFiltersEvent();
    }

    // Função para atualizar o botão de ordenação
    function updateSortButton() {
        console.log('Atualizando botão de ordenação');
        
        if (sortDirectionBtn) {
            const icon = sortDirectionBtn.querySelector('i');
            if (icon) {
                icon.className = `fa-solid fa-sort-${currentFilters.sort_direction === 'asc' ? 'up' : 'down'}`;
            }
            sortDirectionBtn.value = currentFilters.sort_direction;
            console.log('Botão de direção atualizado para:', currentFilters.sort_direction);
        }

        // Atualizar o select de ordenação
        if (sortBySelect) {
            sortBySelect.value = currentFilters.sort_by;
            console.log('Select de ordenação atualizado para:', currentFilters.sort_by);
        }
    }

    // Função para atualizar resumo dos resultados
    function updateResultsSummary() {
        if (resultsSummary) {
            const hasFilters = currentFilters.search || currentFilters.collaborator_id;
            console.log('Atualizando resumo. Filtros ativos:', hasFilters, currentFilters);
            resultsSummary.style.display = hasFilters ? 'inline' : 'none';
        } else {
            console.log('results-summary element não encontrado');
        }
    }

    // Função para anexar eventos de paginação
    function attachPaginationEvents() {
        const paginationButtons = document.querySelectorAll('.pagination-btn');
        console.log('Anexando eventos de paginação. Botões encontrados:', paginationButtons.length);
        
        paginationButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const page = this.dataset.page;
                console.log('Clique na paginação, página:', page);
                if (page) {
                    performAjaxPagination(page);
                }
            });
        });
    }

    // Função para paginação AJAX
    function performAjaxPagination(page) {
        console.log('Executando paginação AJAX para página:', page);
        
        // Verificar se GlobalLoading existe
        if (!window.GlobalLoading) {
            console.error('GlobalLoading não encontrado!');
            return;
        }

        // Usar o sistema de loading global
        window.GlobalLoading.show('Carregando página...');

        const params = new URLSearchParams();
        Object.keys(currentFilters).forEach(key => {
            if (currentFilters[key]) {
                params.set(key, currentFilters[key]);
            }
        });
        params.set('page', page);

        const url = '/cadastros/registro-ponto' + '?' + params.toString();
        console.log('URL da paginação:', url);

        // Atualizar URL sem recarregar a página
        window.history.pushState({}, '', url);

        // Usar fetch normal
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            console.log('Resposta da paginação:', response.status);
            
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                throw new Error('Resposta não é JSON válida');
            }
        })
        .then(data => {
            console.log('Dados da paginação:', data);
            
            if (data.success) {
                updateContent(data);
                updateResultsSummary();
            } else {
                showError(data.message || 'Erro ao carregar os dados');
            }
        })
        .catch(error => {
            console.error('Erro na paginação:', error);
            showError('Erro ao carregar página. Tente novamente.');
        })
        .finally(() => {
            if (window.GlobalLoading) {
                window.GlobalLoading.hide();
            }
        });
    }

    // Função para anexar eventos de tabela
    function attachTableEvents() {
        // Botões de editar
        const editButtons = document.querySelectorAll('.edit-tracking-btn');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const trackingId = this.dataset.trackingId;
                const collaboratorName = this.dataset.trackingCollaborator;
                editTimeTracking(trackingId, collaboratorName);
            });
        });

        // Botões de excluir
        const deleteButtons = document.querySelectorAll('.delete-tracking-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const trackingId = this.dataset.trackingId;
                const collaboratorName = this.dataset.trackingCollaborator;
                const date = this.dataset.trackingDate;
                deleteTimeTracking(trackingId, collaboratorName, date);
            });
        });
    }

    // Função para anexar evento de limpar filtros
    function attachClearFiltersEvent() {
        const clearFiltersBtn = document.getElementById('btn-clear-filters');
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', clearFilters);
        }
    }

    // Função para limpar filtros
    function clearFilters() {
        currentFilters = {
            search: '',
            collaborator_id: '',
            sort_by: 'date',
            sort_direction: 'desc'
        };

        // Limpar campos do formulário
        if (searchInput) searchInput.value = '';
        if (collaboratorSelect) collaboratorSelect.value = '';
        if (sortBySelect) sortBySelect.value = 'date';

        // Realizar nova busca
        performAjaxSearch();
    }

    // Função para mostrar erro
    function showError(message) {
        console.error('Erro detalhado:', message);

        // Se houver um elemento de loading ativo, escondê-lo
        if (window.GlobalLoading) {
            window.GlobalLoading.hide();
        }

        // Mostrar erro em alert temporariamente para debug
        alert('Erro: ' + message);
        
        // TODO: Implementar toast de erro em vez de alert
        // window.location.href = '/cadastros/registro-ponto?error=' + encodeURIComponent(message);
    }

    // Funções globais para os botões
    window.editTimeTracking = function(trackingId, collaboratorName) {
        // TODO: Implementar modal de edição
        console.log('Editar registro:', trackingId, collaboratorName);
        alert(`Funcionalidade de editar será implementada para: ${collaboratorName}`);
    };

    window.deleteTimeTracking = function(trackingId, collaboratorName, date) {
        if (confirm(`Tem certeza que deseja excluir o registro de ponto de ${collaboratorName} do dia ${date}?`)) {
            // TODO: Implementar exclusão via AJAX
            console.log('Excluir registro:', trackingId, collaboratorName, date);
            alert(`Funcionalidade de exclusão será implementada para: ${collaboratorName} - ${date}`);
        }
    };

    // Anexar eventos iniciais
    attachTableEvents();
    attachClearFiltersEvent();
    attachPaginationEvents(); // Anexar eventos de paginação na inicialização

    // Atualizações iniciais
    updateSortButton(); // Garantir que o botão de ordenação está correto
    updateResultsSummary(); // Atualizar resumo inicial
    
    console.log('Inicialização completa. Estado dos filtros:', currentFilters);
});