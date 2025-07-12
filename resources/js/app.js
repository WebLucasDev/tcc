import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const forgotPassword = document.getElementById('forgot-password');
    const modalForgot = document.getElementById('modal-forgot');
    const modalContainer = document.getElementById('modal-container');
    const closeModal = document.getElementById('close-modal');
    const modalOverlay = document.getElementById('modal-overlay');

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

    if (forgotPassword && modalForgot && closeModal && modalContainer && modalOverlay) {
        forgotPassword.addEventListener('click', (event) => {
            event.preventDefault();
            openModal();
        });

        closeModal.addEventListener('click', closeModalFunc);
        modalOverlay.addEventListener('click', closeModalFunc);
    }
});

