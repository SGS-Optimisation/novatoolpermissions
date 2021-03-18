<template>
    <app-layout>
        <template #header>
            <div class="flex justify-between">
                <div class="flex flex-row content-center">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Welcome
                    </h2>
                </div>

                <div class="flex ml-6">
                    <action-menu/>
                </div>
            </div>



        </template>

        <div class="mx-auto py-12 flex flex-grow">
            <div class="px-2">
                <h2>Your teams</h2>
                <ul>
                    <li v-for="availableTeam in myTeams">
                        <jet-nav-link :href="route('pm.client-account.dashboard', {clientAccount: availableTeam.client_account.slug })">
                            {{ availableTeam.client_account.name }}
                        </jet-nav-link>
                    </li>
                </ul>
            </div>
            <div class="px-2">
                <h2>Other teams</h2>
                <ul class="h-72 flex flex-col flex-wrap">
                    <li class="px-2" v-for="otherTeam in otherTeams">
                        <jet-nav-link :href="route('pm.client-account.dashboard', {clientAccount: otherTeam.client_account.slug })">
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
import ActionMenu from "@/Components/PM/ActionMenu";

export default {
    name: "Landing",
    props: [
        'team',
        'myTeams',
        'otherTeams',
    ],

    components: {
        ActionMenu,
        AppLayout,
        Welcome,
        JetNavLink
    },
}
</script>
