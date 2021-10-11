<template>
    <Link :href="href" :class="classes">
        <slot></slot>
    </Link>
</template>

<script>
import { defineComponent } from 'vue'
import { Link } from '@inertiajs/inertia-vue3';

export default defineComponent({
    props: ['href', 'active'],
    components: {
        Link
    },

    computed: {
        classes() {
            return this.active
                ? 'text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none text-blue-500 border-b-2 font-medium border-blue-500'
                : 'text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none'
        }
    }
})
</script>
