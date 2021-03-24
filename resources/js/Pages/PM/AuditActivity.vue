<template>
    <client-layout :client-account="clientAccount">
        <template #body>
            <div class="mx-auto sm:px-6 lg:px-8">


            <div class="flex lg:pt-36">
                <div class="m-auto w-2/3">
                    <div class="container">
                        <div class="panel panel-info">
                            <!-- Default panel contents -->
                            <div class="panel-heading font-semibold text-2xl text-cool-gray-600">
                                <jet-nav-link
                                    title="Back to rule"
                                    :href="route('pm.client-account.rules.edit', {clientAccount: clientAccount.slug, id: ruleId })"  >
                                    <i class="fa fa-caret-square-left"></i>
                                </jet-nav-link>

                                Rule Changes
                            </div>


                            <ul class="list-group" v-if="audits.length !== 0">
                                <li class="list-group-item mb-4 pb-5" v-for="(item, index) in audits"
                                    :key="index">


                                    On {{ item.created_at | date }},
                                    {{ item.user.name }} with <strong>IP:</strong>{{ item.ip_address }}


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
                                        <tbody>
                                        <tr v-for="(key, value) in item.old_values" class="border-collapse border border-gray-400">
                                            <td class=" ">{{ value }}</td>
                                            <td><span class="  text-red-700" v-html="key"></span></td>
                                            <td><span class="  text-blue-700" v-html="item.new_values[value]"></span></td>
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
import ClientLayout from '@/Layouts/ClientAccount'
import JetNavLink from "@/Jetstream/NavLink";
export default {
    name: "AuditActivity",
    props: ['audits', 'clientAccount', 'team', 'ruleId'],


    components: {
        ClientLayout,
        JetNavLink,
    },

}
</script>
