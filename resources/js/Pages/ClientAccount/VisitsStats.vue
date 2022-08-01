<template>
    <client-layout :client-account="clientAccount">
        <Head>
            <title>Visits Stats for {{clientAccount.name}} - Dagobah</title>
        </Head>
        <template #body>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" v-if="clientAccount != null">
                        <visits-stats-table :stats="stats" :view_by="view_by" :date="date" mode="account-specific"
                                            level="client" :grouping="grouping" :name="name"
                                            :client-account="clientAccount.slug" :client-accounts="clientAccounts"/>
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
import RuleStatsGraph from "../../Components/Stats/RuleStatsGraph.vue";
import VisitsStatsTable from "@/Components/Stats/VisitsStatsTable.vue";

export default {

    props: [
        'stats',
        'view_by',
        'date',
        'isGlobalMode',
        'level',
        'clientAccount',
        'clientAccounts',
        'grouping',
        'name',
    ],

    components: {
        VisitsStatsTable,
        Head,
        ClientLayout,
        JetActionMessage,
        JetButton,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        JetSecondaryButton,
        RuleStatsGraph,
    },

    data() {
        return {
        }
    },
}
</script>
