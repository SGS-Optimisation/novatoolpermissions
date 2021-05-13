<template>
    <app-layout>

        <div class="flex lg:pt-36 pt-12">
            <div class="m-auto lg:w-2/5">

                    <job-search></job-search>
            </div>
        </div>

    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'

import JobSearch from "@/Components/OP/JobSearchForm";

export default {
    title() {
        return "Dagobah";
    },
    props: [
        'team', ['audits']
    ],

    components: {
        AppLayout,
        JobSearch
    },
}
</script>
