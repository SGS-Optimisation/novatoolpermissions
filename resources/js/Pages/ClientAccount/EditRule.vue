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
                                    :href="route('pm.client-account.rules.index', {clientAccount: clientAccount.slug })">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.707-10.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L9.414 11H13a1 1 0 100-2H9.414l1.293-1.293z" clip-rule="evenodd" />
                                    </svg>
                                </jet-nav-link>
                                <span class="font-bold">Updating rule</span>
                                <jet-nav-link
                                    title="View history"
                                    :href="route('pm.client-account.rules.history', [ clientAccount.slug, rule.id])">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                </jet-nav-link>
                            </div>
                        </div>
                    </template>
                </rule-form>

                <div class="mt-6">
                    <attachments-form :client-account="clientAccount" :rule="rule"/>
                </div>

                <div class="mt-6">
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
import AttachmentsForm from "../../Components/PM/Rules/AttachmentsForm";


export default {
    name: "EditRule",
    title() {
        return `Update rule in ${this.clientAccount.name} - Dagobah`;
    },

    components: {
        AttachmentsForm,
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
