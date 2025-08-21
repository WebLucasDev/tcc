// Sidebar functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('aside');
    const toggleButton = document.getElementById('toggle-sidebar');
    const sidebarTexts = document.querySelectorAll('.sidebar-text');
    const userInfo = document.getElementById('user-info');
    const dropdownButtons = document.querySelectorAll('.dropdown-btn');
    const dropdownIcons = document.querySelectorAll('.dropdown-icon');
    const dropdownContents = document.querySelectorAll('.dropdown-content');
    const menuItems = document.querySelectorAll('.menu-item');

    // Prevent menu clicks when sidebar is collapsed
    menuItems.forEach(item => {
        item.addEventListener('click', function(e) {
            if (sidebar.classList.contains('w-16')) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
    });

    dropdownButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Don't allow dropdown interaction when sidebar is collapsed
            if (sidebar.classList.contains('w-16')) {
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
            // Toggle sidebar width
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
        });
    }
});
