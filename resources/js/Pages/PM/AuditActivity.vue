<template>
    <client-layout :client-account="clientAccount">
        <template #body>
            <div class="mx-auto sm:px-6 lg:px-8">


            <div class="flex pt-2">
                <div class="m-auto w-4/5">
                    <div class="container">
                        <div class="panel panel-info">
                            <!-- Default panel contents -->
                            <div class="panel-heading font-semibold text-2xl text-cool-gray-600">
                                <jet-nav-link
                                    title="Back to rule"
                                    :href="route('pm.client-account.rules.edit', {clientAccount: clientAccount.slug, id: ruleId })"  >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.707-10.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L9.414 11H13a1 1 0 100-2H9.414l1.293-1.293z" clip-rule="evenodd" />
                                    </svg>
                                </jet-nav-link>

                                Rule Changes
                            </div>


                            <ul class="list-group" v-if="audits.length !== 0">
                                <li class="list-group-item mb-4 pb-5" v-for="(item, index) in orderedAudits"
                                    :key="index">


                                    On {{ dateFormat(item.created_at)}} by
                                    {{ item.user_name }}


                                    <table class="table-fixed w-full border-collapse border border-gray-400">
                                        <thead>
                                        <tr class=" border border-gray-400">
                                            <th class=" ">
                                                Field
                                            </th>
                                            <th class=" ">
                                                Old
                                            </th>
                                            <th class=" ">
                                                New
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody v-if="item.tax_names">

                                        <tr v-for="(key, value) in item.tax_names" class="border-collapse border border-gray-400">
                                            <td class=" ">{{ value }}</td>
                                            <td><span class="  text-red-700" v-html="key.old"></span></td>
                                            <td><span class="  text-blue-700" v-html="key.new"></span></td>
                                        </tr>
                                        </tbody>
                                        <tbody v-else-if="item.audit">

                                        <tr v-for="(key, value) in item.audit" class="border-collapse border border-gray-400">


                                                <td class=" ">{{ value }}</td>
                                                <td><span class="text-red-700" v-html="key.old"></span></td>
                                                <td><span class="text-blue-700" v-html="key.new"></span></td>

                                        </tr>

                                        </tbody>
                                    </table>


                                    <hr>
                                </li>
                            </ul>
                            <span v-if="audits.length === 0">No history</span>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </template>

    </client-layout>
</template>

<script>
import {defineComponent} from "vue";
import ClientLayout from '@/Layouts/ClientAccount.vue'
import JetNavLink from "@/Jetstream/NavLink.vue";
import date from '@/filters/Date.js';

export default defineComponent({
    name: "AuditActivity",
    props: ['audits', 'clientAccount', 'team', 'ruleId'],

    methods: {
        dateFormat(value) {
            return date(value);
        }
    },

    computed: {
        orderedAudits() {
            return _.orderBy(this.audits, 'created_at', 'desc');
        },
    },

    components: {
        ClientLayout,
        JetNavLink,
    },

})
</script>
