<template>
    <div class="bg-white">
        <div class="flex justify-between">
            <nav class="flex flex-col sm:flex-row">
                <sub-nav-link
                    :href="route('pm.client-account.dashboard', {clientAccount: clientAccount ? clientAccount.slug : ''})"
                    :active="route().current('pm.client-account.dashboard')">
                    Overview
                </sub-nav-link>

                <sub-nav-link :href="route('pm.client-account.rules', {clientAccount: clientAccount.slug })"
                              :active="route().current('pm.client-account.rules')">
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
                    <sub-nav-link :href="route('teams.show', clientAccount.team.id)">
                        Team
                    </sub-nav-link>
                </template>
                <template v-else>
                    <jet-dropdown align="right" width="48" class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none">
                        <template #trigger>
                            <button class="flex items-center text-gray-600 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                <div>Teams</div>

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
                            <jet-dropdown-link v-for="team in clientAccount.teams"
                                               :href="route('teams.show', team.id)">
                                {{team.name}}
                            </jet-dropdown-link>
                        </template>
                    </jet-dropdown>
                </template>
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
