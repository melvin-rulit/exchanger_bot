import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
      server: {
    host: '0.0.0.0',
    port: 5173,
    cors: true,
    // orign: 'http://192.168.0.103:5173',
    hmr: {
        host: '192.168.1.27'
    }
    // proxy: {
    //   '/api': {
    //     target: 'http://192.168.0.103:9000',
    //     changeOrigin: true,
    //     secure: false
    //   }
    // }
  },
    clearScreen: false,
    optimizeDeps: {
        exclude: ['<conflicting-package>']
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
