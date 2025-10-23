document.addEventListener('DOMContentLoaded', () => {
    const password = document.getElementById('password')
    const eyeIcon = document.getElementById('eye-icon')
    const modalForgotPassword = document.getElementById('modal-forgot-password')
    const modalContainer = document.getElementById('modal-container')

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
})
