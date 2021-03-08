<template>
    <client-layout :client-account="clientAccount">
        <template #body>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-gray-50 pt-5">
                <!-- <simple-pagination :data="rules"></simple-pagination> -->
                <div id="filter">
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
                        <button
                            v-for="taxonomy in taxonomies"
                            @click="$refs.cpt.filter(taxonomy)"
                            :class="[{ 'bg-blue-500 text-white' : filterOption === taxonomy }, { 'bg-white text-blue-500' : filterOption !== taxonomy  }, 'hover:bg-blue-500 hover:text-white border border-r-0 border-blue-500 px-4 py-2 mx-0 outline-none focus:shadow-outline']">
                            {{ taxonomy }}
                        </button>
                        <button @click="$refs.cpt.unfilter()"
                                class="bg-white text-blue-500 hover:bg-blue-500 hover:text-white border border-r-0 border-blue-500 px-4 py-2 mx-0 outline-none focus:shadow-outline rounded-r-lg">
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
                <!--                </div>-->
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

export default {
    props: [
        'clientAccount',
        'team',
        'rules',
    ],

    data() {
        return {
            allRules: [ ..._.orderBy(this.rules, 'created_at', 'desc') ],
            sortOption: null,
            filterOption: null,
            filterText: "",
            filterObject: {},
            taxonomies: [],
        }
    },

    created() {
        this.allRules.forEach(rule => {
            rule.terms.forEach(term => {
                if (!this.taxonomies.includes(term.taxonomy.name)) {
                    this.taxonomies.push(term.taxonomy.name);
                }
            });
            this.taxonomies.forEach(taxonomy => {
                this.filterObject[taxonomy] = itemElem => {
                    console.log(itemElem, taxonomy)
                    return itemElem.terms.some(term => term.taxonomy.name === taxonomy);
                }
            });
        });

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
        }
    },

    components: {
        ClientLayout,
        ViewRule,
        isotope,
    },
}
</script>
