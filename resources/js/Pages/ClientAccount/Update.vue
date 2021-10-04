<template>
    <client-layout :client-account="clientAccount">
        <Head>
            <title>Settings for {{clientAccount.name}} - Dagobah</title>
        </Head>
        <template #body>
            <client-account-form :client="client"/>
        </template>

    </client-layout>

</template>

<script>

import {Head} from "@inertiajs/inertia-vue3";
import ClientAccountForm from "@/Components/PM/ClientAccount/Form";
import ClientLayout from '@/Layouts/ClientAccount'

export default {
    name: "UpdateClientAccount",

    components: {
        Head,
        ClientLayout,
        ClientAccountForm
    },

    props: [
        'clientAccount',
    ],

    date() {
        return {}
    },

    mounted() {

    },

    computed: {
        client: function() {
            let client = JSON.parse(JSON.stringify(this.clientAccount));
            client.image = null;
            return client;
        }
    }

}
</script>

<style scoped>

</style>
