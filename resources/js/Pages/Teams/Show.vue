<template>
    <client-layout :client-account="clientAccount">
        <Head>
            <title>{{team.name}} Settings - Dagobah</title>
        </Head>

        <template #body>
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
        </template>

    </client-layout>
</template>

<script>
import {Head, Link} from "@inertiajs/inertia-vue3";
import TeamMemberManager from './TeamMemberManager'
import ClientLayout from '@/Layouts/ClientAccount'
import DeleteTeamForm from './DeleteTeamForm'
import JetSectionBorder from '@/Jetstream/SectionBorder'
import UpdateTeamNameForm from './UpdateTeamNameForm'

export default {
    props: [
        'team',
        'availableRoles',
        'permissions',
        'clientAccount',
    ],

    components: {
        Head,
        Link,
        ClientLayout,
        DeleteTeamForm,
        JetSectionBorder,
        TeamMemberManager,
        UpdateTeamNameForm,
    },
}
</script>
