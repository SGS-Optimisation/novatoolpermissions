<template>
    <client-layout :client-account="clientAccount">
        <Head>
            <title>Rules Stats for {{clientAccount.name}} - Dagobah</title>
        </Head>
        <template #body>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" v-if="clientAccount != null">
                        <rule-stats-graph :stats="stats" :view_by="view_by" :range="range" :column="column" :cumulative="cumulative"
                                    :level="level" :region="region" mode="account-specific"/>
                    </div>
                </div>
            </div>
        </template>
    </client-layout>
</template>


<script>
import {Head} from "@inertiajs/inertia-vue3";
import ClientLayout from '@/Layouts/ClientAccount'
import JetButton from '@/Jetstream/Button'
import JetFormSection from '@/Jetstream/FormSectionNoGrid'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetLabel from '@/Jetstream/Label'
import JetActionMessage from '@/Jetstream/ActionMessage'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import RuleStatsGraph from "../../Components/Stats/RuleStatsGraph";

export default {

    props: [
        'clientAccount',
        'stats',
        'view_by',
        'range',
        'column',
        'cumulative',
        'mode',
        'level'
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
        JetSecondaryButton,
        RuleStatsGraph,
    },

    data() {
        return {
        }
    },
}
</script>
