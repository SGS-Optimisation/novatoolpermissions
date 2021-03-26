<template>
    <app-layout>
        <template #header>
            <div class="flex justify-between align-middle">

                <div class="flex-grow">
                    <h2 v-if="currentJob" class="pt-2 font-bold text-xl text-gray-800 leading-tight">
                        Rules for job {{ currentJob.job_number }}
                    </h2>
                </div>

                <div class="flex-grow">
                    <job-search @loaded="newJobLoaded"
                                classes="bg-white flex"
                                placeholder="Search another job">
                    </job-search>
                </div>
            </div>
        </template>

        <div v-if="currentJob.metadata.processing_mysgs">
            <loader></loader>
        </div>
        <div v-else>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 bg-gray-50 shadow-inner border border-orange-200">
                <div class="flex justify-between">
                    <div class="p-2">
                        <div>
                            <h3 class="font-semibold text-lg leading-loose text-gray-800">Client Account:
                                {{ currentJob.metadata.client.name }}
                            </h3>
                        </div>

                        <div class="flex flex-shrink-0">
                            <div v-for="(value, item) in currentJob.metadata.job_taxonomy" class="flex flex-col">
                                <div class="flex flex-wrap flex-shrink-0 text-xs items-center pr-3 text-xs mr-3">
                                    <div class="flex-grow h-full bg-gray-300 text-gray-600 px-2 rounded-l-lg">
                                        <div class="grid h-full">
                                            <div class="place-self-center">{{ item }}</div>
                                        </div>
                                    </div>
                                    <div class="">

                                        <div :class="currentJob.metadata.matched_taxonomy[item].length ?
                                                'bg-blue-200 text-green-800 px-2 rounded-tr-lg'
                                                :'bg-blue-200 text-green-800 px-2 rounded-r-lg'"
                                             title="MySGS value">{{ value }}
                                        </div>

                                        <div v-for="(terms, index) in currentJob.metadata.matched_taxonomy[item]"
                                             :class="(index === currentJob.metadata.matched_taxonomy[item].length - 1) ?
                                                    'bg-pink-200 text-green-800 px-2 rounded-br-lg'
                                                    :'bg-pink-200 text-green-800 px-2'"
                                             title="Matched Dagobah terms">
                                            {{ terms }}<br>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 bg-white">
                <div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-green-300" v-if="rulesUpdated">
                <span class="text-xl inline-block mr-5 align-middle">
                    <i class="fas fa-bell"/>
                </span>
                    <span class="inline-block align-middle mr-8">
                    <b class="capitalize">Hello!</b> Rules list updated. Do you want to check?
                </span>
                    <button
                        class="absolute bg-transparent text-2xl font-semibold leading-none right-0 top-0 mt-4 mr-6 outline-none focus:outline-none"
                        @click="reloadPage">
                        Reload
                    </button>
                </div>

                <div class="flex flex-wrap overflow-hidden sm:-mx-px md:-mx-px lg:-mx-px xl:-mx-px mb-2">

                    <div class="flex text-xs mx-2" role="group">
                        <button @click="filterByNew"
                                :class="[{ 'bg-blue-500 text-white' : filterOption === 'isNew' }, { 'bg-white text-blue-500' : filterOption !== 'isNew' }, 'hover:bg-blue-500 hover:text-white border border-r-0 border-blue-500 px-4 py-2 mx-0 outline-none focus:shadow-outline rounded-l-lg']">
                            New
                        </button>
                        <button @click="filterByUpdated"
                                :class="[{ 'bg-blue-500 text-white' : filterOption === 'isUpdated' }, { 'bg-white text-blue-500' : filterOption !== 'isUpdated' }, 'hover:bg-blue-500 hover:text-white border border-r-0 border-blue-500 px-4 py-2 mx-0 outline-none focus:shadow-outline']">
                            Updated
                        </button>
                        <button
                            v-for="taxonomy in taxonomies"
                            @click="filterByTaxonomy(taxonomy)"
                            :class="[{ 'bg-blue-500 text-white' : filterOption === taxonomy }, { 'bg-white text-blue-500' : filterOption !== taxonomy  }, 'hover:bg-blue-500 hover:text-white border border-r-0 border-blue-500 px-4 py-2 mx-0 outline-none focus:shadow-outline']">
                            {{ taxonomy }}
                        </button>
                        <button @click="$refs.cpt.unfilter()"
                                class="bg-white text-blue-500 hover:bg-blue-500 hover:text-white border border-blue-500 px-4 py-2 mx-0 outline-none focus:shadow-outline rounded-r-lg">
                            Unfilter
                        </button>
                    </div>

                    <isotope ref="cpt" id="root_isotope" class="w-full m-2" :options='getOptions()'
                             :list="Object.entries(rulesByTaxonomies)"
                             @filter="filterOption=arguments[0]" @sort="sortOption=arguments[0]">

                        <div class="w-1/3 rounded"
                             v-for="(ruleGroup, ruleIndex) in Object.entries(rulesByTaxonomies)" :key="ruleIndex">
                            <view-rule-item :rules="ruleGroup[1]" :group="ruleGroup[0]" :filter-flag="filterFlag"
                                            @on-click-view="openRuleModal"/>
                        </div>

                    </isotope>

                </div>
            </div>
        </div>


        <!-- Rule Viewing Modal -->
        <jet-dialog-modal :show="isOpen && currentRule" max-width="6xl" @close="closeRuleModal">
            <template #title>
                <div class="flex justify-between">
                    <div><p>Viewing rule</p></div>
                    <jet-secondary-button @click.native="closeRuleModal">
                        <i class="fa fa-times"/>
                    </jet-secondary-button>
                </div>
            </template>

            <template #content>
                <div class="overflow-scroll">
                    <view-rule :rule="currentRule"/>
                </div>
            </template>

            <template #footer>

                <div>
                    <jet-action-message :on="flagRuleForm.recentlySuccessful" class="ml-3">
                        Rule was flagged.
                    </jet-action-message>
                </div>

                <jet-button class="ml-2" @click.native="flagRule(currentRule)">
                    Flag rule?
                </jet-button>

                <jet-secondary-button @click.native="closeRuleModal">
                    Close
                </jet-secondary-button>
            </template>
        </jet-dialog-modal>


        <!-- Rule Flagging Modal -->
        <jet-dialog-modal :show="isFlaggingRule" @close="closeFlagModal">
            <template #title>
                <span v-if="currentFlaggingRule">Flag rule {{ currentFlaggingRule.name }}</span>
            </template>
            <template #content>
                <div class="mt-4">
                    <jet-label for="reason" value="Flag reason"/>
                    <textarea class="form-input rounded-md shadow-sm mt-1 block w-full" id="reason"
                              placeholder="Please provide a short explanation"
                              v-model="flagRuleForm.reason"/>
                    <!--                    <jet-input type="text" class="mt-1 block w-3/4"
                                                   :value="flagRuleForm.reason"
                                                   v-model="flagRuleForm.reason"/>-->

                </div>
            </template>
            <template #footer>
                <jet-secondary-button @click.native="closeFlagModal">
                    Nevermind
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click.native="sendFlagRule"
                                   :class="{ 'opacity-25': flagRuleForm.processing }"
                                   :disabled="flagRuleForm.processing">
                    Flag
                </jet-danger-button>
            </template>
        </jet-dialog-modal>


    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Input from "@/Jetstream/Input";
import Button from "@/Jetstream/Button";
import JetActionMessage from '@/Jetstream/ActionMessage'
import JetButton from '@/Jetstream/Button'
import JetDangerButton from '@/Jetstream/DangerButton'
import JetDialogModal from '@/Jetstream/DialogModal';
import JetInput from '@/Jetstream/Input'
import JetLabel from '@/Jetstream/Label'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import Loader from "@/Components/Loader";
import ViewRule from '@/Components/PM/Rules/ViewRule'
import ViewRuleItem from "@/Components/PM/Rules/ViewRuleItem";
import JobSearch from "@/Components/OP/JobSearchForm";
import isotope from 'vueisotope'
import moment from "moment";

export default {
    props: [
        'team',
        'job',
        'jobNumber',
        'rules'
    ],

    data() {
        return {
            currentJob: this.job,
            currentRules: this.rules,
            searchJobKey: this.jobNumber,
            searching: false,
            searchedRules: [..._.orderBy(this.currentRules, 'created_at', 'desc')],
            isOpen: false,
            currentRule: null,
            rulesUpdated: false,

            // isotope integration
            sortOption: null,
            filterOption: null,
            filterText: "",
            filterObject: {},
            taxonomies: [],

            rulesByTaxonomies: {},

            filterFlag: null,
            isFlaggingRule: false,
            currentFlaggingRule: null,

            flagRuleForm: this.$inertia.form({
                reason: "",
            }, {
                bag: 'sendFlagRule'
            }),
        }
    },

    watch: {
        searchJobKey() {
            if (this.searchJobKey) {
                Echo.channel(`rules-filtered.${this.searchJobKey}`)
                    .listen('.rules-updated', (e) => {
                        this.rulesUpdated = true;
                    });
            }
        }
    },

    created() {
        if (!this.currentJob.metadata.processing_mysgs) {
            this.initRulesParsing();
        } else {
            this.timeOut = setTimeout(() => {
                this.queryRules();
            }, 2000);
        }

        this.initSearchFunctions();
    },

    methods: {
        initRulesParsing() {
            this.searchedRules.forEach(rule => {
                rule.terms.forEach(term => {
                    if (term.taxonomy.parent.name === 'Job Categorizations') {
                        if (!this.taxonomies.includes(term.taxonomy.name)) {
                            this.taxonomies.push(term.taxonomy.name);
                        }

                        if (this.rulesByTaxonomies[term.taxonomy.name] === undefined) {
                            this.rulesByTaxonomies[term.taxonomy.name] = [];
                        }

                        this.rulesByTaxonomies[term.taxonomy.name].push(rule);
                    }
                });
                this.taxonomies.forEach(taxonomy => {
                    this.filterObject[taxonomy] = itemElem => {
                        return itemElem[0] === taxonomy;
                    }
                });
            });
        },

        initSearchFunctions() {
            this.filterObject['isNew'] = (itemElem) => {
                return itemElem[1].filter(rule => moment().subtract(3, 'months').isSameOrBefore(moment(rule.created_at))).length > 0;
            };

            this.filterObject['isUpdated'] = (itemElem) => {
                return itemElem[1].filter(rule => moment().subtract(3, 'months').isSameOrBefore(moment(rule.updated_at))).length > 0;
            };
        },

        queryRules() {
            axios.get(route('job.rules', this.jobNumber))
                .then(({data}) => {
                    console.log(data);
                    if (!data.job.metadata.processing_mysgs) {
                        clearTimeout(this.timeOut);

                        this.currentJob = data.job;
                        this.currentRules = data.rules;
                        this.newJobLoaded();
                        this.initRulesParsing();
                    }

                })
        },

        getOptions() {
            return {
                layoutMode: 'masonry',
                // masonry: {
                //     gutter: 2,
                // },
                getSortData: {
                    id: "id",
                },
                getFilterData: this.filterObject
            }
        },
        newJobLoaded() {
            this.searchedRules = [...this.currentRules];
        },
        reloadPage() {
            window.location = window.location + this.searchJobKey;
        },
        openRuleModal(rule) {
            this.currentRule = rule;
            this.isOpen = true
        },
        closeRuleModal() {
            this.isOpen = false;
            this.currentRule = null;
        },
        search() {
            if (this.searchJobKey && this.searchJobKey !== '') {
                this.searching = true;
                axios({
                    url: route("rule_search", this.searchJobKey),
                    method: "GET",
                })
                    .then(result => {
                        this.searchedRules = result.data;
                        this.searching = false;
                    })
                    .catch(err => {
                        this.searching = false;
                    });
            }
        },
        filterByNew() {
            this.$refs.cpt.filter('isNew');
            this.filterFlag = "new";
        },
        filterByUpdated() {
            this.$refs.cpt.filter('isUpdated');
            this.filterFlag = "updated";
        },
        filterByTaxonomy(taxonomy) {
            this.$refs.cpt.filter(taxonomy)
            this.filterFlag = null;
        },

        flagRule(rule) {
            this.currentFlaggingRule = rule;
            this.isFlaggingRule = true;
        },

        closeFlagModal() {
            this.isFlaggingRule = false;
            this.currentFlaggingRule = null;
        },

        sendFlagRule() {
            console.log('flagging rule', this.currentFlaggingRule);

            this.flagRuleForm.post(route('rule.flag', this.currentFlaggingRule.id), {
                preserveScroll: true
            }).then(() => {

                this.closeFlagModal();
            });
        },


    },

    components: {
        ViewRuleItem,
        Button,
        Input,
        AppLayout,
        JetActionMessage,
        JetButton,
        JetDangerButton,
        JetDialogModal,
        JetInput,
        JetLabel,
        JetSecondaryButton,
        Loader,
        ViewRule,
        JobSearch,
        isotope
    },
}
</script>

