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

    <div class="mt-6 rule-content" v-html="rule.content"/>
  </div>
</template>

<script>
import RuleTags from "@/Components/PM/Rules/RuleTags";
import Attachment from "@/Components/PM/Rules/Attachment";

export default {
  name: "RuleView",
  components: {RuleTags, Attachment},
  props: ['rule']
}
</script>
