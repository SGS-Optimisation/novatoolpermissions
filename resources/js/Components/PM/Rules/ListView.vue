<template>
  <div class="bg-white shadow-md my-2 p-3 rounded" :data-rule-id="rule.id">
    <div class="flex items-center justify-between">
      <div class="description">
        <h2 @click="open = !open; $emit('toggle')" class="cursor-pointer text-xl font-bold">{{ rule.name }}</h2>
        <div class="flex">
          <div class="flex-shrink cursor-default align-bottom text-xs border-dashed border-b border-gray-500"
               :title="date()">
            Last updated {{ humanDate() }}
          </div>
          <nav-link :href="route('pm.client-account.rules.edit',  {clientAccount: clientAccount.slug, id: rule.id })">
            <i class="fa fa-pen"></i>
          </nav-link>
        </div>
        <rule-tags :rule="rule"/>
      </div>

      <!-- Button for opening card -->
      <div class="ml-4">
        <div @click="open = !open; $emit('toggle')"
             class="flex items-center cursor-pointer px-3 py-2 text-gray-200 hover:text-gray-600"
             :class="{'transform rotate-180': open}">
          <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
               stroke="black">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </div>

      </div>

    </div>


    <!-- Collapsed content -->
    <div class="w-full flex flex-col mt-8" :class="{'hidden': !open}">
      <hr class="mb-4 border-gray-700">
      <div v-html="rule.content"></div>
    </div>

  </div>
</template>

<script>

import RuleTags from "@/Components/PM/Rules/RuleTags";

const moment = require('moment');

import NavLink from "@/Jetstream/NavLink";

export default {
  name: "Rule",
  props: ['rule', 'clientAccount'],

  components: {
    RuleTags,
    NavLink,
  },

  data() {
    return {
      open: false,
    }
  },

  methods: {
    date: function () {
      return moment(this.rule.updated_at).format('MMM DD YYYY, HH:mm:ss');
    },

    humanDate: function () {
      return moment(this.rule.updated_at).fromNow();
    },
  }

}
</script>
