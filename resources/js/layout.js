// Sidebar functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('aside');
    const toggleButton = document.getElementById('toggle-sidebar');
    const sidebarTexts = document.querySelectorAll('.sidebar-text');
    const userInfo = document.getElementById('user-info');
    const dropdownButtons = document.querySelectorAll('.dropdown-btn');
    const dropdownIcons = document.querySelectorAll('.dropdown-icon');
    const dropdownContents = document.querySelectorAll('.dropdown-content');
    const overlay = document.getElementById('sidebar-overlay');

    // Função para verificar se está em mobile
    function isMobile() {
        return window.innerWidth < 768; // md breakpoint do Tailwind
    }

    // Função para abrir sidebar no mobile
    function openMobileSidebar() {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevenir scroll do body
    }

    // Função para fechar sidebar no mobile
    function closeMobileSidebar() {
        sidebar.classList.remove('translate-x-0');
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        document.body.style.overflow = ''; // Restaurar scroll do body
    }

    // Fechar sidebar ao clicar no overlay
    if (overlay) {
        overlay.addEventListener('click', closeMobileSidebar);
    }

    // Fechar sidebar apenas ao clicar em links <a> com classe menu-item (não botões)
    const menuLinks = document.querySelectorAll('a.menu-item');
    menuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // No desktop, prevenir cliques quando sidebar está colapsada
            if (!isMobile() && sidebar.classList.contains('w-16')) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }

            // No mobile, fechar sidebar ao clicar em links
            if (isMobile()) {
                closeMobileSidebar();
            }
        });
    });

    // Prevenir interação com botões dropdown quando sidebar está colapsada no desktop
    dropdownButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Don't allow dropdown interaction when sidebar is collapsed (desktop only)
            if (!isMobile() && sidebar.classList.contains('w-16')) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }

            const target = this.getAttribute('data-target');
            const menu = document.getElementById(target);
            const arrow = this.querySelector('.dropdown-icon');

            // Close other dropdowns
            dropdownButtons.forEach(otherButton => {
                if (otherButton !== this) {
                    const otherTarget = otherButton.getAttribute('data-target');
                    const otherMenu = document.getElementById(otherTarget);
                    const otherArrow = otherButton.querySelector('.dropdown-icon');

                    otherMenu.classList.remove('max-h-96');
                    otherMenu.classList.add('max-h-0');
                    otherArrow.classList.remove('rotate-180');
                }
            });

            // Toggle current dropdown
            if (menu.classList.contains('max-h-0')) {
                menu.classList.remove('max-h-0');
                menu.classList.add('max-h-96');
                arrow.classList.add('rotate-180');
            } else {
                menu.classList.remove('max-h-96');
                menu.classList.add('max-h-0');
                arrow.classList.remove('rotate-180');
            }
        });
    });

    // Fechar sidebar ao clicar em links dentro dos dropdowns no mobile
    const dropdownLinks = document.querySelectorAll('.dropdown-content a');
    dropdownLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (isMobile()) {
                closeMobileSidebar();
            }
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.dropdown-btn') && !event.target.closest('.dropdown-content')) {
            dropdownButtons.forEach(button => {
                const target = button.getAttribute('data-target');
                const menu = document.getElementById(target);
                const arrow = button.querySelector('.dropdown-icon');

                menu.classList.remove('max-h-96');
                menu.classList.add('max-h-0');
                arrow.classList.remove('rotate-180');
            });
        }
    });

    // Sidebar toggle functionality
    if (toggleButton) {
        toggleButton.addEventListener('click', function() {
            // Comportamento diferente para mobile e desktop
            if (isMobile()) {
                // No mobile, apenas abrir/fechar a sidebar
                if (sidebar.classList.contains('-translate-x-full')) {
                    openMobileSidebar();
                } else {
                    closeMobileSidebar();
                }
            } else {
                // No desktop, manter o comportamento de colapsar/expandir
                if (sidebar.classList.contains('w-64')) {
                    // Collapse sidebar
                    sidebar.classList.remove('w-64');
                    sidebar.classList.add('w-16');

                    // Hide text elements
                    sidebarTexts.forEach(text => {
                        text.style.display = 'none';
                    });

                    // Hide dropdown icons specifically
                    dropdownIcons.forEach(icon => {
                        icon.style.display = 'none';
                    });

                    // Hide dropdown contents
                    dropdownContents.forEach(content => {
                        content.style.display = 'none';
                    });

                    // Hide user info
                    if (userInfo) {
                        userInfo.style.display = 'none';
                    }

                    // Close all dropdowns when collapsing
                    dropdownButtons.forEach(button => {
                        const target = button.getAttribute('data-target');
                        const menu = document.getElementById(target);
                        const arrow = button.querySelector('.dropdown-icon');

                        menu.classList.remove('max-h-96');
                        menu.classList.add('max-h-0');
                        arrow.classList.remove('rotate-180');
                    });

                } else {
                    // Expand sidebar
                    sidebar.classList.remove('w-16');
                    sidebar.classList.add('w-64');

                    // Show text elements
                    sidebarTexts.forEach(text => {
                        text.style.display = '';
                    });

                    // Show dropdown icons
                    dropdownIcons.forEach(icon => {
                        icon.style.display = '';
                    });

                    // Show dropdown contents
                    dropdownContents.forEach(content => {
                        content.style.display = '';
                    });

                    // Show user info
                    if (userInfo) {
                        userInfo.style.display = '';
                    }
                }
            }
        });
    }

    // Ajustar sidebar ao redimensionar a janela
    window.addEventListener('resize', function() {
        if (!isMobile()) {
            // No desktop, garantir que o overlay está oculto e a sidebar visível
            overlay.classList.add('hidden');
            document.body.style.overflow = '';

            // Se estava colapsada, manter colapsada
            if (!sidebar.classList.contains('w-16')) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
            }
        } else {
            // No mobile, garantir que a sidebar está fora da tela
            if (!overlay.classList.contains('hidden')) {
                // Se o overlay está visível, manter sidebar aberta
                return;
            }
            sidebar.classList.remove('translate-x-0');
            sidebar.classList.add('-translate-x-full');

            // Resetar para w-64 no mobile
            sidebar.classList.remove('w-16');
            sidebar.classList.add('w-64');

            // Mostrar todos os elementos
            sidebarTexts.forEach(text => {
                text.style.display = '';
            });
            dropdownIcons.forEach(icon => {
                icon.style.display = '';
            });
            dropdownContents.forEach(content => {
                content.style.display = '';
            });
            if (userInfo) {
                userInfo.style.display = '';
            }
        }
    });
});
