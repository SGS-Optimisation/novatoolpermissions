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
                <p>{{ customerName }}</p>
                <p>{{ rules }}</p>
                <input v-model="searchJobKey" />
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Input from "@/Jetstream/Input";

export default {
    props: [
        'team',
        'jobNumber',
        'customerName',
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
    },
}
</script>
