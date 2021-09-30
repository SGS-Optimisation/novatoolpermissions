<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 bg-gray-50 shadow-inner border border-orange-200">
        <div class="flex flex-row">
            <img :src="job.metadata.client.image" style="max-height: 80px">
            <div class="flex flex-col">
                <h3 class="font-semibold text-lg leading-loose text-gray-800 ml-2">
                    {{ job.designation }}
                </h3>
                <div class="flex flex-row flex-wrap my-5 ">
                    <template v-for="(values, item) in job.metadata.job_taxonomy">
                        <div v-if="values.length" class="flex flex-col mb-2"
                        :class="{'order-last': job.metadata.matched_taxonomy[item].length === 0}">
                            <div class="flex flex-wrap flex-shrink-0 text-xs items-center pr-3 text-xs mr-3">
                                <div class="flex-grow h-full bg-gray-300 text-gray-600 px-2 rounded-l-lg">
                                    <div class="grid h-full">
                                        <div class="place-self-center">{{ item }}</div>
                                    </div>
                                </div>
                                <div class="">

                                    <div
                                        v-if="job.metadata.matched_taxonomy[item].length === 0"
                                        class="bg-gray-100 text-green-800 px-2 flex flex-row justify-between"
                                        :class="job.metadata.matched_taxonomy[item] && job.metadata.matched_taxonomy[item].length ?
                                     'b rounded-tr-lg':'rounded-r-lg'"
                                        title="No match for MySGS value">
                                        <div class="flex flex-row">
                                            <template v-if="typeof values == 'object'">
                                                <div class="flex flex-col">
                                                    <div class="w-32" v-for="value in values.slice(0, limit)"
                                                         :title="'No match for MySGS value' + (value.length < 20 ? '' : ': ' +value)">
                                                        <span v-if="values.length > 1">- </span>
                                                        {{ shorten(value) }}
                                                    </div>
                                                </div>

                                                <div>
                                                    <button class="" v-if="values.length > 1 & limit === 1"
                                                            @click="limit=12">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                             viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                  d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                                                  clip-rule="evenodd"/>
                                                        </svg>
                                                    </button>
                                                    <button class="" v-if="values.length > 1 && limit !== 1"
                                                            @click="limit=1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                             viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                  d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z"
                                                                  clip-rule="evenodd"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </template>
                                            <template v-else>
                                                <div class="w-32"
                                                     :title="'MySGS value' + (values.length < 20 ? '' : ': ' +values)">
                                                    {{ shorten(values) }}
                                                </div>
                                            </template>
                                        </div>

                                    </div>

                                    <div v-for="(terms, index) in job.metadata.matched_taxonomy[item]"
                                         class="bg-blue-100 text-green-800 px-2"
                                         :class="{'rounded-r-lg': (index === (job.metadata.matched_taxonomy[item].length - 1))}"
                                         title="Matched MySGS and Dagobah terms">
                                        {{ terms }}
                                    </div>
                                </div>

                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import clip from "text-clipper";

export default {
    name: "JobIdentification",
    props: ['job'],

    data() {
        return {
            limit: 1,
            showLess: true,
        }
    },

    methods: {
        shorten(txt) {
            return clip(txt, 20, {maxLines: 1});
        }
    }
}
</script>

<style scoped>

</style>
