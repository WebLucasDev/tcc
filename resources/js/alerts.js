document.addEventListener('DOMContentLoaded', () => {
    const error = document.getElementById('error')
    const success = document.getElementById('success')

    function hideWithFade(element) {
        if (!element) return;
        element.classList.add('transition', 'duration-500', 'ease-in', 'opacity-0', '-translate-y-3');
        setTimeout(() => {
            if (element.parentNode) {
                element.parentNode.removeChild(element);
            }
        }, 500);
    }

    if (error) {
        setTimeout(() => {
            hideWithFade(error)
        }, 3000)
    }
    if (success) {
        setTimeout(() => {
            hideWithFade(success)
        }, 3000)
    }
})
