<template>
    <app-layout>
        <Head>
            <title>{{team.name}} Settings - Dagobah</title>
        </Head>
        <template #header>
            <div class="flex flex-row">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Team Settings
                </h2>

                <a v-if="team.client_account_id"
                   class="ml-8"
                   :href="route('pm.client-account.getById', {id: team.client_account_id})">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.707-10.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L9.414 11H13a1 1 0 100-2H9.414l1.293-1.293z" clip-rule="evenodd" />
                    </svg>
                    Return to Client Account
                </a>
            </div>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <update-team-name-form :team="team" :permissions="permissions"/>

                <team-member-manager class="mt-10 sm:mt-0"
                                     :team="team"
                                     :available-roles="availableRoles"
                                     :user-permissions="permissions"/>

                <template v-if="permissions.canDeleteTeam && ! team.personal_team">
                    <jet-section-border/>

                    <delete-team-form class="mt-10 sm:mt-0" :team="team"/>
                </template>
            </div>
        </div>
    </app-layout>
</template>

<script>
import {Head} from "@inertiajs/inertia-vue3";
import TeamMemberManager from './TeamMemberManager'
import AppLayout from '@/Layouts/AppLayout'
import DeleteTeamForm from './DeleteTeamForm'
import JetSectionBorder from '@/Jetstream/SectionBorder'
import UpdateTeamNameForm from './UpdateTeamNameForm'

export default {
    props: [
        'team',
        'availableRoles',
        'permissions',
    ],

    components: {
        Head,
        AppLayout,
        DeleteTeamForm,
        JetSectionBorder,
        TeamMemberManager,
        UpdateTeamNameForm,
    },
}
</script>
