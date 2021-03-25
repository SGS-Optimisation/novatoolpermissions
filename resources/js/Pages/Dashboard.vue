<template>
    <app-layout>

        <div class="flex lg:pt-36">
            <div class="m-auto w-2/3">

                    <job-search></job-search>
            </div>
        </div>

    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'

import JobSearch from "@/Components/OP/JobSearchForm";

export default {
    props: [
        'team', ['audits']
    ],

    components: {
        AppLayout,
        JobSearch
    },
}
</script>
