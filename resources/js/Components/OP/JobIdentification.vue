<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 bg-gray-50 shadow-inner border border-orange-200">
        <div class="flex flex-col">

            <div class="flex flex-row">
                <img :src="'/'+job.metadata.client.image" style="max-width: 80px">
                <h3 class="font-semibold text-lg leading-loose text-gray-800 ">
                    {{ job.designation }}
                </h3>
            </div>

            <div class="flex flex-row my-5 ">
                <div v-for="(values, item) in job.metadata.job_taxonomy" class="flex flex-col">
                    <div class="flex flex-wrap flex-shrink-0 text-xs items-center pr-3 text-xs mr-3">
                        <div class="flex-grow h-full bg-gray-300 text-gray-600 px-2 rounded-l-lg">
                            <div class="grid h-full">
                                <div class="place-self-center">{{ item }}</div>
                            </div>
                        </div>
                        <div class="">

                            <div class="bg-blue-200 text-green-800 px-2"
                                 :class="job.metadata.matched_taxonomy[item].length ? 'b rounded-tr-lg':'rounded-r-lg'"
                                 title="MySGS value">
                                <div class="w-32" v-for="value in values">
                                    <span v-if="values.length > 1">- </span>
                                    {{ value }}
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
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "JobIdentification",
    props: ['job'],
}
</script>

<style scoped>

</style>
