<template>
    <app-layout>
        <Head><title>
            Dagobah Stats
        </title></Head>
        <template #header>
            <stats-menu/>
        </template>

        <rule-stats :stats="stats" :view_by="view_by" :range="range" :column="column" :cumulative="cumulative"
        :level="level" :region="region" :mode="mode"/>
    </app-layout>
</template>

<script>
import {Head} from "@inertiajs/inertia-vue3";
import AppLayout from '@/Layouts/AppLayout'
import RuleStats from "../../Components/Stats/RuleStatsGraph";
import SubNavLink from "@/Components/SubNavLink";
import StatsMenu from "@/Components/Stats/StatsMenu";

export default {
    name: "RulesActivityPerClientAccountStats",
    components: {
        SubNavLink,
        Head,
        RuleStats,
        AppLayout,
        StatsMenu,
    },
    props: [
        'stats',
        'view_by',
        'range',
        'column',
        'level',
        'region',
        'cumulative',
        'mode'
    ],
}
</script>
