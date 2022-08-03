import './bootstrap';

import 'primevue/resources/themes/tailwind-light/theme.css';
import 'primevue/resources/primevue.min.css';
import 'primeicons/primeicons.css';
import '/resources/sass/ribbons.scss';

import { createApp, h } from 'vue';
import { createPinia } from 'pinia'
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import PrimeVue from 'primevue/config';
import AutoComplete from 'primevue/autocomplete';
import Tooltip from 'primevue/tooltip';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

const pinia = createPinia();
const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Dagobah';

window.fetcher = url => axios(url).then(res => res.data)

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, app, props, plugin }) {

        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(PrimeVue)
            .use(pinia)
            .component('AutoComplete', AutoComplete)
            .directive('tooltip', Tooltip)
            .mixin({ methods: { route } })
            .mount(el);
    },
});

InertiaProgress.init({ color: '#4B5563' });
