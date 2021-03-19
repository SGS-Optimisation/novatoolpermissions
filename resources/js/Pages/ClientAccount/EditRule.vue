<template>
    <client-layout :client-account="clientAccount">
        <template #body>
            <div class="mx-auto sm:px-6 lg:px-8">

                <jet-nav-link  as="button" :href="route('rules.history', [ clientAccount.name, rule.id])"  >
                    View History
                </jet-nav-link>
<!--                <inertia-link as="button"-->
<!--                              :href="route('rules.history', [clientAccount.name, rule.id])">-->
<!--                    View History-->
<!--                </inertia-link>-->



                <rule-form :rule="rule"
                           :client-account="clientAccount"
                           :taxonomy-hierarchy="taxonomyHierarchy"
                           :top-taxonomies="topTaxonomies"
                >
                    <template #title>
                        Updating rule {{ rule.name }}
                    </template>
                </rule-form>

                <div class="mt-5">
                    <metadata-form :rule="rule"
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


export default {
    name: "CreateRule",

    components: {
        JetButton,
        JetDropdownLink,
        JetNavLink,
        ClientLayout,
        RuleForm,
        MetadataForm
    },

    props:[
        'team',
        'clientAccount',
        'taxonomyHierarchy',
        'topTaxonomies',
        'rule',

    ],











}
</script>
