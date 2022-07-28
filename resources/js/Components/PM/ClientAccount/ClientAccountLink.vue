<template>
    <div>
        <Card>
            <template #title>

            </template>
            <template #content>
                <div class="card-content flex flex-row">
                    <jet-nav-link
                        class="hover:no-underline border-none block relative"
                        :class="{'bg-green-200': isInvitation}"
                        :href="route('pm.client-account.dashboard', {clientAccount: team.client_account.slug })">

                        <div class="flex flex-col">
                            <img v-if="team.client_account.image"
                                 :src="team.client_account.image"
                                 :alt="team.name"
                                 :title="team.name"
                                 class="client-logo">

                            <span v-if="shouldShowTeamName"
                                  class=" text-xs" :class="{'client-logo' : team.client_account.image}">
                            {{ team.name }}
                        </span>
                        </div>

                        <span class="num-rules"
                              :class="{'bg-green-100': hasRules,'bg-pink-100': !hasRules}"
                              title="Number of rules">
                        {{ team.client_account.rules_count }}
                    </span>
                        <span class="num-rules omnipresent" v-if="team.client_account.omnipresent_rules_count"
                              :class="{
                                'bg-green-100': omnipresentRulesInfo,
                                'bg-yellow-200': omnipresentRulesWarning,
                                'bg-pink-200': omnipresentRulesDanger,
                          }"
                              title="Number of omnipresent rules">
                        {{ team.client_account.omnipresent_rules_count }}
                    </span>
                    </jet-nav-link>
                    <div class="flex flex-col">
                        <Button icon="pi pi-list" class="p-button-sm p-button-icon-only p-button-rounded p-button-text p-button-plain"
                                v-tooltip="'Rules'"
                                @click="$inertia.get(
                                    route('pm.client-account.rules.index', {clientAccount: team.client_account.slug})
                                )"/>
                        <Button icon="pi pi-tags" class="p-button-sm p-button-icon-only p-button-rounded p-button-text p-button-plain"
                                v-tooltip="'Categories'"
                                @click="$inertia.get(
                                    route('pm.client-account.taxonomy', {clientAccount: team.client_account.slug})
                                )"/>
                        <Button icon="pi pi-cog" class="p-button-sm p-button-icon-only p-button-rounded p-button-text p-button-plain"
                                v-tooltip="'Settings'"
                                @click="$inertia.get(
                                    route('pm.client-account.edit', {clientAccount: team.client_account.slug})
                                )"/>
                        <Button v-if="alwaysShowTeamName" icon="pi pi-users" class="p-button-sm p-button-icon-only p-button-rounded p-button-text p-button-plain"
                                v-tooltip="'Team'"
                                @click="$inertia.get(
                                    route('pm.client-account.teams.show', {clientAccount: team.client_account.slug, teamId: team.id})
                                )"/>
                    </div>
                </div>
            </template>
            <template #footer v-if="isInvitation">
                <Link :href="route('pm.team-invitations.accept', invitation.id)"
                      class="mt-1 inline-flex text-center p-1 bg-gray-800 border border-transparent rounded-md text-xs text-white tracking-widest hover:bg-green-600 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                      :class="{'client-logo' : team.client_account.image}">
                    Accept Invitation
                </Link>
            </template>
        </Card>

    </div>
</template>

<script>
import JetNavLink from "@/Jetstream/NavLink";
import {Link} from '@inertiajs/inertia-vue3';
import Card from 'primevue/card/sfc';
import Button from 'primevue/button/sfc';

export default {
    name: "ClientAccountLink",
    components: {
        JetNavLink,
        Link,
        Card,
        Button,
    },
    props: {
        team: Object,
        alwaysShowTeamName: {
            type: Boolean,
            default: false,
        },
        invitation: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
        }
    },
    computed: {
        isInvitation() {
            return this.invitation !== null;
        },

        shouldShowTeamName() {
            return !this.team.client_account.image || this.alwaysShowTeamName;
        },

        hasRules: function () {
            return this.team.client_account.rules_count > 0;
        },

        ratio() {
            return (this.team.client_account.omnipresent_rules_count / this.team.client_account.rules_count)
        },

        omnipresentRulesInfo: function () {
            return this.ratio < 0.1;
        },

        omnipresentRulesWarning: function () {
            return this.ratio >= 0.1 && this.ratio < 0.2;
        },

        omnipresentRulesDanger: function () {
            return this.ratio >= 0.2;
        },
    },
    methods: {
        onRightClick(event) {
            this.$refs.menu.show(event);
        },
    }
}
</script>

<style scoped>
.client-logo {
    max-width: 100px;
    max-height: 100px;
}

.num-rules {
    font-size: 8px;
    height: 20px;
    width: 20px;
    top: 0px;
    left: -10px;
    @apply bg-opacity-50 rounded-xl text-center absolute
}

.num-rules.omnipresent {
    top: 20px;
}
</style>
