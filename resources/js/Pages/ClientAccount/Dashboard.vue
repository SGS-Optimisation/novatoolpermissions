<template>
    <client-layout :client-account="clientAccount">

        <template #body>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                    <div v-if="clientAccount != null"
                         class="p-10 grid grid-cols-1 sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-1 xl:grid-cols-4 gap-5">

                        <!--Card 1-->
                        <div class="text-gray-500 lg:flex ">
                            <div class="shadow-lg rounded-xl w-full lg:max-w-full bg-white p-4 flex flex-col justify-between leading-normal">
                                <div class="mb-8">
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
                        <div class="text-gray-500 lg:flex">
                            <div class="shadow-lg rounded-xl w-full lg:max-w-full bg-white p-4 flex flex-col justify-between leading-normal">
                                <div class="mb-8">
                                    <div class="text-gray-500 font-bold text-xl mb-2">
                                        Team
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <div class="text-sm text-gray-600">
                                        <p>Owner: {{ team.owner.name }}</p>
                                        <p>{{ teamMembers.length }} {{ pluralize('Member', teamMembers.length) }}</p>
                                        <p v-for="(members, role) in userRoles">
                                            {{ members.length }} {{ _.capitalize(pluralize(role, members.length)) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--Card 3-->
                        <div class="text-gray-500 lg:flex">
                            <div class="shadow-lg rounded-xl w-full lg:max-w-full bg-white p-4 flex flex-col justify-between leading-normal">
                                <div class="mb-8">
                                    <div class="text-gray-500 font-bold text-xl mb-2">
                                        Categories
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <div class="text-sm text-gray-600">
                                        <p>{{ taxonomiesCount }} {{ pluralize('Category', taxonomiesCount) }}</p>
                                        <p>{{ termsCount }} {{ pluralize('Term', termsCount) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <h3 class="h3">Rule stats</h3>
                <rule-stats :stats="stats" :view_by="view_by" :range="range" :column="column" :cumulative="cumulative"
                            :level="level" :region="region" :mode="mode"/>

            </div>
        </template>
    </client-layout>
</template>

<script>

import ClientLayout from '@/Layouts/ClientAccount'
import RuleStats from "../../Components/Stats/RuleStats";

export default {
    title() {
        return `${this.clientAccount.name} - Dagobah`;
    },
    props: {
        'clientAccount': Object,
        'team': Object,
        'teamMembers': {
            type: Array,
            default: []
        },
        'rulesCount': Number,
        'flaggedRulesCount': Number,
        'publishedRulesCount': Number,
        'omnipresentRulesCount': Number,
        'taxonomiesCount': Number,
        'termsCount': Number,

        'stats': Object,
        'view_by': String,
        'range': String,
        'column': String,
        'level': String,
        'region': String,
        'cumulative': String,
        'mode': String,
    },

    components: {
        RuleStats,
        ClientLayout,
    },

    computed: {
        userRoles() {

            return _.groupBy(
                _.filter(this.teamMembers, function (user) {
                    return user.membership != null;
                }),
                function (user) {
                    console.log(user.membership);
                    return user.membership.role;
                })
        },
    }
}
</script>
