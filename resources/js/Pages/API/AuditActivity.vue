<template>
    <app-layout>

        <div class="flex lg:pt-36">
            <div class="m-auto w-2/3">


                <audit-activity audits="audits"></audit-activity>

            </div>
        </div>

    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'

import AuditActivity from "@/Components/Api/AuditActivity";

export default {
    props: [
        'audits'
    ],

    components: {
        AuditActivity,
        AppLayout,

    },
}
</script>
