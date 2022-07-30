<template>
    <client-layout :client-account="clientAccount">
        <Head>
            <title>New rule in {{clientAccount.name}} - Dagobah</title>
        </Head>
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
import {Head} from "@inertiajs/inertia-vue3";
import ClientLayout from '@/Layouts/ClientAccount.vue'
import RuleForm from '@/Components/PM/Rules/Form.vue'
import JetNavLink from "@/Jetstream/NavLink.vue";

export default {
    name: "CreateRule",

    components: {
        Head,
        ClientLayout,
        JetNavLink,
        RuleForm
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
    ],

}
</script>
