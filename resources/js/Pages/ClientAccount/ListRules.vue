<template>
    <client-layout :client-account="clientAccount">
        <template #body>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!--                <simple-pagination :data="rules"></simple-pagination>-->

                <div v-for="rule in _.orderBy(rules, 'created_at', 'desc')">
                    <view-rule :rule="rule"></view-rule>

                </div>

                <!--                <simple-pagination :data="rules"></simple-pagination>-->
            </div>
        </div>
        </template>
    </client-layout>
</template>

<script>
import ClientLayout from '@/Layouts/ClientAccount'
import ViewRule from '@/Components/PM/Rules/ListView'

export default {
    props: [
        'clientAccount',
        'team',
        'rules',
    ],

    components: {
        ClientLayout,
        ViewRule,
    },
}
</script>
