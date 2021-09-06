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
                    <job-search @searching="runningSearch"
                                classes="bg-white flex"
                                placeholder="Search another job">
                    </job-search>
                </div>
            </div>
        </template>

        <div v-if="searching || !currentJob.hasOwnProperty('metadata')">
            <loader></loader>
        </div>
        <div v-else-if="currentJob.metadata.processing_mysgs">
            <loader></loader>
        </div>
        <div v-else-if="currentJob.metadata.error_mysgs">
            <div class="h-64 bg-white flex justify-center align-middle">
                <p class="mt-16 text-red-700">There was an error loading data for the job "{{ currentJob.job_number }}".
                    <br><span v-if="currentJob.metadata.error_mysgs_reason">
                        {{ currentJob.metadata.error_mysgs_reason }}
                    </span>
                    <span v-else>Please try again later.</span>
                </p>
            </div>
        </div>
        <div v-else-if="currentJob.metadata.client_found === false">
            <div class="h-64 bg-white flex justify-center align-middle">
                <p class="mt-16 text-red-700">
                    "{{ currentJob.metadata.client.name }}" was not matched with any client account.
                </p>
            </div>
        </div>
        <div v-else>
            <job-identification :job="currentJob"/>

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

                    <div class="flex flex-col w-full">
                        <!-- Stage filter -->
                        <div class="flex flex-grow text-xs mx-2 mb-2" role="group" v-if="showStage">
                            <button
                                v-for="(stage, index) in stages"
                                @click="filterStageButtonClicked(stage)"
                                class="flex-grow hover:bg-blue-400 hover:text-white border border-r-0 border-blue-500 px-1 py-2 mx-0 outline-none focus:shadow-outline"
                                :class="[
                                    {'rounded-l-lg' : index === 0 },
                                    { 'bg-blue-500 text-white' : $data[`filterStage${stage}`] },
                                    { 'bg-white text-blue-500' : !$data[`filterStage${stage}`] },
                                    {'bg-green-100' : !$data[`filterStage${stage}`] && currentJob.metadata.hasOwnProperty('stages') && currentJob.metadata.stages.includes(stage) },
                                ]">
                                {{stage}}
                            </button>
                            <button @click="resetStage"
                                class="rounded-r-lg flex-grow hover:bg-blue-400 hover:text-white text-blue-500 border border-r-0 border-blue-500 px-1 py-2 mx-0 outline-none focus:shadow-outline">
                                Reset
                            </button>
                        </div>

                        <!-- Artwork structure and date Filters -->
                        <div class="flex flex-grow text-xs mx-2" role="group">
                            <button @click="filterArtworkStructureButtonClicked('isNew')"
                                    :title="$page.settings.rule_filter_new_duration + ' days'"
                                    class="flex-grow hover:bg-blue-500 hover:text-white border border-r-0 border-blue-500 px-1 py-2 mx-0 outline-none focus:shadow-outline rounded-l-lg"
                                    :class="[
                                    { 'bg-blue-500 text-white' : filterOption === 'isNew' },
                                    { 'bg-white text-blue-500' : filterOption !== 'isNew' }
                                ]">
                                New
                            </button>
                            <button @click="filterArtworkStructureButtonClicked('isUpdated')"
                                    :title="$page.settings.rule_filter_updated_duration + ' days'"
                                    class="flex-grow hover:bg-blue-500 hover:text-white border border-r-0 border-blue-500 px-1 py-2 mx-0 outline-none focus:shadow-outline"
                                    :class="[
                                    { 'bg-blue-500 text-white' : filterOption === 'isUpdated' },
                                    { 'bg-white text-blue-500' : filterOption !== 'isUpdated' }
                                ]">
                                Updated
                            </button>
                            <button
                                v-for="term in artworkStructureTerms"
                                @click="filterArtworkStructureButtonClicked(term)"
                                class="flex-grow hover:bg-blue-500 hover:text-white border border-r-0 border-blue-500 px-1 py-2 mx-0 outline-none focus:shadow-outline"
                                :class="[
                                { 'bg-blue-500 text-white' : filterOption === term },
                                 { 'bg-white text-blue-500' : filterOption !== term  }, ]">
                                {{ term }}
                            </button>
                            <button @click="unfilter"
                                    class="flex-grow bg-white text-blue-500 hover:bg-blue-500 hover:text-white border border-blue-500 px-1 py-2 mx-0 outline-none focus:shadow-outline rounded-r-lg">
                                Unfilter
                            </button>
                        </div>
                        <!-- End Filters -->
                    </div>

                    <isotope ref="cpt" id="root_isotope" class="w-full m-2"
                             v-if="Object.keys(filterObject).length"
                             :options='getOptions()'
                             :list="Object.entries(rulesByTaxonomies)"
                             @filter="filterOption=arguments[0]" @sort="sortOption=arguments[0]">

                        <div class="w-1/3 rounded"
                             v-for="(ruleGroup, ruleIndex) in Object.entries(rulesByTaxonomies)" :key="ruleIndex">
                            <view-rule-item :rules="ruleGroup[1]"
                                            :group="ruleGroup[0]"
                                            :filter-flag="filterFlag"
                                            :filter-stage-pa="filterStagePA"
                                            :filter-stage-pp="filterStagePP"
                                            :filter-stage-pf="filterStagePF"
                                            @on-click-view="openRuleModal"/>
                        </div>

                    </isotope>

                </div>
            </div>
        </div>

        <!-- Rule Viewing Modal -->
        <jet-dialog-modal :show="isOpen && currentRule" max-width="6xl" @close="closeRuleModal">
            <template #title>
                <div v-if="currentRule" class="flex justify-between">
                    <div class="flex-grow border-gray-200 border-b-2 mr-6">
                        <p class="font-bold">
                            <span class="text-xs" title="Rule ID">[{{ currentRule.dagId }}]</span>
                            {{ currentRule.name }}
                        </p>
                    </div>
                    <jet-secondary-button @click.native="closeRuleModal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                  clip-rule="evenodd"/>
                        </svg>
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

                <a v-if="currentRule && $page.user_permissions.updateRules" target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150"
                   :href="route('pm.client-account.rules.edit', {clientAccount: currentJob.metadata.client.slug, id: currentRule.id})">
                    Edit
                </a>

                <jet-secondary-button @click.native="closeRuleModal">
                    Close
                </jet-secondary-button>
            </template>
        </jet-dialog-modal>

        <!-- Rule Flagging Modal -->
        <jet-dialog-modal :show="isFlaggingRule" @close="closeFlagModal">
            <template #title v-if="currentFlaggingRule">
                <span>Flag rule {{ currentFlaggingRule.name }}</span>
                <br><span class="text-xs">Rule ID: {{ currentFlaggingRule.dagId }}</span>
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
import JobIdentification from "@/Components/OP/JobIdentification";

export default {
    props: [
        'team',
        'job',
        'jobNumber',
        'rules'
    ],

    title() {
        return `${this.currentJob.job_number} - Dagobah`;
    },

    data() {
        return {
            currentJob: this.job,
            currentRules: this.rules,
            searchJobKey: this.jobNumber,
            searching: false,
            searchedRules: [..._.orderBy(this.rules, 'created_at', 'desc')],
            isOpen: false,
            currentRule: null,
            rulesUpdated: false,

            // isotope integration
            sortOption: null,
            filterOption: null,
            filterStagePA: false,
            filterStagePP: false,
            filterStagePF: false,
            filterOptionTracker: null,
            filterText: "",
            filterObject: {},

            /* Contains alls the terms used under job categorizations taxonomies, for indexing */
            artworkStructureTerms: [],
            stages: ['PA', 'PP', 'PF'],

            /* Contains terms grouped by taxonomy under job categoizations, for filtering */
            taxonomies: {},

            rulesByTaxonomies: {},

            filterFlag: null,
            isFlaggingRule: false,
            currentFlaggingRule: null,

            flagRuleForm: this.$inertia.form({
                reason: "",
            }, {
                bag: 'sendFlagRule'
            }),

            isotopeFixRanOnce: false,
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
        },

        jobNumber: function (newJobNumber, oldJobNumber) {
            console.log('detected job number change');
            this.initJobLoaded();
        }
    },

    mounted() {
        this.initJobLoaded();
    },

    destroyed() {
        clearTimeout(this.timeOut);
    },

    methods: {
        initJobLoaded() {
            this.currentJob = this.job;
            if (this.currentJob.metadata.processing_mysgs === false) {
                console.log('job rules already loaded');
                this.currentRules = this.rules;
                this.newRulesLoaded();
                this.initRulesParsing();
            } else {
                this.waitMode();
            }
        },

        newRulesLoaded() {
            this.searchedRules = [..._.orderBy(this.currentRules, 'created_at', 'desc')];
            this.searching = false;

            if(this.currentJob.metadata.hasOwnProperty('stages') && this.currentJob.metadata.stages.length) {
                for (let index in this.currentJob.metadata.stages) {
                    this.$data[`filterStage${this.currentJob.metadata.stages[index]}`] = true;
                }
            }
        },

        initRulesParsing() {
            this.searchedRules.forEach(rule => {
                rule.job_categorizations_terms.forEach(term => {
                    //if (term.taxonomy.name === 'Artwork Structure Elements') {

                    /*
                    Collect filterable terms
                    */
                    if (!this.artworkStructureTerms.includes(term.name) && term.taxonomy.name === 'Artwork Structure Elements') {
                        this.artworkStructureTerms.push(term.name);
                    }

                    /*
                    Group terms by parent taxonomy
                     */
                    if (!this.taxonomies.hasOwnProperty(term.taxonomy.name)) {
                        this.taxonomies[term.taxonomy.name] = [];
                    }
                    if (!this.taxonomies[term.taxonomy.name].includes(term.name)) {
                        this.taxonomies[term.taxonomy.name].push(term.name);
                    }

                    /*
                    Group rules by Artwork Structure Elements terms
                     */
                    if(term.taxonomy.name === 'Artwork Structure Elements'){
                        if (this.rulesByTaxonomies[term.name] === undefined) {
                            this.rulesByTaxonomies[term.name] = [];
                        }

                        this.rulesByTaxonomies[term.name].push(rule);
                    }
                });
                this.artworkStructureTerms.forEach(taxonomy => {
                    this.filterObject[taxonomy] = itemElem => {
                        return itemElem[0] === taxonomy;
                    }
                });
            });

            this.initSearchFunctions();
        },

        initSearchFunctions() {
            this.filterObject['isNew'] = (itemElem) => {
                return itemElem[1].filter(rule => moment()
                    .subtract(parseInt(this.$page.settings.rule_filter_new_duration), 'days')
                    .isSameOrBefore(moment(rule.created_at))
                ).length > 0;
            };

            this.filterObject['isUpdated'] = (itemElem) => {
                return itemElem[1].filter(rule => moment()
                    .subtract(parseInt(this.$page.settings.rule_filter_updated_duration), 'days')
                    .isSameOrBefore(moment(rule.updated_at))
                ).length > 0;
            };

            this.filterObject['all'] = (itemElem) => {
                return true;
            };
        },

        waitMode() {
            this.timeOut = setTimeout(() => {
                this.queryRules();
            }, 5000);
        },

        queryRules() {
            const headers = {
                "content-type": "application/json",
                "Accept": "application/json"
            };

            axios.get(route('job.rules', this.jobNumber), {headers})
                .then(({data}) => {
                    console.log(data);
                    if (data.job && data.job.hasOwnProperty('metadata') && !data.job.metadata.processing_mysgs && !data.job.metadata.error_mysgs) {
                        clearTimeout(this.timeOut);

                        this.currentJob = data.job;
                        this.currentRules = data.rules;
                        this.newRulesLoaded();
                        this.initRulesParsing();
                    } else if (data.job && data.job.hasOwnProperty('metadata') && data.job.metadata.error_mysgs) {
                        this.currentJob = data.job;
                        this.currentJob.metadata.error_mysgs = data.job.metadata.error_mysgs;
                        console.log('mysgs error, halt');
                        clearTimeout(this.timeOut);
                    } else {
                        this.waitMode();
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

        runningSearch() {
            this.currentJob = {metadata: {}};
            this.searching = true;
            this.artworkStructureTerms = [];
            this.rulesByTaxonomies = {};
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
            //this.currentRule = null;
        },

        filterArtworkStructureButtonClicked(filterName) {
            console.log('filter taxomy button clicked', filterName);

            if (this.filterOptionTracker !== '' && this.filterOptionTracker === filterName) {
                this.unfilter();
                console.log('unfiltering');
                return;
            }

            this.filterOptionTracker = filterName;
            switch (filterName) {
                case 'isNew':
                    this.filterByNew()
                    break;
                case 'isUpdated':
                    this.filterByUpdated();
                    break;
                default:
                    this.filterByTaxonomy(filterName);
                    break;
            }
        },

        filterStageButtonClicked(stage) {
            console.log('filter stage button clicked', stage);

            this.$data[`filterStage${stage}`] = !this.$data[`filterStage${stage}`];

            if(!this.isotopeFixRanOnce) {
                this.isotopeFix = setTimeout( () => this.$refs.cpt.unfilter(), 300);
                this.isotopeFixRanOnce = true;
            }

            this.$forceUpdate();
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
            console.log('filtering by taxonomy', taxonomy);
            this.$refs.cpt.filter(taxonomy);
            this.filterFlag = null;
        },
        filterByStage(stage) {
            console.log('filtering by stage', stage);
            this.$refs.cpt.filter(stage);
        },
        unfilter() {
            this.filterOptionTracker = '';
            this.filterFlag = null;
            this.$refs.cpt.filter('all');
            this.$refs.cpt.unfilter();
        },

        resetStage() {

            console.log('resetting to initial stage', this.filterStage);
            this.filterStagePA = false;
            this.filterStagePP = false;
            this.filterStagePF = false;
            for(let index in this.currentJob.metadata.stages) {
                this.$data[`filterStage${this.currentJob.metadata.stages[index]}`] = true;
            }

            this.$forceUpdate();
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

    computed: {
        showStage() {
            return this.taxonomies.hasOwnProperty('Stage');
        },
    },

    components: {
        JobIdentification,
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

