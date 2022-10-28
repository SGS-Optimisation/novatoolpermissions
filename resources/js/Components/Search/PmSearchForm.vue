<template>
  <form @submit.prevent="runSearch">
    <div :class="classes">

            <span class="w-auto flex justify-end items-center text-gray-500 p-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                          d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                          clip-rule="evenodd"/>
                </svg>
            </span>


      <AutoComplete v-model="search" :suggestions="filteredItems"
                    @complete="searchSuggestions($event)"
                    :field="labelize" optionLabel="label"
                    optionGroupLabel="label" optionGroupChildren="items"
                    inputClass="w-full shadow rounded p-2 focus:outline-none"
                    placeholder="Job Number, Client or Brand"
                    :forceSelection="!isInputJobNumber"
                    scrollHeight="415px"
                    autofocus
      >
        <template #optiongroup="slotProps">
          <div class="flex align-items-center search-section">
            <img src="https://www.primefaces.org/wp-content/uploads/2020/05/placeholder.png" width="18"/>
            <div class="ml-2">{{ slotProps.item.label }}</div>
          </div>
        </template>
        <template #item="slotProps">
          <div class="search-item">
            <div class="ml-2">{{ slotProps.item.label }}</div>
          </div>
        </template>
      </AutoComplete>

      <!--            <input v-model="jobNumber" autofocus class="w-full shadow rounded p-2 focus:outline-none" type="text"
                         :placeholder="placeholder">-->

      <jet-button :type="'submit'" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
        Go
      </jet-button>
    </div>
  </form>
</template>

<script>
import {defineComponent} from 'vue'
import JetButton from "@/Jetstream/Button.vue";
import AutoComplete from 'primevue/autocomplete/sfc';
import {FilterService, FilterMatchMode} from 'primevue/api';
const ANY_FILTER = 'ANY';

export default defineComponent({
  name: "PmSearchForm",
  props: {
    classes: {
      type: String,
      default: "bg-white flex p-4",
    },
    placeholder: {
      type: String,
      default: "Enter a MySGS job number"
    },
    suggestions: {
      type: Object,
      required: false,
    }
  },
  components: {
    JetButton,
    AutoComplete,
  },
  data() {
    return {
      search: "",
      filteredItems: null,

      form: this.$inertia.form({}, {
            //bag: 'findJob'
          }
      ),
    }
  },
  computed: {
    isInputJobNumber() {
      if (typeof this.search === 'string' || this.search instanceof String) {

        const re = /[0-9\-]+/;

        return this.search.match(re) !== null;
      }
      return false;
    },
  },
  mounted(){
    FilterService.register(ANY_FILTER, (value, filter) => {
      if (filter === undefined || filter === null || filter.trim() === '') {
        return true;
      }

      if (value === undefined || value === null) {
        return false;
      }

      return filter.toString().split(' ').includes(value.toString());
    });
  },

  methods: {
    labelize(item) {
      if (item.type === 'bu') {
        return item.client + ' - ' + item.label;
      }
      //if (item.type === 'ca') {} // no need

      return item.label;
    },

    searchSuggestions(event) {
      let query = event.query;
      let filteredSuggestions = [];

      for (let group of this.suggestions) {
        //console.log('searching in ', group.items);
        const query_parts = query.split(' ');

        let filteredItems = FilterService.filter(
            group.items,
            ['label', 'client', 'taxonomy'],
            query,
            FilterMatchMode.CONTAINS);

        if (filteredItems && filteredItems.length) {
          filteredSuggestions.push({...group, ...{items: filteredItems}});
        }
      }

      this.filteredItems = filteredSuggestions;
    },

    runSearch() {
      this.$emit('searching')
      if (this.isInputJobNumber) {
        this.$inertia.get(route('pm.job-rules', this.search));
      } else if (this.search.type === 'bu') {
        // preload taxonomy into url params
        let parameters = {slug: this.search.slug}
        parameters[this.search.taxonomy] = this.search.value
        this.$inertia.get(route('pm.rules', parameters));
      } else {
        // search client only
        this.$inertia.get(route('pm.rules', this.search.value));
      }

    }
  },

  watch: {
    jobNumber(newVal) {
      let re = /[^0-9\-]/gi;
      this.jobNumber = newVal.replace(re, '');
    }
  }
})
</script>

<style scoped>
:deep(.p-autocomplete) {
  @apply w-full;
}
</style>
