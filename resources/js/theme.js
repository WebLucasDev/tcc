document.addEventListener('DOMContentLoaded', function() {

    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    if (savedTheme) {
        document.documentElement.classList.toggle('dark', savedTheme === 'dark');
    } else if (prefersDark) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    }

    updateThemeIcon();
});

window.toggleTheme = function() {
    const html = document.documentElement;
    const isDark = html.classList.contains('dark');

    if (isDark) {
        html.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    } else {
        html.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    }

    updateThemeIcon();
}

window.updateThemeIcon = function() {
    const isDark = document.documentElement.classList.contains('dark');
    const themeIcon = document.querySelector('[onclick="toggleTheme()"] i');

    if (themeIcon) {
        if (isDark) {
            themeIcon.className = 'fa-solid fa-sun';
        } else {
            themeIcon.className = 'fa-solid fa-moon';
        }
    }
}
