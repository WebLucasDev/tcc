document.addEventListener('DOMContentLoaded', () => {
    const password = document.getElementById('password')
    const eyeIcon = document.getElementById('eye-icon')
    const modalForgotPassword = document.getElementById('modal-forgot-password')
    const modalContainer = document.getElementById('modal-container')
    const forgotPasswordForm = document.getElementById('forgot-password-form')
    const forgotPasswordFormSubmit = document.getElementById('forgot-password-form-submit')

    window.showPasswordFunc = () => {
        if (password && eyeIcon) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password'

            password.setAttribute('type', type)
            eyeIcon.classList.toggle('fa-eye-slash')
            eyeIcon.classList.toggle('fa-eye')
        }
    }

    window.openModalFunc = () => {
        if (modalForgotPassword && modalContainer){
            modalForgotPassword.classList.remove('hidden')

            setTimeout(() => {
                modalContainer.classList.remove('translate-x-full')
                modalContainer.classList.add('translate-x-0')
            }, 100)
        }
    }

    window.closeModalFunc = () => {
        if (modalForgotPassword && modalContainer){
            modalContainer.classList.remove('translate-x-0')
            modalContainer.classList.add('translate-x-full')

            setTimeout(() => {
                modalForgotPassword.classList.add('hidden')
            }, 300)
        }
    }

    function showSuccessMessage(message) {

        const successMessage = document.getElementById('success-message')
        const successMessageText = document.getElementById('success-message-text')

        if (successMessage && successMessageText) {
            successMessageText.textContent = message
            successMessage.classList.remove('hidden')

            // Rola para o topo para mostrar a mensagem
            window.scrollTo({ top: 0, behavior: 'smooth' })

            // Remove a mensagem após 8 segundos
            setTimeout(() => {
                successMessage.classList.add('hidden')
            }, 8000)
        } else {
            // Fallback: cria notificação flutuante se o componente não estiver disponível
            const notification = document.createElement('div')
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 max-w-sm transition-all duration-300'
            notification.innerHTML = `
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>${message}</span>
                </div>
            `

            document.body.appendChild(notification)

            // Remove a notificação após 5 segundos
            setTimeout(() => {
                notification.style.opacity = '0'
                notification.style.transform = 'translateX(100%)'
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification)
                    }
                }, 300)
            }, 1000)
        }
    }

    // Adiciona funcionalidade AJAX para o formulário de recuperação de senha
    if (forgotPasswordForm) {
        forgotPasswordForm.addEventListener('submit', async (event) => {
            event.preventDefault()

            const formData = new FormData(forgotPasswordForm)

            const originalText = forgotPasswordFormSubmit.textContent
            forgotPasswordFormSubmit.textContent = 'Enviando...'
            forgotPasswordFormSubmit.disabled = true

            // Remove mensagens de erro anteriores
            removeErrorMessages()

            try {
                const response = await fetch('/forgot-password/send', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })

                const data = await response.json()

                if (data.success) {

                    forgotPasswordForm.reset()
                    closeModalFunc()
                    showSuccessMessage(data.message)

                } else {
                    // Mostra mensagem de erro
                    const errorMessage = data.message || 'Erro ao enviar email de recuperação'
                    showErrorMessage(errorMessage)

                    // Se há debug info e estamos em modo debug, mostra
                    if (data.debug) {
                        console.error('Debug info:', data.debug)
                    }
                }

            } catch (error) {
                console.error('Erro:', error)
                showErrorMessage('Erro de conexão. Tente novamente.')

            } finally {
                // Reabilita o botão
                forgotPasswordFormSubmit.textContent = originalText
                forgotPasswordFormSubmit.disabled = false
            }
        })
    }

    function removeErrorMessages() {
        const errorMessages = document.querySelectorAll('.error-message')
        errorMessages.forEach(msg => msg.remove())
    }

    function showErrorMessage(message) {
        const emailInput = document.getElementById('forgot_email')
        if (emailInput) {
            // Remove mensagem anterior se existir
            const existingError = emailInput.parentNode.querySelector('.error-message')
            if (existingError) existingError.remove()

            const errorDiv = document.createElement('div')
            errorDiv.className = 'error-message text-red-500 text-xs mt-1'
            errorDiv.textContent = message
            emailInput.parentNode.appendChild(errorDiv)
        }
    }
})
