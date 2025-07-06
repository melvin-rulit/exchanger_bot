import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createPinia } from 'pinia';
import { useUserStore } from './stores/userStore';
import { useLockScreenStore } from '@/stores/lockScreenStore'

const pinia = createPinia();

async function init() {
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

            const userStore = useUserStore();
            const lockStore = useLockScreenStore();
            const initialUser = props.initialPage.props.auth.user || null
            if (initialUser) {
                initialUser.role = props.initialPage.props.auth.role
                userStore.setCurrentUser(props.initialPage.props.auth.user)

                const isLocked = initialUser.settings?.is_locked ?? false;
                lockStore.setInitialLockState(isLocked);
            }
            if (props.initialPage.props.auth?.user) {
                userStore.setUser(props.initialPage.props.auth.user);
            }

            return vueApp.mount(el);
        },
        progress: {
            color: '#4B5563',
        },
    });
}

init();
