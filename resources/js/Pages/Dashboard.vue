<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ team.name }}
            </h2>

        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <p>Well hello there</p>
                <input v-model="searchJobKey" />
                <div v-for="rule in _.orderBy(rules, 'created_at', 'desc')">
                    <view-rule :rule="rule"></view-rule>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Input from "@/Jetstream/Input";
import ViewRule from '@/Components/PM/Rules/ListView'

export default {
    props: [
        'team',
        'jobNumber',
        'rules'
    ],

    data(){
        return {
            searchJobKey : this.jobNumber
        }
    },

    components: {
        Input,
        AppLayout,
        ViewRule,
    },
}
</script>
