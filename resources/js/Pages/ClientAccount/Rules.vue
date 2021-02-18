<template>
    <app-layout>
        <template #header>
            <div class="flex flex-row">
                <client-header :client-account="clientAccount"></client-header>

                <client-menu :client-account="clientAccount"></client-menu>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!--                <simple-pagination :data="rules"></simple-pagination>-->

                <div v-for="rule in _.orderBy(rules, 'created_at', 'desc')">
                    <view-rule :rule="rule"></view-rule>

                </div>

                <!--                <simple-pagination :data="rules"></simple-pagination>-->
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Welcome from '@/Jetstream/Welcome'
import ClientAccountOverview from '@/Pages/ClientAccount/Overview'
import ClientMenu from '@/Components/PM/ClientMenu'
import ViewRule from '@/Components/PM/Rules/ListView'
import SimplePagination from "@/Components/SimplePagination";
import ClientHeader from "@/Components/PM/ClientHeader";

export default {
    props: [
        'clientAccount',
        'team',
        'rules',
    ],

    components: {
        ClientMenu,
        AppLayout,
        Welcome,
        ClientAccountOverview,
        ViewRule,
        SimplePagination,
        ClientHeader
    },
}
</script>
