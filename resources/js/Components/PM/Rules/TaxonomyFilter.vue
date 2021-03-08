<template>
    <div class="">
        <label id="listbox-label" class="block text-xs font-medium text-gray-700">
            {{ taxonomy[0] }}
        </label>
        <div class="mt-1 relative">
            <button type="button" aria-haspopup="listbox" aria-expanded="true" aria-labelledby="listbox-label"
                    class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-xs"
                    @click="open = !open">
                <span class="flex items-center">
                    <span class="ml-3 block truncate">
                      {{ selected ? selected : 'None' }}
                    </span>
                </span>
                <span class="ml-3 absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <!-- Heroicon name: solid/selector -->
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                         aria-hidden="true">
                      <path fill-rule="evenodd"
                            d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                            clip-rule="evenodd"/>
                    </svg>
                </span>
            </button>

            <!--
              Select popover, show/hide based on select state.
              Entering: ""
                From: ""
                To: ""
              Leaving: "transition ease-in duration-100"
                From: "opacity-100"
                To: "opacity-0"
            -->
            <div v-if="open" class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-50">
                <ul tabindex="-1" role="listbox" aria-labelledby="listbox-label"
                    class="max-h-56 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-xs">
                    <!--
                      Select option, manage highlight styles based on mouseenter/mouseleave and keyboard navigation.

                      Highlighted: "text-white bg-indigo-600", Not Highlighted: "text-gray-900"
                    -->
                    <li v-for="term in taxonomy[1]" @click="onSelectTerm(term)" id="listbox-item-0" role="option" class="text-gray-900 cursor-default select-none relative py-2 pl-3 pr-9">
                        <div class="flex items-center">
                            <!-- Selected: "font-semibold", Not Selected: "font-normal" -->
                            <span class="ml-3 block font-normal truncate">
                              {{ term }}
                            </span>
                        </div>

                        <!--
                          Checkmark, only display for selected option.
                          Highlighted: "text-white", Not Highlighted: "text-indigo-600"
                        -->
                        <span class="absolute inset-y-0 right-0 flex items-center pr-4" v-if="term === selected">
                            <!-- Heroicon name: solid/check -->
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                 aria-hidden="true">
                              <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"/>
                            </svg>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "TaxonomyFilter",
    props: [ 'taxonomy' ],
    data(){
        return {
            selected: null,
            open: false
        }
    },
    methods:{
        onSelectTerm(term){
            this.selected = term;
            this.open = false;
            this.$emit('on-change-filter', this.taxonomy[0], this.selected);
        }
    }
}
</script>

<style scoped>

</style>
