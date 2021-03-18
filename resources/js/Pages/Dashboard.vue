<template>
    <app-layout>

        <div class="flex lg:pt-36">
            <div class="m-auto w-2/3">

                    <job-search></job-search>
                <audit-activity audits="audits"></audit-activity>

            </div>
        </div>

    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'

import JobSearch from "@/Components/OP/JobSearchForm";

import AuditActivity from "@/Components/Api/AuditActivity";

export default {
    props: [
        'team', ['audits']
    ],

    components: {
        AuditActivity,
        AppLayout,
        JobSearch
    },
}
</script>
