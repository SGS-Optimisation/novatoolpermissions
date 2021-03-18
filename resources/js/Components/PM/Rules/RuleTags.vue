<template>
    <div class="flex flex-shrink-0">
        <div v-for="(taxonomyTerms, taxonomy) in _.groupBy(rule.terms, function(item){
                            return item.taxonomy ? item.taxonomy.name : 'ERROR';
                        })">

            <div class="flex flex-col">
                <div class="text-xs  bg-purple-400 m-2 text-white rounded-t-md mb-0 pl-1"
                     v-if="taxonomyTerms[0].taxonomy && taxonomyTerms[0].taxonomy.parent">

                    {{ taxonomyTerms[0].taxonomy.parent.name }}
                </div>

                <div class="flex flex-wrap flex-shrink-0 text-sm items-center px-2">
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
</template>

<script>
export default {
    name: "RuleTags",
    props: ['rule']
}
</script>
