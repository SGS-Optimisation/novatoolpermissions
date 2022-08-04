<template>
    <div>
        <Head><title>
            Rules for {{clientAccount.name}} - Dagobah
        </title></Head>

        <client-layout :client-account="clientAccount">

            <template #additionalActions v-if="selectedRules.length > 0">
                <div class="flex ml-auto items-center">
                    <span class="mr-1">{{ selectedRules.length }} rules selected.
                    Possible actions:</span>
                    <template
                        v-if="$page.props.user_permissions.publishRules && _every(selectedRules, ['state', 'Reviewing'])">
                        <Button label="Publish" class="p-button-success p-button-sm" @click="confirmingPublish = true"/>
                    </template>
                    <template
                        v-else-if="$page.props.user_permissions.publishRules && _every(selectedRules, ['state', 'Published'])">
                        <Button label="Unpublish" class="p-button-success p-button-sm" @click="confirmingUnpublish = true"/>
                    </template>
                    <template v-else>
                        None
                    </template>
                    <Button class="p-button-text p-button-sm" @click="deselectAll">
                        Clear selection
                    </Button>
                </div>
            </template>

            <template #body>
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-gray-50 pt-5">
                    <div class="grid grid-cols-5 gap-1">

                        <taxonomy-selector v-for="(terms, taxonomyName) in termsByTaxonomies"
                                           :taxonomy-name="taxonomyName"
                                           :key="taxonomyName"
                                           :terms="terms"
                                           @termSelected="filterByTaxonomyTerm"
                                           :ref="setTaxonomySelectorRef"

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
                                        :title="$page.props.settings.rule_filter_new_duration + ' days'"
                                        :class="[{ 'bg-blue-500 text-white' : filterOption === 'isNew' }, { 'bg-white text-blue-500' : filterOption !== 'isNew' }, 'hover:bg-blue-500 hover:text-white border border-r-0 border-blue-500 px-4 py-2 mx-0 outline-none focus:shadow-outline rounded-l-lg']">
                                    New <span title="Total number of rules considered new"
                                              class="px-1 rounded-xl bg-pink-300">{{ numNewRules }}</span>
                                </button>
                                <button @click="setFilterDate('isUpdated')"
                                        :title="$page.props.settings.rule_filter_updated_duration + ' days'"
                                        :class="[{ 'bg-blue-500 text-white' : filterOption === 'isUpdated' }, { 'bg-white text-blue-500' : filterOption !== 'isUpdated' }, 'hover:bg-blue-500 hover:text-white border border-r-0 border-blue-500 px-4 py-2 mx-0 outline-none focus:shadow-outline']">
                                    Updated <span title="Total number of rules considered updated"
                                                  class="px-1 rounded-xl bg-pink-300">{{ numUpdatedRules }}</span>
                                </button>
                                <button @click="setFilterDate('isFlagged')"
                                        :class="[{ 'bg-blue-500 text-white' : filterOption === 'isFlagged' }, { 'bg-white text-blue-500' : filterOption !== 'isFlagged' }, 'hover:bg-blue-500 hover:text-white border border-r-0 border-blue-500 px-4 py-2 mx-0 outline-none focus:shadow-outline']">
                                    Flagged <span title="Total number of flagged rules"
                                                  class="px-1 rounded-xl bg-pink-300">{{
                                        numFlaggedRules
                                    }}</span>
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

                    <div class="flex flex-row w-full justify-between">
                        <div class="flex flex-col">
                            {{ numFilteredRules }} Rules.
                            <div>
                                Select:
                                <a class="text-blue-500 cursor-pointer pr-1" @click="selectAll">All</a>
                                <a class="text-blue-500  cursor-pointer pr-1" @click="deselectAll">None</a>
                            </div>
                        </div>
                        <v-pagination v-model="page" :pages="numPages"/>


                        <!-- sorting -->
                        <div class="flex flex-row">
                            <div class="w-40 mr-4">
                                <taxonomy-selector taxonomy-name="Rule Status"
                                                   :terms="states"
                                                   @termSelected="filterByState"
                                                   ref="stateSelector"
                                />
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs">Sorting</span>
                                <div class="flex flex-row">
                                    <Dropdown v-model="selectedSortOption"
                                              panelClass="text-xs"
                                              @change="updateSort"
                                              :options="sortFields"
                                              optionLabel="label"
                                              placeholder="Sort by…"/>

                                    <div class="flex flex-col">
                                        <a class="cursor-pointer" @click="sortAsc"
                                           :class="{'text-blue-500': this.sortOption.direction==='asc'}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-6" fill="none"
                                                 viewBox="0 0 24 24"
                                                 stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 15l7-7 7 7"/>
                                            </svg>
                                        </a>
                                        <a class="cursor-pointer" @click="sortDesc"
                                           :class="{'text-blue-500': this.sortOption.direction==='desc'}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-6" fill="none"
                                                 viewBox="0 0 24 24"
                                                 stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="flex justify-center" v-if="!rulesLoaded">
                        <ProgressSpinner/>
                    </div>

                    <div v-else v-for="(rule, ruleKey) in displayedRules"
                         :key="ruleKey">
                        <div class="flex flex-row items-center">
                            <div class="flex-shrink mr-1">
                                <Checkbox name="selectedRules"
                                          :value="rule"
                                          v-model="selectedRules"
                                          @change="selectRule($event, rule)"
                                />
                                <!--                            <input type="checkbox" :value="rule" v-model="selectedRules"
                                                                   @change="selectRule($event, rule)">-->
                            </div>
                            <div class="flex-grow">
                                <view-rule :rule="rule" :client-account="clientAccount"
                                           :state-models="stateModels"
                                           :selected="rule.selected"
                                           ref="viewRule"
                                           :show-contributors="showContributors" @updated="getFilteredRules"/>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center pb-16 pt-4">
                        <v-pagination v-model="page" :pages="numPages"/>
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

                <!-- Unpublish confirmation modal -->
                <jet-confirmation-modal :show="confirmingUnpublish" @close="cancelUnpublish">
                    <template #title>
                        Unpublish Rules
                    </template>

                    <template #content>
                        Are you sure you want to unpublish these rules?
                        <br>Target status:
                        <Dropdown v-model="unpublishForm.status"
                                  panelClass="text-xs"
                                  :options="['Draft', 'Reviewing']"/>
                    </template>

                    <template #footer>
                        <jet-secondary-button @click.native="cancelUnpublish">
                            Nevermind
                        </jet-secondary-button>

                        <jet-danger-button class="ml-2" @click.native="unpublishRules"
                                           :class="{ 'opacity-25': unpublishForm.processing }"
                                           :disabled="unpublishForm.processing">
                            Unpublish
                        </jet-danger-button>
                    </template>
                </jet-confirmation-modal>
            </template>
        </client-layout>
    </div>
</template>

<script>
import {defineComponent} from "vue";
import {Head} from "@inertiajs/inertia-vue3";
import ClientLayout from '@/Layouts/ClientAccount.vue'
import ViewRule from '@/Components/PM/Rules/ListView.vue'
import moment from 'moment'
import TaxonomyFilter from '@/Components/PM/Rules/TaxonomyFilter.vue'
import TaxonomySelector from "@/Components/PM/Rules/TaxonomySelector.vue";
import FilterCondition from "@/Components/PM/Rules/FilterCondition.vue";
import JetButton from '@/Jetstream/Button.vue'
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetFormSection from '@/Jetstream/FormSection.vue'
import JetInput from '@/Jetstream/Input.vue'
import JetInputError from '@/Jetstream/InputError.vue'
import JetLabel from '@/Jetstream/Label.vue'
import JetActionMessage from '@/Jetstream/ActionMessage.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import VPagination from "@hennge/vue3-pagination";
import Button from 'primevue/button/sfc';
import Checkbox from 'primevue/checkbox/sfc';
import Dropdown from 'primevue/dropdown/sfc';
import ProgressSpinner from 'primevue/progressspinner/sfc';
import useSWRV from 'swrv';
import {every as _every, drop as _drop} from 'lodash';
import "@hennge/vue3-pagination/dist/vue3-pagination.css";

export default defineComponent({
    props: [
        'clientAccount',
        'team',
        'allTeams',
        //'rules',
        'search',
        'states',
        'stateModels',
        'rootTaxonomies',
        'users',
    ],
    components: {
        Head,
        FilterCondition,
        Button,
        Checkbox,
        Dropdown,
        ProgressSpinner,
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
        VPagination,
    },

    data() {
        return {
            page: 1,
            perPage: 25,

            rules: [],
            //allRules: [..._.orderBy(this.rules, 'created_at', 'desc')],
            filteredRules: [],
            filterOption: 'all',
            filterText: "",
            filterState: "",
            filterContributor: "",
            filterTeam: "",
            filterObject: {},
            taxonomies: {},
            termsByTaxonomies: {},
            filterCondition: true, // true = AND, false = OR
            rulesLoaded: false,

            showContributors: false,

            selectedRules: [],
            confirmingPublish: false,
            confirmingUnpublish: false,

            publishForm: this.$inertia.form({
                rule_ids: [],
            }, {
                bag: 'publishData',
                resetOnSuccess: false,
            }),

            unpublishForm: this.$inertia.form({
                status: 'Draft',
                rule_ids: [],
            }, {
                bag: 'unpublishData',
                resetOnSuccess: false,
            }),

            taxonomySelectorRefs: [],

            sortOption: null,
            selectedSortOption: null,
            sortFields: [
                {label: 'Created', field: 'created_at'},
                {label: 'Updated', field: 'updated_at'},
                {label: 'Name', field: 'name'},
                //{label: 'ID', field: 'dagId'}
            ],

        }
    },

    mounted() {
        this.loadRules();
        this._keyListener = function (e) {
            if (e.key === "l" && (e.ctrlKey || e.metaKey)) {
                e.preventDefault(); // present "Save Page" from getting triggered.
                console.log('shortcut ctrl+ detected');
                this.toggleContributorVisibility();
            }
        };

        document.addEventListener('keydown', this._keyListener.bind(this));
    },
    beforeUpdate() {
        this.taxonomySelectorRefs = []
    },
    beforeDestroy() {
        document.removeEventListener('keydown', this._keyListener);
    },

    created() {
        this.getSortOption();
    },

    updated() {
        this.getSortOption();
    },

    methods: {
        _every,
        _drop,

        loadRules(){
            const { data, error } = useSWRV(route('api.pm.client-account.rules',[this.clientAccount.slug]), fetcher);

            this.rules = data;

            /*this.rules = axios.get(route('api.pm.client-account.rules', [this.clientAccount.slug]))
                .then((response) => {
                    this.rules = response.data;
                    this.initializeFilters();
                    this.getFilteredRules();
                    this.rulesLoaded = true;
                })*/
        },

        getSortOption() {
            if (localStorage.getItem('pmSortOption')) {
                try {
                    this.sortOption = JSON.parse(localStorage.getItem('pmSortOption'));
                    this.selectedSortOption = _.find(this.sortFields, (entry) => entry.field === this.sortOption.field);
                    return;
                } catch (e) {
                    localStorage.removeItem('pmSortOption');
                }
            }

            this.sortOption = {field: 'created_at', direction: 'desc'};
            this.selectedSortOption = _.find(this.sortFields, (entry) => entry.field === this.sortOption.field);
        },

        saveSortOption() {
            localStorage.setItem('pmSortOption', JSON.stringify(this.sortOption));
        },

        sortAsc() {
            this.sortOption.direction = 'asc';
            this.saveSortOption();
            this.debounceGetRules();
        },
        sortDesc() {
            this.sortOption.direction = 'desc';
            this.saveSortOption();
            this.debounceGetRules();
        },

        initializeFilters() {
            this.rules.forEach(rule => {
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
                            if (!matchingTerms) return false
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
                return moment().subtract(parseInt(this.$page.props.settings.rule_filter_new_duration), 'days').isSameOrBefore(moment(itemElem.created_at));
            };

            this.filterObject['isUpdated'] = (itemElem) => {
                return moment().subtract(this.$page.props.settings.rule_filter_updated_duration, 'days').isSameOrBefore(moment(itemElem.updated_at));
            };

            this.filterObject['isFlagged'] = (itemElem) => {
                return itemElem.flagged === true;
            };

            this.filterObject['isOmnipresent'] = (itemElem) => {
                return itemElem.flagged === true;
            };

            this.filterObject['textSearch'] = (rule) => {
                if (!this.filterText || this.filterText === '') {
                    return true;
                }
                return rule.name.toLowerCase().indexOf(this.filterText.toLowerCase()) !== -1
                    || rule.content.toLowerCase().indexOf(this.filterText.toLowerCase()) !== -1
                    || rule.dagId.toLowerCase().indexOf(this.filterText.toLowerCase()) !== -1;
            };

            this.filterObject['isTagError'] = (itemElem) => {
                let noTerms = (itemElem.terms.length === 0
                    || (itemElem.terms.length === 1 && itemElem.terms[0].name === 'No term')
                );

                if (noTerms) {
                    return true;
                }

                let hasStructure = _.some(itemElem.terms, function (term) {
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
        },

        selectAll() {
            this.selectedRules = [];
            this.displayedRules.forEach((rule) => {
                rule.selected = true;
                this.selectedRules.push(rule);
            });
        },

        deselectAll() {
            this.selectedRules = [];
            this.rules.forEach((rule) => rule.selected = false);
        },

        setTaxonomySelectorRef(el) {
            if (el) {
                this.taxonomySelectorRefs.push(el)
            }
        },

        selectRule(e, rule) {
            if (!rule.hasOwnProperty('selected')) {
                rule.selected = true;
            } else {
                rule.selected = !rule.selected;
            }
        },

        publishRules() {
            this.publishForm.rule_ids = _.map(this.selectedRules, 'id');

            this.publishForm.post(
                route('pm.client-account.rules.publish', {clientAccount: this.clientAccount.slug}),
                {
                    onSuccess: () => {
                        this.confirmingPublish = false;

                        _.forEach(this.selectedRules, (rule) => {
                            rule.state = "Published";
                            _.find(this.rules, {dagId: rule.dagId}).state = "Published";
                        });

                        this.$refs.viewRule.$forceUpdate();
                    }
                })
        },

        unpublishRules() {
            this.unpublishForm.rule_ids = _.map(this.selectedRules, 'id');

            this.unpublishForm.post(
                route('pm.client-account.rules.unpublish', {clientAccount: this.clientAccount.slug}),
                {
                    onSuccess: () => {
                        this.confirmingUnpublish = false;

                        _.forEach(this.selectedRules, (rule) => {
                            rule.state = this.unpublishForm.status;
                            _.find(this.rules, {dagId: rule.dagId}).state = this.unpublishForm.status;
                        });

                        this.$refs.viewRule.$forceUpdate();
                    }
                })
        },

        cancelPublish() {
            this.confirmingPublish = false;
        },

        cancelUnpublish() {
            this.confirmingUnpublish = false;
        },

        toggleContributorVisibility() {
            this.showContributors = !this.showContributors;
        },

        updateSort(event) {
            this.sortOption.field = this.selectedSortOption.field;
            this.saveSortOption();
            this.debounceGetRules();
        },

        getFilteredRules() {
            this.filteredRules =
                _.orderBy(this.rules, [rule => rule[this.sortOption.field].toLowerCase()], [this.sortOption.direction])
                    .filter(this.filterObject['textSearch'])
                    .filter(this.filterObject['filterByTaxonomyTerm'])
                    .filter(this.filterObject[this.filterOption])
                    .filter(this.filterObject['filterState'])
                    .filter(this.filterObject['filterContributor'])
                    .filter(this.filterObject['filterTeam']);

            this.page = 1;
        },

        debounceGetRules: _.debounce(function (e) {
            this.getFilteredRules()
        }, 300),

        filterByTaxonomyTerm(taxonomy, term) {
            this.taxonomies[taxonomy] = (term ? term : '');
            this.getFilteredRules();
        },

        filterByState(dummy, state) {
            this.filterState = state ? state : '';
            this.getFilteredRules();
        },

        filterByContributor(dummy, contributor) {
            this.filterContributor = contributor ? contributor : '';
            this.getFilteredRules();
        },

        filterByTeam(dummy, team) {
            this.filterTeam = team ? team : '';
            this.getFilteredRules();
        },

        setFilterDate(value) {
            this.filterOption = value;
            this.getFilteredRules();
        },

        onChangeFilterCondition(condition) {
            this.filterCondition = condition;
            this.getFilteredRules();
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

            this.taxonomySelectorRefs.forEach((selector) => {
                selector.clearSelected();
            })

            this.$refs.stateSelector.clearSelected();
            this.$refs.contributorSelector.clearSelected();
            this.$refs.teamSelector.clearSelected();

            this.getFilteredRules();
        }
    },

    computed: {
        displayedRules() {
            return _.drop(this.filteredRules, ((this.page - 1) * this.perPage)).slice(0, this.perPage);
        },

        numPages() {
            return Math.ceil(this.numFilteredRules / this.perPage);
        },

        numFilteredRules: function () {
            return this.filteredRules.length;
        },

        numAllRules: function () {
            return this.rules.length;
        },

        numNewRules: function () {
            return (_.filter(this.rules, this.filterObject.isNew)).length;
        },
        numUpdatedRules: function () {
            return (_.filter(this.rules, this.filterObject.isUpdated)).length;
        },
        numFlaggedRules: function () {
            return (_.filter(this.rules, this.filterObject.isFlagged)).length;
        },
        numTagError: function () {
            return (_.filter(this.rules, this.filterObject.isTagError)).length;
        },
    },

    watch: {
        rules: function(newRules, oldRules) {
            if(Array.isArray(newRules)) {
                this.initializeFilters();
                this.getFilteredRules();
                this.rulesLoaded = true;
            }
        }
    },
})
</script>

<style scoped>
::v-deep(.p-dropdown-label) {
    @apply text-sm;
}

:deep(.p-inputtext) {
    padding: 0.5rem 0.75rem;
}
</style>
