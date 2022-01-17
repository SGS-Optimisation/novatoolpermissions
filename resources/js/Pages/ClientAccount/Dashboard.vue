<template>
    <client-layout :client-account="clientAccount">
        <Head>
            <title>{{clientAccount.name}} - Dagobah</title>
        </Head>
        <template #body>
            <div class="pb-12 pt-4 flex flex-row">
                <div class="w-1/2 mr-3">

                    <div v-if="clientAccount != null" class="grid grid-flow-row auto-rows-max">

                        <!--Card 1-->
                        <div class="text-gray-500 lg:flex mb-2">
                            <div
                                class="shadow-lg rounded-xl w-full lg:max-w-full bg-white p-4 flex flex-col justify-between leading-normal">
                                <div class="mb-2">
                                    <div class="text-gray-500 font-bold text-xl mb-2">
                                        Rules
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <div class="text-sm text-gray-600">
                                        <p>Total: {{ rulesCount }}</p>
                                        <p>Flagged: {{ flaggedRulesCount }}</p>
                                        <p>Published: {{ publishedRulesCount }}</p>
                                        <p>Omnipresent: {{ omnipresentRulesCount }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--Card 2-->
                        <div class="text-gray-500 lg:flex mb-2">
                            <div
                                class="shadow-lg rounded-xl w-full lg:max-w-full bg-white p-4 flex flex-col justify-between leading-normal">
                                <div class="mb-2">
                                    <div class="text-gray-500 font-bold text-xl mb-2">
                                        Teams
                                    </div>
                                </div>
                                <div class="flex items-center pb-5" v-for="team in teams">
                                    <div class="text-sm text-gray-600">
                                        <p class="font-bold">
                                            <Link
                                                :href="route('pm.client-account.teams.show', {clientAccount: clientAccount.slug, teamId: team.id })">
                                                {{ team.name }}
                                            </Link>
                                        </p>
                                        <p>Owner: {{ team.owner.name }}</p>
                                        <p>{{ team.teamMembers.length }} Members
                                            <span v-for="(members, role) in userRoles(team.teamMembers)">
                                            | {{ members.length }} {{ capitalize(role) }}
                                        </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--Card 3-->
                        <div class="text-gray-500 lg:flex mb-2">
                            <div
                                class="shadow-lg rounded-xl w-full lg:max-w-full bg-white p-4 flex flex-col justify-between leading-normal">
                                <div class="mb-2">
                                    <div class="text-gray-500 font-bold text-xl mb-2">
                                        Categories
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <div class="text-sm text-gray-600">
                                        <p>{{ taxonomiesCount }} Categories</p>
                                        <p>{{ termsCount }} Terms</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="flex-grow">
                    <job-stats-graph :stats="job_stats.stats" :view_by="job_stats.view_by"
                                     :level="job_stats.level" :range="job_stats.range"
                                     :cumulative="job_stats.cumulative" mode="account-specific"/>

                    <rule-stats-graph :stats="rule_stats.stats" :view_by="rule_stats.view_by"
                                      :range="rule_stats.range" :column="rule_stats.column"
                                      :cumulative="rule_stats.cumulative"
                                      :level="rule_stats.level" :region="rule_stats.region"
                                      :chart-height="150" mode="account-specific"/>


                </div>

            </div>
        </template>
    </client-layout>
</template>

<script>

import {Head, Link} from "@inertiajs/inertia-vue3";
import capitalize from 'lodash/capitalize';
import pluralize from 'pluralize/pluralize';
import ClientLayout from '@/Layouts/ClientAccount'
import RuleStatsGraph from "../../Components/Stats/RuleStatsGraph";
import JobStatsGraph from "../../Components/Stats/JobStatsGraph";

export default {
    props: {
        'clientAccount': Object,
        'teams': Object,
        'rulesCount': Number,
        'flaggedRulesCount': Number,
        'publishedRulesCount': Number,
        'omnipresentRulesCount': Number,
        'taxonomiesCount': Number,
        'termsCount': Number,

        'rule_stats': Object,
        'job_stats': Object,
    },

    components: {
        Head,
        Link,
        RuleStatsGraph,
        JobStatsGraph,
        ClientLayout,
    },
    methods: {
        capitalize,
        pluralize,

        userRoles(users) {

            return _.groupBy(
                _.filter(users, function (user) {
                    return user.membership != null;
                }),
                function (user) {
                    return user.membership.role;
                })
        },
    },

    computed: {}
}
</script>
