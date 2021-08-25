<template>
    <div v-if="rule">
        <rule-tags :rule="rule"/>
        <div class="mt-6 rule-content" v-html="rule.content"/>
    </div>
</template>

<script>
import RuleTags from "@/Components/PM/Rules/RuleTags";

export default {
    name: "RuleView",
    components: {RuleTags},
    props: ['rule']
}
</script>
