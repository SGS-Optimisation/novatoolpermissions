<template>
    <div v-if="rule">
        <rule-tags :rule="rule"/>

        <div class="flex flex-wrap mb-3">
            <template class="w-16" v-for="attachment in rule.attachments">
                <attachment
                    :attachment="attachment"
                    :rule="rule"
                    :show-delete="false"
                />
            </template>
        </div>

        <div class="flex items-center bg-red-200 h-32 justify-center" v-if="rule.flagged">
            Rule currently flagged. Please see your PM.
        </div>
        <div class="mt-6 rule-content"
             v-if="showContent"
             v-html="rule.content"/>
    </div>
</template>

<script>
import RuleTags from "@/Components/RulesLibrary/Rules/RuleTags.vue";
import Attachment from "@/Components/RulesLibrary/Rules/Attachment.vue";

export default {
    name: "RuleView",
    components: {RuleTags, Attachment},
    props: ['rule'],
    computed: {
        showContent() {
            return !this.rule.flagged || this.$page.props.settings.show_flagged_rules_content;
        }
    },
}
</script>
