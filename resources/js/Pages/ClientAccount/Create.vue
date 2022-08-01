<template>
    <app-layout>
        <Head>
            <title>Create Client Account - Dagobah</title>
        </Head>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create a new Client Account
            </h2>

        </template>

        <client-account-form :client="client" :account-structure="accountStructure"
                             :users="users" :creatingMode=true
        />

    </app-layout>

</template>

<script>
import {Head} from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import ClientAccountForm from "@/Components/PM/ClientAccount/Form.vue";

export default {
    name: "CreateClientAccount",

    components: {
        Head,
        ClientAccountForm,
        AppLayout,
    },

    props: [
        'client',
        'accountStructure',
        'users',
    ],

}
</script>

<style scoped>

</style>
