// Positions page functionality
document.addEventListener('DOMContentLoaded', function() {
    // Cache dos elementos
    const searchInput = document.querySelector('input[name="search"]');
    const departmentSelect = document.querySelector('select[name="department_id"]');
    const sortBySelect = document.querySelector('select[name="sort_by"]');
    const sortDirectionBtn = document.querySelector('[name="sort_direction"]');
    const tableContainer = document.getElementById('positions-table-container');
    const paginationContainer = document.getElementById('pagination-container');
    const statisticsContainer = document.getElementById('statistics-container');
    const resultsSummary = document.getElementById('results-summary');

    // Botões para criar novo cargo
    const btnNewPosition = document.getElementById('btn-new-position');
    const btnNewPositionEmpty = document.getElementById('btn-new-position-empty');

    // Event listeners para botões de novo cargo
    if (btnNewPosition) {
        btnNewPosition.addEventListener('click', function() {
            // TODO: Implementar modal ou redirecionamento para criar cargo
            console.log('Criar novo cargo');
        });
    }

    if (btnNewPositionEmpty) {
        btnNewPositionEmpty.addEventListener('click', function() {
            // TODO: Implementar modal ou redirecionamento para criar cargo
            console.log('Criar primeiro cargo');
        });
    }

    // Estado atual dos filtros
    let currentFilters = {
        search: searchInput ? searchInput.value : '',
        department_id: departmentSelect ? departmentSelect.value : '',
        sort_by: sortBySelect ? sortBySelect.value : 'name',
        sort_direction: getSortDirection()
    };

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

    // Event listener para filtro de departamento
    if (departmentSelect) {
        departmentSelect.addEventListener('change', function() {
            currentFilters.department_id = this.value;
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
            return sortDirectionBtn.value || 'asc';
        }
        return 'asc';
    }

    // Função principal para realizar busca AJAX
    function performAjaxSearch() {
        // Usar o sistema de loading global
        window.GlobalLoading.show('Buscando cargos...');

        // Construir parâmetros da URL
        const params = new URLSearchParams();
        Object.keys(currentFilters).forEach(key => {
            if (currentFilters[key]) {
                params.set(key, currentFilters[key]);
            }
        });

        // Atualizar URL sem recarregar a página
        const newUrl = '/cadastros/cargos' + (params.toString() ? '?' + params.toString() : '');
        window.history.pushState({}, '', newUrl);

        // Fazer requisição AJAX usando o sistema centralizado
        window.GlobalLoading.fetch(newUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            // Verificar se a resposta é JSON válida
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                throw new Error('Resposta não é JSON válida');
            }
        })
        .then(data => {
            if (data.success) {
                updateContent(data);
                updateSortButton();
                updateResultsSummary();
                highlightSearchTerm(currentFilters.search);
            } else {
                throw new Error(data.message || 'Erro desconhecido');
            }
        })
        .catch(error => {
            console.error('Erro na busca:', error);
            showError('Erro ao carregar os dados. Tente novamente.');
        });
    }

    // Função para atualizar o conteúdo da página
    function updateContent(data) {
        if (tableContainer) {
            tableContainer.innerHTML = data.html;
        }

        if (paginationContainer) {
            paginationContainer.innerHTML = data.pagination;
        }

        // Atualizar estatísticas
        if (data.statistics && statisticsContainer) {
            updateStatistics(data.statistics);
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
        attachDeleteEvents();
        attachClearFiltersEvent();
    }

    // Função para atualizar estatísticas
    function updateStatistics(stats) {
        const totalElement = statisticsContainer.querySelector('#total-positions');
        const withDeptElement = statisticsContainer.querySelector('#with-department');
        const withoutDeptElement = statisticsContainer.querySelector('#without-department');

        if (totalElement) totalElement.textContent = stats.total;
        if (withDeptElement) withDeptElement.textContent = stats.with_department;
        if (withoutDeptElement) withoutDeptElement.textContent = stats.without_department;
    }

    // Função para atualizar o botão de ordenação
    function updateSortButton() {
        if (sortDirectionBtn) {
            const icon = sortDirectionBtn.querySelector('i');
            if (icon) {
                icon.className = `fas fa-sort-${currentFilters.sort_direction === 'asc' ? 'up' : 'down'}`;
            }
            sortDirectionBtn.value = currentFilters.sort_direction;
        }

        // Atualizar o select de ordenação
        if (sortBySelect) {
            sortBySelect.value = currentFilters.sort_by;
        }
    }

    // Função para atualizar resumo dos resultados
    function updateResultsSummary() {
        if (resultsSummary) {
            const hasFilters = currentFilters.search || currentFilters.department_id;

            if (hasFilters) {
                resultsSummary.style.display = 'inline';
            } else {
                resultsSummary.style.display = 'none';
            }
        }
    }

    // Função para anexar eventos de paginação
    function attachPaginationEvents() {
        const paginationLinks = document.querySelectorAll('.pagination-links a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = new URL(this.href);
                const page = url.searchParams.get('page');
                if (page) {
                    performAjaxPagination(page);
                }
            });
        });
    }

    // Função para paginação AJAX
    function performAjaxPagination(page) {
        // Usar o sistema de loading global
        window.GlobalLoading.show('Carregando página...');

        const params = new URLSearchParams();
        Object.keys(currentFilters).forEach(key => {
            if (currentFilters[key]) {
                params.set(key, currentFilters[key]);
            }
        });
        params.set('page', page);

        const url = '/cadastros/cargos' + '?' + params.toString();

        // Usar o sistema centralizado de fetch
        window.GlobalLoading.fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            // Verificar se a resposta é JSON válida
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                throw new Error('Resposta não é JSON válida');
            }
        })
        .then(data => {
            if (data.success) {
                updateContent(data);
                highlightSearchTerm(currentFilters.search);

                // Atualizar URL
                window.history.pushState({}, '', url);

                // Scroll para o topo da tabela
                if (tableContainer) {
                    tableContainer.scrollIntoView({ behavior: 'smooth' });
                }
            } else {
                throw new Error(data.message || 'Erro desconhecido');
            }
        })
        .catch(error => {
            console.error('Erro na paginação:', error);
            showError('Erro ao carregar a página. Tente novamente.');
        });
    }

    // Função para anexar eventos de exclusão
    function attachDeleteEvents() {
        const deleteButtons = document.querySelectorAll('[title="Excluir"]');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('Tem certeza que deseja excluir este cargo?')) {
                    // TODO: Implementar exclusão via AJAX
                    console.log('Excluir cargo');
                }
            });
        });
    }

    // Função para anexar evento de limpar filtros
    function attachClearFiltersEvent() {
        const clearFiltersBtn = document.getElementById('btn-clear-filters');
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', function(e) {
                e.preventDefault();
                clearFilters();
            });
        }
    }

    // Função para limpar filtros
    function clearFilters() {
        currentFilters = {
            search: '',
            department_id: '',
            sort_by: 'name',
            sort_direction: 'asc'
        };

        // Limpar campos do formulário
        if (searchInput) searchInput.value = '';
        if (departmentSelect) departmentSelect.value = '';
        if (sortBySelect) sortBySelect.value = 'name';

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

        // Mostrar erro simples no console (removido notificação visual)
        alert('Erro: ' + message);
    }    // Função para highlight na busca
    function highlightSearchTerm(term) {
        if (!term) return;

        const regex = new RegExp(`(${term})`, 'gi');
        const elements = document.querySelectorAll('td, .position-name');

        elements.forEach(element => {
            if (element.textContent.toLowerCase().includes(term.toLowerCase())) {
                element.innerHTML = element.innerHTML.replace(regex, '<mark class="bg-yellow-200 dark:bg-yellow-600">$1</mark>');
            }
        });
    }

    // Anexar eventos iniciais
    attachDeleteEvents();
    attachClearFiltersEvent();

    // Highlight inicial se houver termo de busca na URL
    const urlParams = new URLSearchParams(window.location.search);
    const initialSearchTerm = urlParams.get('search');
    if (initialSearchTerm) {
        highlightSearchTerm(initialSearchTerm);
    }
});
