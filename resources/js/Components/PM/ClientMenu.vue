<template>
    <div class="bg-white">
        <div class="flex justify-between">
            <nav class="flex flex-col sm:flex-row">
                <sub-nav-link :href="route('dashboard', {clientAccount: clientAccount ? clientAccount.slug : ''})"
                              :active="route().current('dashboard')">
                    Overview
                </sub-nav-link>

                <sub-nav-link :href="route('rules', {clientAccount: clientAccount.slug })"
                              :active="route().current('rules')">
                    Rules
                </sub-nav-link>

                <sub-nav-link :href="route('configuration', {clientAccount: clientAccount.slug })"
                              :active="route().current('configuration')">
                    Configuration
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
