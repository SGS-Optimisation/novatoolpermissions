<template>
    <div>
        <Head><title>
            Update rule in {{clientAccount.name}} - Dagobah
        </title></Head>
        <client-layout :client-account="clientAccount">

            <template #body>
                <div class="mx-auto sm:px-6 lg:px-8">
                    <rule-form :rule="rule"
                               :states="states"
                               :allowed-states="allowedStates"
                               :state-objects="stateModels"
                               :client-account="clientAccount"
                               :taxonomy-hierarchy="taxonomyHierarchy"
                               :top-taxonomies="topTaxonomies"
                               :publishers="publishers"
                               :default-publishers="defaultPublishers"
                               :initial-assignees="initialAssignees"
                    >
                        <template #title>
                            <div class="flex w-48 bg-white rounded-b-lg shadow">
                                <div class="mt-1 mx-auto">
                                    <jet-nav-link
                                        title="Back to list"
                                        :href="backRoute">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.707-10.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L9.414 11H13a1 1 0 100-2H9.414l1.293-1.293z" clip-rule="evenodd" />
                                        </svg>
                                    </jet-nav-link>
                                    <span class="font-bold">Updating rule</span>
                                    <jet-nav-link
                                        title="View history"
                                        :href="route('library.client-account.rules.history', [ clientAccount.slug, rule.id])">
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
    </div>
</template>

<script>

import {Head} from "@inertiajs/inertia-vue3";
import {defineComponent} from "vue";
import ClientLayout from '@/Layouts/ClientAccount.vue'
import RuleForm from '@/Components/RulesLibrary/Rules/Form.vue'
import MetadataForm from '@/Components/RulesLibrary/Rules/Meta.vue'
import JetNavLink from "@/Jetstream/NavLink.vue";
import JetButton from "@/Jetstream/DangerButton.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink.vue";
import Button from "@/Jetstream/Button.vue";
import AttachmentsForm from "../../Components/RulesLibrary/Rules/AttachmentsForm.vue";

export default defineComponent({
    name: "EditRule",

    components: {
        Head,
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
        'stateModels',
        'publishers',
        'defaultPublishers',
        'initialAssignees',
        'referer',
    ],

    computed: {
        backRoute() {
            var mainBack = route('library.client-account.rules.index', {clientAccount: this.clientAccount.slug });
            if(this.referer && this.referer !== mainBack && this.referer !== window.location.href) {
                return this.referer;
            }
            return mainBack;
        },
    }


})
</script>
