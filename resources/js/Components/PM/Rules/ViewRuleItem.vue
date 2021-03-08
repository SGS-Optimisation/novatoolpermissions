<template>
    <div class="mx-2" v-if="taxonomyRules.length > 0">
        <div class="w-full">
            <h1 class="text-md font-medium text-gray-900">
                {{ rule[0] }}
            </h1>
        </div>
        <div class="w-full">
            <div v-for="ruleItem in taxonomyRules" class="flex flex-row border p-1 my-1">
                <div class="w-full">
                    <div class="text-xs font-medium text-gray-900">
                        {{ ruleItem.name }}
                    </div>
                    <div class="text-xs text-gray-500">
                        Created {{ moment(ruleItem.created_at).fromNow() }}
                        &nbsp;
                        Updated {{ moment(ruleItem.updated_at).fromNow() }}
                    </div>
                </div>
                <a href="#" @click="$emit('on-click-view', ruleItem)"
                   class="text-indigo-600 hover:text-indigo-900 float-right justify-center -align-center">View</a>
            </div>
        </div>
    </div>
</template>

<script>

import moment from "moment";

export default {
    name: "ViewRuleItem",
    props: ['rule', 'filterFlag'],
    computed:{
        taxonomyRules() {
            return [...this.filterFlag ? this.rule[1].filter(rule => {
                let time = this.filterFlag === 'updated' ? moment(rule.updated_at) : moment(rule.created_at)
                return moment().subtract(3, 'months').isSameOrBefore(time)
            }) : this.rule[1]]
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
