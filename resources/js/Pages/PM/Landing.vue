<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Welcome
            </h2>

        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h2>Your teams</h2>
                <ul>
                    <li v-for="availableTeam in myTeams">
                        <jet-nav-link :href="route('pm.client-account.dashboard', {clientAccount: availableTeam.client_account.slug })">
                            {{ availableTeam.client_account.name }}
                        </jet-nav-link>
                    </li>
                </ul>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Welcome from '@/Jetstream/Welcome'
import JetNavLink from "@/Jetstream/NavLink";

export default {
    name: "Landing",
    props: [
        'team',
        'myTeams',
    ],

    components: {
        AppLayout,
        Welcome,
        JetNavLink
    },
}
</script>
