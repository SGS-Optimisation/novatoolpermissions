<template>
    <div class="mr-4 mb-4" v-if="taxonomyRules.length > 0"
         :class="{'bg-indigo-50' : !termFocus}">

        <div class="w-full" v-if="!termFocus">
            <h1 class="bg-gray-500 text-md font-medium text-gray-100 px-2">
                {{ group }}
            </h1>
        </div>

        <div class="w-full flex flex-wrap">
            <template v-for="(rule, index) in taxonomyRules">
                <view-rule :taxonomy-rules="taxonomyRules"
                           :rule="rule"
                           :job="job"
                           :index="index"
                           :term-focus="termFocus"
                           @on-click-view="$emit('on-click-view', rule)"
                />
            </template>
        </div>
    </div>
</template>

<script>

import moment from "moment";
import ViewRule from "@/Components/OP/ViewRule";

export default {
    name: "ViewRuleGroup",
    components: {ViewRule},
    props: ['group', 'rules', 'filterFlag', 'filterStagePa', 'filterStagePp', 'filterStagePf', 'filterOption', 'job'],
    data() {
        return {}
    },

    computed: {
        termFocus() {
            return this.filterOption && this.filterOption !== 'isNew' && this.filterOption !== 'isUpdated';
        },

        taxonomyRules() {
            let filteredRules = this.rules;

            if (this.filterFlag) {
                filteredRules = filteredRules.filter(rule => {
                    let time = (this.filterFlag === 'updated') ? moment(rule.updated_at) : moment(rule.created_at);
                    let numDays = (this.filterFlag === 'updated') ?
                        this.$page.props.settings.rule_filter_updated_duration
                        : this.$page.props.settings.rule_filter_new_duration;

                    return moment().subtract(parseInt(numDays), 'days').isSameOrBefore(time)
                        && (this.filterFlag !== 'updated' || rule.created_at !== rule.updated_at)
                })
            }

            if (this.filterStage.length) {
                filteredRules = filteredRules.filter(rule => {
                    return _.every(rule.job_categorizations_terms, (term) => term.taxonomy.name !== 'Stage')
                        || _.some(rule.job_categorizations_terms, (term) => this.filterStage.includes(term.name));
                });
            }

            return _.orderBy([...filteredRules], [rule => rule.name.toLowerCase()], ['asc'])
        },

        filterStage() {
            let stages = [];
            if (this.filterStagePa) {
                stages.push('PA');
            }
            if (this.filterStagePp) {
                stages.push('PP');
            }
            if (this.filterStagePf) {
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
