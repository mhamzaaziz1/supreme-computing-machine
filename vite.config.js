import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    // Relative, so lazily loaded chunks resolve next to the script that
    // imports them. The plugin's default ("/build/") is absolute from the
    // domain root, which 404s when the app is served from a subdirectory
    // such as /POS/public — pages then load their entry script but fail on
    // every chunk it preloads, and render empty.
    base: './',
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
