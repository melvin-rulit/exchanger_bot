import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    server: {
        host: '0.0.0.0',
        port: 5173,
        cors: true,
        origin: 'http://192.168.1.27:9000',
        hmr: {
            host: '192.168.1.27',
        },
        // proxy: {
        //   '/api': {
        //     target: 'http://192.168.0.103:9000',
        //     changeOrigin: true,
        //     secure: false
        //   }
        // }
    },
    // server: {
    //     host: process.env.VITE_HOST || '127.0.0.1',
    //     port: Number(process.env.VITE_PORT) || 5173,
    //     cors: process.env.VITE_CORS === 'true',
    //     origin: process.env.VITE_ORIGIN || 'http://127.0.0.1:9000',
    //     hmr: {
    //         host: process.env.VITE_HMR_HOST || 'localhost',
    //     },
    // },
    clearScreen: false,
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
});
