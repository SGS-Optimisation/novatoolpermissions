<template>
    <app-layout>
        <template #header>
            <div class="flex flex-row">
                <client-header :client-account="clientAccount"></client-header>

                <div v-if="clientAccount != null">
                    <client-menu :client-account="clientAccount"></client-menu>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" v-if="clientAccount != null">
                    <client-account-overview :client-account="clientAccount" :rulesCount="rulesCount"/>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Welcome from '@/Jetstream/Welcome'
import ClientAccountOverview from '@/Pages/ClientAccount/Overview'
import ClientMenu from '@/Components/PM/ClientMenu'
import ClientHeader from "@/Components/PM/ClientHeader";

export default {
    props: [
        'clientAccount',
        'team',
        'rules',
        'rulesCount'
    ],

    components: {
        ClientMenu,
        AppLayout,
        Welcome,
        ClientAccountOverview,
        ClientHeader
    },
}
</script>
