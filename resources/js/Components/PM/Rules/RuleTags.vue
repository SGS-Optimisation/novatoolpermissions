<template>
    <div class="flex flex-wrap">
        <div v-for="(taxonomyTerms, taxonomy) in _.groupBy(rule.terms, function(item){
                            return item.taxonomy ? item.taxonomy.name : 'ERROR';
                        })">

            <rule-tag :terms="taxonomyTerms" :taxonomy="taxonomy"/>

        </div>
    </div>
</template>

<script>
import RuleTag from "@/Components/PM/Rules/RuleTag";
export default {
    name: "RuleTags",
    components: {RuleTag},
    props: ['rule'],
}
</script>

<style scoped>
.text-xxs {
    font-size: 0.5rem;
    text-transform: uppercase;
}
</style>
