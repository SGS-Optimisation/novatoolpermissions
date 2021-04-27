<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 bg-gray-50 shadow-inner border border-orange-200">
        <div class="flex flex-col">

            <div class="flex flex-row">
                <img :src="'/'+job.metadata.client.image" style="max-width: 80px">
                <h3 class="font-semibold text-lg leading-loose text-gray-800 ">
                    {{ job.designation }}
                </h3>
            </div>

            <div class="flex flex-row flex-wrap my-5 ">
                <template v-for="(values, item) in job.metadata.job_taxonomy">
                    <div v-if="values.length" class="flex flex-col mb-2">
                        <div class="flex flex-wrap flex-shrink-0 text-xs items-center pr-3 text-xs mr-3">
                            <div class="flex-grow h-full bg-gray-300 text-gray-600 px-2 rounded-l-lg">
                                <div class="grid h-full">
                                    <div class="place-self-center">{{ item }}</div>
                                </div>
                            </div>
                            <div class="">

                                <div class="bg-blue-200 text-green-800 px-2 flex flex-row justify-between"
                                     :class="job.metadata.matched_taxonomy[item].length ? 'b rounded-tr-lg':'rounded-r-lg'"
                                     title="MySGS value">
                                    <div>
                                        <div class="w-32" v-for="value in values.slice(0, limit)"
                                             :title="'MySGS value' + (value.length < 20 ? '' : ': ' +value)">
                                            <span v-if="values.length > 1">- </span>
                                            {{ shorten(value) }}
                                        </div>
                                    </div>
                                    <div>
                                        <button v-if="values.length > 1 & limit === 1" @click="limit=12">
                                            <i class="fa fa-plus-square"></i>
                                        </button>
                                        <button v-if="values.length > 1 && limit !== 1" @click="limit=1">
                                            <i class="fa fa-minus-square"></i>
                                        </button>
                                    </div>
                                </div>

                                <div v-for="(terms, index) in job.metadata.matched_taxonomy[item]"
                                     :class="(index === job.metadata.matched_taxonomy[item].length - 1) ?
                                                    'bg-pink-200 text-green-800 px-2 rounded-br-lg'
                                                    :'bg-pink-200 text-green-800 px-2'"
                                     title="Matched Dagobah terms">
                                    {{ terms }}
                                </div>
                            </div>

                        </div>
                    </div>
                </template>
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
