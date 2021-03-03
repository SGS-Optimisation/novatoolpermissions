<template>
    <nav class="flex items-center ml-6 z-0">
        <jet-dropdown align="right" width="48">
            <template #trigger>
                <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                    Add&nbsp;<i class="fa fa-plus-square"></i>
                </button>
            </template>
            <template #content>
                <jet-dropdown-link :href="route('pm.client-account.rules.create', {clientAccount: clientAccount.slug })">
                    New rule
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
