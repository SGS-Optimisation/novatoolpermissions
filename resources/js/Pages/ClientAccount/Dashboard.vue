<template>
    <client-layout :client-account="clientAccount">

        <template #body>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" v-if="clientAccount != null">
                        <client-account-overview :client-account="clientAccount" :rulesCount="rulesCount"/>
                    </div>
                </div>
            </div>
        </template>
    </client-layout>
</template>

<script>

import ClientLayout from '@/Layouts/ClientAccount'
import ClientAccountOverview from '@/Pages/ClientAccount/Overview'

export default {
    props: [
        'clientAccount',
        'team',
        'rules',
        'rulesCount'
    ],

    components: {
        ClientLayout,
        ClientAccountOverview,
    },
}
</script>
