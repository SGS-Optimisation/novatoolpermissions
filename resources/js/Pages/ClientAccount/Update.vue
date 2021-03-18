<template>
    <client-layout :client-account="clientAccount">
        <template #body>
            <client-account-form :client="client"/>
        </template>

    </client-layout>

</template>

<script>

import ClientAccountForm from "@/Components/PM/ClientAccount/Form";
import ClientLayout from '@/Layouts/ClientAccount'

export default {
    name: "UpdateClientAccount",

    components: {
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
