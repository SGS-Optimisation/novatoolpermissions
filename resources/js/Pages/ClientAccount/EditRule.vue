<template>
    <client-layout :client-account="clientAccount">
        <template #body>
            <div class="mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="findRuleAudit">
                    <div>
                        <jet-button :type="'submit'" >
                            View History
                        </jet-button>
                    </div>
                </form>


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
import DangerButton from "@/Jetstream/DangerButton";
import JetButton from "@/Jetstream/DangerButton";
import JetDropdownLink from "@/Jetstream/DropdownLink";

export default {
    name: "CreateRule",

    components: {
        JetButton,
        JetDropdownLink,
        ClientLayout,
        RuleForm,
        MetadataForm
    },

    props: [
        'team',
        'clientAccount',
        'taxonomyHierarchy',
        'topTaxonomies',
        'rule'

    ],
    data() {
        return {
            form: this.$inertia.form({}, {
                    bag: 'findRuleAudit'
                }
            ),
        }
    },

    methods: {
        findRuleAudit() {
            var currentUrl = window.location.pathname;

            console.log(this.rule);
            this.form.post(route('ruleaudits', this.rule), {
                preserveScroll: true
            }).then(() => {
                this.$emit('loaded')
            });
        }
    },

}
</script>
