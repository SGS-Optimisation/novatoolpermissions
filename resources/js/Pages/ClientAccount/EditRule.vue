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

export default {
    name: "CreateRule",

    components: {
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

}
</script>
