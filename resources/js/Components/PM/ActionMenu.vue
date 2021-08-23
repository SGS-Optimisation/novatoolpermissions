<template>
    <nav class="flex items-center ml-6 z-0">
        <jet-dropdown align="right" width="48">
            <template #trigger>
                <button class="flex items-center text-sm font-medium text-white hover:text-white hover:border-gray-300 focus:outline-none focus:border-gray-300 transition duration-150 ease-in-out px-2 py-1 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150">
                    Add&nbsp; <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z" clip-rule="evenodd" />
                </svg>
                </button>
            </template>
            <template #content>
                <jet-dropdown-link v-if="clientAccount" :href="route('pm.client-account.rules.create', {clientAccount: clientAccount.slug })">
                    Rule
                </jet-dropdown-link>
                <jet-dropdown-link v-if="clientAccount" :href="route('pm.client-account.teams.create', {clientAccount: clientAccount.slug })">
                    Team
                </jet-dropdown-link>
                <jet-dropdown-link v-if="$page.user_permissions.createClientAccounts" :href="route('pm.client-account.create')">
                    Client Account
                </jet-dropdown-link>
            </template>
        </jet-dropdown>
    </nav>
</template>

<script>

import SubNavLink from "@/Components/SubNavLink";
import JetDropdown from '@/Jetstream/Dropdown';
import JetDropdownLink from '@/Jetstream/DropdownLink'
import Button from "@/Jetstream/Button";

export default {
    name: "ActionMenu",
    props: [
        'clientAccount',
        'team',
    ],
    components: {
        SubNavLink,
        JetDropdown,
        JetDropdownLink,
        Button
    }
}
</script>

<style scoped>

</style>
