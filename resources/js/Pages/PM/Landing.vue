<template>
    <app-layout>
        <Head><title>
            Client Accounts - Dagobah
        </title></Head>
        <template #header>
            <div class="flex justify-between">
                <div class="flex flex-row content-center">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Welcome
                    </h2>
                </div>

                <div class="flex ml-6">
                    <action-menu/>
                </div>
            </div>
        </template>

        <div class="bg-white justify-around flex">
            <div class="flex flex-col w-4/5 py-12">

                <div class="px-2">
                    <h2 class="text-center bg-gray-100 font-semibold">Your Teams</h2>
                    <DataView :value="myTeams" :layout="mlayout">
                        <template #header>
                            <DataViewLayoutOptions v-model="mlayout"></DataViewLayoutOptions>
                        </template>
                        <template #list="slotProps">
                            <client-account-link :team="slotProps.data"/>
                        </template>
                        <template #grid="slotProps">
                            <div class="col-12 md:col-4 px-2 mb-2">
                                <client-account-link :team="slotProps.data" :alwaysShowTeamName="true"/>
                            </div>
                        </template>
                    </DataView>
                    <div class="flex flex-wrap justify-start pt-1">
                        <div class="p-3" v-for="invite in invitations">
                            <client-account-link :alwaysShowTeamName="true" :team="invite.team" :invitation="invite"/>
                        </div>
                    </div>
                </div>
                <div class="px-2 mt-8">
                    <h2 class="text-center bg-gray-100 font-semibold">Other Teams</h2>

                    <DataView :value="otherTeamsList" :layout="olayout" :sortOrder="sortOrder" :sortField="sortField">

                        <template #header>
                            <div class="flex justify-between">
                                <DataViewLayoutOptions v-model="olayout" />
                                <Dropdown v-model="sortKey" :options="sortOptions"
                                          optionLabel="label" placeholder="Sort By"
                                          @change="onSortChange($event)"/>
                            </div>
                        </template>
                        <template #list="slotProps">
                            <client-account-link :team="slotProps.data"/>
                        </template>
                        <template #grid="slotProps">
                            <div class="col-12 md:col-4 px-2 mb-2">
                                <client-account-link :team="slotProps.data"/>
                            </div>
                        </template>
                    </DataView>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import {Head, Link} from "@inertiajs/inertia-vue3";
import AppLayout from '@/Layouts/AppLayout.vue'
import JetNavLink from "@/Jetstream/NavLink.vue";
import ActionMenu from "@/Components/PM/ActionMenu.vue";
import ClientAccountLink from "@/Components/PM/ClientAccount/ClientAccountLink.vue";
import DataView from 'primevue/dataview/sfc';
import DataViewLayoutOptions from 'primevue/dataviewlayoutoptions/sfc';
import Dropdown from 'primevue/dropdown/sfc';


export default {
    name: "Landing",
    components: {
        Head,
        Link,
        ClientAccountLink,
        ActionMenu,
        AppLayout,
        JetNavLink,
        DataView,
        DataViewLayoutOptions,
        Dropdown,
    },
    props: [
        'team',
        'myTeams',
        'otherTeams',
        'invitations',
    ],
    computed: {
        otherTeamsList() {
            return Object.values(this.otherTeams);
        }
    },
    data() {
        return {
            mlayout: 'grid',
            olayout: 'grid',
            sortKey: {label: 'Name', value: 'name'},
            sortOrder: 1,
            sortField: 'name',
            sortOptions: [
                {label: 'Name', value: 'name'},
                {label: 'Newest First', value: '!created_at'},
                {label: 'Oldest First', value: 'created_at'},
                {label: 'Most rules first', value: '!client_account.rules_count'},
                {label: 'Least rules first', value: 'client_account.rules_count'},
            ]
        }
    },
    methods: {
        onSortChange(event){
            console.log('sort changed', event);
            const value = event.value.value;
            const sortValue = event.value;

            if (value.indexOf('!') === 0) {
                this.sortOrder = -1;
                this.sortField = value.substring(1, value.length);
                this.sortKey = sortValue;
            }
            else {
                this.sortOrder = 1;
                this.sortField = value;
                this.sortKey = sortValue;
            }
        }
    }
}
</script>

<style>
.p-dataview-grid .p-grid {
    @apply grid-cols-4;
}

.p-dataview-grid .p-grid .card-content{
    @apply h-24;
}
</style>
