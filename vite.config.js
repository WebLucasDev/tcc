import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css',
                    'resources/js/alerts.js',
                    'resources/js/login.js',
                    'resources/js/layout.js',
                    'resources/js/theme.js',
                    'resources/js/positions.js',
                ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
