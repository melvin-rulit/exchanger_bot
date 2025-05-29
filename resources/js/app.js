import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createPinia } from 'pinia'
import { useUserStore } from './stores/userStore';

const appName = import.meta.env.VITE_APP_NAME || '';
const pinia = createPinia()

await createInertiaApp({
    title: (title) => `${title}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) });

        vueApp.use(plugin).use(ZiggyVue).use(pinia);

        // ✅ Установи пользователя в store
        const userStore = useUserStore();
        if (props.initialPage.props.auth?.user) {
            userStore.setUser(props.initialPage.props.auth.user);
        }

        return vueApp.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
