<template>
    <div class="flex flex-wrap">
        <div v-for="(taxonomyTerms, taxonomy) in _.groupBy(rule.terms, function(item){
                            return item.taxonomy ? item.taxonomy.name : 'ERROR';
                        })">

            <div class="flex flex-col">
                <div class="text-xxs m-2 rounded-t-md mb-0 pl-1 flex-grow h-1"
                     :class="{
                        'text-pink-200 bg-pink-400' : taxonomyTerms[0].taxonomy.parent.name === 'Account Structure',
                        'text-purple-200 bg-purple-400' : taxonomyTerms[0].taxonomy.parent.name === 'Job Categorizations'
                        }"
                     v-if="taxonomyTerms[0].taxonomy && taxonomyTerms[0].taxonomy.parent"
                    :title="taxonomyTerms[0].taxonomy.parent.name">
                </div>

                <div class="flex flex-wrap flex-grow flex-shrink-0 text-sm items-center px-2">
                    <div class="bg-gray-300 text-gray-600 px-2 py-1 rounded-bl-md text-xs">
                        {{ taxonomy }}
                    </div>
                    <div v-for="(term, index) in taxonomyTerms"
                         class="h-full text-xs flex-grow bg-blue-200 text-green-800 px-2 py-1 "
                         :class="(index == taxonomyTerms.length -1) ?
                                     'rounded-br-md'
                                     : 'border-r border-blue-100'"
                    >
                        {{ term.name }}
                    </div>

                </div>
            </div>

        </div>
    </div>
</template>

<script>
export default {
    name: "RuleTags",
    props: ['rule']
}
</script>

<style scoped>
.text-xxs {
    font-size: 0.5rem;
    text-transform: uppercase;
}
</style>
