<template>
    <div class="bg-white">
        <div class="flex justify-between">
            <nav class="flex flex-col sm:flex-row">
                <sub-nav-link
                    :href="route('pm.client-account.dashboard', {clientAccount: clientAccount ? clientAccount.slug : ''})"
                    :active="route().current('pm.client-account.dashboard')">
                    Overview
                </sub-nav-link>

                <sub-nav-link :href="route('pm.client-account.rules.index', {clientAccount: clientAccount.slug })"
                              :active="route().current('pm.client-account.rules.index')">
                    Rules
                </sub-nav-link>

                <sub-nav-link :href="route('pm.client-account.taxonomy', {clientAccount: clientAccount.slug })"
                              :active="route().current('pm.client-account.taxonomy')">
                    Categories
                </sub-nav-link>
                <sub-nav-link :href="route('pm.client-account.edit', {clientAccount: clientAccount.slug })"
                              :active="route().current('pm.client-account.edit')">
                    Settings
                </sub-nav-link>
                <template v-if="clientAccount.teams.length === 1">
                    <sub-nav-link :href="route('pm.client-account.teams.show', {clientAccount: clientAccount.slug, teamId: clientAccount.teams[0].id })"
                        :active="route().current('pm.client-account.teams.show')"
                    >
                        Team
                    </sub-nav-link>
                </template>
                <template v-else>
                    <jet-dropdown align="right" width="48" class="text-gray-600 block hover:text-blue-500 focus:outline-none">
                        <template #trigger>
                            <button :class="{'border-b-2 border-blue-500': route().current('pm.client-account.teams.show')}"
                                class="py-4 px-6 flex items-center text-gray-600 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                <div class="h-full">Teams</div>

                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                         viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </button>
                        </template>
                        <template #content>
                            <jet-dropdown-link v-for="team in clientAccount.teams" :key=team.id
                                               :href="route('pm.client-account.teams.show', {clientAccount: clientAccount.slug, teamId: team.id })">
                                {{team.name}}
                            </jet-dropdown-link>
                        </template>
                    </jet-dropdown>
                </template>

                <jet-dropdown align="right" width="48" class="text-gray-600 block hover:text-blue-500 focus:outline-none">
                    <template #trigger>
                        <button :class="{'border-b-2 border-blue-500': route().current().startsWith('pm.client-account.stats')}"
                                class="py-4 px-6 flex items-center text-gray-600 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                            <div class="h-full">Stats</div>

                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </button>
                    </template>
                    <template #content>
                        <jet-dropdown-link :href="route('pm.client-account.stats.jobs', {clientAccount: clientAccount.slug})">
                            Jobs
                        </jet-dropdown-link>
                        <jet-dropdown-link :href="route('pm.client-account.stats.rules', {clientAccount: clientAccount.slug})">
                            Rules
                        </jet-dropdown-link>
                        <jet-dropdown-link :href="route('pm.client-account.stats.visits', {clientAccount: clientAccount.slug})">
                            Visits
                        </jet-dropdown-link>
                        <jet-dropdown-link :href="route('pm.client-account.stats.visits-by-jobteam', {clientAccount: clientAccount.slug})">
                            Visits by JobTeam
                        </jet-dropdown-link>
                        <jet-dropdown-link :href="route('pm.client-account.stats.visits-by-country', {clientAccount: clientAccount.slug})">
                            Visits by Country
                        </jet-dropdown-link>
                    </template>
                </jet-dropdown>
            </nav>

        </div>
    </div>
</template>

<script>

import SubNavLink from "@/Components/SubNavLink";
import JetDropdown from '@/Jetstream/Dropdown';
import JetDropdownLink from '@/Jetstream/DropdownLink'

export default {
    name: "ClientMenu",
    props: [
        'clientAccount',
    ],
    components: {
        SubNavLink,
        JetDropdown,
        JetDropdownLink,
    }
}
</script>

<style scoped>

</style>
