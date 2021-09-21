<template>
    <div :class="{
                    'flex flex-row p-1 my-1': !termFocus && (index === taxonomyRules.length-1),
                    'flex flex-row border-b border-indigo-100 p-1 my-1': !termFocus && (index !== taxonomyRules.length-1),
                    'w-1/3 flex px-3 py-1 my-1': termFocus,
                 }">
        <div class="w-full"
             :class="{'bg-indigo-50 p-2': termFocus}">
            <div class="cursor-pointer">
                <p class="text-sm font-bold text-gray-900">
                    <input type="checkbox" v-model="completed" @change="saveTask"/>
                    <span  @click="$emit('on-click-view', rule)">{{ rule.name }}</span>
                </p>
                <p  @click="$emit('on-click-view', rule)" class="text-xs text-gray-700 break-all" v-html="excerpt(rule)"/>
            </div>
            <div class="text-xs text-gray-500 flex flex-row justify-between">
                <div class="flex flex-row">
                    <span class="flex flex-row" v-if="rule.attachments && rule.attachments.length">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-6" fill="none" viewBox="0 0 24 24"
                                   stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                              </svg>
                              {{ rule.attachments.length }}
                            </span>
                    <div class="ml-2" v-if="rule.flagged">
                        <i title="This rule is currently flagged"
                           class="text-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                 fill="currentColor">
                                <title>This rule is currently flagged</title>
                                <path fill-rule="evenodd"
                                      d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z"
                                      clip-rule="evenodd"/>
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
    </div>
</template>

<script>
import moment from "moment";
import clip from "text-clipper";

export default {
    name: "ViewRule",
    props: [
        'rule',
        'job',
        'index',
        'termFocus',
        'taxonomyRules',
    ],
    data() {
        return {
            completed: this.rule.completed,
        }
    },
    created() {
        //this.rule.completed = false;
        //this.rule.completed = false;
    },
    mounted() {
        if (localStorage.getItem('job-'+this.job)) {
            try {
                var tasks = JSON.parse(localStorage.getItem('job-'+this.job));
                if (tasks.hasOwnProperty(this.rule.dagId)) {
                    this.rule.completed = tasks[this.rule.dagId];
                    this.completed = tasks[this.rule.dagId];
                }
            } catch(e) {
                localStorage.removeItem(this.job);
            }
        }
    },
    methods: {
        excerpt(rule) {
            return clip(rule.content.replace(/<[^>]+>/g, ''), 180, {html: true, maxLines: 3});
        },

        saveTask() {
            var tasks = JSON.parse(localStorage.getItem('job-'+this.job));
            if(!tasks) {
                tasks = {};
            }
            tasks[this.rule.dagId] = this.completed;
            localStorage.setItem('job-'+ this.job, JSON.stringify(tasks));
        },
    },
}
</script>

<style scoped>

</style>
