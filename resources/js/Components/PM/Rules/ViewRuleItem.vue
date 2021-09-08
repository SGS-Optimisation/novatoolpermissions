<template>
    <div class="mr-4 mb-4 p-2" v-if="taxonomyRules.length > 0"
        :class="{'bg-indigo-50' : !termFocus}">

        <div class="w-full" v-if="!termFocus">
            <h1 class="bg-gray-500 text-md font-medium text-gray-100 px-2">
                {{ group }}
            </h1>
        </div>

        <div class="w-full flex flex-wrap">
            <div v-for="(rule, index) in taxonomyRules"
                 :class="{
                    'flex flex-row p-1 my-1': !termFocus && (index === taxonomyRules.length-1),
                    'flex flex-row border-b border-indigo-100 p-1 my-1': !termFocus && (index !== taxonomyRules.length-1),
                    'w-1/3 flex px-3 py-1 my-1': termFocus,
                 }"
            >
                <div class="w-full"
                :class="{'bg-indigo-50 p-2': termFocus}">
                    <div class="cursor-pointer" @click="$emit('on-click-view', rule)">
                        <p class="text-sm font-bold text-gray-900">{{ rule.name }}</p>
                        <p class="text-xs text-gray-700 break-words" v-html="excerpt(rule)"/>
                    </div>
                    <div class="text-xs text-gray-500 flex flex-row justify-between">
                        <div class="flex flex-row">
<!--                            <div class="text-xs bg-pink-200 rounded-xl px-2">
                                {{
                                    _.find(rule.terms, (item) => {
                                        return item.name === group
                                    }).name
                                }}
                            </div>-->
                            <span class="flex flex-row" v-if="rule.attachments && rule.attachments.length">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                              </svg>
                              {{rule.attachments.length}}
                            </span>
                            <div class="ml-2" v-if="rule.flagged">
                                <i title="This rule is currently flagged"
                                   class="text-red-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <title>This rule is currently flagged</title>
                                        <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd" />
                                    </svg>
                                </i>
                            </div>
                        </div>

                        <div>
                            <span v-if="rule.created_at === rule.updated_at" :title="moment(rule.created_at)">
                                Created {{ moment(rule.created_at).fromNow() }}
                            </span>
                            <span v-else :title="moment(rule.updated_at)">
                                Updated {{ moment(rule.updated_at).fromNow() }}
                            </span>
                        </div>
                    </div>

                </div>
                <!--                <a href="#" @click="$emit('on-click-view', ruleItem)"
                                   class="text-indigo-600 hover:text-indigo-900 float-right justify-center -align-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
  <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
  <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
</svg>
                                </a>-->
            </div>
        </div>
    </div>
</template>

<script>

import moment from "moment";
import clip from "text-clipper";

export default {
    name: "ViewRuleItem",
    props: ['group', 'rules', 'filterFlag', 'filterStagePa', 'filterStagePp', 'filterStagePf', 'filterOption'],
    data() {
        return {
        }
    },
    methods: {
        excerpt(rule) {
            return clip(rule.content.replace(/<[^>]+>/g, ''), 180, {html: true, maxLines: 3});
        },
    },
    computed: {
        termFocus() {
            return this.filterOption && this.filterOption !== 'isNew' && this.filterOption !== 'isUpdated';
        },

        taxonomyRules() {
            let filteredRules = this.rules;

            if(this.filterFlag) {
                filteredRules = filteredRules.filter(rule => {
                    let time = (this.filterFlag === 'updated') ? moment(rule.updated_at) : moment(rule.created_at);
                    let numDays = (this.filterFlag === 'updated') ?
                        this.$page.settings.rule_filter_updated_duration
                        : this.$page.settings.rule_filter_new_duration;

                    return moment().subtract(parseInt(numDays), 'days').isSameOrBefore(time)
                        && (this.filterFlag !== 'updated' || rule.created_at !== rule.updated_at)
                })
            }

            if(this.filterStage.length) {
                filteredRules = filteredRules.filter(rule => {
                    return _.every(rule.job_categorizations_terms, (term) => term.taxonomy.name !== 'Stage')
                    || _.some(rule.job_categorizations_terms, (term) => this.filterStage.includes(term.name));
                });
            }

            return _.orderBy([...filteredRules], 'updated_at', 'desc')
        },

        filterStage() {
            let stages = [];
            if(this.filterStagePa) {
                stages.push('PA');
            }
            if(this.filterStagePp) {
                stages.push('PP');
            }
            if(this.filterStagePf) {
                stages.push('PF');
            }

            return stages;
        },

    }
    // created(){
    //     if(this.filterFlag) {
    //         this.taxonomyRules = this.rule[1].filter(rule => {
    //             let time = this.filterFlag === 'updated' ? moment(rule.updated_at) : moment(rule.created_at)
    //             return moment().subtract(3, 'months').isSameOrBefore(time)
    //         })
    //     } else {
    //         this.taxonomyRules = this.rule[1]
    //     }
    // }
}

</script>
