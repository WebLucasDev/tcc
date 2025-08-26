// Positions page functionality
document.addEventListener('DOMContentLoaded', function() {
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

    // Auto-submit do filtro de busca
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                // Auto-submit após 500ms de inatividade
                if (this.value.length >= 3 || this.value.length === 0) {
                    this.form.submit();
                }
            }, 500);
        });
    }

    // Confirmação para ações de exclusão
    const deleteButtons = document.querySelectorAll('[title="Excluir"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Tem certeza que deseja excluir este cargo?')) {
                // TODO: Implementar exclusão
                console.log('Excluir cargo');
            }
        });
    });

    // Highlight na busca
    const searchTerm = new URLSearchParams(window.location.search).get('search');
    if (searchTerm) {
        highlightSearchTerm(searchTerm);
    }

    function highlightSearchTerm(term) {
        const regex = new RegExp(`(${term})`, 'gi');
        const elements = document.querySelectorAll('td, .position-name');

        elements.forEach(element => {
            if (element.textContent.toLowerCase().includes(term.toLowerCase())) {
                element.innerHTML = element.innerHTML.replace(regex, '<mark class="bg-yellow-200 dark:bg-yellow-600">$1</mark>');
            }
        });
    }

    // Função para atualizar URL com parâmetros de filtro
    function updateURLParams(params) {
        const url = new URL(window.location);
        Object.keys(params).forEach(key => {
            if (params[key]) {
                url.searchParams.set(key, params[key]);
            } else {
                url.searchParams.delete(key);
            }
        });
        window.history.pushState({}, '', url);
    }

    // Loading state para formulários
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitButtons = this.querySelectorAll('button[type="submit"]');
            submitButtons.forEach(button => {
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Carregando...';
            });
        });
    });
});
