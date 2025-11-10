import { defineConfig } from 'vite';
import laravel, { refreshPaths } from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/filament-chart-js-plugins.js'],
            refresh: [
                ...refreshPaths,
                'app/Livewire/**',
            ],
        }),
        tailwindcss(),
    ],
    server: {
        host: "0.0.0.0",
        port: 5179,
        strictPort: true,
        https: false,
        hmr: {
            protocol: "wss",
            host: "basis-system.ddev.site", // Update this with your app name
            clientPort: 5179,
        },
    },
});
