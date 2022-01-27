<template>
    <app-layout>
        <Head><title>
            Dagobah Stats
        </title></Head>
        <template #header>
            <stats-menu/>
        </template>

        <visits-stats-table :stats="stats" :view_by="view_by" :date="date" mode="global"
                            :level="level" :grouping="grouping" :name="name"
                            :client-account="(clientAccount ? clientAccount.slug : null)"
                            :client-accounts="clientAccounts"/>
    </app-layout>
</template>

<script>
import {Head} from "@inertiajs/inertia-vue3";
import AppLayout from '@/Layouts/AppLayout'
import SubNavLink from "@/Components/SubNavLink";
import StatsMenu from "@/Components/Stats/StatsMenu";
import VisitsStatsTable from "@/Components/Stats/VisitsStatsTable";

export default {
    name: "VisitStats",
    props: [
        'stats',
        'view_by',
        'date',
        'isGlobalMode',
        'level',
        'clientAccount',
        'clientAccounts',
        'grouping',
        'name',
    ],
    components: {
        SubNavLink,
        Head,
        AppLayout,
        StatsMenu,
        VisitsStatsTable
    },
}
</script>

<style scoped>

</style>
