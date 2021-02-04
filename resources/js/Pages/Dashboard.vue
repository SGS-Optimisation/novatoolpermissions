<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{clientAccount.name}}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <client-account-overview :client-account="clientAccount" :rules="rules"/>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import Welcome from '@/Jetstream/Welcome'
    import ClientAccountOverview from './ClientAccount/Overview'

    export default {
        props: [
            'clientAccount',
            'rules',
        ],

        components: {
            AppLayout,
            Welcome,
            ClientAccountOverview
        },
    }
</script>
