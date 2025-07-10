import './bootstrap';

// Abertura do modal recuperação de senha
document.addEventListener('DOMContentLoaded', () => {
    const forgotPassword = document.getElementById('forgot-password')
    const modalForgot = document.getElementById('modal-forgot')
    const closeModal = document.getElementById('close-modal')

    if (forgotPassword && modalForgot && closeModal) {
        forgotPassword.addEventListener('click', () => {
            modalForgot.classList.remove('hidden')
        })
    }

    closeModal.addEventListener('click', () => {
        modalForgot.classList.add('hidden')
    })
})

