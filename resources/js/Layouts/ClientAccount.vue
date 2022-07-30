<template>
    <app-layout>
        <template #header>
            <div class="flex justify-between">
                <div class="flex flex-row content-center">
                    <client-header :client-account="clientAccount"></client-header>
                    <client-menu :client-account="clientAccount"></client-menu>
                </div>

                <div class="flex flex-col ml-6 justify-end">
                    <action-menu :client-account="clientAccount"></action-menu>
                    <slot name="additionalActions"></slot>
                </div>
            </div>
        </template>

        <slot name="body"></slot>

    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import ClientHeader from "@/Components/PM/ClientHeader.vue";
import ClientMenu from '@/Components/PM/ClientMenu.vue'
import ActionMenu from '@/Components/PM/ActionMenu.vue'

export default {
    props: [
        'clientAccount',
    ],

    components: {
        AppLayout,
        ClientHeader,
        ClientMenu,
        ActionMenu
    },
}
</script>
