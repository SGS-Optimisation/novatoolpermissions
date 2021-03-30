<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 bg-gray-50 shadow-inner border border-orange-200">
        <div class="flex flex-col">

            <div class="flex flex-row">
                <img :src="'/'+job.metadata.client.image" style="max-width: 80px">
                <h3 class="font-semibold text-lg leading-loose text-gray-800 ">
                    {{ job.designation }}
                </h3>
            </div>

            <div class="flex flex-row my-2 ">
                <div v-for="(value, item) in job.metadata.job_taxonomy" class="flex flex-col">
                    <div class="flex flex-wrap flex-shrink-0 text-xs items-center pr-3 text-xs mr-3">
                        <div class="flex-grow h-full bg-gray-300 text-gray-600 px-2 rounded-l-lg">
                            <div class="grid h-full">
                                <div class="place-self-center">{{ item }}</div>
                            </div>
                        </div>
                        <div class="">

                            <div :class="job.metadata.matched_taxonomy[item].length ?
                                                'bg-blue-200 text-green-800 px-2 rounded-tr-lg'
                                                :'bg-blue-200 text-green-800 px-2 rounded-r-lg'"
                                 title="MySGS value">{{ value }}
                            </div>

                            <div v-for="(terms, index) in job.metadata.matched_taxonomy[item]"
                                 :class="(index === job.metadata.matched_taxonomy[item].length - 1) ?
                                                    'bg-pink-200 text-green-800 px-2 rounded-br-lg'
                                                    :'bg-pink-200 text-green-800 px-2'"
                                 title="Matched Dagobah terms">
                                {{ terms }}<br>
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
