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

        <div class="bg-white justify-around flex">
        <div class="flex flex-col w-4/5 py-12">

            <div class="pl-2 pr-8">
                <h2 class="text-center bg-gray-100 font-semibold">Your teams</h2>
                <div class="flex flex-wrap justify-center pt-1">
                    <div class="p-3" v-for="team in myTeams">
                        <client-account-link :team="team"/>
                    </div>
                </div>
            </div>
            <div class="px-2 mt-8">
                <h2 class="text-center bg-gray-100 font-semibold">Other teams</h2>
                <div class="flex flex-wrap justify-center pt-1">
                    <div class="p-3" v-for="team in otherTeams">
                        <client-account-link :team="team"/>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Welcome from '@/Jetstream/Welcome'
import JetNavLink from "@/Jetstream/NavLink";
import ActionMenu from "@/Components/PM/ActionMenu";
import ClientAccountLink from "@/Components/PM/ClientAccount/ClientAccountLink";

export default {
    name: "Landing",
    props: [
        'team',
        'myTeams',
        'otherTeams',
    ],

    components: {
        ClientAccountLink,
        ActionMenu,
        AppLayout,
        Welcome,
        JetNavLink
    },
}
</script>
