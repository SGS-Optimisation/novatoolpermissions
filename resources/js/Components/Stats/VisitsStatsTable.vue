<template>
    <div class="bg-white justify-around flex">
        <div class="flex flex-col w-11/12 py-12">
            <jet-form-section @submitted="updateStats">
                <template #title>
                    Visits Stats
                </template>
                <template #description>
                    Parameters
                </template>

                <template #form>
                    <div class="col-span-1 sm:col-span-1">
                        <jet-label class="text-xs" for="grouping" value="Grouping"/>
                        <select class="text-xs" id="grouping" v-model="groupBy">
                            <option value="user">User</option>
                            <option value="job_number">Job Number</option>
                        </select>
                    </div>

                    <div class="col-span-1 sm:col-span-1">
                        <jet-label class="text-xs" for="view_by" value="View By"/>
                        <select class="text-xs" id="view_by" v-model="form.view_by">
                            <option>Day</option>
                            <option>Range</option>
                        </select>
                    </div>

                    <div class="col-span-1 sm:col-span-1">
                        <jet-label class="text-xs" for="date_from"
                                   :value="form.view_by === 'Day' ? 'Date' : 'Date From'"/>

                        <calendar v-model="form.date_from"
                                  selectionMode="single"
                                  dateFormat="yy-mm-dd"/>
                    </div>

                    <div class="col-span-1 sm:col-span-1" v-if="form.view_by === 'Range'">
                        <jet-label class="text-xs" for="date_to" value="Date To"/>

                        <calendar v-model="form.date_to"
                                  selectionMode="single"
                                  dateFormat="yy-mm-dd"/>
                    </div>

                    <div class="col-span-1 sm:col-span-1" v-if="isGlobalMode">
                        <jet-label class="text-xs" for="level" value="Level"/>
                        <select class="text-xs" id="level" v-model="form.level">
                            <option value="client">Account</option>
                            <option value="global">Global</option>
                        </select>
                    </div>

                    <div v-if="form.level === 'client' && isGlobalMode" class="col-span-2 sm:col-span-2">
                        <jet-label class="text-xs" for="account" value="Client Account"/>
                        <select class="text-xs" id="account" v-model="form.client_account">
                            <option v-for="(name, slug) in clientAccounts"
                                    :value="slug">{{ name }}
                            </option>
                        </select>
                    </div>


                </template>

                <template #actions>
                    <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Update
                    </jet-button>
                </template>
            </jet-form-section>

            <div class="mt-3">
                <DataTable :value="stats" rowGroupMode="subheader" :groupRowsBy="groupBy"
                           sortMode="single" :sortField="groupBy" :sortOrder="1" responsiveLayout="scroll"
                           :expandableRowGroups="true" v-model:expandedRowGroups="expandedRowGroups"
                >
                    <Column field="job_number" header="Job#"></Column>
                    <Column field="user" header="User"></Column>
                    <Column field="client" header="Client"></Column>
                    <Column field="time" header="Time"></Column>
                    <template #groupheader="slotProps">
                        <span class="image-text">{{ slotProps.data[groupBy] }}</span>
                    </template>
                    <template #groupfooter="slotProps">
                        <td colspan="2" style="text-align: right">Total Visits</td>
                        <td>{{ calculateTotal(slotProps.data[groupBy]) }}</td>
                    </template>

                </DataTable>
            </div>
        </div>
    </div>
</template>

<script>
import DataTable from 'primevue/datatable/sfc';
import Column from 'primevue/column/sfc';
import Calendar from 'primevue/calendar/sfc';
import JetNavLink from "@/Jetstream/NavLink";
import JetActionMessage from '@/Jetstream/ActionMessage'
import JetButton from '@/Jetstream/Button'
import JetFormSection from '@/Jetstream/FormSection'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetLabel from '@/Jetstream/Label'
import SwitchFlag from "../SwitchFlag";
import dateFormat from "dateformat";

export default {
    name: "VisitsStatsTable",
    components: {
        DataTable,
        Column,
        Calendar,
        SwitchFlag,
        JetNavLink,
        JetActionMessage,
        JetButton,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
    },
    props: [
        'stats',
        'view_by',
        'date',
        'level',
        'clientAccount',
        'clientAccounts',
        'mode',
    ],
    data() {
        return {
            customers: null,
            expandedRowGroups: null,

            groupBy: 'user',

            form: this.$inertia.form({
                level: this.level,
                view_by: this.view_by,
                date_from: (() => {
                    if (this.date.includes(',')) {
                        return this.date.split(',')[0];
                    }
                    return this.date;
                })(),
                date_to: (() => {
                    if (this.date.includes(',')) {
                        return this.date.split(',')[1];
                    }
                    return null;
                })(),
                client_account: this.clientAccount,
            }, {
                resetOnSuccess: false,
            }),
        }
    },

    methods: {
        updateStats() {

            let visits_date = (this.form.view_by === 'Range') ?
                [dateFormat(this.form.date_from, 'yyyy-mm-dd'), dateFormat(this.form.date_to, 'yyyy-mm-dd')]
                : dateFormat(this.form.date_from, 'yyyy-mm-dd');

            this.$inertia.get('', {
                visits_view_by: this.form.view_by,
                visits_date: visits_date,
                visits_level: this.form.level,
                visits_client_account: this.form.client_account
            });
        },

        calculateTotal(val) {
            let total = 0;

            if (this.stats) {
                for (let entry of this.stats) {
                    if (entry[this.groupBy] === val) {
                        total++;
                    }
                }
            }

            return total;
        }
    },

    computed: {
        calendarView() {
            switch (this.form.view_by.toLowerCase()) {
                case 'day':
                    return null;
                    break;
                default:
                    return this.form.view_by;
                    break;
            }
        },

        calendarSelectionMode() {
            switch (this.form.view_by.toLowerCase()) {
                case 'day':
                    return 'single';
                    break;
                case 'week':
                    return 'range';
                    break;
                case 'month':
                    return 'range';
                    break;
            }
        },

        calendarDateFormat() {
            switch (this.form.view_by.toLowerCase()) {
                case 'day':
                    return 'yy-mm-dd';
                    break;
                case 'week':
                    return 'yy-mm-dd';
                    break;
                case 'month':
                    return 'yy-mm';
                    break;
            }
        },

        isAccountSpecific() {
            return this.mode === 'account-specific';
        },

        isGlobalMode() {
            return this.mode === 'global';
        }
    }
}
</script>

<style scoped>
:deep(.p-inputtext) {
    font-size: 0.7rem;
}
</style>
