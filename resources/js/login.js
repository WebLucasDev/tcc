
document.addEventListener('DOMContentLoaded', () => {
    const forgotPassword = document.getElementById('forgot-password');
    const modalForgot = document.getElementById('modal-forgot');
    const modalContainer = document.getElementById('modal-container');
    const closeModal = document.getElementById('close-modal');
    const modalOverlay = document.getElementById('modal-overlay');
    const passwordInput = document.getElementById('password_input');
    const toggleIconPassword = document.getElementById('toggle-icon-password');
    const eyeIcon = document.getElementById('eye-icon');
    const messageSuccess = document.getElementById('message-success');
    const messageError = document.getElementById('message-errors');
    const forgotPasswordForm = document.getElementById('forgot-password-form');
    const forgotErrorMessage = document.getElementById('forgot-error-message');
    const submitText = document.getElementById('submit-text');
    const loadingText = document.getElementById('loading-text');

    function openModal() {
        modalForgot.classList.remove('hidden');
        setTimeout(() => {
            modalContainer.classList.remove('translate-x-full');
            modalContainer.classList.add('translate-x-0');
        }, 10);
    }

    function closeModalFunc() {
        modalContainer.classList.remove('translate-x-0');
        modalContainer.classList.add('translate-x-full');
        setTimeout(() => {
            modalForgot.classList.add('hidden');
            // Limpar formulário e mensagens de erro
            if (forgotPasswordForm) {
                forgotPasswordForm.reset();
            }
            if (forgotErrorMessage) {
                forgotErrorMessage.classList.add('hidden');
            }
        }, 300);
    }

    function showRecoverSuccessMessage() {
        // Usar o arquivo message-recover-password.blade.php que você já criou
        const successDiv = document.createElement('div');
        successDiv.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 z-50';
        successDiv.innerHTML = `
            <div class="border border-[var(--color-main)] px-2 py-1 rounded-md shadow-md/25">
                <p class="text-sm tracking-wide text-[var(--color-main)]">
                    Instruções de redefinição de senha enviadas por email
                    <span>
                        <i class="fa-solid fa-circle-check text-green-600"></i>
                    </span>
                </p>
            </div>
        `;
        
        document.body.appendChild(successDiv);

        // Remover a mensagem após 5 segundos
        setTimeout(() => {
            successDiv.style.transition = 'opacity 0.5s ease-out';
            successDiv.style.opacity = '0';
            setTimeout(() => {
                if (document.body.contains(successDiv)) {
                    document.body.removeChild(successDiv);
                }
            }, 500);
        }, 5000);
    }

    if (toggleIconPassword && passwordInput && eyeIcon) {
        toggleIconPassword.addEventListener('click', (event) => {
            event.preventDefault();
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });
    }

    if (forgotPassword && modalForgot && closeModal && modalContainer && modalOverlay) {
        forgotPassword.addEventListener('click', (event) => {
            event.preventDefault();
            openModal();
        });

        closeModal.addEventListener('click', closeModalFunc);
        modalOverlay.addEventListener('click', closeModalFunc);
    }

    // Manipular envio do formulário de esqueci senha
    if (forgotPasswordForm) {
        forgotPasswordForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            
            // Mostrar loading
            if (submitText && loadingText) {
                submitText.classList.add('hidden');
                loadingText.classList.remove('hidden');
            }
            
            // Esconder mensagens de erro anteriores
            if (forgotErrorMessage) {
                forgotErrorMessage.classList.add('hidden');
            }

            const formData = new FormData(forgotPasswordForm);
            
            try {
                const response = await fetch(forgotPasswordForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Fechar modal
                    closeModalFunc();
                    
                    // Mostrar mensagem de sucesso após fechar o modal
                    setTimeout(() => {
                        showRecoverSuccessMessage();
                    }, 350);
                } else {
                    // Mostrar erro no modal
                    if (forgotErrorMessage) {
                        forgotErrorMessage.textContent = data.message || 'Erro ao enviar email de redefinição.';
                        forgotErrorMessage.classList.remove('hidden');
                    }
                }
            } catch (error) {
                // Mostrar erro de rede
                if (forgotErrorMessage) {
                    forgotErrorMessage.textContent = 'Erro de conexão. Tente novamente.';
                    forgotErrorMessage.classList.remove('hidden');
                }
            } finally {
                // Esconder loading
                if (submitText && loadingText) {
                    submitText.classList.remove('hidden');
                    loadingText.classList.add('hidden');
                }
            }
        });
    }

    if (messageSuccess) {
        setTimeout(() => {
            messageSuccess.style.transition = 'opacity 0.5s ease-out';
            messageSuccess.style.opacity = '0';
            setTimeout(() => {
                messageSuccess.style.display = 'none';
            }, 500);
        }, 3000);
    }

    if (messageError) {
        setTimeout(() => {
            messageError.style.transition = 'opacity 0.5s ease-out';
            messageError.style.opacity = '0';
            setTimeout(() => {
                messageError.style.display = 'none';
            }, 500);
        }, 3000);
    }

});
