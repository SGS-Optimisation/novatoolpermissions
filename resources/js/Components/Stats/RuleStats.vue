<template>
    <div class="bg-white justify-around flex">
        <div class="flex flex-col w-4/5 py-12">
            <jet-form-section @submitted="updateStats">
                <template #title>
                    Chart Parameters
                </template>

                <template #form>
                    <div class="col-span-1 sm:col-span-1">
                        <jet-label for="view_by" value="View By" />
                        <select id="view_by" v-model="form.view_by">
                            <option>Day</option>
                            <option>Week</option>
                            <option>Month</option>
                        </select>
                    </div>

                    <div class="col-span-1 sm:col-span-1">
                        <jet-label for="range" value="Time Range" />
                        <jet-input id="range" type="number" increment="1" min="0" class="mt-1 block w-full" v-model="form.range"/>
                    </div>

                    <div class="col-span-1 sm:col-span-1">
                        <switch-flag @on-switch="onChangeCumul"
                                     :initial-state=form.cumulative
                                     name="Cumulative"
                                     left-text="No"
                                     right-text="Yes"/>
                    </div>

                    <div class="col-span-1 sm:col-span-1">
                        <jet-label for="column" value="Rule Status" />
                        <select id="column" v-model="form.column">
                            <option value="created_at">Created</option>
                            <option value="updated_at">Updated</option>
                        </select>
                    </div>

                    <div class="col-span-1 sm:col-span-1" v-if="isGlobalMode">
                        <jet-label for="level" value="Level" />
                        <select id="level" v-model="form.level">
                            <option value="client">Account</option>
                            <option value="team">Job Team</option>
                            <option value="region">Region</option>
                        </select>
                    </div>

                    <div v-if="form.level === 'team' && isGlobalMode" class="col-span-1 sm:col-span-1">
                        <jet-label for="region" value="Region" />
                        <select id="region" v-model="form.region">
                            <option value="">ALL</option>
                            <option>APAC</option>
                            <option>EMEA</option>
                            <option>LATAM</option>
                            <option>NA</option>
                        </select>
                    </div>



                    <!--<div class="col-span-1 sm:col-span-1">
                        <jet-label for="function" value="Function" />
                        <select v-model="form.function">
                            <option value="count">Count</option>
                            <option value="sum">Sum</option>
                        </select>
                    </div>-->
                </template>

                <template #actions>
                    <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Update
                    </jet-button>
                </template>
            </jet-form-section>
            <div class="small" v-if="datacollection">
                <rules-line-chart :height="200" :chart-data="datacollection"></rules-line-chart>
            </div>
        </div>
    </div>
</template>

<script>
import JetNavLink from "@/Jetstream/NavLink";
import RulesLineChart from "./ClientAccountRulesLineChart";
import JetActionMessage from '@/Jetstream/ActionMessage'
import JetButton from '@/Jetstream/Button'
import JetFormSection from '@/Jetstream/FormSection'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetLabel from '@/Jetstream/Label'
import SwitchFlag from "../SwitchFlag";

export default {
    name: "RuleStats",
    components: {
        SwitchFlag,
        JetNavLink,
        RulesLineChart,
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
        'range',
        'column',
        'level',
        'region',
        'cumulative',
        'mode',
    ],
    data() {
        return {
            datacollection: null,

            form: this.$inertia.form({
                view_by: this.view_by,
                range: this.range,
                column: this.column,
                level: this.level,
                region: this.region,
                cumulative: this.cumulative,
            }, {
                bag: 'clientAccountStats',
                resetOnSuccess: false,
            }),
        }
    },
    mounted() {
        this.fillData()
    },
    computed: {
        isAccountSpecific() {
            return this.mode === 'account-specific';
        },

        isGlobalMode() {
            return this.mode === 'global';
        }
    },
    methods: {
        updateStats()
        {
            this.$inertia.get('', {
                view_by: this.form.view_by,
                range: this.form.range,
                column: this.form.column,
                level: this.form.level,
                region: this.form.region,
                cumulative: this.form.cumulative,
            });
        },

        onChangeCumul(state) {
            //this.cumulMode = state;

            this.form.cumulative = state ? 1 : 0;
        },

        fillData() {
            var datasets = [];
            var labels = [];
            var labelsFilled = false;

            for (const client in this.stats) {
                datasets.push({
                    label: client,
                    data: Object.values(this.stats[client]['trend']),
                    tension: 0.1,
                    borderColor: this.makeColour(client),
                    fill: false,
                });

                if (!labelsFilled)
                    labels = Object.keys(this.stats[client].trend);
                labelsFilled = true;
            }

            this.datacollection = {
                labels,
                datasets,
            }
        },

        makeColour(client) {
            //return '#' + this.intToRGB(parseInt(this.rgbVal(this.stats[client].client_id) + this.rgbVal(this.stats[client].created_at)));
            return this.LightenDarkenColor(
                '#' + this.intToRGB(this.hashCode(  (client.length < 4 ? 'AA': '') + client.substring(0, 8) + this.stats[client].created_at)),
                25);
        },

        rgbVal(str) {
            return Math.abs(this.hashCode(str) % 255);
        },

        hashCode(str) { // java String#hashCode
            var hash = 0;
            for (var i = 0; i < str.length; i++) {
                hash = str.charCodeAt(i) + ((hash << 5) - hash);
            }
            return hash;
        },

        intToRGB(i) {
            var c = (i & 0x00FFFFFF)
                .toString(16)
                .toUpperCase();

            return "00000".substring(0, 6 - c.length) + c;
        },

        LightenDarkenColor(col, amt) {
            var usePound = false;

            if (col[0] == "#") {
                col = col.slice(1);
                usePound = true;
            }

            var num = parseInt(col, 16);
            var r = (num >> 16) + amt;

            if (r > 255) r = 255;
            else if (r < 0) r = 0;

            var b = ((num >> 8) & 0x00FF) + amt;

            if (b > 255) b = 255;
            else if (b < 0) b = 0;

            var g = (num & 0x0000FF) + amt;

            if (g > 255) g = 255;
            else if (g < 0) g = 0;

            return (usePound ? "#" : "") + (g | (b << 8) | (r << 16)).toString(16);

        },
    }
}
</script>

<style scoped>

</style>
