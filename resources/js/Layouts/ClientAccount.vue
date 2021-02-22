<template>
    <app-layout>
        <template #header>
            <div class="flex flex-row">
                <client-header :client-account="clientAccount"></client-header>

                <client-menu :client-account="clientAccount"></client-menu>
            </div>
        </template>

        <slot name="body"></slot>

    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import ClientLayout from '@/Layouts/ClientAccount'
import ClientHeader from "@/Components/PM/ClientHeader";
import ClientMenu from '@/Components/PM/ClientMenu'

export default {
    props: [
        'clientAccount',
    ],

    components: {
        AppLayout,
        ClientHeader,
        ClientMenu
    },
}
</script>
