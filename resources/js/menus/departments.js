// Departments page functionality
document.addEventListener('DOMContentLoaded', function() {
    // Cache dos elementos
    const searchInput = document.querySelector('input[name="search"]');
    const sortBySelect = document.querySelector('select[name="sort_by"]');
    const sortDirectionBtn = document.querySelector('[name="sort_direction"]');
    const tableContainer = document.getElementById('departments-table-container');
    const paginationContainer = document.getElementById('pagination-container');
    const statisticsContainer = document.getElementById('statistics-container');
    const resultsSummary = document.getElementById('results-summary');

    // Botões para criar novo departamento
    const btnNewDepartment = document.getElementById('btn-new-department');
    const btnNewDepartmentEmpty = document.getElementById('btn-new-department-empty');
    const saveDepartmentBtn = document.getElementById('save-department-btn');

    // Prevenir múltiplos cliques no botão de salvar
    if (saveDepartmentBtn) {
        const form = saveDepartmentBtn.closest('form');
        if (form) {
            form.addEventListener('submit', function() {
                saveDepartmentBtn.disabled = true;
            });
        }
    }

    // Elementos do modal
    const deleteModal = document.getElementById('delete-modal');
    const departmentNameDisplay = document.getElementById('department-name-display');
    const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
    const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
    let currentDepartmentId = null;

    // Event listeners para o modal
    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', hideDeleteModal);
    }

    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            if (currentDepartmentId) {
                deleteDepartment(currentDepartmentId);
            }
        });
    }

    // Fechar modal ao clicar no overlay
    if (deleteModal) {
        deleteModal.addEventListener('click', function(e) {
            if (e.target === deleteModal) {
                hideDeleteModal();
            }
        });
    }

    // Fechar modal com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !deleteModal.classList.contains('hidden')) {
            hideDeleteModal();
        }
    });

    // Estado atual dos filtros
    let currentFilters = {
        search: searchInput ? searchInput.value : '',
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
        window.GlobalLoading.show('Buscando departamentos...');

        // Construir parâmetros da URL
        const params = new URLSearchParams();
        Object.keys(currentFilters).forEach(key => {
            if (currentFilters[key]) {
                params.set(key, currentFilters[key]);
            }
        });

        // Atualizar URL sem recarregar a página
        const newUrl = '/cadastros/departamentos' + (params.toString() ? '?' + params.toString() : '');
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
        const totalElement = statisticsContainer.querySelector('#total-departments');
        const withPositionsElement = statisticsContainer.querySelector('#with-positions');
        const withCollaboratorsElement = statisticsContainer.querySelector('#with-collaborators');

        if (totalElement) totalElement.textContent = stats.total;
        if (withPositionsElement) withPositionsElement.textContent = stats.with_positions;
        if (withCollaboratorsElement) withCollaboratorsElement.textContent = stats.with_collaborators;
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
            const hasFilters = currentFilters.search;

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

        const url = '/cadastros/departamentos' + '?' + params.toString();

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
        const deleteButtons = document.querySelectorAll('.delete-department-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const departmentId = this.dataset.departmentId;
                const departmentName = this.dataset.departmentName;

                showDeleteModal(departmentId, departmentName);
            });
        });
    }

    // Função para deletar departamento
    function deleteDepartment(departmentId) {
        // Esconder modal
        hideDeleteModal();

        // Criar formulário para submissão
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/cadastros/departamentos/${departmentId}`;

        // Token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.getAttribute('content');
            form.appendChild(csrfInput);
        }

        // Method DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        // Adicionar ao body e submeter
        document.body.appendChild(form);
        form.submit();
    }

    // Função para mostrar modal de exclusão
    function showDeleteModal(departmentId, departmentName) {
        currentDepartmentId = departmentId;
        if (departmentNameDisplay) {
            departmentNameDisplay.textContent = departmentName;
        }
        if (deleteModal) {
            deleteModal.classList.remove('hidden');
        }
    }

    // Função para esconder modal de exclusão
    function hideDeleteModal() {
        if (deleteModal) {
            deleteModal.classList.add('hidden');
            currentDepartmentId = null;
        }
    }    // Função para anexar evento de limpar filtros
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
            sort_by: 'name',
            sort_direction: 'asc'
        };

        // Limpar campos do formulário
        if (searchInput) searchInput.value = '';
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
    }    // Função para highlight na busca
    function highlightSearchTerm(term) {
        if (!term) return;

        const regex = new RegExp(`(${term})`, 'gi');
        const elements = document.querySelectorAll('td, .department-name');

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
