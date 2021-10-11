<template>
    <app-layout>

        <Head>
            <title>Dagobah</title>
        </Head>

        <div class="flex lg:pt-36 pt-12">
            <div class="m-auto lg:w-2/5">
                    <job-search></job-search>
            </div>
        </div>

    </app-layout>
</template>

<script>
import { defineComponent } from 'vue'
import {Head} from "@inertiajs/inertia-vue3";
import AppLayout from '@/Layouts/AppLayout'
import JobSearch from "@/Components/OP/JobSearchForm";

export default defineComponent({
    props: [
        'team'
    ],

    components: {
        AppLayout,
        JobSearch,
        Head,
    },
})
</script>
