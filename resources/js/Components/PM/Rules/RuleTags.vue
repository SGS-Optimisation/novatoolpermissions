<template>
    <div class="flex flex-wrap">
        <div v-for="taxonomyTerms in sortedTaxonomyTerms">

            <rule-tag :terms="taxonomyTerms"/>

        </div>
    </div>
</template>

<script>
import RuleTag from "@/Components/PM/Rules/RuleTag";

export default {
    name: "RuleTags",
    components: {RuleTag},
    props: ['rule'],
    data: function () {
        return {}
    },

    computed: {
        termsByTaxonomy() {
            return _.groupBy(this.rule.terms, function (item) {
                return item.taxonomy ? item.taxonomy.name : 'ERROR';
            })
        },

        taxonomyTerms() {
            return _.values(this.termsByTaxonomy);
        },

        sortedTaxonomyTerms() {
            return _.orderBy(
                this.taxonomyTerms,
                [
                    (termLists) => termLists[0].taxonomy.parent.name,
                    (termLists) => termLists.length,
                    (termLists) => termLists[0].taxonomy.name,
                ],
                [
                    'desc',
                    'asc',
                    'asc'
                ]
            )
        }
    }
}
</script>

<style scoped>
.text-xxs {
    font-size: 0.5rem;
    text-transform: uppercase;
}
</style>
