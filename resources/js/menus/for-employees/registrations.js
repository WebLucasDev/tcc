/**
 * Gerenciamento de mensagens de sucesso e erro na página de cadastro de colaboradores
 */

document.addEventListener('DOMContentLoaded', function() {
    // Configuração do tempo de exibição das mensagens (em milissegundos)
    const TIMEOUT_DURATION = 5000; // 5 segundos

    // Seleciona os elementos de mensagem
    const successMessage = document.getElementById('success');
    const errorMessage = document.getElementById('error');

    /**
     * Remove a mensagem com animação de fade out
     * @param {HTMLElement} element - Elemento a ser removido
     */
    function removeMessage(element) {
        if (!element) return;

        // Adiciona animação de fade out
        element.style.transition = 'opacity 0.5s ease-out';
        element.style.opacity = '0';

        // Remove o elemento após a animação
        setTimeout(() => {
            element.remove();
        }, 500);
    }

    // Configura timeout para mensagem de sucesso
    if (successMessage) {
        setTimeout(() => {
            removeMessage(successMessage);
        }, TIMEOUT_DURATION);
    }

    // Configura timeout para mensagem de erro
    if (errorMessage) {
        setTimeout(() => {
            removeMessage(errorMessage);
        }, TIMEOUT_DURATION);
    }

    // Permite fechar as mensagens ao clicar nelas
    if (successMessage) {
        successMessage.style.cursor = 'pointer';
        successMessage.addEventListener('click', () => removeMessage(successMessage));
    }

    if (errorMessage) {
        errorMessage.style.cursor = 'pointer';
        errorMessage.addEventListener('click', () => removeMessage(errorMessage));
    }
});
