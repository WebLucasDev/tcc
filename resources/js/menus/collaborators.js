// Collaborators page functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize form masks and department logic if we're on create/edit page
    initializeFormFunctionality();

    // Cache dos elementos
    const searchInput = document.querySelector('input[name="search"]');
    const departmentSelect = document.querySelector('select[name="department_id"]');
    const positionSelect = document.querySelector('select[name="position_id"]');
    const statusSelect = document.querySelector('select[name="status"]');
    const sortBySelect = document.querySelector('select[name="sort_by"]');
    const sortDirectionBtn = document.querySelector('[name="sort_direction"]');
    const tableContainer = document.getElementById('collaborators-table-container');
    const paginationContainer = document.getElementById('pagination-container');
    const statisticsContainer = document.getElementById('statistics-container');
    const resultsSummary = document.getElementById('results-summary');

    // Botões para criar novo colaborador
    const btnNewCollaborator = document.getElementById('btn-new-collaborator');
    const btnNewCollaboratorEmpty = document.getElementById('btn-new-collaborator-empty');
    const saveCollaboratorBtn = document.getElementById('save-collaborator-btn');

    // Prevenir múltiplos cliques no botão de salvar
    if (saveCollaboratorBtn) {
        const form = saveCollaboratorBtn.closest('form');
        if (form) {
            form.addEventListener('submit', function() {
                saveCollaboratorBtn.disabled = true;
            });
        }
    }

    // Elementos do modal
    const deleteModal = document.getElementById('delete-modal');
    const collaboratorNameDisplay = document.getElementById('collaborator-name-display');
    const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
    const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
    let currentCollaboratorId = null;

    // Event listeners para o modal
    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', hideDeleteModal);
    }

    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            if (currentCollaboratorId) {
                deleteCollaborator(currentCollaboratorId);
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
        department_id: departmentSelect ? departmentSelect.value : '',
        position_id: positionSelect ? positionSelect.value : '',
        status: statusSelect ? statusSelect.value : '',
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

    // Event listener para filtro de cargo
    if (positionSelect) {
        positionSelect.addEventListener('change', function() {
            currentFilters.position_id = this.value;
            performAjaxSearch();
        });
    }

    // Event listener para filtro de status
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            currentFilters.status = this.value;
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
        window.GlobalLoading.show('Buscando colaboradores...');

        // Construir parâmetros da URL
        const params = new URLSearchParams();
        Object.keys(currentFilters).forEach(key => {
            if (currentFilters[key]) {
                params.set(key, currentFilters[key]);
            }
        });

        // Atualizar URL sem recarregar a página
        const newUrl = '/cadastros/colaboradores' + (params.toString() ? '?' + params.toString() : '');
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

            // Forçar re-aplicação do tema após inserção do HTML
            applyThemeToNewContent(tableContainer);
        }

        if (paginationContainer) {
            paginationContainer.innerHTML = data.pagination;

            // Aplicar tema na paginação também
            applyThemeToNewContent(paginationContainer);
        }

        // Atualizar estatísticas
        if (data.statistics && statisticsContainer) {
            updateStatistics(data.statistics);
        }

        // Reanexar eventos após atualizar o conteúdo
        reattachEvents();
    }

    // Função para aplicar tema ao conteúdo novo
    function applyThemeToNewContent(container) {
        // Trigger do sistema de tema se existir
        if (window.ThemeManager && window.ThemeManager.applyTheme) {
            window.ThemeManager.applyTheme();
        }

        // Forçar recálculo das variáveis CSS
        setTimeout(() => {
            container.style.display = 'none';
            container.offsetHeight; // Força reflow
            container.style.display = '';

            // Disparar evento personalizado para re-aplicação de estilos
            const event = new CustomEvent('themeUpdate', {
                detail: { container: container }
            });
            document.dispatchEvent(event);
        }, 10);
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
        const totalElement = statisticsContainer.querySelector('#total-collaborators');
        const withDeptElement = statisticsContainer.querySelector('#with-department');
        const withPositionElement = statisticsContainer.querySelector('#with-position');

        if (totalElement) totalElement.textContent = stats.total;
        if (withDeptElement) withDeptElement.textContent = stats.with_department;
        if (withPositionElement) withPositionElement.textContent = stats.with_position;
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
            const hasFilters = currentFilters.search || currentFilters.department_id || currentFilters.position_id;

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

        const url = '/cadastros/colaboradores' + '?' + params.toString();

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
                updateSortButton();
                updateResultsSummary();
                highlightSearchTerm(currentFilters.search);
                window.history.pushState({}, '', url);
            } else {
                throw new Error(data.message || 'Erro desconhecido');
            }
        })
        .catch(error => {
            console.error('Erro na paginação:', error);
        });
    }

    // Função para anexar eventos de exclusão
    function attachDeleteEvents() {
        const deleteButtons = document.querySelectorAll('.delete-collaborator-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const collaboratorId = this.getAttribute('data-collaborator-id');
                const collaboratorName = this.getAttribute('data-collaborator-name');
                showDeleteModal(collaboratorId, collaboratorName);
            });
        });
    }

    // Função para deletar colaborador
    function deleteCollaborator(collaboratorId) {
        // Esconder modal
        hideDeleteModal();

        // Criar formulário para submissão
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/cadastros/colaboradores/${collaboratorId}`;

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
    function showDeleteModal(collaboratorId, collaboratorName) {
        currentCollaboratorId = collaboratorId;
        if (collaboratorNameDisplay) {
            collaboratorNameDisplay.textContent = collaboratorName;
        }
        if (deleteModal) {
            deleteModal.classList.remove('hidden');
        }
    }

    // Função para esconder modal de exclusão
    function hideDeleteModal() {
        if (deleteModal) {
            deleteModal.classList.add('hidden');
            currentCollaboratorId = null;
        }
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
            position_id: '',
            status: '',
            sort_by: 'name',
            sort_direction: 'asc'
        };

        // Limpar campos do formulário
        if (searchInput) searchInput.value = '';
        if (departmentSelect) departmentSelect.value = '';
        if (positionSelect) positionSelect.value = '';
        if (statusSelect) statusSelect.value = '';
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
    }

    // Função para highlight na busca
    function highlightSearchTerm(term) {
        if (!term) return;

        const regex = new RegExp(`(${term})`, 'gi');
        const elements = document.querySelectorAll('td, .collaborator-name');

        elements.forEach(element => {
            if (element.textContent.toLowerCase().includes(term.toLowerCase())) {
                const originalText = element.textContent;
                const highlightedText = originalText.replace(regex, '<mark class="bg-yellow-200 dark:bg-yellow-600">$1</mark>');
                element.innerHTML = highlightedText;
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

// Form functionality for create/edit pages
function initializeFormFunctionality() {
    // Apply input masks
    applyInputMasks();

    // Initialize department selection logic
    initializeDepartmentLogic();
}

// Function to apply input masks
function applyInputMasks() {
    const cpfInput = document.getElementById('cpf');
    const phoneInput = document.getElementById('phone');
    const zipCodeInput = document.getElementById('zip_code');

    if (cpfInput) {
        cpfInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            e.target.value = value;
        });

        // Prevent paste of invalid content
        cpfInput.addEventListener('paste', function(e) {
            setTimeout(() => {
                let value = e.target.value.replace(/\D/g, '');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                e.target.value = value;
            }, 10);
        });
    }

    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
            value = value.replace(/(\d)(\d{4})$/, '$1-$2');
            e.target.value = value;
        });

        // Prevent paste of invalid content
        phoneInput.addEventListener('paste', function(e) {
            setTimeout(() => {
                let value = e.target.value.replace(/\D/g, '');
                value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
                value = value.replace(/(\d)(\d{4})$/, '$1-$2');
                e.target.value = value;
            }, 10);
        });
    }

    if (zipCodeInput) {
        zipCodeInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/^(\d{5})(\d)/, '$1-$2');
            e.target.value = value;
        });

        // Prevent paste of invalid content
        zipCodeInput.addEventListener('paste', function(e) {
            setTimeout(() => {
                let value = e.target.value.replace(/\D/g, '');
                value = value.replace(/^(\d{5})(\d)/, '$1-$2');
                e.target.value = value;
            }, 10);
        });
    }
}

// Function to initialize department selection logic
function initializeDepartmentLogic() {
    const positionSelect = document.getElementById('position_id');
    const departmentDisplay = document.getElementById('department_display');

    if (positionSelect && departmentDisplay) {
        // Update department on position change
        positionSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const departmentName = selectedOption.getAttribute('data-department-name');

            if (departmentName && departmentName !== '') {
                departmentDisplay.value = departmentName;
                departmentDisplay.placeholder = '';
            } else {
                departmentDisplay.value = '';
                departmentDisplay.placeholder = 'Selecione um cargo primeiro';
            }
        });

        // Trigger initial update if there's already a selected position
        if (positionSelect.value) {
            const selectedOption = positionSelect.options[positionSelect.selectedIndex];
            const departmentName = selectedOption.getAttribute('data-department-name');

            if (departmentName && departmentName !== '') {
                departmentDisplay.value = departmentName;
                departmentDisplay.placeholder = '';
            }
        }
    }
}
