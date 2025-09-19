// Work Hours page functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize form functionality for create/edit pages
    initializeFormFunctionality();

    // Cache dos elementos
    const searchInput = document.querySelector('input[name="search"]');
    const statusSelect = document.querySelector('select[name="status"]');
    const sortBySelect = document.querySelector('select[name="sort_by"]');
    const sortDirectionBtn = document.querySelector('[name="sort_direction"]');
    const tableContainer = document.getElementById('work-hours-table-container');
    const paginationContainer = document.getElementById('pagination-container');
    const statisticsContainer = document.getElementById('statistics-container');
    const resultsSummary = document.getElementById('results-summary');

    // Elementos do modal
    const deleteModal = document.getElementById('delete-modal');
    const workHourNameDisplay = document.getElementById('work-hour-name-display');
    const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
    const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
    let currentWorkHourId = null;

    // Event listeners para o modal
    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', hideDeleteModal);
    }

    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            if (currentWorkHourId) {
                deleteWorkHour(currentWorkHourId);
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

    // Event listener para direção da ordenação
    if (sortDirectionBtn) {
        sortDirectionBtn.addEventListener('click', function() {
            const currentDirection = this.getAttribute('value');
            const newDirection = currentDirection === 'asc' ? 'desc' : 'asc';
            this.setAttribute('value', newDirection);
            currentFilters.sort_direction = newDirection;
            performAjaxSearch();
        });
    }

    // Função para obter direção da ordenação
    function getSortDirection() {
        return sortDirectionBtn ? sortDirectionBtn.getAttribute('value') : 'asc';
    }

    // Função para busca AJAX
    function performAjaxSearch() {
        const params = new URLSearchParams();
        Object.keys(currentFilters).forEach(key => {
            if (currentFilters[key]) {
                params.set(key, currentFilters[key]);
            }
        });

        const url = '/cadastros/jornadas-trabalho' + '?' + params.toString();

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
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
            console.error('Erro na busca:', error);
        });
    }

    // Função para atualizar conteúdo
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
        attachDeleteEvents();
        attachPaginationEvents();
        attachSortingEvents();
    }

    // Função para anexar eventos de ordenação
    function attachSortingEvents() {
        const newSortBySelect = document.querySelector('select[name="sort_by"]');
        const newSortDirectionBtn = document.querySelector('[name="sort_direction"]');

        // Event listener para mudança de campo de ordenação
        if (newSortBySelect) {
            newSortBySelect.addEventListener('change', function() {
                currentFilters.sort_by = this.value;
                performAjaxSearch();
            });
        }

        // Event listener para mudança de direção da ordenação
        if (newSortDirectionBtn) {
            newSortDirectionBtn.addEventListener('click', function() {
                const currentDirection = this.getAttribute('value');
                const newDirection = currentDirection === 'asc' ? 'desc' : 'asc';
                this.setAttribute('value', newDirection);
                currentFilters.sort_direction = newDirection;
                performAjaxSearch();
            });
        }
    }

    // Função para atualizar estatísticas
    function updateStatistics(statistics) {
        const totalElement = document.getElementById('total-work-hours');
        const activeElement = document.getElementById('active-work-hours');
        const inactiveElement = document.getElementById('inactive-work-hours');

        if (totalElement) totalElement.textContent = statistics.total;
        if (activeElement) activeElement.textContent = statistics.active;
        if (inactiveElement) inactiveElement.textContent = statistics.inactive;
    }

    // Função para aplicar tema ao conteúdo novo
    function applyThemeToNewContent(container) {
        // Trigger do sistema de tema se existir
        if (window.ThemeManager && window.ThemeManager.applyTheme) {
            window.ThemeManager.applyTheme();
        }

        // Aplicar tema baseado em variáveis CSS customizadas
        if (window.applyThemeToNewContent) {
            window.applyThemeToNewContent(container);
        }
    }

    // Função para atualizar botão de ordenação
    function updateSortButton() {
        const currentSortDirectionBtn = document.querySelector('[name="sort_direction"]');
        const currentSortBySelect = document.querySelector('select[name="sort_by"]');

        if (currentSortDirectionBtn) {
            const direction = currentFilters.sort_direction;
            const icon = currentSortDirectionBtn.querySelector('i');
            currentSortDirectionBtn.setAttribute('value', direction);
            if (icon) {
                icon.className = `fa-solid fa-sort-${direction === 'asc' ? 'up' : 'down'}`;
            }
        }

        if (currentSortBySelect) {
            currentSortBySelect.value = currentFilters.sort_by;
        }
    }

    // Função para atualizar resumo dos resultados
    function updateResultsSummary() {
        if (resultsSummary) {
            const hasFilters = Object.values(currentFilters).some(value =>
                value && value !== 'name' && value !== 'asc'
            );

            if (hasFilters) {
                resultsSummary.style.display = 'inline';
            } else {
                resultsSummary.style.display = 'none';
            }
        }
    }

    // Função para destacar termo de busca
    function highlightSearchTerm(searchTerm) {
        if (!searchTerm || !tableContainer) return;

        const textNodes = [];
        const walker = document.createTreeWalker(
            tableContainer,
            NodeFilter.SHOW_TEXT,
            null,
            false
        );

        let node;
        while (node = walker.nextNode()) {
            if (node.textContent.toLowerCase().includes(searchTerm.toLowerCase())) {
                textNodes.push(node);
            }
        }

        textNodes.forEach(node => {
            const parent = node.parentNode;
            if (parent.tagName !== 'SCRIPT') {
                const regex = new RegExp(`(${searchTerm})`, 'gi');
                const highlightedText = node.textContent.replace(regex, '<mark class="bg-yellow-200 dark:bg-yellow-800">$1</mark>');

                if (highlightedText !== node.textContent) {
                    const wrapper = document.createElement('span');
                    wrapper.innerHTML = highlightedText;
                    parent.replaceChild(wrapper, node);
                }
            }
        });
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
        const params = new URLSearchParams();
        Object.keys(currentFilters).forEach(key => {
            if (currentFilters[key]) {
                params.set(key, currentFilters[key]);
            }
        });
        params.set('page', page);

        const url = '/cadastros/jornadas-trabalho' + '?' + params.toString();

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
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
        const deleteButtons = document.querySelectorAll('.delete-work-hour-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const workHourId = this.getAttribute('data-work-hour-id');
                const workHourName = this.getAttribute('data-work-hour-name');
                showDeleteModal(workHourId, workHourName);
            });
        });
    }

    // Função para mostrar modal de exclusão
    function showDeleteModal(workHourId, workHourName) {
        currentWorkHourId = workHourId;
        if (workHourNameDisplay) {
            workHourNameDisplay.textContent = workHourName;
        }
        if (deleteModal) {
            deleteModal.classList.remove('hidden');
        }
    }

    // Função para esconder modal de exclusão
    function hideDeleteModal() {
        currentWorkHourId = null;
        if (deleteModal) {
            deleteModal.classList.add('hidden');
        }
    }

    // Função para deletar jornada
    function deleteWorkHour(workHourId) {
        hideDeleteModal();

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/cadastros/jornadas-trabalho/${workHourId}`;

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.getAttribute('content');
            form.appendChild(csrfInput);
        }

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        document.body.appendChild(form);
        form.submit();
    }

    // Inicializar eventos
    attachDeleteEvents();
    attachPaginationEvents();
    attachSortingEvents();
});

// Função para alternar a exibição dos campos de horário por dia
function toggleDayInputs(day) {
    const checkbox = document.getElementById(day + '_active');
    const inputs = document.getElementById(day + '_inputs');

    if (checkbox && inputs) {
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

            if (entry1 && exit1 && entry1.value && exit1.value) {
                totalMinutes += calculateMinutesDiff(entry1.value, exit1.value);
            }

            // Período 2
            const entry2 = document.querySelector(`input[name="${day}_entry_2"]`);
            const exit2 = document.querySelector(`input[name="${day}_exit_2"]`);

            if (entry2 && exit2 && entry2.value && exit2.value) {
                totalMinutes += calculateMinutesDiff(entry2.value, exit2.value);
            }
        }
    });

    const hours = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;
    const weeklyHoursElement = document.getElementById('weekly-hours');

    if (weeklyHoursElement) {
        weeklyHoursElement.textContent = `Total: ${hours}h${minutes > 0 ? ` ${minutes}min` : ''} semanais`;
    }
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

// Função para inicializar funcionalidades do formulário
function initializeFormFunctionality() {
    // Adicionar event listeners para todos os campos de tempo
    const timeInputs = document.querySelectorAll('input[type="time"]');
    timeInputs.forEach(input => {
        input.addEventListener('change', calculateWeeklyHours);
    });

    // Calcular na inicialização se houver dados
    calculateWeeklyHours();
}
