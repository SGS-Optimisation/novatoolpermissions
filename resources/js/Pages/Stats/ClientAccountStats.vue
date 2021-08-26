<template>
    <app-layout>
        <template #header>
            <div class="flex justify-between">
                <div class="flex flex-row content-center">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Rules activity
                    </h2>
                </div>

                <div class="flex ml-6">
                    <!-- right content -->
                </div>
            </div>


        </template>

        <rule-stats :stats="stats" :view_by="view_by" :range="range" :column="column" :cumulative="cumulative"
        :level="level" :region="region" :mode="mode"/>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import RuleStats from "../../Components/Stats/RuleStats";

export default {
    name: "RulesActivityPerClientAccountStats",
    title: 'Rules Activity per Client Account- Dagobah Stats',
    components: {
        RuleStats,
        AppLayout,
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
