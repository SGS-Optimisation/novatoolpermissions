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
                        Updating rule <i>{{ rule.name }}</i>

                        <jet-nav-link
                            title="View history"
                            :href="route('pm.client-account.rules.history', [ clientAccount.slug, rule.id])">
                            <i class="fa fa-history"/>
                        </jet-nav-link>
                    </template>
                </rule-form>

                <div class="mt-5">
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
    name: "CreateRule",

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


    ],


}
</script>
