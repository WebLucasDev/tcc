// Loading Global System
class GlobalLoading {
    constructor() {
        this.loadingElement = null;
        this.init();
    }

    init() {
        // Aguardar o DOM carregar
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.loadingElement = document.getElementById('global-loading');
            });
        } else {
            this.loadingElement = document.getElementById('global-loading');
        }
    }

    show(message = null) {
        if (this.loadingElement) {
            // Atualizar mensagem se fornecida
            if (message) {
                const messageElement = this.loadingElement.querySelector('.loading-message');
                if (messageElement) {
                    messageElement.textContent = message;
                }
            }

            this.loadingElement.classList.remove('hidden');

            // Prevenir scroll da página
            document.body.style.overflow = 'hidden';
        }
    }

    hide() {
        if (this.loadingElement) {
            this.loadingElement.classList.add('hidden');

            // Restaurar scroll da página
            document.body.style.overflow = '';
        }
    }

    // Método para usar com promises
    async wrap(promise, message = null) {
        this.show(message);
        try {
            const result = await promise;
            return result;
        } finally {
            this.hide();
        }
    }

    // Método para usar com fetch
    async fetch(url, options = {}, message = 'Carregando...') {
        this.show(message);
        try {
            const response = await fetch(url, options);
            return response;
        } finally {
            this.hide();
        }
    }
}

// Instância global
window.GlobalLoading = new GlobalLoading();

// Funções de conveniência para compatibilidade
window.showGlobalLoading = (message) => window.GlobalLoading.show(message);
window.hideGlobalLoading = () => window.GlobalLoading.hide();
