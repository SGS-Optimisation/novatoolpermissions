<template>
    <div v-if="rule">
        <div v-html="rule.content"/>
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
