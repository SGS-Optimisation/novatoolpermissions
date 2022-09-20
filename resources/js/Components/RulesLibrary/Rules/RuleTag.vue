<template>
    <div class="flex flex-col">
        <div class="text-xxs m-2 rounded-t-md mb-0 pl-1 flex-grow h-1"
             :class="{
                    'text-pink-200 bg-pink-400' : terms[0].taxonomy.parent.name === 'Account Structure',
                    'text-purple-200 bg-purple-400' : terms[0].taxonomy.parent.name === 'Job Categorizations'
            }"
             v-if="terms[0].taxonomy && terms[0].taxonomy.parent"
             :title="terms[0].taxonomy.parent.name">
        </div>

        <div class="flex flex-wrap flex-grow flex-shrink-0 text-sm items-center px-2">
            <div class="bg-gray-300 text-gray-600 px-2 py-1 rounded-bl-md text-xs">
                {{ taxonomy }}
            </div>

            <div v-for="(term, index) in displayedTerms"
                 class="h-full text-xs flex-grow bg-blue-200 text-green-800 px-2 py-1 "
                 :class="(index == terms.length -1 && !shouldTruncate) ?
                                     'rounded-br-md'
                                     : 'border-r border-blue-100'"
            >
                {{ term.name }}
            </div>
            <div v-if="truncateMode" class="h-full text-xs flex-grow bg-blue-200 text-green-800 px-2 py-1 rounded-br-md">
                <span @click="truncateMode = false" class="cursor-pointer border-dashed border-b border-gray-500">
                    <span class="text-xs">{{terms.length - displayInTruncateMode}} more</span>
                    >
                </span>
            </div>
            <div v-if="shouldTruncate && !truncateMode" class="h-full text-xs flex-grow bg-blue-200 text-green-800 px-2 py-1 rounded-br-md">
                <span @click="truncateMode = true" class="cursor-pointer border-dashed border-b border-gray-500"> &lt; hide </span>
            </div>

        </div>
    </div>
</template>

<script>
export default {
    name: "RuleTag",
    props: [
        'terms',
    ],
    data: function () {
        return {
            truncateMode: false,

            displayInTruncateMode: 3,
        }
    },

    computed: {

        taxonomy() {
            return this.terms[0].taxonomy.name;
        },

        shouldTruncate() {
            return this.terms.length > 4;
        },

        sortedTerms() {
            return _.sortBy(this.terms, function(term) {return term.name});
        },

        displayedTerms: function () {
            if(this.shouldTruncate && this.truncateMode) {
                return _.slice(this.sortedTerms, 0, this.displayInTruncateMode);
            }
            else {
                return this.sortedTerms;
            }
        },
    },

    mounted() {
        if(this.shouldTruncate) {
            this.truncateMode = true;
        }
    },
}
</script>

<style scoped>

</style>
