<template>
    <div class="shadow-md m-8 p-6 rounded"  :data-rule-id="rule.id">
        <div class="flex items-center justify-between">
            <div class="description">
                <h2 @click="open = !open;" class="cursor-pointer text-xl font-bold">{{ rule.name }}</h2>
                <div class="flex">
                    <div class="flex-shrink cursor-default align-bottom text-xs border-dashed border-b border-gray-500" :title="date()">
                        Last updated {{humanDate()}}
                    </div>
                </div>
                <div class="flex flex-shrink-0">
                    <div v-for="(taxonomyTerms, taxonomy) in _.groupBy(rule.terms, function(item){
                            return item.taxonomy.name
                        }) ">

                        <div class="flex flex-col">
                            <div class="text-xs  bg-purple-400 m-2 text-white rounded-t-md mb-0 pl-1"
                                 v-if="taxonomyTerms[0].taxonomy.parent">

                                {{taxonomyTerms[0].taxonomy.parent.name}}
                            </div>

                            <div class="flex flex-shrink-0 text-sm items-center px-2">
                                <div class="bg-gray-300 text-gray-600 px-2 py-1 rounded-bl-md text-xs">
                                    {{ taxonomy }}
                                </div>
                                <div v-for="(term, index) in taxonomyTerms"
                                     :class="(index == taxonomyTerms.length -1) ?
                                     'h-full text-xs bg-blue-200 text-green-800 px-2 py-1 rounded-br-md'
                                     : 'h-full text-xs bg-blue-200 text-green-800 px-2 py-1 border-r border-blue-100'"
                                     >
                                    {{ term.name }}
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Button for opening card -->
            <div class="ml-4">
                <div @click="open = !open;"
                     class="flex items-center cursor-pointer px-3 py-2 text-gray-200 hover:text-gray-600"
                     :class="{'transform rotate-180': open}">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke="black">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

            </div>

        </div>


        <!-- Collapsed content -->
        <div class="w-full flex flex-col mt-8" :class="{'hidden': !open}">
            <hr class="mb-4 border-gray-700">
            <div v-html="rule.content"></div>
        </div>

    </div>
</template>

<script>

const moment = require('moment');

export default {
    name: "Rule",
    props: ['rule'],

    data() {
        return {
            open: false,
        }
    },

    methods: {
        date: function(){
            return moment(this.rule.updated_at).format('MMM DD YYYY, HH:mm:ss');
        },

        humanDate: function(){
            return moment(this.rule.updated_at).fromNow();

        },
    }

}
</script>

<style scoped>

</style>
