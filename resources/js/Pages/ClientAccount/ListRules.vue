<template>
    <client-layout :client-account="clientAccount">
        <template #body>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-gray-50 pt-5">
                <!-- <simple-pagination :data="rules"></simple-pagination> -->
                <div class="grid grid-cols-5 gap-1">
                    <taxonomy-filter v-for="(taxonomy, taxonomyIndex) in Object.entries(termsByTaxonomies)"
                                     :taxonomy="taxonomy"
                                     :selected-value="taxonomies[taxonomy[0]]"
                                     :key="taxonomyIndex"
                                     @on-change-filter="filterByTaxonomyTerm"/>
                    <filter-condition @on-change-filter-condition="onChangeFilterCondition"/>

                    <jet-button @click.native="clearAllFilters">
                        Reset All
                    </jet-button>
                </div>
                <div id="filter" class="m-2">
                    <!--                    <div class="bg-white flex shadow p-2 mb-2">-->
                    <!--                        <input type="text" v-model="filterText" class="w-full rounded p-2 focus:outline-none"-->
                    <!--                               placeholder="Enter text here">-->
                    <!--                        <button @click="$refs.cpt.filter('filterByText')" type="button"-->
                    <!--                                :class="[{'bg-blue-600' : filterOption==='filterByText'}, 'w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium bg-red-400 text-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm']"-->
                    <!--                        >Filter-->
                    <!--                        </button>-->
                    <!--                    </div>-->
                    <div class="flex text-xs" role="group">
                        <button @click="$refs.cpt.filter('isNew')"
                                :class="[{ 'bg-blue-500 text-white' : filterOption === 'isNew' }, { 'bg-white text-blue-500' : filterOption !== 'isNew' }, 'hover:bg-blue-500 hover:text-white border border-r-0 border-blue-500 px-4 py-2 mx-0 outline-none focus:shadow-outline rounded-l-lg']">
                            New
                        </button>
                        <button @click="$refs.cpt.filter('isUpdated')"
                                :class="[{ 'bg-blue-500 text-white' : filterOption === 'isUpdated' }, { 'bg-white text-blue-500' : filterOption !== 'isUpdated' }, 'hover:bg-blue-500 hover:text-white border border-r-0 border-blue-500 px-4 py-2 mx-0 outline-none focus:shadow-outline']">
                            Updated
                        </button>
                        <button @click="$refs.cpt.unfilter()"
                                class="bg-white text-blue-500 hover:bg-blue-500 hover:text-white border border-blue-500 px-4 py-2 mx-0 outline-none focus:shadow-outline rounded-r-lg">
                            Unfilter
                        </button>
                    </div>
                </div>
                <!--                <div id="sort" class="mt-2">-->
                <!--                    <button @click="$refs.cpt.sort('name')" type="button"-->
                <!--                            :class="[ { 'bg-blue-600 text-white' : sortOption === 'name' }, 'w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium bg-gray-400 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm' ]">-->
                <!--                        Sort by name-->
                <!--                    </button>-->
                <!--                    <button @click="$refs.cpt.sort('id')" type="button"-->
                <!--                            :class="[ { 'bg-blue-600 text-white' : sortOption === 'id' }, 'w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium bg-gray-400 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm' ]">-->
                <!--                        Sort by id-->
                <!--                    </button>-->
                <!--                    <button @click="$refs.cpt.shuffle()"-->
                <!--                            :class="'w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium bg-gray-400 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm'">-->
                <!--                        Shuffle-->
                <!--                    </button>-->
                <!--                 </div>-->
                <isotope ref="cpt" id="root_isotope" class="isoDefault" :options='getOptions()' :list="allRules"
                         @filter="filterOption=arguments[0]" @sort="sortOption=arguments[0]">
                    <div v-for="(rule, ruleKey) in allRules" :key="ruleKey">
                        <view-rule :rule="rule" :client-account="clientAccount"/>
                    </div>
                </isotope>
                <!-- <simple-pagination :data="rules"></simple-pagination> -->
            </div>
        </template>
    </client-layout>
</template>

<script>
import ClientLayout from '@/Layouts/ClientAccount'
import ViewRule from '@/Components/PM/Rules/ListView'
import isotope from 'vueisotope'
import moment from 'moment'
import TaxonomyFilter from '@/Components/PM/Rules/TaxonomyFilter'
import FilterCondition from "@/Components/PM/Rules/FilterCondition";
import JetButton from "@/Jetstream/DangerButton";

export default {
    props: [
        'clientAccount',
        'team',
        'rules',
    ],

    data() {
        return {
            allRules: [..._.orderBy(this.rules, 'created_at', 'desc')],
            sortOption: null,
            filterOption: null,
            filterText: "",
            filterObject: {},
            taxonomies: {},
            termsByTaxonomies: {},
            filterCondition: false
        }
    },

    created() {

        this.allRules.forEach(rule => {
            rule.terms.forEach(term => {
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
                if(taxonomies.length === 0)
                    return false;
                for (const taxonomy of taxonomies) {
                    if (taxonomy[1] !== '') {
                        let matchingTaxonomy = itemElem.terms.map(term => term.taxonomy.name).includes(taxonomy[0])
                        if(!matchingTaxonomy) return false
                        let matchingTerms = itemElem.terms.map(term => term.name).includes(taxonomy[1])
                        if(!matchingTerms) return false
                    }
                }
                return true;
            }
            return itemElem.terms.some(term => this.taxonomies[term.taxonomy.name] === term.name);
        };

        this.filterObject['isNew'] = (itemElem) => {
            return moment().subtract(3, 'months').isSameOrBefore(moment(itemElem.created_at));
        };

        this.filterObject['isUpdated'] = (itemElem) => {
            return moment().subtract(3, 'months').isSameOrBefore(moment(itemElem.updated_at));
        };
    },

    methods: {
        getOptions() {
            return {
                layoutMode: 'masonry',
                masonry: {
                    gutter: 10
                },
                getSortData: {
                    id: "id",
                },
                getFilterData: this.filterObject
            }
        },

        filterByTaxonomyTerm(taxonomy, term) {
            this.taxonomies[taxonomy] = term;
            this.$refs.cpt.filter('filterByTaxonomyTerm')
        },

        onChangeFilterCondition(condition) {
            this.filterCondition = condition;
            this.$refs.cpt.filter('filterByTaxonomyTerm')
        },

        clearAllFilters(){
            for (const taxonomy in this.taxonomies) {
                this.taxonomies[taxonomy] = '';
            }
            this.$refs.cpt.unfilter();
        }
    },

    components: {
        FilterCondition,
        ClientLayout,
        ViewRule,
        isotope,
        TaxonomyFilter,
        JetButton
    },
}
</script>
