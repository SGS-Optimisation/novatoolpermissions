<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span v-if="clientAccount != null">
                    Client: {{ clientAccount.name }}
                </span>
                <span v-else>
                    {{ team.name }}
                </span>
            </h2>

            <div v-if="clientAccount != null">
                <client-menu :client-account="clientAccount"></client-menu>
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
        ClientAccountOverview
    },
}
</script>
