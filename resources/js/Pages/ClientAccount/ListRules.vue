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
                    />
                    <taxonomy-selector taxonomy-name="Rule Status"
                                       :terms="states"
                                       @termSelected="filterByState"
                                       ref="stateSelector"
                    />
                    <taxonomy-selector taxonomy-name="Contributor"
                                       :terms="users"
                                       @termSelected="filterByContributor"
                                       ref="contributorSelector"
                    />
                    <taxonomy-selector taxonomy-name="Team"
                                       :terms="allTeams"
                                       @termSelected="filterByTeam"
                                       ref="teamSelector"
                    />

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
                                New <span title="Total number of rules considered new"
                                          class="px-1 rounded-xl bg-pink-300">{{ numNewRules }}</span>
                            </button>
                            <button @click="setFilterDate('isUpdated')"
                                    :title="$page.settings.rule_filter_updated_duration + ' days'"
                                    :class="[{ 'bg-blue-500 text-white' : filterOption === 'isUpdated' }, { 'bg-white text-blue-500' : filterOption !== 'isUpdated' }, 'hover:bg-blue-500 hover:text-white border border-r-0 border-blue-500 px-4 py-2 mx-0 outline-none focus:shadow-outline']">
                                Updated <span title="Total number of rules considered updated"
                                              class="px-1 rounded-xl bg-pink-300">{{ numUpdatedRules }}</span>
                            </button>
                            <button @click="setFilterDate('isFlagged')"
                                    :class="[{ 'bg-blue-500 text-white' : filterOption === 'isFlagged' }, { 'bg-white text-blue-500' : filterOption !== 'isFlagged' }, 'hover:bg-blue-500 hover:text-white border border-r-0 border-blue-500 px-4 py-2 mx-0 outline-none focus:shadow-outline']">
                                Flagged <span title="Total number of flagged rules" class="px-1 rounded-xl bg-pink-300">{{ numFlaggedRules }}</span>
                            </button>
                            <button @click="setFilterDate('isTagError')"
                                    :class="[{ 'bg-blue-500 text-white' : filterOption === 'isTagError' }, { 'bg-white text-blue-500' : filterOption !== 'isTagError' }, 'hover:bg-blue-500 hover:text-white border border-r-0 border-blue-500 px-4 py-2 mx-0 outline-none focus:shadow-outline']">
                                Tagging error <span title="Total number of rules with tagging errors"
                                                    class="px-1 rounded-xl bg-pink-300">{{ numTagError }}</span>
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

                <div class="flex flex-row w-full items-end">
                    <pagination v-model="page"
                                :options="paginationOptions"
                                :per-page="perPage"
                                :records="numFilteredRules"/>

                    <div v-if="selectedRules.length > 0" class="ml-auto">
                        Possible actions:
                        <template
                            v-if="$page.user_permissions.publishRules && _.every(selectedRules, ['state', 'Reviewing'])">
                            <button @click="confirmingPublish = true"
                                    class="inline-flex items-center px-1 py-1 bg-gray-800 border border-transparent
                            rounded-md font-semibold text-xs text-white uppercase tracking-widest
                            hover:bg-gray-700 active:bg-gray-900
                            focus:outline-none focus:border-gray-900 focus:shadow-outline-gray
                            transition ease-in-out duration-150">
                                Publish
                            </button>
                        </template>
                        <template v-else>
                            None
                        </template>
                    </div>
                </div>

                <div v-for="(rule, ruleKey) in _.drop(filteredRules, ((page-1)*perPage)).slice(0, perPage)"
                     :key="ruleKey">
                    <div class="flex flex-row items-center">
                        <div class="flex-shrink mr-1">
                            <input type="checkbox" :value="rule" v-model="selectedRules"
                                   @change="selectRule($event, rule)">
                        </div>
                        <div class="flex-grow">
                            <view-rule :rule="rule" :client-account="clientAccount"
                                       :state-models="stateModels"
                                       :selected="rule.selected"
                                       ref="viewRule"
                                       :show-contributors="showContributors" @updated="getRules"/>
                        </div>
                    </div>
                </div>

                <div class="px-2 pb-16 pt-4">
                    <pagination v-model="page"
                                :options="paginationOptions"
                                :per-page="perPage"
                                :records="numFilteredRules"/>
                </div>

            </div>

            <!-- Publish confirmation modal -->
            <jet-confirmation-modal :show="confirmingPublish" @close="cancelPublish">
                <template #title>
                    Publish Rules
                </template>

                <template #content>
                    Are you sure you want to publish these rules?
                </template>

                <template #footer>
                    <jet-secondary-button @click.native="cancelPublish">
                        Nevermind
                    </jet-secondary-button>

                    <jet-danger-button class="ml-2" @click.native="publishRules"
                                       :class="{ 'opacity-25': publishForm.processing }"
                                       :disabled="publishForm.processing">
                        Publish
                    </jet-danger-button>
                </template>
            </jet-confirmation-modal>
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
import JetButton from '@/Jetstream/Button'
import JetConfirmationModal from "@/Jetstream/ConfirmationModal";
import JetDangerButton from '@/Jetstream/DangerButton'
import JetFormSection from '@/Jetstream/FormSection'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetLabel from '@/Jetstream/Label'
import JetActionMessage from '@/Jetstream/ActionMessage'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import TailwindPagination from "../../Components/TailwindPagination";

export default {
    title() {
        return `Rules for ${this.clientAccount.name} - Dagobah`;
    },

    props: [
        'clientAccount',
        'team',
        'allTeams',
        'rules',
        'search',
        'states',
        'stateModels',
        'rootTaxonomies',
        'users',
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
                template: TailwindPagination,
            },
            page: 1,
            perPage: 25,


            allRules: [..._.orderBy(this.rules, 'created_at', 'desc')],
            filteredRules: [],
            sortOption: null,
            filterOption: 'all',
            filterText: "",
            filterState: "",
            filterContributor: "",
            filterTeam: "",
            filterObject: {},
            taxonomies: {},
            termsByTaxonomies: {},
            filterCondition: true, // true = AND, false = OR

            showContributors: false,

            selectedRules: [],
            confirmingPublish: false,

            publishForm: this.$inertia.form({
                rule_ids: [],
            }, {
                bag: 'publishData',
                resetOnSuccess: false,
            }),
        }
    },

    mounted() {
        this._keyListener = function (e) {
            if (e.key === "l" && (e.ctrlKey || e.metaKey)) {
                e.preventDefault(); // present "Save Page" from getting triggered.
                console.log('shortcut ctrl+ detected');
                this.toggleContributorVisibility();
            }
        };

        document.addEventListener('keydown', this._keyListener.bind(this));
    },
    beforeDestroy() {
        document.removeEventListener('keydown', this._keyListener);
    },

    created() {

        this.allRules.forEach(rule => {
            rule.terms.forEach(term => {
                if (!term.taxonomy) {
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
                        if (!matchingTerms) return faxlse
                    }
                }
                return true;
            }
            return itemElem.terms.some(term => this.taxonomies[term.taxonomy.name] === term.name);
        };

        this.filterObject['filterContributor'] = (itemElem) => {
            return !this.filterContributor || itemElem.users.some(user => user.name === this.filterContributor);
        };

        this.filterObject['filterTeam'] = (itemElem) => {
            return !this.filterTeam || itemElem.teams.some(team => team.name === this.filterTeam);
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

        this.filterObject['isOmnipresent'] = (itemElem) => {
            return itemElem.flagged === true;
        };

        this.filterObject['isTagError'] = (itemElem) => {
            let noTerms = (itemElem.terms.length === 0
                || (itemElem.terms.length === 1 && itemElem.terms[0].name === 'No term')
            );

            if(noTerms) {
                return true;
            }

            let hasStructure = _.some(itemElem.terms, function(term) {
                return term.hasOwnProperty('taxonomy') && term.taxonomy.name === 'Artwork Structure Elements';
            });

            let atLeastOneTermPerRootTaxonomy = _.uniq(_.map(itemElem.terms, function (term) {
                return term.hasOwnProperty('taxonomy') && term.taxonomy.parent.name;
            })).length === this.rootTaxonomies.length;

            return !hasStructure || !atLeastOneTermPerRootTaxonomy;

        };

        this.filterObject['all'] = (itemElem) => {
            return true;
        };

        this.getRules();
    },

    methods: {
        selectRule(e, rule) {
            console.log(e, rule);
            if (!rule.hasOwnProperty('selected')) {
                rule.selected = true;
            } else {
                rule.selected = !rule.selected;
            }
        },

        publishRules() {
            this.publishForm.rule_ids = _.map(this.selectedRules, 'id');

            this.publishForm.post(route('pm.client-account.rules.publish', {clientAccount: this.clientAccount.slug}))
                .then(() => {
                    this.confirmingPublish = false;

                    _.forEach(this.selectedRules, function(rule) {
                        rule.state = "Published";
                    });

                    this.$refs.viewRule.$forceUpdate();
                })
        },

        cancelPublish() {
            this.confirmingPublish = false;
        },

        toggleContributorVisibility() {
            this.showContributors = !this.showContributors;
        },

        getRules() {
            this.filteredRules =
                _.filter(this.allRules, (rule) => {
                    if (!this.filterText || this.filterText === '') {
                        return true;
                    }
                    return rule.name.toLowerCase().indexOf(this.filterText.toLowerCase()) !== -1
                        || rule.content.toLowerCase().indexOf(this.filterText.toLowerCase()) !== -1
                        || rule.dagId.toLowerCase().indexOf(this.filterText.toLowerCase()) !== -1;
                })
                    .filter(this.filterObject['filterByTaxonomyTerm'])
                    .filter(this.filterObject[this.filterOption])
                    .filter(this.filterObject['filterState'])
                    .filter(this.filterObject['filterContributor'])
                    .filter(this.filterObject['filterTeam']);

            this.page = 1;
        },

        debounceGetRules: _.debounce(function (e) {
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

        filterByContributor(dummy, contributor) {
            this.filterContributor = contributor ? contributor : '';
            this.getRules();
        },

        filterByTeam(dummy, team) {
            this.filterTeam = team ? team : '';
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
            this.filterTeam = '';
            this.filterContributor = '';

            this.$refs.taxonomySelectors.forEach(selector => selector.clearSelected());
            this.$refs.stateSelector.clearSelected();
            this.$refs.contributorSelector.clearSelected();
            this.$refs.teamSelector.clearSelected();

            this.getRules();
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
        numTagError: function () {
            return (_.filter(this.allRules, this.filterObject.isTagError)).length;
        },
    },

    components: {
        FilterCondition,
        ClientLayout,
        ViewRule,
        TaxonomyFilter,
        TaxonomySelector,
        JetActionMessage,
        JetButton,
        JetConfirmationModal,
        JetDangerButton,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        JetSecondaryButton,
    },
}
</script>
