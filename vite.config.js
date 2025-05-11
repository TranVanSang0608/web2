import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    // Add base path if ASSET_URL is set
    base: process.env.ASSET_URL ? process.env.ASSET_URL + '/' : '/',
    build: {
        // Generate manifest for Laravel to use
        manifest: true,
        outDir: 'public/build',
        rollupOptions: {
            output: {
                manualChunks: undefined
            }
        }
    }
});
