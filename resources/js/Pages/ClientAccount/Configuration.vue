<template>
    <client-layout :client-account="clientAccount">

        <template #body>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" v-if="clientAccount != null">
                        <tabs>
                            <tab
                                v-for="(topTaxonomy, index) in topTaxonomies"
                                :key="topTaxonomy.name"
                                :name="topTaxonomy.name"
                                :selected="index == 0"
                            >
                                <taxonomy-definition :parent-taxonomy="topTaxonomy"
                                                     :taxonomy-hierarchy="taxonomyHierarchy"
                                                     :client-account="clientAccount">
                                </taxonomy-definition>
                            </tab>
                        </tabs>

                    </div>
                </div>
            </div>
        </template>
    </client-layout>
</template>


<script>
import ClientLayout from '@/Layouts/ClientAccount'
import TermDefinition from "@/Components/PM/ClientAccount/TermDefinition";
import JetButton from '@/Jetstream/Button'
import JetFormSection from '@/Jetstream/FormSectionNoGrid'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetLabel from '@/Jetstream/Label'
import JetActionMessage from '@/Jetstream/ActionMessage'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import Tabs from "@/Components/Tabs";
import Tab from "@/Components/Tab";
import TaxonomyDefinition from "@/Components/PM/ClientAccount/TaxonomyDefinition";

export default {
    props: [
        'clientAccount',
        'team',
        'taxonomyHierarchy',
        'topTaxonomies',
    ],

    components: {
        ClientLayout,
        TermDefinition,
        JetActionMessage,
        JetButton,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        Tabs,
        Tab,
        TaxonomyDefinition
    },
}
</script>
