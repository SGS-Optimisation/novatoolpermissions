<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create a new Client Account
            </h2>

        </template>

        <client-account-form :client="client"/>

    </app-layout>

</template>

<script>
import AppLayout from "@/Layouts/AppLayout";

import ClientAccountForm from "@/Components/PM/ClientAccount/Form";

export default {
    name: "CreateClientAccount",

    components: {
        ClientAccountForm,
        AppLayout,
    },

    props: [
        'client',
    ],

}
</script>

<style scoped>

</style>
