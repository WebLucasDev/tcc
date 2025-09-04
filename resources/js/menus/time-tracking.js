// Time Tracking page functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('Time Tracking JS carregado');

    // Cache dos elementos
    const searchInput = document.querySelector('input[name="search"]');
    const collaboratorSelect = document.querySelector('#filter_collaborator_id'); // ID único do filtro
    const sortBySelect = document.querySelector('select[name="sort_by"]');
    const sortDirectionBtn = document.querySelector('[name="sort_direction"]');
    const tableContainer = document.getElementById('table-container');
    const paginationContainer = document.getElementById('pagination-container');
    const resultsSummary = document.getElementById('results-summary');

    // Elementos do relógio
    const currentTime = document.getElementById('current-time');
    const currentDate = document.getElementById('current-date');

    // Elementos do formulário de registro
    const timeTrackingForm = document.getElementById('time-tracking-form');
    const collaboratorIdSelect = timeTrackingForm ? timeTrackingForm.querySelector('#collaborator_id') : null;
    const dateInput = timeTrackingForm ? timeTrackingForm.querySelector('#date') : null;
    const timeInput = timeTrackingForm ? timeTrackingForm.querySelector('#time') : null;
    const timeObservationInput = timeTrackingForm ? timeTrackingForm.querySelector('#time_observation') : null;
    const charCounter = timeTrackingForm ? timeTrackingForm.querySelector('#char-counter') : null;

    // Debug: Verificar se os elementos foram encontrados
    console.log('Elementos encontrados:', {
        searchInput: !!searchInput,
        collaboratorSelect: !!collaboratorSelect,
        sortBySelect: !!sortBySelect,
        sortDirectionBtn: !!sortDirectionBtn,
        tableContainer: !!tableContainer,
        paginationContainer: !!paginationContainer,
        currentTime: !!currentTime,
        currentDate: !!currentDate,
        timeTrackingForm: !!timeTrackingForm,
        collaboratorIdSelect: !!collaboratorIdSelect,
        timeObservationInput: !!timeObservationInput,
        charCounter: !!charCounter
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

    // Inicializar formulário de registro
    initTimeTrackingForm();

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
        console.log('✅ Filtro de colaborador encontrado (#filter_collaborator_id), anexando event listener');
        collaboratorSelect.addEventListener('change', function() {
            console.log('🔄 Filtro de colaborador alterado para:', this.value);
            currentFilters.collaborator_id = this.value;
            console.log('📊 Filtros atualizados:', currentFilters);
            performAjaxSearch();
        });
    } else {
        console.error('❌ Elemento collaboratorSelect (#filter_collaborator_id) não encontrado!');
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

        // Atualizar campos de data e hora do formulário se existirem
        updateFormDateTime(now);
    }

    // Função para inicializar o formulário de registro de ponto
    function initTimeTrackingForm() {
        if (!timeTrackingForm) return;

        console.log('Inicializando formulário de registro de ponto');

        // Atualizar campos de data e hora a cada segundo
        updateFormDateTime(new Date());

        // Event listener para o formulário
        timeTrackingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitTimeTracking();
        });

        // Event listener para mudança de colaborador - buscar próximo tipo de registro
        if (collaboratorIdSelect) {
            collaboratorIdSelect.addEventListener('change', function() {
                updateNextTrackingInfo();

                // Auto-completar horário atual quando colaborador é selecionado
                if (this.value && timeInput) {
                    const now = new Date();
                    const currentTime = now.toTimeString().slice(0, 5);
                    timeInput.value = currentTime;
                }
            });
        }

        // Event listener para mudança de data - atualizar próximo tipo
        if (dateInput) {
            dateInput.addEventListener('change', function() {
                updateNextTrackingInfo();
            });
        }

        // Contador de caracteres para observação
        if (timeObservationInput && charCounter) {
            timeObservationInput.addEventListener('input', function() {
                const currentLength = this.value.length;
                charCounter.textContent = `${currentLength}/30`;

                // Mudança de cor baseada no limite
                if (currentLength >= 25) {
                    charCounter.className = 'text-xs text-red-500';
                } else if (currentLength >= 20) {
                    charCounter.className = 'text-xs text-yellow-500';
                } else {
                    charCounter.className = 'text-xs text-gray-500';
                }
            });
        }

        // Inicializar informação do próximo registro
        updateNextTrackingInfo();
    }    // Função para atualizar data e hora no formulário
    function updateFormDateTime(now) {
        if (dateInput && !dateInput.value) {
            const dateString = now.toISOString().split('T')[0];
            dateInput.value = dateString;
        }

        if (timeInput && !timeInput.value) {
            const timeString = now.toTimeString().slice(0, 5);
            timeInput.value = timeString;
        }
    }

    // Função para enviar registro de ponto via formulário normal
    function submitTimeTracking() {
        if (!timeTrackingForm) return;

        console.log('Enviando registro de ponto');

        // Validação básica
        const collaboratorId = collaboratorIdSelect ? collaboratorIdSelect.value : '';

        if (!collaboratorId) {
            alert('Por favor, selecione um colaborador.');
            return;
        }

        // Usar o sistema de loading global
        if (window.GlobalLoading) {
            window.GlobalLoading.show('Registrando ponto...');
        }

        // Submit normal do formulário - não AJAX
        // O Laravel irá processar e redirecionar com as mensagens de sucesso/erro
        timeTrackingForm.submit();
    }

    // Função para resetar o formulário de registro
    function resetTimeTrackingForm() {
        if (!timeTrackingForm) return;

        // Resetar campos selecionados
        if (collaboratorIdSelect) collaboratorIdSelect.value = '';

        // Manter data atual mas limpar horário para novo registro
        const now = new Date();
        if (dateInput) dateInput.value = now.toISOString().split('T')[0];
        if (timeInput) timeInput.value = now.toTimeString().slice(0, 5);

        // Limpar observação e resetar contador
        if (timeObservationInput) {
            timeObservationInput.value = '';
            if (charCounter) {
                charCounter.textContent = '0/30';
                charCounter.className = 'text-xs text-gray-500';
            }
        }

        // Limpar observações gerais (se ainda existir)
        const observationsField = timeTrackingForm.querySelector('#observations');
        if (observationsField) observationsField.value = '';

        // Atualizar informações do próximo registro
        updateNextTrackingInfo();

        console.log('Formulário resetado');
    }

    // Função para atualizar informações do próximo tipo de registro
    function updateNextTrackingInfo() {
        const nextTrackingTypeElement = document.getElementById('next-tracking-type');
        if (!nextTrackingTypeElement) return;

        const collaboratorId = collaboratorIdSelect ? collaboratorIdSelect.value : '';
        const date = dateInput ? dateInput.value : new Date().toISOString().split('T')[0];

        // Se não há colaborador selecionado, mostrar mensagem padrão
        if (!collaboratorId) {
            nextTrackingTypeElement.textContent = 'Selecione um colaborador';
            return;
        }

        // Fazer requisição AJAX para buscar próximo tipo de registro
        const params = new URLSearchParams({
            collaborator_id: collaboratorId,
            date: date
        });

        fetch(`/gestao-ponto/registro-ponto/next-tracking-info?${params}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.next_type_name) {
                nextTrackingTypeElement.textContent = data.next_type_name;

                // Se todos os registros estão completos, desabilitar o botão de submit
                const submitBtn = document.getElementById('submit-btn');
                if (submitBtn) {
                    if (!data.next_type) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        submitBtn.innerHTML = '<i class="fa-solid fa-check"></i> Registros Completos';
                    } else {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        submitBtn.innerHTML = '<i class="fa-solid fa-clock"></i> Registrar Ponto';
                    }
                }
            }
        })
        .catch(error => {
            console.error('Erro ao buscar próximo tipo de registro:', error);
            nextTrackingTypeElement.textContent = 'Erro ao carregar informações';
        });
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
        const newUrl = '/gestao-ponto/registro-ponto' + (params.toString() ? '?' + params.toString() : '');
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
        attachActionEvents();
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

        const url = '/gestao-ponto/registro-ponto' + '?' + params.toString();
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
        // window.location.href = '/gestao-ponto/registro-ponto?error=' + encodeURIComponent(message);
    }

    // Funções globais para os botões
    window.editTimeTracking = function(trackingId, collaboratorName) {
        openEditTimeSelectModal(trackingId, collaboratorName);
    };

    // === FUNÇÕES DE CONTROLE DOS MODAIS DE EDIÇÃO ===

    // Variável para armazenar dados da ação atual
    let currentActionData = null;

    // Abrir modal de seleção de horário
    function openEditTimeSelectModal(trackingId, collaboratorName) {
        console.log('🔥 Abrindo modal de seleção de horário para:', collaboratorName);

        const modal = document.getElementById('editTimeSelectModal');
        const collaboratorNameElement = document.getElementById('selectedCollaboratorName');
        const timeSlotsList = document.getElementById('timeSlotsList');

        if (!modal || !collaboratorNameElement || !timeSlotsList) {
            console.error('❌ Elementos do modal de seleção não encontrados');
            return;
        }

        // Atualizar nome do colaborador
        collaboratorNameElement.textContent = collaboratorName;

        // Buscar dados do registro via AJAX
        fetchTrackingData(trackingId, collaboratorName);

        // Mostrar modal
        modal.style.display = 'flex';
        modal.classList.remove('hidden');

        console.log('✅ Modal de seleção de horário aberto');
    }

    // Fechar modal de seleção de horário
    window.closeEditTimeSelectModal = function() {
        console.log('🔒 Fechando modal de seleção de horário');
        const modal = document.getElementById('editTimeSelectModal');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.add('hidden');

            // Limpar lista de horários
            const timeSlotsList = document.getElementById('timeSlotsList');
            if (timeSlotsList) {
                timeSlotsList.innerHTML = '';
            }
        }
    };

    // Abrir modal final de edição
    function openEditTimeFinishModal(trackingId, collaboratorName, timeSlotType, timeSlotName, currentTime, currentObservation) {
        console.log('🔥 Abrindo modal de edição final para:', timeSlotName);

        // Fechar modal anterior
        window.closeEditTimeSelectModal();

        const modal = document.getElementById('editTimeFinishModal');
        const collaboratorNameElement = document.getElementById('editCollaboratorName');
        const timeSlotNameElement = document.getElementById('editTimeSlotName');
        const timeInput = document.getElementById('editTimeInput');
        const observationInput = document.getElementById('editObservationInput');
        const trackingIdInput = document.getElementById('editTrackingId');
        const timeSlotTypeInput = document.getElementById('editTimeSlotType');
        const charCounter = document.getElementById('editCharCounter');

        if (!modal) {
            console.error('❌ Modal de edição final não encontrado');
            return;
        }

        // Preencher dados do modal
        if (collaboratorNameElement) collaboratorNameElement.textContent = collaboratorName;
        if (timeSlotNameElement) timeSlotNameElement.textContent = timeSlotName;
        if (timeInput) timeInput.value = currentTime || '';
        if (observationInput) {
            observationInput.value = currentObservation || '';
            updateCharCounter();
        }
        if (trackingIdInput) trackingIdInput.value = trackingId;
        if (timeSlotTypeInput) timeSlotTypeInput.value = timeSlotType;

        // Mostrar modal
        modal.style.display = 'flex';
        modal.classList.remove('hidden');

        // Focar no campo de horário
        if (timeInput) {
            setTimeout(() => timeInput.focus(), 100);
        }

        console.log('✅ Modal de edição final aberto');
    }

    // Fechar modal final de edição
    window.closeEditTimeFinishModal = function() {
        console.log('🔒 Fechando modal de edição final');
        const modal = document.getElementById('editTimeFinishModal');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.add('hidden');

            // Limpar formulário
            const form = document.getElementById('editTimeForm');
            if (form) form.reset();

            // Resetar contador
            const charCounter = document.getElementById('editCharCounter');
            if (charCounter) {
                charCounter.textContent = '0/30';
                charCounter.className = 'text-xs text-gray-500';
            }
        }
    };

    // Buscar dados do registro via AJAX
    function fetchTrackingData(trackingId, collaboratorName) {
        console.log('📡 Buscando dados do registro:', trackingId);

        // Mostrar loading enquanto busca os dados
        const timeSlotsList = document.getElementById('timeSlotsList');
        if (timeSlotsList) {
            timeSlotsList.innerHTML = `
                <div class="flex items-center justify-center py-6">
                    <i class="fa-solid fa-spinner fa-spin text-2xl text-[var(--color-main)] mr-3"></i>
                    <span class="text-[var(--color-text)]">Carregando horários...</span>
                </div>
            `;
        }

        // Fazer requisição AJAX para buscar dados reais
        fetch(`/gestao-ponto/registro-ponto/${trackingId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            console.log('📡 Resposta recebida:', response);
            return response.json().then(json => ({ status: response.status, json }));
        })
        .then(({ status, json }) => {
            console.log('📊 Dados recebidos:', json);

            if (status === 200 && json.success) {
                // Sucesso - popular lista com dados reais
                populateTimeSlotsList(trackingId, collaboratorName, json.data);
            } else {
                // Erro
                console.error('❌ Erro ao buscar dados:', json);

                if (timeSlotsList) {
                    timeSlotsList.innerHTML = `
                        <div class="text-center py-6">
                            <i class="fa-solid fa-exclamation-triangle text-2xl text-red-500 mb-3"></i>
                            <p class="text-[var(--color-text)] mb-3">Erro ao carregar horários:</p>
                            <p class="text-sm text-red-500">${json.message || 'Erro desconhecido'}</p>
                            <button onclick="fetchTrackingData(${trackingId}, '${collaboratorName}')"
                                    class="mt-3 px-3 py-1 text-sm bg-[var(--color-main)] text-white rounded hover:bg-[var(--color-main)]/90">
                                Tentar Novamente
                            </button>
                        </div>
                    `;
                }
            }
        })
        .catch(error => {
            console.error('❌ Erro na requisição:', error);

            if (timeSlotsList) {
                timeSlotsList.innerHTML = `
                    <div class="text-center py-6">
                        <i class="fa-solid fa-wifi text-2xl text-red-500 mb-3"></i>
                        <p class="text-[var(--color-text)] mb-3">Erro de conexão</p>
                        <p class="text-sm text-red-500">Verifique sua internet e tente novamente</p>
                        <button onclick="fetchTrackingData(${trackingId}, '${collaboratorName}')"
                                class="mt-3 px-3 py-1 text-sm bg-[var(--color-main)] text-white rounded hover:bg-[var(--color-main)]/90">
                            Tentar Novamente
                        </button>
                    </div>
                `;
            }
        });
    }

    // Popular lista de horários disponíveis para edição
    function populateTimeSlotsList(trackingId, collaboratorName, data) {
        const timeSlotsList = document.getElementById('timeSlotsList');
        if (!timeSlotsList) return;

        console.log('🕐 Populando lista de horários com dados:', data);

        const timeSlots = [
            { type: 'entry_time_1', name: 'Entrada', icon: 'fa-sun', time: data.entry_time_1, observation: data.entry_time_1_observation },
            { type: 'return_time_1', name: 'Saída', icon: 'fa-utensils', time: data.return_time_1, observation: data.return_time_1_observation },
            { type: 'entry_time_2', name: 'Entrada', icon: 'fa-coffee', time: data.entry_time_2, observation: data.entry_time_2_observation },
            { type: 'return_time_2', name: 'Saída', icon: 'fa-moon', time: data.return_time_2, observation: data.return_time_2_observation }
        ];

        // Filtrar apenas horários que existem (foram registrados)
        const availableSlots = timeSlots.filter(slot => slot.time !== null && slot.time !== undefined);

        timeSlotsList.innerHTML = '';

        if (availableSlots.length === 0) {
            // Nenhum horário registrado
            timeSlotsList.innerHTML = `
                <div class="text-center py-6">
                    <i class="fa-solid fa-clock text-2xl text-gray-400 mb-3"></i>
                    <p class="text-[var(--color-text)] mb-2">Nenhum horário registrado</p>
                    <p class="text-sm text-gray-500">Este colaborador não possui registros de ponto para esta data.</p>
                </div>
            `;
            return;
        }

        // Mostrar apenas os horários que foram registrados
        availableSlots.forEach(slot => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'w-full flex items-center justify-between p-3 text-left border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors group';
            button.onclick = () => openEditTimeFinishModal(trackingId, collaboratorName, slot.type, slot.name, slot.time, slot.observation);

            const observationText = slot.observation ?
                `<span class="text-xs text-gray-500 block mt-1">"${slot.observation}"</span>` : '';

            button.innerHTML = `
                <div class="flex items-center gap-3">
                    <i class="fa-solid ${slot.icon} text-[var(--color-main)] text-lg"></i>
                    <div>
                        <div class="font-medium text-[var(--color-text)] group-hover:text-gray-900 dark:group-hover:text-gray-100">
                            ${slot.name}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Registrado às ${slot.time}
                        </div>
                        ${observationText}
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-lg font-semibold text-green-600">${slot.time}</span>
                    <i class="fa-solid fa-chevron-right text-gray-400 group-hover:text-[var(--color-main)] transition-colors"></i>
                </div>
            `;

            timeSlotsList.appendChild(button);
        });

        console.log(`✅ Lista populada com ${availableSlots.length} horários disponíveis para edição`);
    }

    // Função para atualizar contador de caracteres do modal de edição
    function updateCharCounter() {
        const observationInput = document.getElementById('editObservationInput');
        const charCounter = document.getElementById('editCharCounter');

        if (observationInput && charCounter) {
            const currentLength = observationInput.value.length;
            charCounter.textContent = `${currentLength}/30`;

            if (currentLength > 30) {
                charCounter.className = 'text-xs text-red-500';
            } else if (currentLength > 25) {
                charCounter.className = 'text-xs text-yellow-500';
            } else {
                charCounter.className = 'text-xs text-gray-500';
            }
        }
    }

    // Event listener para contador de caracteres do modal
    document.addEventListener('input', function(e) {
        if (e.target && e.target.id === 'editObservationInput') {
            updateCharCounter();
        }
    });

    // Função para enviar edição (placeholder)
    // Função para enviar edição via AJAX
    window.submitTimeEdit = function() {
        const form = document.getElementById('editTimeForm');
        if (!form) {
            console.error('❌ Formulário de edição não encontrado');
            return;
        }

        const formData = new FormData(form);
        const data = {
            tracking_id: formData.get('tracking_id'),
            time_slot_type: formData.get('time_slot_type'),
            time: formData.get('time'),
            observation: formData.get('observation') || ''
        };

        console.log('💾 Enviando dados para edição:', data);

        // Validação básica
        if (!data.tracking_id) {
            console.error('❌ ID do registro não encontrado');
            if (window.GlobalAlerts) {
                window.GlobalAlerts.show('Erro: ID do registro não encontrado', 'error');
            } else {
                alert('Erro: ID do registro não encontrado');
            }
            return;
        }

        if (!data.time_slot_type) {
            console.error('❌ Tipo de horário não encontrado');
            if (window.GlobalAlerts) {
                window.GlobalAlerts.show('Erro: Tipo de horário não encontrado', 'error');
            } else {
                alert('Erro: Tipo de horário não encontrado');
            }
            return;
        }

        if (!data.time) {
            console.error('❌ Horário não informado');
            if (window.GlobalAlerts) {
                window.GlobalAlerts.show('Por favor, informe o horário.', 'warning');
            } else {
                alert('Por favor, informe o horário.');
            }
            document.getElementById('editTimeInput')?.focus();
            return;
        }

        // Mostrar loading
        if (window.GlobalLoading) {
            window.GlobalLoading.show('Salvando alterações...');
        }

        // Desabilitar botão de salvar temporariamente
        const saveButton = document.querySelector('button[onclick="submitTimeEdit()"]');
        if (saveButton) {
            saveButton.disabled = true;
            saveButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Salvando...';
        }

        // Enviar requisição AJAX
        fetch('/gestao-ponto/registro-ponto/update', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            console.log('📡 Resposta recebida:', response);
            return response.json().then(json => ({ status: response.status, json }));
        })
        .then(({ status, json }) => {
            console.log('📊 Dados da resposta:', json);

            if (status === 200 && json.success) {
                // Sucesso
                console.log('✅ Edição realizada com sucesso');

                if (window.GlobalAlerts) {
                    window.GlobalAlerts.show(json.message || 'Horário atualizado com sucesso!', 'success');
                } else {
                    alert(json.message || 'Horário atualizado com sucesso!');
                }

                // Fechar modal
                window.closeEditTimeFinishModal();

                // Atualizar tabela
                performAjaxSearch();

            } else {
                // Erro do servidor
                console.error('❌ Erro na edição:', json);

                const errorMessage = json.message || 'Erro ao atualizar horário';

                if (window.GlobalAlerts) {
                    window.GlobalAlerts.show(errorMessage, 'error');
                } else {
                    alert('Erro: ' + errorMessage);
                }

                // Se há erros de validação específicos, mostrar o primeiro
                if (json.errors && typeof json.errors === 'object') {
                    const firstError = Object.values(json.errors)[0];
                    if (Array.isArray(firstError) && firstError.length > 0) {
                        if (window.GlobalAlerts) {
                            window.GlobalAlerts.show(firstError[0], 'error');
                        } else {
                            alert(firstError[0]);
                        }
                    }
                }
            }
        })
        .catch(error => {
            console.error('❌ Erro na requisição:', error);

            const errorMessage = 'Erro de conexão. Verifique sua internet e tente novamente.';

            if (window.GlobalAlerts) {
                window.GlobalAlerts.show(errorMessage, 'error');
            } else {
                alert(errorMessage);
            }
        })
        .finally(() => {
            // Esconder loading
            if (window.GlobalLoading) {
                window.GlobalLoading.hide();
            }

            // Reabilitar botão de salvar
            if (saveButton) {
                saveButton.disabled = false;
                saveButton.innerHTML = '<i class="fa-solid fa-save mr-1"></i> Salvar Alterações';
            }
        });
    };

    // === FUNÇÕES DE CONTROLE DO MODAL DE AÇÃO ===

    // Abrir modal de confirmação de ação
    function openActionConfirmModal(trackingId, collaboratorName, date, action, actionType) {
        console.log('🔥 Abrindo modal de confirmação de ação:', actionType);

        const modal = document.getElementById('actionConfirmModal');
        const collaboratorNameElement = document.getElementById('actionCollaboratorName');
        const dateElement = document.getElementById('actionDate');
        const modalTitle = document.getElementById('actionModalTitle');
        const modalIcon = document.getElementById('actionModalIcon');
        const actionQuestion = document.getElementById('actionQuestion');
        const actionDescription = document.getElementById('actionDescription');
        const currentActionBadge = document.getElementById('currentActionBadge');
        const messageContainer = document.getElementById('actionMessageContainer');
        const warningIcon = document.getElementById('actionWarningIcon');
        const confirmButton = document.getElementById('confirmActionButton');
        const confirmIcon = document.getElementById('confirmActionIcon');
        const confirmText = document.getElementById('confirmActionText');

        if (!modal) {
            console.error('❌ Modal de confirmação de ação não encontrado');
            return;
        }

        // Armazenar dados da ação
        currentActionData = {
            trackingId,
            collaboratorName,
            date,
            currentAction: action,
            actionType
        };

        // Configurar conteúdo baseado no tipo de ação
        if (actionType === 'cancel') {
            modalTitle.textContent = 'Cancelar Registro';
            modalIcon.className = 'fa-solid fa-ban text-red-600 mr-2';
            actionQuestion.textContent = 'Tem certeza que deseja cancelar este registro de ponto?';
            actionDescription.textContent = 'Esta ação marcará o registro como cancelado. Você poderá restaurá-lo posteriormente se necessário.';
            messageContainer.className = 'mb-6 p-4 rounded-lg border border-red-200 bg-red-50 dark:border-red-700 dark:bg-red-900/20';
            warningIcon.className = 'fa-solid fa-exclamation-triangle text-red-600 text-xl mt-1';
            confirmButton.className = 'px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors duration-200';
            confirmIcon.className = 'fa-solid fa-ban mr-1';
            confirmText.textContent = 'Cancelar Registro';
        } else if (actionType === 'restore') {
            modalTitle.textContent = 'Restaurar Registro';
            modalIcon.className = 'fa-solid fa-undo text-green-600 mr-2';
            actionQuestion.textContent = 'Tem certeza que deseja restaurar este registro de ponto?';
            actionDescription.textContent = 'Esta ação restaurará o registro cancelado, marcando-o como restaurado e tornando-o válido novamente.';
            messageContainer.className = 'mb-6 p-4 rounded-lg border border-green-200 bg-green-50 dark:border-green-700 dark:bg-green-900/20';
            warningIcon.className = 'fa-solid fa-info-circle text-green-600 text-xl mt-1';
            confirmButton.className = 'px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors duration-200';
            confirmIcon.className = 'fa-solid fa-undo mr-1';
            confirmText.textContent = 'Restaurar Registro';
        }

        // Preencher informações do registro
        if (collaboratorNameElement) collaboratorNameElement.textContent = collaboratorName;
        if (dateElement) dateElement.textContent = date;

        // Mostrar status atual
        if (currentActionBadge) {
            if (action) {
                // Simular enum para determinar classe e texto
                let badgeClass, badgeIcon, badgeText;
                switch (action) {
                    case 'edited':
                        badgeClass = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-blue-600';
                        badgeIcon = 'fa-solid fa-edit mr-1';
                        badgeText = 'Editado';
                        break;
                    case 'cancelled':
                        badgeClass = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-red-600';
                        badgeIcon = 'fa-solid fa-ban mr-1';
                        badgeText = 'Cancelado';
                        break;
                    case 'restored':
                        badgeClass = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-yellow-600';
                        badgeIcon = 'fa-solid fa-undo mr-1';
                        badgeText = 'Restaurado';
                        break;
                    default:
                        badgeClass = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-gray-600';
                        badgeIcon = 'fa-solid fa-circle mr-1';
                        badgeText = 'Normal';
                        break;
                }
                currentActionBadge.innerHTML = `
                    <span class="${badgeClass}">
                        <i class="${badgeIcon}"></i>
                        ${badgeText}
                    </span>
                `;
            } else {
                currentActionBadge.innerHTML = `
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-gray-600">
                        <i class="fa-solid fa-circle mr-1"></i>
                        Normal
                    </span>
                `;
            }
        }

        // Mostrar modal
        modal.style.display = 'flex';
        modal.classList.remove('hidden');

        console.log('✅ Modal de confirmação de ação aberto');
    }

    // Fechar modal de confirmação de ação
    window.closeActionConfirmModal = function() {
        console.log('🔒 Fechando modal de confirmação de ação');
        const modal = document.getElementById('actionConfirmModal');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.add('hidden');
            currentActionData = null;
        }
    };

    // Executar ação confirmada
    window.executeAction = function() {
        if (!currentActionData) {
            console.error('❌ Dados da ação não encontrados');
            return;
        }

        console.log('💾 Executando ação:', currentActionData);

        // Preparar formulário
        const form = document.getElementById('actionForm');
        if (!form) {
            console.error('❌ Formulário de ação não encontrado');
            return;
        }

        // Definir action do formulário baseado no tipo
        if (currentActionData.actionType === 'cancel') {
            form.action = `/gestao-ponto/registro-ponto/${currentActionData.trackingId}/cancel`;
        } else if (currentActionData.actionType === 'restore') {
            form.action = `/gestao-ponto/registro-ponto/${currentActionData.trackingId}/restore`;
        }

        // Mostrar loading
        if (window.GlobalLoading) {
            window.GlobalLoading.show(`${currentActionData.actionType === 'cancel' ? 'Cancelando' : 'Restaurando'} registro...`);
        }

        // Fechar modal
        window.closeActionConfirmModal();

        // Submeter formulário
        form.submit();
    };

    // Anexar eventos para botões de ação
    function attachActionEvents() {
        // Botões de cancelar
        const cancelButtons = document.querySelectorAll('.cancel-tracking-btn');
        cancelButtons.forEach(button => {
            button.addEventListener('click', function() {
                const trackingId = this.getAttribute('data-tracking-id');
                const collaboratorName = this.getAttribute('data-tracking-collaborator');
                const date = this.getAttribute('data-tracking-date');
                const action = this.getAttribute('data-tracking-action');

                openActionConfirmModal(trackingId, collaboratorName, date, action, 'cancel');
            });
        });

        // Botões de restaurar
        const restoreButtons = document.querySelectorAll('.restore-tracking-btn');
        restoreButtons.forEach(button => {
            button.addEventListener('click', function() {
                const trackingId = this.getAttribute('data-tracking-id');
                const collaboratorName = this.getAttribute('data-tracking-collaborator');
                const date = this.getAttribute('data-tracking-date');
                const action = this.getAttribute('data-tracking-action');

                openActionConfirmModal(trackingId, collaboratorName, date, action, 'restore');
            });
        });
    }

    // Anexar eventos iniciais
    attachTableEvents();
    attachActionEvents();
    attachClearFiltersEvent();
    attachPaginationEvents(); // Anexar eventos de paginação na inicialização

    // Atualizações iniciais
    updateSortButton(); // Garantir que o botão de ordenação está correto
    updateResultsSummary(); // Atualizar resumo inicial

    console.log('Inicialização completa. Estado dos filtros:', currentFilters);
});
