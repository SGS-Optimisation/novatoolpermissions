<template>
    <client-layout :client-account="clientAccount">
        <Head>
            <title>Categories for {{clientAccount.name}} - Dagobah</title>
        </Head>
        <template #body>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" v-if="clientAccount != null">
                        <TabView v-model:activeIndex="activeTab">
                            <TabPanel v-for="(topTaxonomy, index) in topTaxonomies" :key="topTaxonomy.name" :header="topTaxonomy.name">

                                <child-taxonomies-section
                                    :parent-taxonomy="topTaxonomy"
                                    :taxonomy-hierarchy="taxonomyHierarchy"
                                    :client-account="clientAccount">
                                </child-taxonomies-section>

                            </TabPanel>
                        </TabView>

                    </div>
                </div>
            </div>
        </template>
    </client-layout>
</template>


<script>
import {Head} from "@inertiajs/inertia-vue3";
import ClientLayout from '@/Layouts/ClientAccount.vue'
import JetButton from '@/Jetstream/Button.vue'
import JetFormSection from '@/Jetstream/FormSectionNoGrid.vue'
import JetInput from '@/Jetstream/Input.vue'
import JetInputError from '@/Jetstream/InputError.vue'
import JetLabel from '@/Jetstream/Label.vue'
import JetActionMessage from '@/Jetstream/ActionMessage.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import ChildTaxonomiesSection from "@/Components/PM/ClientAccount/ChildTaxonomiesSection.vue";
import TabView from 'primevue/tabview/sfc';
import TabPanel from 'primevue/tabpanel/sfc';

export default {

    props: [
        'clientAccount',
        'team',
        'taxonomyHierarchy',
        'topTaxonomies',
    ],

    components: {
        Head,
        ClientLayout,
        JetActionMessage,
        JetButton,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        ChildTaxonomiesSection,
        TabView,
        TabPanel
    },

    data() {
        return {
            activeTab: 0,
        }
    },
}
</script>
