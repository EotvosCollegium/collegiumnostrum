import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/hellas.scss',
                'resources/sass/nostrum.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
