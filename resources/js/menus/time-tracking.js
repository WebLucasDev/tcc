document.addEventListener('DOMContentLoaded', function() {
    // Cache de elementos DOM
    const elements = {
        currentTime: document.getElementById('current-time'),
        currentDate: document.getElementById('current-date'),
        searchInput: document.getElementById('search'),
        searchSuggestions: document.getElementById('search-suggestions'),
        collaboratorSelect: document.getElementById('collaborator_id'),
        sortBy: document.querySelector('select[name="sort_by"]'),
        sortDirection: document.querySelector('button[name="sort_direction"]'),
        clearFiltersBtn: document.getElementById('btn-clear-filters')
    };

    // Estado da aplicação
    let searchTimeout = null;
    const collaborators = window.timeTrackingData?.collaborators || [];

    // Inicialização
    init();

    function init() {
        initClock();
        bindEvents();
        console.log('Sistema de registro de ponto inicializado');
    }

    function bindEvents() {
        // Relógio digital
        updateClock();
        setInterval(updateClock, 1000);

        // Busca com autocompletar
        if (elements.searchInput) {
            elements.searchInput.addEventListener('input', handleSearchInput);
            elements.searchInput.addEventListener('focus', handleSearchFocus);
            elements.searchInput.addEventListener('blur', handleSearchBlur);
        }

        // Eventos de ordenação
        if (elements.sortBy) {
            elements.sortBy.addEventListener('change', handleSortChange);
        }

        if (elements.sortDirection) {
            elements.sortDirection.addEventListener('click', handleSortDirectionChange);
        }

        // Limpar filtros
        if (elements.clearFiltersBtn) {
            elements.clearFiltersBtn.addEventListener('click', clearFilters);
        }

        // Botões de ação nas tabelas
        bindTableEvents();

        // Paginação AJAX
        bindPaginationEvents();

        // Clique fora das sugestões
        document.addEventListener('click', function(e) {
            if (!elements.searchInput?.contains(e.target) && !elements.searchSuggestions?.contains(e.target)) {
                hideSuggestions();
            }
        });
    }

    function initClock() {
        // Clock já será atualizado no bindEvents
    }

    function updateClock() {
        if (!elements.currentTime || !elements.currentDate) return;

        const now = new Date();
        
        // Atualizar hora
        const timeString = now.toLocaleTimeString('pt-BR', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });
        elements.currentTime.textContent = timeString;
        
        // Atualizar data
        const dateString = now.toLocaleDateString('pt-BR', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        elements.currentDate.textContent = dateString.charAt(0).toUpperCase() + dateString.slice(1);
    }

    function handleSearchInput(e) {
        const query = e.target.value.trim();
        
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (query.length >= 1) {
                showSuggestions(query);
            } else {
                hideSuggestions();
            }
        }, 300);
    }

    function handleSearchFocus(e) {
        const query = e.target.value.trim();
        if (query.length >= 1) {
            showSuggestions(query);
        }
    }

    function handleSearchBlur(e) {
        // Delay para permitir clique nas sugestões
        setTimeout(() => {
            hideSuggestions();
        }, 200);
    }

    function showSuggestions(query) {
        if (!elements.searchSuggestions) return;

        const filteredCollaborators = collaborators.filter(collaborator =>
            collaborator.name.toLowerCase().includes(query.toLowerCase())
        );

        if (filteredCollaborators.length === 0) {
            hideSuggestions();
            return;
        }

        const suggestionsHtml = filteredCollaborators.map(collaborator => `
            <div class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer suggestion-item" 
                 data-name="${collaborator.name}" 
                 data-id="${collaborator.id}">
                <div class="text-sm font-medium text-[var(--color-text)]">${collaborator.name}</div>
                <div class="text-xs text-[var(--color-text)] opacity-70">${collaborator.position.name}</div>
            </div>
        `).join('');

        elements.searchSuggestions.innerHTML = suggestionsHtml;
        elements.searchSuggestions.classList.remove('hidden');

        // Bind eventos das sugestões
        elements.searchSuggestions.querySelectorAll('.suggestion-item').forEach(item => {
            item.addEventListener('click', function() {
                const name = this.dataset.name;
                const id = this.dataset.id;
                
                elements.searchInput.value = name;
                if (elements.collaboratorSelect) {
                    elements.collaboratorSelect.value = id;
                }
                hideSuggestions();
            });
        });
    }

    function hideSuggestions() {
        if (elements.searchSuggestions) {
            elements.searchSuggestions.classList.add('hidden');
        }
    }

    function handleSortChange() {
        submitForm();
    }

    function handleSortDirectionChange(e) {
        e.preventDefault();
        const currentDirection = e.target.value;
        const newDirection = currentDirection === 'asc' ? 'desc' : 'asc';
        e.target.value = newDirection;
        
        // Atualizar ícone
        const icon = e.target.querySelector('i');
        if (icon) {
            icon.className = `fa-solid fa-sort-${newDirection === 'asc' ? 'up' : 'down'}`;
        }
        
        submitForm();
    }

    function submitForm() {
        const form = elements.sortBy?.closest('form');
        if (form) {
            // Criar input hidden para sort_direction
            let sortDirectionInput = form.querySelector('input[name="sort_direction"]');
            if (!sortDirectionInput) {
                sortDirectionInput = document.createElement('input');
                sortDirectionInput.type = 'hidden';
                sortDirectionInput.name = 'sort_direction';
                form.appendChild(sortDirectionInput);
            }
            sortDirectionInput.value = elements.sortDirection?.value || 'desc';
            
            form.submit();
        }
    }

    function clearFilters() {
        window.location.href = window.location.pathname;
    }

    function bindTableEvents() {
        // Botões de editar
        document.querySelectorAll('.edit-tracking-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const trackingId = this.dataset.trackingId;
                const collaboratorName = this.dataset.trackingCollaborator;
                editTimeTracking(trackingId, collaboratorName);
            });
        });

        // Botões de excluir
        document.querySelectorAll('.delete-tracking-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const trackingId = this.dataset.trackingId;
                const collaboratorName = this.dataset.trackingCollaborator;
                const date = this.dataset.trackingDate;
                deleteTimeTracking(trackingId, collaboratorName, date);
            });
        });
    }

    function bindPaginationEvents() {
        document.querySelectorAll('.pagination-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const page = this.dataset.page;
                if (page) {
                    loadPage(page);
                }
            });
        });
    }

    async function loadPage(page) {
        try {
            // Mostrar loading global
            window.GlobalLoading.show('Carregando página...');

            // Construir URL com parâmetros atuais
            const url = new URL(window.location.href);
            url.searchParams.set('page', page);

            // Fazer requisição AJAX
            const response = await fetch(url.toString(), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const html = await response.text();
            
            // Parse do HTML retornado
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Extrair apenas a tabela do HTML retornado
            const newTableContainer = doc.querySelector('#time-tracking-container');
            const currentTableContainer = document.querySelector('#time-tracking-container');
            
            if (newTableContainer && currentTableContainer) {
                currentTableContainer.innerHTML = newTableContainer.innerHTML;
                
                // Re-bind eventos após atualizar conteúdo
                bindTableEvents();
                bindPaginationEvents();
                
                // Atualizar URL do browser sem recarregar
                window.history.pushState(null, '', url.toString());
                
                // Scroll suave para o topo da tabela
                currentTableContainer.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start' 
                });
            }

        } catch (error) {
            console.error('Erro ao carregar página:', error);
            showError('Erro ao carregar dados. Tente novamente.');
        } finally {
            window.GlobalLoading.hide();
        }
    }

    function showError(message) {
        // Criar toast de erro
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-transform duration-300 translate-x-full';
        toast.innerHTML = `
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-exclamation-circle"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Animar entrada
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);
        
        // Remover após 3 segundos
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 300);
        }, 3000);
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

    // Auto-update da contagem de resultados
    function updateResultsSummary() {
        const resultsElement = document.getElementById('results-summary');
        if (resultsElement && (elements.searchInput?.value || elements.collaboratorSelect?.value)) {
            resultsElement.style.display = 'inline';
        }
    }

    // Atualizar resumo de resultados na inicialização
    updateResultsSummary();
});