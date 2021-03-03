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
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Welcome from '@/Jetstream/Welcome'
import ClientAccountOverview from './ClientAccount/Overview'
import ClientMenu from '@/Components/PM/ClientMenu'

export default {
    props: [
        'team',
    ],

    components: {
        AppLayout,
        Welcome,
    },
}
</script>
