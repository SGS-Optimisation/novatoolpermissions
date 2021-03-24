<template>
    <div v-if="rule">
        <div class="py-3 sm:py-6 sm:flex sm:flex-row">
            <h1 class="text-lg text-gray-900 font-bold w-full" id="modal-headline">
                {{ rule.name.slice(0, 30) }}
            </h1>
            <slot></slot>
        </div>
        <rule-tags :rule="rule"/>
        <div class="mt-8">
            <span v-html="rule.content"></span>
        </div>
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
