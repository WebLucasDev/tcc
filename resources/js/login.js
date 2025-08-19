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

    // Forgot password form submission
    const forgotForm = document.getElementById('forgot-password-form')
    
    if (forgotForm) {
        forgotForm.addEventListener('submit', function(e) {
            e.preventDefault()
            
            const submitButton = document.getElementById('forgot-password-form-submit')
            const originalText = submitButton.textContent
            submitButton.textContent = 'Enviando...'
            
            const formData = new FormData(this)
            
            fetch('/forgot-password/send', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitButton.textContent = originalText
                
                if (data.success) {
                    // Show success message
                    modalContainer.innerHTML = `
                        <div class="flex flex-col h-full justify-center items-center">
                            <div class="border border-[var(--color-main)] px-4 py-6 rounded-md shadow-md/25 max-w-xs">
                                <p class="text-sm tracking-wide text-[var(--color-main)] text-center">
                                    Instruções de alteração de senha enviadas por email
                                    <span class="block text-center mt-2">
                                        <i class="fa-solid fa-circle-check text-green-600 text-2xl"></i>
                                    </span>
                                </p>
                            </div>
                            <button onclick="closeModalFunc()" class="mt-6 px-4 py-2 text-sm text-[var(--color-text)] hover:text-[var(--color-main)]">
                                Close
                            </button>
                        </div>
                    `
                } else {
                    alert(data.message || 'Error sending recovery email')
                }
            })
            .catch(error => {
                submitButton.textContent = originalText
                alert('Error processing your request')
                console.error('Error:', error)
            })
        })
    }
})
