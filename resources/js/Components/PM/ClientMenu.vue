<template>
    <div class="bg-white">
        <nav class="flex flex-col sm:flex-row">
            <sub-nav-link :href="route('dashboard', {clientAccount: clientAccount ? clientAccount.id : ''})"
                          :active="route().current('dashboard')">
                Overview
            </sub-nav-link>

            <sub-nav-link :href="route('rules', {clientAccount: clientAccount.id })"
                          :active="route().current('rules')">
                Rules
            </sub-nav-link>

            <sub-nav-link :href="route('configuration', {clientAccount: clientAccount.id })"
                          :active="route().current('configuration')">
                Configuration
            </sub-nav-link>
        </nav>
    </div>
</template>

<script>

import SubNavLink from "@/Components/SubNavLink";

export default {
    name: "ClientMenu",
    props: [
        'clientAccount',
        'team',
    ],
    components: {
        SubNavLink
    }
}
</script>

<style scoped>

</style>
