<template>
    <div>
        <label id="listbox-label" class="block text-xs font-medium font-bold text-gray-700">
            {{ taxonomyName }}
        </label>

        <multiselect v-model="selection"
                     :options="terms"
                     @select="setSelected"
        >

        </multiselect>
    </div>
</template>

<script>

import Multiselect from 'vue-multiselect';

export default {
    name: "TaxonomySelector",
    props: [
        'taxonomyName',
        'terms',
        'filters',
    ],
    components: {
        Multiselect,
    },
    data() {
        return {
            selection: null,
        }
    },
    methods: {
        clearSelected: function () {
            console.log("clearing selection on " + this.taxonomyName);
            this.selection = null;
        },

        setSelected: function (value) {
            this.selection = value;
            console.log("value selected for " + this.taxonomyName, value);
            this.$emit('termSelected', this.taxonomyName, value);
        }
    },
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
