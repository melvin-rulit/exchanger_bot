import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    clearScreen: false,
    esbuild: {
        target: 'esnext',
    },
    build: {
        target: 'esnext',
    },
    optimizeDeps: {
        exclude: ['<conflicting-package>'],
    },
    plugins: [
        laravel({
            input: 'resources/js/app.js',
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
})
