import { build, defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css',
                    'resources/js/alerts.js',
                    'resources/js/layout.js',
                    'resources/js/loading.js',
                    'resources/js/theme.js',
                    'resources/js/menus/collaborators.js',
                    'resources/js/menus/comp-time.js',
                    'resources/js/menus/dashboard.js',
                    'resources/js/menus/departments.js',
                    'resources/js/menus/login.js',
                    'resources/js/menus/positions.js',
                    'resources/js/menus/solicitations.js',
                    'resources/js/menus/time-tracking.js',
                    'resources/js/menus/work-hours.js',
                    'resources/js/menus/for-employees/comp-time.js',
                    'resources/js/menus/for-employees/registrations.js',
                    'resources/js/menus/for-employees/solicitations.js',
                    'resources/js/menus/for-employees/solicitation-create.js',
                    'resources/js/menus/for-employees/time-tracking.js'
                ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
