<template>
    <app-layout>
        <Head><title>
            Dagobah Stats
        </title></Head>
        <template #header>
            <stats-menu/>
        </template>

        <job-stats-graph :stats="stats" :view_by="view_by" :level="level"
                         :range="range" :column="column" :cumulative="cumulative" :mode="mode"/>
    </app-layout>
</template>

<script>
import {Head} from "@inertiajs/inertia-vue3";
import AppLayout from '@/Layouts/AppLayout.vue'
import JobStatsGraph from "../../Components/Stats/JobStatsGraph.vue";
import SubNavLink from "@/Components/SubNavLink.vue";
import StatsMenu from "@/Components/Stats/StatsMenu.vue";

export default {
    name: "JobActivityPerClientAccountStats",
    components: {
        SubNavLink,
        Head,
        JobStatsGraph,
        AppLayout,
        StatsMenu
    },
    props: [
        'stats',
        'view_by',
        'range',
        'column',
        'cumulative',
        'mode',
        'level'
    ],
}
</script>
