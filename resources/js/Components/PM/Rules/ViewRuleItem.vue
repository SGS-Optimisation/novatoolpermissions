<template>
    <div class="mr-4 mb-4 p-2 bg-indigo-50" v-if="taxonomyRules.length > 0">
        <div class="w-full">
            <h1 class="bg-gray-500 text-md font-medium text-gray-100 px-2">
                {{ group }}
            </h1>
        </div>
        <div class="w-full">
            <div v-for="(rule, index) in taxonomyRules"
                 :class="(index == taxonomyRules.length-1) ?
                 'flex flex-row p-1 my-1'
                :'flex flex-row border-b border-indigo-100 p-1 my-1'"
            >
                <div class="w-full">
                    <div class="cursor-pointer text- font-medium text-gray-900" @click="$emit('on-click-view', rule)">
                        {{ rule.name }}
                    </div>
                    <div class="text-xs text-gray-500 flex flex-row justify-between">
                        <div class="flex flex-row">
                            <div class="text-xs bg-pink-200 rounded-xl px-2">
                                {{ _.find(rule.terms, (item) => {return item.taxonomy.name === group }).name  }}
                            </div>
                            <div class="ml-2" v-if="rule.flagged">
                                <i title="This rule is currently flagged"
                                   class="text-red-700 fa fa-flag"></i>
                            </div>
                        </div>

                        <div>
                            <span v-if="rule.created_at === rule.updated_at">
                                Created {{ moment(rule.created_at).fromNow() }}
                            </span>
                            <span v-else>
                                Updated {{ moment(rule.updated_at).fromNow() }}
                            </span>
                        </div>
                    </div>

                </div>
<!--                <a href="#" @click="$emit('on-click-view', ruleItem)"
                   class="text-indigo-600 hover:text-indigo-900 float-right justify-center -align-center">
                    <i class="fa fa-eye"></i>
                </a>-->
            </div>
        </div>
    </div>
</template>

<script>

import moment from "moment";

export default {
    name: "ViewRuleItem",
    props: [ 'group', 'rules', 'filterFlag'],
    data() {
        return {
        }
    },
    computed:{
        taxonomyRules() {
            return _.orderBy([...this.filterFlag ? this.rules.filter(rule => {
                let time = this.filterFlag === 'updated' ? moment(rule.updated_at) : moment(rule.created_at);

                return moment().subtract(3, 'months').isSameOrBefore(time)
                    && (this.filterFlag !== 'updated' || rule.created_at !== rule.updated_at)
            }) : this.rules], 'updated_at', 'desc')
        }
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
