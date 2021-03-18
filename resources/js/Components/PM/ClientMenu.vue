<template>
    <div class="bg-white">
        <div class="flex justify-between">
            <nav class="flex flex-col sm:flex-row">
                <sub-nav-link :href="route('pm.client-account.dashboard', {clientAccount: clientAccount ? clientAccount.slug : ''})"
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
        'team',
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
