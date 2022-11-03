<template>
  <app-layout>
    <Head><title>
      PM Rules
      {{currentJob ? jobNumber : clientAccount.name }}
      - Dagobah
    </title></Head>
    <template #header>
      <div class="flex justify-between align-middle">

        <div class="flex-grow">
          <div class="flex flex-row align-baseline items-baseline">
            <h2 class="pt-2 font-bold text-xl text-gray-800 leading-tight">
              PM Rules for
              <span v-if="currentJob">job {{ jobNumber }}</span>
              <span v-else>{{ clientAccount.name }}</span>
            </h2>

            <template v-if="jobNumber">
              <Link :href="route('job.rules', jobNumber)"
                    class="ml-2 text-xs text-blue-400 hover:text-blue-600 underline">
                Switch to Prod Rules
              </Link>
            </template>

            <!--                    <manual-account-selection v-if="forcedAccount"
                                                          :initial-selection="forcedAccount"
                                                          :jobNumber="currentJob.job_number"/>-->
          </div>
        </div>

        <div class="flex-shrink">
          <job-search @searching="runningSearch"
                      classes="bg-white flex"
                      search-route="pm.job-rules"
                      placeholder="Search job">
          </job-search>
        </div>
      </div>
    </template>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 bg-white relative">
      <div class="flex flex-col sm:-mx-px md:-mx-px lg:-mx-px xl:-mx-px pb-2 mb-2 min-h-max ">

        <div v-if="searching || (currentJob && !currentJob.hasOwnProperty('metadata'))">
          <loader></loader>
        </div>
        <div v-else-if="currentJob && currentJob.metadata.processing_mysgs">
          <loader></loader>
        </div>
        <div v-else-if="currentJob && currentJob.metadata.error_mysgs">
          <div class="h-64 bg-white flex justify-center align-middle">
            <p class="mt-16 text-red-700">There was an error loading data for the job
              "{{ currentJob.job_number }}".
              <br><span v-if="currentJob.metadata.error_mysgs_reason">
                        {{ currentJob.metadata.error_mysgs_reason }}
                    </span>
              <span v-else>Please try again later.</span>
            </p>
          </div>
        </div>
        <div v-else-if="currentJob && currentJob.metadata.client_found === false && !forcedAccount">
          <div class="h-64 bg-white flex justify-center align-middle items-center">
            <Message severity="error" :closable="false">
                            <span v-if="currentJob.metadata.error_reason">
                                {{ currentJob.metadata.error_reason }}<br>
                            </span>
              <span v-else>
                                "{{ currentJob.metadata.client.name }}"
                                was not matched with any client account.


                            </span>
            </Message>

          </div>

          <div class="flex justify-center items-center">
            <manual-account-selection v-if="currentJob && !currentJob.metadata.error_reason"
                                      :jobNumber="currentJob.job_number"/>
          </div>
        </div>
        <div v-else>
          <job-identification v-if="currentJob" :job="currentJob"/>


          <div class="sticky z-50 flex flex-col w-full" style="top:107px;">
            <!-- Selectable Filters -->
            <div v-if="!currentJob" class="search-section grid grid-cols-5 gap-1 mx-2 mb-2">
              <taxonomy-selector v-for="(terms, taxonomyName) in accountStructureTaxonomies"
                                 :taxonomy-name="taxonomyName"
                                 :key="taxonomyName"
                                 :terms="terms"
                                 @termSelected="filterByTaxonomyTerm"
                                 :ref="setTaxonomySelectorRef"
              />
            </div>
            <!-- Stage filter -->
            <div class="flex flex-grow text-xs mx-2 mb-2" role="group" v-if="showStage()">
              <button
                  v-for="(stage, index) in processedStages"
                  @click="filterStageButtonClicked(stage)"
                  class="flex-grow hover:bg-blue-400 hover:text-white border border-r-0 border-blue-500 px-1 py-2 mx-0 outline-none focus:shadow-outline"
                  :class="[
                                    { 'rounded-l-lg' : index === 0 },
                                    { 'bg-blue-500 text-white' : stageStates[stage] },
                                    { 'bg-white text-blue-500' : !stageStates[stage] },
                                    { 'bg-green-100' : !stageStates[stage] && currentJob && currentJob.metadata.hasOwnProperty('stages') && currentJob.metadata.stages.includes(stage) },
                                ]">
                {{ stage }}
              </button>
              <button @click="resetStage"
                      class="rounded-r-lg flex-grow hover:bg-blue-400 hover:text-white text-blue-500 border border-r-0 border-blue-500 px-1 py-2 mx-0 outline-none focus:shadow-outline">
                Clear
              </button>
            </div>
            <!-- Clickable Filters -->
            <!-- Artwork structure and date Filters -->
            <div class="flex flex-grow text-xs mx-2 shadow-lg" role="group">
              <button v-if="numNewRules"
                      @click="filterArtworkStructureButtonClicked('isNew')"
                      :title=" '< ' + $page.props.settings.rule_filter_new_duration + ' days'"
                      class="flex-grow hover:bg-blue-500 hover:text-white border border-r-0 border-blue-500 px-1 py-2 mx-0 outline-none focus:shadow-outline rounded-l-lg"
                      :class="[
                                    { 'bg-blue-500 text-white' : filterOption === 'isNew' },
                                    { 'bg-white text-blue-500' : filterOption !== 'isNew' }
                                ]">
                New
                <Tag :value="numNewRules" icon="pi pi-exclamation-circle"></Tag>
              </button>
              <button v-if="numUpdatedRules"
                      @click="filterArtworkStructureButtonClicked('isUpdated')"
                      :title=" '< ' + $page.props.settings.rule_filter_updated_duration + ' days'"
                      class="flex-grow hover:bg-blue-500 hover:text-white border border-r-0 border-blue-500 px-1 py-2 mx-0 outline-none focus:shadow-outline"
                      :class="[
                                    { 'bg-blue-500 text-white' : filterOption === 'isUpdated' },
                                    { 'bg-white text-blue-500' : filterOption !== 'isUpdated' }
                                ]">
                Updated
                <Tag :value="numUpdatedRules" severity="warning"
                     icon="pi pi-exclamation-triangle"></Tag>
              </button>
              <button
                  v-for="term in pmSectionTerms"
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

          <div class="flex flex-row justify-end text-xs mx-2" v-if="numRules">
            <template v-if="numDisplayedRules != numRules">
              Displaying {{ numDisplayedRules }} {{ numDisplayedRules > 1 ? 'rules' : 'rule' }}
              <template v-if="numDisplayedRules != numFilteredRules">
                of {{ numFilteredRules }} filtered rules
              </template>
              .
            </template>

            <template v-if="true">
              All rules: {{ numRules }}.
            </template>
          </div>
          <div class="box-border mx-auto before:box-inherit after:box-inherit mt-2"
               :class="{
                        'md:masonry': !termFocus
                    }">

            <div v-for="(ruleGroup, groupName) in displayedRules" :key="groupName"
                 class="break-inside">

              <view-rule-group :rules="ruleGroup"
                               :job="currentJob ? currentJob.job_number: ''"
                               :group="groupName"
                               :filter-option="filterOptionTracker"
                               :filter-stage-states="stageStates"
                               :stages="processedStages"
                               :filter-flag="filterFlag"
                               @on-click-view="openRuleModal"/>
            </div>

          </div>

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

        <a v-if="currentRule && $page.props.user_permissions.updateRules" target="_blank"
           class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150"
           :href="route('library.client-account.rules.edit', {clientAccount: clientAccount.slug, id: currentRule.id})">
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
import {Head, Link} from "@inertiajs/inertia-vue3";
import AppLayout from '@/Layouts/AppLayout.vue'
import Input from "@/Jetstream/Input.vue";
import Button from "@/Jetstream/Button.vue";
import JetActionMessage from '@/Jetstream/ActionMessage.vue'
import JetButton from '@/Jetstream/Button.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetDialogModal from '@/Jetstream/DialogModal.vue';
import JetInput from '@/Jetstream/Input.vue'
import JetLabel from '@/Jetstream/Label.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import Loader from "@/Components/Loader.vue";
import ViewRule from '@/Components/RulesLibrary/Rules/ViewRule.vue'
import ViewRuleGroup from "@/Components/Search/ViewRuleGroup.vue";
import JobSearch from "@/Components/Search/JobSearchForm.vue";
import TaxonomyFilter from '@/Components/RulesLibrary/Rules/TaxonomyFilter.vue'
import TaxonomySelector from "@/Components/RulesLibrary/Rules/TaxonomySelector.vue";
import FilterCondition from "@/Components/RulesLibrary/Rules/FilterCondition.vue";
import moment from "moment";
import JobIdentification from "@/Components/Search/JobIdentification.vue";
import Message from 'primevue/message/sfc';
import Tag from 'primevue/tag';
import ManualAccountSelection from "@/Components/Search/ManualAccountSelection.vue";

export default {
  props: [
    'jobNumber',
    'job',
    'rules',
    'clientAccount',
    'stages',
  ],
  components: {
    ManualAccountSelection,
    Head,
    Link,
    JobIdentification,
    ViewRuleGroup,
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
    Message,
    Tag,
    TaxonomyFilter,
    TaxonomySelector,
    FilterCondition,
  },

  data() {
    return {
      currentJob: this.job,
      currentRules: this.rules,
      searching: false,
      searchedRules: [..._.orderBy(this.rules, 'name', 'asc')],
      isOpen: false,
      currentRule: null,
      rulesUpdated: false,

      sortOption: null,
      filterOption: null,
      filterOptionTracker: null,

      stageStates: {},

      filterText: "",
      filterObject: {},

      /* Contains alls the terms used under job categorizations taxonomies, for indexing */
      pmSectionTerms: [],

      all_stages: this.$page.props.all_job_stages,

      /* Contains terms grouped by taxonomy under job categoizations, for filtering */
      taxonomies: {},
      accountStructureTaxonomies: {},
      accountStructureTaxonomyFilter: {},
      groupedTaxonomies: {},
      termsByTaxonomies: {},

      rulesByTaxonomies: {},

      filterFlag: null,
      isFlaggingRule: false,
      currentFlaggingRule: null,

      flagRuleForm: this.$inertia.form({
        reason: "",
      }, {
        bag: 'sendFlagRule'
      }),

      linkTrackingEnabled: false,

      showRuleId: false,
      taxonomySelectorRefs: [],
    }
  },

  watch: {

    jobNumber: function (newJobNumber, oldJobNumber) {
      console.log('detected job number change');
      this.unfilter();
      Echo.leaveChannel(`job-${oldJobNumber}`)
      Echo.channel(`job-${newJobNumber}`)
          .listen('Jobs\\JobLoaded', (e) => {
            console.log('job ready', e);
            this.queryRules();
          })
      this.initJobLoaded();
    }
  },

  created() {
    this.initSearchFunctions();

    let qp = new URLSearchParams(window.location.search);

    qp.forEach((value, key) => {
      if (!['filterText', 'advanced', 'filterRuleStatus', 'filterRuleStatus',
        'filterRuleType', 'filterContributor', 'filterTeam'].includes(key)) {
        var taxonomy = key.replace('filter', '');
        this.accountStructureTaxonomyFilter[taxonomy] = value;
      }
    });

    console.log('tracking enabled: ' + (this.$page.props.features.matomo_tracking_enabled ? 'yes' : 'no'));

    if (this.$page.props.features.matomo_tracking_enabled) {
      window._paq = window._paq || [];
      (() => {
        var u = this.$page.props.features.matomo_host;
        window._paq.push(['setTrackerUrl', u + '/matomo.php']);
        window._paq.push(['setSiteId', this.$page.props.features.matomo_site_id]);
        window._paq.push(['setUserId', this.$page.props.user.email]);
        window._paq.push(['enableHeartBeatTimer', 10]);
        var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
        g.type = 'text/javascript';
        g.async = true;
        g.src = u + '/matomo.js';
        s.parentNode.insertBefore(g, s);
      })();
    }
  },

  mounted() {

    if (this.job) {
      this.initJobLoaded();
      this.initRulesParsing();

    } else if (this.rules.length) {
      this.currentRules = this.rules;
      this.newRulesLoaded();
      this.initRulesParsing();
    }

    this._keyListener = function (e) {
      if (e.key === "l" && (e.ctrlKey || e.metaKey)) {
        e.preventDefault(); // present "Save Page" from getting triggered.
        console.log('shortcut ctrl+ detected');
        this.toggleRuleIdVisibility();
      }
    };

    document.addEventListener('keydown', this._keyListener.bind(this));

    if (this.jobNumber) {
      Echo.channel(`job-${this.jobNumber}`)
          .listen('Jobs\\JobLoaded', (e) => {
            console.log('job ready', e);
            this.queryRules();
          })
    }
  },

  beforeUpdate() {
    this.taxonomySelectorRefs = []
  },

  beforeDestroy() {
    document.removeEventListener('keydown', this._keyListener);
  },

  destroyed() {
    clearTimeout(this.timeOut);
  },

  methods: {

    initJobLoaded() {

      this.currentJob = this.job;
      if (this.currentJob && this.currentJob.metadata.processing_mysgs === false) {
        console.log('job rules already loaded');

      } else if (window.Echo.connector.pusher.connection.state === 'unavailable') {
        console.log('socket unavailable, switching to polling mode');
        this.waitMode();
      }
    },

    updateURL() {
      let qp = new URLSearchParams();

      for (var taxonomy in this.accountStructureTaxonomyFilter) {
        if (this.accountStructureTaxonomyFilter[taxonomy] !== '') {
          qp.set((taxonomy), this.accountStructureTaxonomyFilter[taxonomy]);
        }
      }

      history.replaceState(null, null, "?" + qp.toString());
    },

    setupStages() {
      this.stageStates = {}
      for (let stage of this.processedStages) {
        this.stageStates[stage] = false;
      }
    },

    toggleRuleIdVisibility() {
      this.showRuleId = !this.showRuleId;
    },

    track() {
      if (!this.$page.props.features.matomo_tracking_enabled)
        return;

      console.log('tracking');

      let activeJobTeam = [];
      let activeMatchingJobTeams = [];

      if (this.currentJob && this.currentJob.metadata.hasOwnProperty('jobTeam')) {
        let jobTeams = this.currentJob.metadata.jobTeam;
        let userJobTeams = this.$page.props.user.jobteams;

        for (let i in jobTeams) {
          if (jobTeams[i].inUse) {
            activeJobTeam.push(jobTeams[i].teamName);
            console.log('found active job team ' + jobTeams[i].teamName);

            if (userJobTeams.includes(jobTeams[i].teamName)) {
              activeMatchingJobTeams.push(jobTeams[i].teamName);
            }
          }
        }
      }

      var selectedJobTeams = activeMatchingJobTeams.length ? activeMatchingJobTeams : activeJobTeam;

      //_paq.push(['setCustomDimension', 2, activeJobTeam]);

      window._paq.push(['setCustomUrl', window.location.origin + '/' + this.jobNumber]);
      window._paq.push(['setDocumentTitle', this.jobNumber]);

      window._paq.push(['trackPageView', this.jobNumber, {
        'client': this.clientAccount.name,
        'dimension2': selectedJobTeams.join('|')
      }]);
      window._paq.push(['trackEvent',
        'Search Viewed Job',
        this.clientAccount.name,
        this.jobNumber,
        '',
        {
          'dimension2': selectedJobTeams.join('|'),
        }
      ]);

      if (!this.linkTrackingEnabled) {
        window._paq.push(['enableLinkTracking']);
        this.linkTrackingEnabled = true;
      }
    },

    newRulesLoaded() {
      this.track();

      this.searchedRules = [..._.orderBy(this.currentRules, 'name', 'asc')];
      this.searching = false;

      this.setupStages();
      if (this.currentJob
          && this.currentJob.metadata.hasOwnProperty('stages')
          && this.currentJob.metadata.stages.length) {
        for (let index in this.currentJob.metadata.stages) {
          this.stageStates[this.currentJob.metadata.stages[index]] = true;
        }
        this.$forceUpdate();
      }
    },

    initRulesParsing() {
      this.searchedRules.forEach(rule => {
        rule.job_categorizations_terms.forEach(term => {
          //if (term.taxonomy.name === 'PM Section Elements') {

          /*
          Collect filterable terms
          */
          if (!this.pmSectionTerms.includes(term.name)
              && term.taxonomy.name === 'PM Section Elements'
          ) {
            this.pmSectionTerms.push(term.name);
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
          Group rules by PM Section Elements terms
           */
          if (term.taxonomy.name === 'PM Section Elements') {
            if (this.rulesByTaxonomies[term.name] === undefined) {
              this.rulesByTaxonomies[term.name] = [];
            }

            this.rulesByTaxonomies[term.name].push(rule);
          }
        });

        rule.account_structure_terms.forEach(term => {
          var taxonomy = term.taxonomy.name;
          if (!this.accountStructureTaxonomies.hasOwnProperty(taxonomy)) {
            this.accountStructureTaxonomies[taxonomy] = [];
          }

          if (!this.accountStructureTaxonomies[taxonomy].includes(term.name)) {
            this.accountStructureTaxonomies[taxonomy].push(term.name);
          }
        });

        this.pmSectionTerms.forEach(taxonomy => {
          this.filterObject[taxonomy] = (rule) => {
            var pmElmTerms = rule.job_categorizations_terms.filter((term) => term.taxonomy.name === 'PM Section Elements');

            return pmElmTerms.length && pmElmTerms[0].name === taxonomy;
          }
        });
      });

      this.pmSectionTerms.sort();

    },

    initSearchFunctions() {
      this.filterObject['isNew'] = (rule) => {
        return moment()
            .subtract(parseInt(this.$page.props.settings.rule_filter_new_duration), 'days')
            .isSameOrBefore(moment(rule.created_at))
      };

      this.filterObject['isUpdated'] = (rule) => {
        return moment()
            .subtract(parseInt(this.$page.props.settings.rule_filter_updated_duration), 'days')
            .isSameOrBefore(moment(rule.updated_at))
      };

      this.filterObject['filterByTaxonomyTerm'] = (rule) => {
        let taxonomies = Object.entries(this.accountStructureTaxonomyFilter);
        if (taxonomies.length === 0)
          return true;
        for (const taxonomy of taxonomies) {
          if (taxonomy[1] !== '') {
            let matchingTaxonomy = rule.account_structure_terms.map(term => term.taxonomy.name).includes(taxonomy[0])
            if (!matchingTaxonomy) return true
            let matchingTerms = rule.account_structure_terms.map(term => term.name).includes(taxonomy[1])
            if (!matchingTerms) return false
          }
        }
        return true;
      };

      this.filterObject['all'] = (itemElem) => {
        return true;
      };
    },

    waitMode() {
      this.timeOut = setTimeout(() => {
        this.queryRules();
      }, 1500);
    },

    queryRules() {
      const headers = {
        "content-type": "application/json",
        "Accept": "application/json"
      };

      var rules_route = route('pm.job-rules', this.jobNumber);

      axios.get(rules_route, {headers})
          .then(({data}) => {
            console.log(data);
            if (data.job && data.job.hasOwnProperty('metadata')
                && !data.job.metadata.processing_mysgs && !data.job.metadata.error_mysgs
            ) {
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

    runningSearch() {
      this.currentJob = {metadata: {}};
      this.searching = true;
      this.pmSectionTerms = [];
      this.rulesByTaxonomies = {};
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
      console.log('filter taxonomy button clicked', filterName);

      if (this.filterOptionTracker !== '' && this.filterOptionTracker === filterName) {
        this.unfilter();
        console.log('unfiltering');
        return;
      }

      this.filterOption = filterName;
      this.filterOptionTracker = filterName;
      switch (filterName) {
        case 'isNew':
          this.filterFlag = "new";
          break;
        case 'isUpdated':
          this.filterFlag = "updated";
          break;
        default:
          this.filterFlag = null;
          break;
      }
    },

    filterStageButtonClicked(stage) {
      console.log('filter stage button clicked', stage);
      this.stageStates[stage] = !this.stageStates[stage];
      this.$forceUpdate();
    },

    filterByTaxonomyTerm(taxonomy, term) {
      this.accountStructureTaxonomyFilter[taxonomy] = (term ? term : '');
    },

    setTaxonomySelectorRef(el) {
      if (el) {
        this.taxonomySelectorRefs.push(el)
      }
    },

    unfilter() {
      this.filterOptionTracker = '';
      this.filterFlag = null;
      this.filterOption = null;
    },

    resetStage() {
      this.setupStages();
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
        preserveScroll: true,
        onSuccess: () => this.closeFlagModal()
      });
    },

    showStage() {
      return this.taxonomies.hasOwnProperty('Stage');
    },
  },

  computed: {
    termFocus() {
      return this.filterOption && this.filterOption !== 'isNew' && this.filterOption !== 'isUpdated';
    },

    /*displayedRules() {
        return _.filter(Object.entries(this.rulesByTaxonomies), this.filterObject[this.filterOption ? this.filterOption : 'all'])
    },*/

    displayedRules() {
      this.updateURL();
      var passedRules = _.filter(this.searchedRules, this.filterObject['filterByTaxonomyTerm'])
          .filter(this.filterObject[this.filterOption ? this.filterOption : 'all'])


      var groupedRules = _.groupBy(passedRules, (rule) => {
        var pmElmTerms = rule.job_categorizations_terms.filter((term) => term.taxonomy.name === 'PM Section Elements');

        if (pmElmTerms.length) {
          return pmElmTerms[0].name;
        } else {
          console.log('error, rule with no pm section elements taxonomy');
        }
      });

      return Object.keys(groupedRules).sort().reduce(
          (obj, key) => {
            obj[key] = groupedRules[key];
            return obj;
          },
          {}
      );

    },

    processedStages() {
      return this.stages && this.stages.length ? this.stages : this.all_stages;
    },

    numRules() {
      return this.searchedRules.length;
    },

    numFilteredRules() {
      return _.filter(this.searchedRules, this.filterObject['filterByTaxonomyTerm']).length;
    },

    numDisplayedRules() {
      return _.filter(this.searchedRules, this.filterObject['filterByTaxonomyTerm'])
          .filter(this.filterObject[this.filterOption ? this.filterOption : 'all']).length;
    },

    numNewRules() {
      if (!this.currentRules.length) {
        return;
      }

      return this.currentRules
          .filter(rule => {
            let time = moment(rule.created_at);
            let numDays = this.$page.props.settings.rule_filter_new_duration;

            return moment().subtract(parseInt(numDays), 'days').isSameOrBefore(time);
          })
          .length
    },

    numUpdatedRules() {
      if (!this.currentRules.length) {
        return;
      }

      return this.currentRules
          .filter(rule => {
            let created_time = moment(rule.created_at);
            let updated_time = moment(rule.updated_at);

            let numDaysNew = parseInt(this.$page.props.settings.rule_filter_new_duration);
            let numDaysUpdated = parseInt(this.$page.props.settings.rule_filter_updated_duration);

            let rule_matches_new = moment().subtract(numDaysNew, 'days').isSameOrBefore(created_time);

            return moment().subtract(numDaysUpdated, 'days').isSameOrBefore(updated_time)
                && !rule_matches_new;
          })
          .length
    },
  },
}
</script>

