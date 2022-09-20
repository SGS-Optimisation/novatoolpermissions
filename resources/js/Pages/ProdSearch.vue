<template>
    <app-layout>

        <Head>
            <title>Prod Job Search</title>
        </Head>

        <div class="p-2" v-if="hasInvitations">
            <h2 class="text-center bg-gray-100 font-semibold">You have pending invitations</h2>
            <div class="flex flex-wrap justify-center pt-1">
                <div class="p-3" v-for="invite in invitations">
                    <client-account-link :alwaysShowTeamName="true" :team="invite.team" :invitation="invite"/>
                </div>
            </div>
        </div>

        <div class="flex lg:pt-36 pt-12">
            <div class="m-auto lg:w-2/5">
                    <job-search></job-search>
            </div>
        </div>

    </app-layout>
</template>

<script>
import { defineComponent } from 'vue'
import {Head} from "@inertiajs/inertia-vue3";
import AppLayout from '@/Layouts/AppLayout.vue'
import JobSearch from "@/Components/Search/JobSearchForm.vue";
import ClientAccountLink from "@/Components/RulesLibrary/ClientAccount/ClientAccountLink.vue";

export default defineComponent({
    props: [
        'team',
        'invitations'
    ],

    components: {
        ClientAccountLink,
        AppLayout,
        JobSearch,
        Head,
    },

    computed: {
        hasInvitations() {
            return this.invitations.length > 0;
        }
    }
})
</script>
