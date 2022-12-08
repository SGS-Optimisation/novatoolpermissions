<template>
    <div>
        <Card>
            <template #title>

            </template>
            <template #content>
                <div class="card-content flex flex-row justify-around">
                    <jet-nav-link
                        class="hover:no-underline border-none block relative"
                        :class="{'bg-green-200': isInvitation}"
                        :href="route('library.client-account.dashboard', {clientAccount: team.client_account.slug })">

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

                            <div class="flex mt-5 justify-around">
                                <div v-tooltip.bottom="'Rules'"
                                     class="mx-2 text-blue-400 opacity-75 text-xs justify-center align-bottom">
                                    <i class="pi pi-book"></i>
                                    {{ team.client_account.rules_count }}
                                </div>

                                <div v-if="team.client_account.omnipresent_rules_count"
                                     v-tooltip.bottom="'Omnipresent Rules'"
                                     class="mx-2 opacity-75 text-xs justify-center align-bottom"
                                     :class="{
                                                'text-blue-400': omnipresentRulesInfo,
                                                'text-amber-400': omnipresentRulesWarning,
                                                'text-red-400': omnipresentRulesDanger,
                                            }"
                                >
                                    <i class="pi pi-bell"></i>
                                    {{ team.client_account.omnipresent_rules_count }}
                                </div>

                                <div v-if="team.client_account.flagged_rules_count"
                                     v-tooltip.bottom="'Flagged Rules'"
                                     class="mx-2 text-red-400 opacity-75 text-xs justify-center align-bottom">
                                    <i class="pi pi-flag"></i>
                                    {{ team.client_account.flagged_rules_count }}
                                </div>

                            </div>
                        </div>
                    </jet-nav-link>
                    <div class="grid grid-cols-1 place-content-evenly">
                        <Link :href="route('library.client-account.rules.index', {clientAccount: team.client_account.slug})"
                              class="account-page"
                              v-tooltip="'Rules'">
                            <i class="pi pi-list text-xs "></i>
                        </Link>
                        <Link :href="route('library.client-account.taxonomy', {clientAccount: team.client_account.slug})"
                              class="account-page"
                              v-tooltip="'Categories'">
                            <i class="pi pi-tags text-xs "></i>
                        </Link>
                        <Link :href="route('library.client-account.edit', {clientAccount: team.client_account.slug})"
                              class="account-page"
                              v-tooltip="'Settings'">
                            <i class="pi pi-cog text-xs "></i>
                        </Link>
                        <Link v-if="alwaysShowTeamName"
                              :href="route('library.client-account.teams.show', {clientAccount: team.client_account.slug, teamId: team.id})"
                              class="account-page"
                              v-tooltip="'Team'">
                            <i class="pi pi-users text-xs "></i>
                        </Link>
                    </div>
                </div>
            </template>
            <template #footer v-if="isInvitation">
                <Link :href="route('library.team-invitations.accept', invitation.id)"
                      class="mt-1 inline-flex text-center p-1 bg-gray-800 border border-transparent rounded-md text-xs text-white tracking-widest hover:bg-green-600 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                      :class="{'client-logo' : team.client_account.image}">
                    Accept Invitation
                </Link>
            </template>
        </Card>

    </div>
</template>

<script>
import JetNavLink from "@/Jetstream/NavLink.vue";
import {Link} from '@inertiajs/inertia-vue3';
import Card from 'primevue/card/sfc';
import PButton from 'primevue/button/sfc';
import Badge from 'primevue/badge';
import Tag from 'primevue/tag';

export default {
    name: "ClientAccountLink",
    components: {
        JetNavLink,
        Link,
        Card,
        PButton,
        Badge,
        Tag,
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
        return {}
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

        omnipresentSeverity: function () {
            if (this.omnipresentRulesInfo) {
                return 'info';
            }
            if (this.omnipresentRulesWarning) {
                return 'warning';
            }
            if (this.omnipresentRulesDanger) {
                return 'danger';
            }
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
    @apply text-center;
}

.account-page {
    @apply flex hover:bg-gray-100 rounded-full justify-center align-bottom items-center w-8 h-8;
}

.p-button.p-button-icon-only {
    width: 2rem;
    height: 2rem;
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
