<template>
    <div>
        <Head>
            <title>Categories for {{clientAccount.name}} - Dagobah</title>
        </Head>
        
        <client-layout :client-account="clientAccount">

            <template #body>
                <div class="py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" v-if="clientAccount != null">
                            <div class="flex justify-center" v-if="!topTaxonomies.length">
                                <ProgressSpinner/>
                            </div>
                            <TabView v-else v-model:activeIndex="activeTab">
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
    </div>
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
import ProgressSpinner from 'primevue/progressspinner/sfc';
import useSWRV from 'swrv'

export default {

    props: [
        'clientAccount',
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
        TabPanel,
        ProgressSpinner
    },

    data() {
        return {
            activeTab: 0,
            taxonomyHierarchy: null,
            topTaxonomies: [],
            apiData: null
        }
    },

    mounted(){
        this.loadData();
    },

    methods: {
        loadData() {
            const { data, error } = useSWRV(route('api.pm.client-account.taxonomy',[this.clientAccount.slug]), fetcher);
            this.apiData = data;
        },
    },

    watch: {
        apiData: function(newData, oldData) {
            if(typeof newData === 'object' && newData !== null
                && newData.hasOwnProperty('taxonomyHierarchy')) {
                this.taxonomyHierarchy = newData.taxonomyHierarchy;
                this.topTaxonomies = newData.topTaxonomies;
            }
        }
    }
}
</script>
