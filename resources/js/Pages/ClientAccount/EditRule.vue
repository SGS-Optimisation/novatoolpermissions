<template>
    <client-layout :client-account="clientAccount">
        <template #body>
            <div class="mx-auto sm:px-6 lg:px-8">
                <rule-form :rule="rule"
                           :states="states"
                           :allowed-states="allowedStates"
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
                                <span class="font-bold">Updating rule</span>
                                <jet-nav-link
                                    title="View history"
                                    :href="route('pm.client-account.rules.history', [ clientAccount.slug, rule.id])">
                                    <i class="fa fa-history"/>
                                </jet-nav-link>
                            </div>
                        </div>
                    </template>
                </rule-form>

                <div class="mt-12">
                    <metadata-form
                        :rule="rule"
                        :client-account="clientAccount"
                        :taxonomy-hierarchy="taxonomyHierarchy"
                        :top-taxonomies="topTaxonomies">
                    </metadata-form>
                </div>
            </div>
        </template>
    </client-layout>
</template>

<script>

import ClientLayout from '@/Layouts/ClientAccount'
import RuleForm from '@/Components/PM/Rules/Form'
import MetadataForm from '@/Components/PM/Rules/Meta'
import JetNavLink from "@/Jetstream/NavLink";
import JetButton from "@/Jetstream/DangerButton";
import JetDropdownLink from "@/Jetstream/DropdownLink";
import Button from "@/Jetstream/Button";


export default {
    name: "EditRule",
    title() {
        return `Update rule in ${this.clientAccount.name} - Dagobah`;
    },

    components: {
        JetButton,
        JetDropdownLink,
        JetNavLink,
        ClientLayout,
        RuleForm,
        MetadataForm,
        Button
    },


    props: [
        'team',
        'clientAccount',
        'taxonomyHierarchy',
        'topTaxonomies',
        'rule',
        'states',
        'allowedStates',


    ],


}
</script>
