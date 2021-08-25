require('./bootstrap');

require('moment');
const pluralize = require('pluralize');

import Vue from 'vue';

import { InertiaApp } from '@inertiajs/inertia-vue';
import { InertiaForm } from 'laravel-jetstream';
import PortalVue from 'portal-vue';
import moment from "moment";
import vSelect from 'vue-select';
import Multiselect from 'vue-multiselect';
import Pagination from 'vue-pagination-2';
import titleMixin from './mixins/titleMixin'
import VueInstant from 'vue-instant'


Vue.mixin({ methods: { route } });
Vue.mixin(titleMixin);
Vue.use(InertiaApp);
Vue.use(InertiaForm);
Vue.use(PortalVue);
Vue.use(VueInstant)

Vue.component('v-select', vSelect);
Vue.component('multiselect', Multiselect)
Vue.component('pagination', Pagination);

import DateFilter from './filters/date' // Import date

Vue.filter('date', DateFilter ) // register filter globally

Vue.directive('click-outside', {
    bind(el, binding, vnode) {
        var vm = vnode.context;
        var callback = binding.value;

        el.clickOutsideEvent = function (event) {
            if (!(el == event.target || el.contains(event.target))) {
                return callback.call(vm, event);
            }
        };
        document.body.addEventListener('click', el.clickOutsideEvent);
    },
    unbind(el) {
        document.body.removeEventListener('click', el.clickOutsideEvent);
    }
});

const app = document.getElementById('app');

new Vue({
    render: (h) =>
        h(InertiaApp, {
            props: {
                initialPage: JSON.parse(app.dataset.page),
                resolveComponent: (name) => require(`./Pages/${name}`).default,
            },
        }),
}).$mount(app);

//Object.defineProperty(Vue.prototype, '$_', { value: _ });
Vue.set(Vue.prototype, '_', _);
Vue.set(Vue.prototype, 'moment', moment);
Vue.set(Vue.prototype, 'pluralize', pluralize);
