<template>
    <app-layout>
        <template #header>
            <div class="flex justify-between align-middle">

                <div class="flex-grow">
                    <h2 v-if="job" class="pt-2 font-semibold text-xl text-gray-800 leading-tight">
                        Rules for job {{ job.job_number }}
                    </h2>
                </div>

                <div class="flex-grow">
                    <job-search @loaded="newJobLoaded"
                                classes="bg-white flex shadow"
                                placeholder="Search another job">
                    </job-search>
                </div>
            </div>
        </template>

        <div class="p-2">
            <div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-green-300" v-if="rulesUpdated">
                <span class="text-xl inline-block mr-5 align-middle">
                    <i class="fas fa-bell"/>
                </span>
                <span class="inline-block align-middle mr-8">
                    <b class="capitalize">Hello!</b> Rules list updated do you want to check?
                </span>
                <button
                    class="absolute bg-transparent text-2xl font-semibold leading-none right-0 top-0 mt-4 mr-6 outline-none focus:outline-none"
                    @click="reloadPage">
                    Reload
                </button>
            </div>

            <div class="flex flex-wrap overflow-hidden sm:-mx-px md:-mx-px lg:-mx-px xl:-mx-px mt-2">

                <div class="flex text-xs m-2" role="group">
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

                <!--                <isotope ref="cpt" id="root_isotope" class="w-full m-2" :options='getOptions()' :list="searchedRules"-->
                <!--                         @filter="filterOption=arguments[0]" @sort="sortOption=arguments[0]">-->
                <!--                    <div class="w-1/3 rounded shadow-md hover:shadow-lg cursor-pointer p-2"-->
                <!--                         v-for="(rule, ruleIndex) in searchedRules" :key="ruleIndex">-->
                <!--                        <view-rule-item :rule="rule" @on-click-view="openModal"/>-->
                <!--                    </div>-->
                <!--                </isotope>-->

                <isotope ref="cpt" id="root_isotope" class="w-full m-2" :options='getOptions()'
                         :list="Object.entries(rulesByTaxonomies)"
                         @filter="filterOption=arguments[0]" @sort="sortOption=arguments[0]">
                    <div class="w-1/3 rounded shadow-md hover:shadow-lg cursor-pointer p-2"
                         v-for="(rule, ruleIndex) in Object.entries(rulesByTaxonomies)" :key="ruleIndex">
                        <view-rule-item :rule="rule" :filter-flag="filterFlag" @on-click-view="openModal"/>
                    </div>
                </isotope>

            </div>
        </div>

        <div class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="isOpen">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                <div class="fixed inset-0 transition-opacity">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full"
                    role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                    <form>

                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="">
                                <div class="mb-4">
                                    <view-rule :rule="currentRule">
                                        <button @click="closeModal()" type="button"
                                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                            Close
                                        </button>
                                    </view-rule>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                                <button @click="closeModal()" type="button"
                                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Close
                                </button>
                            </span>
                        </div>

                    </form>

                </div>
            </div>
        </div>

    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Input from "@/Jetstream/Input";
import Button from "@/Jetstream/Button";
import JetButton from '@/Jetstream/Button'
import JetInput from '@/Jetstream/Input'
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
            searchJobKey: this.jobNumber,
            searching: false,
            searchedRules: [..._.orderBy(this.rules, 'created_at', 'desc')],
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

            filterFlag: null
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

        this.filterObject['isNew'] = (itemElem) => {
            return itemElem[1].filter(rule => moment().subtract(3, 'months').isSameOrBefore(moment(rule.created_at))).length > 0;
        };

        this.filterObject['isUpdated'] = (itemElem) => {
            return itemElem[1].filter(rule => moment().subtract(3, 'months').isSameOrBefore(moment(rule.updated_at))).length > 0;
        };
    },

    methods: {
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
            this.searchedRules = [...this.rules];
        },
        reloadPage() {
            window.location = window.location + this.searchJobKey;
        },
        openModal(rule) {
            this.currentRule = rule;
            this.isOpen = true
        },
        closeModal() {
            this.currentRule = null;
            this.isOpen = false;
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
        }
    },

    components: {
        ViewRuleItem,
        Button,
        Input,
        AppLayout,
        JetButton,
        JetInput,
        ViewRule,
        JobSearch,
        isotope
    },
}
</script>
