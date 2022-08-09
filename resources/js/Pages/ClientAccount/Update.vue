<template>
    <div>
        <Head><title>
            Settings for {{clientAccount.name}} - Dagobah
        </title></Head>
        <client-layout :client-account="clientAccount">

            <template #body>
                <client-account-form :client="client" :creatingMode=false></client-account-form>
            </template>

        </client-layout>
    </div>

</template>

<script>

import {Head} from "@inertiajs/inertia-vue3";
import ClientAccountForm from "@/Components/PM/ClientAccount/Form.vue";
import ClientLayout from '@/Layouts/ClientAccount.vue'

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
