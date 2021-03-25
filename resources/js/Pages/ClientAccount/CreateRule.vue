<template>
    <client-layout :client-account="clientAccount">
        <template #body>
            <div class="mx-auto sm:px-6 lg:px-8">

                <rule-form :rule="rule"
                           :client-account="clientAccount"
                           :taxonomy-hierarchy="taxonomyHierarchy"
                           :top-taxonomies="topTaxonomies"
                >
                    <template #title>
                        <div class="flex w-48 bg-white rounded-b-lg shadow">
                            <div class="mt-1 mx-auto">
                                <jet-nav-link
                                    title="Back to list"
                                    :href="route('pm.client-account.rules', {clientAccount: clientAccount.slug })">
                                    <i class="fa fa-caret-square-left"></i>
                                </jet-nav-link>
                                <span class="font-bold">Creating rule</span>
                            </div>
                        </div>
                    </template>
                </rule-form>

            </div>
        </template>
    </client-layout>
</template>

<script>
import ClientLayout from '@/Layouts/ClientAccount'
import RuleForm from '@/Components/PM/Rules/Form'
import JetNavLink from "@/Jetstream/NavLink";

export default {
    name: "CreateRule",

    components: {
        ClientLayout,
        JetNavLink,
        RuleForm
    },

    props: [
        'team',
        'clientAccount',
        'taxonomyHierarchy',
        'topTaxonomies',
        'rule'
    ],

}
</script>
