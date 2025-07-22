
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
    const messageError = document.getElementById('message-error');

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
        }, 300);
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

