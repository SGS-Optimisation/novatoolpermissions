<template>
    <client-layout :client-account="clientAccount">
        <template #body>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-gray-50 pt-5">
                <div class="grid grid-cols-5 gap-1">

                    <taxonomy-selector v-for="(terms, taxonomyName) in termsByTaxonomies"
                                       :taxonomy-name="taxonomyName"
                                       :key="taxonomyName"
                                       :terms="terms"
                                       @termSelected="filterByTaxonomyTerm"
                                       ref="taxonomySelectors"
                    >
                    </taxonomy-selector>
                    <taxonomy-selector :taxonomy-name="'State'"
                                       :terms="states"
                                       @termSelected="filterByState"
                                       ref="stateSelector"
                    >
                    </taxonomy-selector>


                    <filter-condition @on-change-filter-condition="onChangeFilterCondition"/>

                </div>
                <div class="flex flex-row m-2 justify-between">
                    <div class="flex justify-start m-2 inline-block" id="text-search">
                        <jet-label class="align-middle mr-2 mt-2" for="text-search" value="Text search:"/>
                        <jet-input type="text" name="text-search" autocomplete="off" class="block"
                                   v-model="filterText"
                                   @input="debounceGetRules"
                        />
                    </div>
                    <div id="filter" class="flex justify-end">
                        <div class="flex text-xs m-2" role="group">
                            <button @click="setFilterDate('isNew')"
                                    :title="$page.settings.rule_filter_new_duration + ' days'"
                                    :class="[{ 'bg-blue-500 text-white' : filterOption === 'isNew' }, { 'bg-white text-blue-500' : filterOption !== 'isNew' }, 'hover:bg-blue-500 hover:text-white border border-r-0 border-blue-500 px-4 py-2 mx-0 outline-none focus:shadow-outline rounded-l-lg']">
                                New <span title="Total number of rules considered new" class="px-1 rounded-xl bg-pink-300">{{numNewRules}}</span>
                            </button>
                            <button @click="setFilterDate('isUpdated')"
                                    :title="$page.settings.rule_filter_updated_duration + ' days'"
                                    :class="[{ 'bg-blue-500 text-white' : filterOption === 'isUpdated' }, { 'bg-white text-blue-500' : filterOption !== 'isUpdated' }, 'hover:bg-blue-500 hover:text-white border border-r-0 border-blue-500 px-4 py-2 mx-0 outline-none focus:shadow-outline']">
                                Updated <span title="Total number of rules considered updated" class="px-1 rounded-xl bg-pink-300">{{numUpdatedRules}}</span>
                            </button>
                            <button @click="setFilterDate('isFlagged')"
                                    :class="[{ 'bg-blue-500 text-white' : filterOption === 'isFlagged' }, { 'bg-white text-blue-500' : filterOption !== 'isFlagged' }, 'hover:bg-blue-500 hover:text-white border border-r-0 border-blue-500 px-4 py-2 mx-0 outline-none focus:shadow-outline']">
                                Flagged <span title="Total number of flagged rules" class="px-1 rounded-xl bg-pink-300">{{numFlaggedRules}}</span>
                            </button>
                            <button @click="setFilterDate('all')"
                                    class="bg-white text-blue-500 hover:bg-blue-500 hover:text-white border border-blue-500 px-4 py-2 mx-0 outline-none focus:shadow-outline rounded-r-lg">
                                Unfilter
                            </button>

                        </div>
                        <jet-button class="mt-2 h-2/3" type="button" @click.native="clearAllFilters">
                            Reset All
                        </jet-button>
                    </div>

                </div>

                <div v-if="search">
                    <p>Showing rules for {{ search }}</p>
                </div>

                <div class="px-2">
                    <pagination v-model="page"
                                :options="paginationOptions"
                                :per-page="perPage"
                                :records="numFilteredRules"/>
                </div>

                <div v-for="(rule, ruleKey) in _.drop(filteredRules, ((page-1)*perPage)).slice(0, perPage)"
                     :key="ruleKey">
                    <view-rule :rule="rule" :client-account="clientAccount" @updated="getRules"/>
                </div>

                <div class="px-2 pb-16 pt-4">
                    <pagination v-model="page"
                                :options="paginationOptions"
                                :per-page="perPage"
                                :records="numFilteredRules"/>
                </div>

            </div>
        </template>
    </client-layout>
</template>

<script>
import ClientLayout from '@/Layouts/ClientAccount'
import ViewRule from '@/Components/PM/Rules/ListView'
import moment from 'moment'
import TaxonomyFilter from '@/Components/PM/Rules/TaxonomyFilter'
import TaxonomySelector from "@/Components/PM/Rules/TaxonomySelector";
import FilterCondition from "@/Components/PM/Rules/FilterCondition";
import JetButton from "@/Jetstream/Button";
import JetInput from "@/Jetstream/Input";
import JetLabel from "@/Jetstream/Label";

export default {
    props: [
        'clientAccount',
        'team',
        'rules',
        'search',
        'states',
    ],

    data() {
        return {
            paginationOptions: {
                texts: {
                    count: 'Showing {from} to {to} of {count} rules|{count} rules|One rule',
                    first: 'First',
                    last: 'Last',
                    nextPage: '>',
                    nextChunk: '>>',
                    prevPage: '<',
                    prevChunk: '<<'
                },
                theme: 'bootstrap4',
            },
            page: 1,
            perPage: 25,


            allRules: [..._.orderBy(this.rules, 'created_at', 'desc')],
            filteredRules: [],
            sortOption: null,
            filterOption: 'all',
            filterText: "",
            filterState: "",
            filterObject: {},
            taxonomies: {},
            termsByTaxonomies: {},
            filterCondition: true, // true = AND, false = OR
        }
    },

    created() {

        this.allRules.forEach(rule => {
            rule.terms.forEach(term => {
                if(!term.taxonomy) {
                    console.log('term has no taxonomy!', {term, rule});
                    return;
                }

                if (this.taxonomies[term.taxonomy.name] === undefined) {
                    this.taxonomies[term.taxonomy.name] = '';
                }

                if (this.termsByTaxonomies[term.taxonomy.name] === undefined) {
                    this.termsByTaxonomies[term.taxonomy.name] = [];
                }

                if (!this.termsByTaxonomies[term.taxonomy.name].includes(term.name)) {
                    this.termsByTaxonomies[term.taxonomy.name].push(term.name);
                }
            })
        });

        this.filterObject['filterByTaxonomyTerm'] = (itemElem) => {
            //return this.filterCondition ? itemElem.terms.every(term => this.taxonomies[term.taxonomy.name] === term.name) : itemElem.terms.some(term => this.taxonomies[term.taxonomy.name] === term.name);
            if (this.filterCondition) {
                let taxonomies = Object.entries(this.taxonomies);
                if (taxonomies.length === 0)
                    return false;
                for (const taxonomy of taxonomies) {
                    if (taxonomy[1] !== '') {
                        let matchingTaxonomy = itemElem.terms.map(term => term.taxonomy.name).includes(taxonomy[0])
                        if (!matchingTaxonomy) return false
                        let matchingTerms = itemElem.terms.map(term => term.name).includes(taxonomy[1])
                        if (!matchingTerms) return false
                    }
                }
                return true;
            }
            return itemElem.terms.some(term => this.taxonomies[term.taxonomy.name] === term.name);
        };

        this.filterObject['filterState'] = (itemElem) => {
            return !this.filterState || itemElem.state === this.filterState;
        };

        this.filterObject['isNew'] = (itemElem) => {
            return moment().subtract(parseInt(this.$page.settings.rule_filter_new_duration), 'days').isSameOrBefore(moment(itemElem.created_at));
        };

        this.filterObject['isUpdated'] = (itemElem) => {
            return moment().subtract(this.$page.settings.rule_filter_updated_duration, 'days').isSameOrBefore(moment(itemElem.updated_at));
        };

        this.filterObject['isFlagged'] = (itemElem) => {
            return itemElem.flagged === true;
        };

        this.filterObject['all'] = (itemElem) => {
            return true;
        };

        this.getRules();
    },

    methods: {

        getRules() {
            this.filteredRules = _.filter(
                _.filter(
                    _.filter(
                        _.filter(this.allRules, (rule) => {
                            if (!this.filterText || this.filterText === '') {
                                return true;
                            }
                            return rule.name.toLowerCase().indexOf(this.filterText.toLowerCase()) !== -1
                                || rule.content.toLowerCase().indexOf(this.filterText.toLowerCase()) !== -1;
                        }),
                        this.filterObject['filterByTaxonomyTerm']
                    ),
                    this.filterObject[this.filterOption]),
                this.filterObject['filterState'],
            );
            this.page = 1;
        },

        debounceGetRules: _.debounce( function(e) {
            this.getRules()
        }, 300),

        filterByTaxonomyTerm(taxonomy, term) {
            this.taxonomies[taxonomy] = (term ? term : '');
            this.getRules();
        },

        filterByState(dummy, state) {
            this.filterState = state ? state : '';
            this.getRules();
        },

        setFilterDate(value) {
            this.filterOption = value;
            this.getRules();
        },

        onChangeFilterCondition(condition) {
            console.log('filtering condition');
            this.filterCondition = condition;
            this.getRules();
        },

        clearAllFilters() {
            for (const taxonomy in this.taxonomies) {
                this.taxonomies[taxonomy] = '';
            }
            this.filterOption = 'all';
            this.filterText = '';
            this.filterState = '';
            this.getRules();
            this.$refs.taxonomySelectors.forEach(selector => selector.clearSelected());
            this.$refs.stateSelector.clearSelected();
        }
    },

    computed: {
        numFilteredRules: function () {
            return this.filteredRules.length;
        },

        numAllRules: function () {
            return this.allRules.length;
        },

        numNewRules: function () {
            return (_.filter(this.allRules, this.filterObject.isNew)).length;
        },
        numUpdatedRules: function () {
            return (_.filter(this.allRules, this.filterObject.isUpdated)).length;
        },
        numFlaggedRules: function () {
            return (_.filter(this.allRules, this.filterObject.isFlagged)).length;
        },
    },

    components: {
        FilterCondition,
        ClientLayout,
        ViewRule,
        TaxonomyFilter,
        TaxonomySelector,
        JetButton,
        JetInput,
        JetLabel,
    },
}
</script>
