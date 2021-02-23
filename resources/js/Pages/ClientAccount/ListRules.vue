<template>
    <client-layout :client-account="clientAccount">
        <template #body>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-gray-50 pt-5">
                <!--                <simple-pagination :data="rules"></simple-pagination>-->

                <div v-for="rule in _.orderBy(rules, 'created_at', 'desc')">
                    <view-rule :rule="rule" :client-account="clientAccount"></view-rule>

                </div>

                <!--                <simple-pagination :data="rules"></simple-pagination>-->
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
