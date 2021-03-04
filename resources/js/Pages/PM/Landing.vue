<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Welcome
            </h2>

        </template>

        <div class="py-12 flex flex-row justify-between">
            <div class="mx-auto sm:px-6 lg:px-8">
                <h2>Your teams</h2>
                <ul>
                    <li v-for="availableTeam in myTeams">
                        <jet-nav-link :href="route('pm.client-account.dashboard', {clientAccount: availableTeam.client_account.slug })">
                            {{ availableTeam.client_account.name }}
                        </jet-nav-link>
                    </li>
                </ul>
            </div>
            <div class="mx-auto sm:px-6 lg:px-8">
                <h2>Other teams</h2>
                <ul>
                    <li v-for="otherTeam in otherTeams">
                        <jet-nav-link v-if="otherTeam.client_account" :href="route('pm.client-account.dashboard', {clientAccount: otherTeam.client_account.slug })">
                            {{ otherTeam.client_account.name }}
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
        'otherTeams',
    ],

    components: {
        AppLayout,
        Welcome,
        JetNavLink
    },
}
</script>
