<template>
    <div>
        <label id="listbox-label" class="block text-xs font-medium font-bold text-gray-700">
            {{ taxonomyName }}
        </label>

        <multiselect v-model="selection"
                     :options="terms"
                     :searchable=true
                     @select="setSelected"
                     @deselect="setSelected"
                     @clear="setSelected"
        >

        </multiselect>
    </div>
</template>

<script>

import {defineComponent} from "vue";
//import Multiselect from 'vue-multiselect';
import Multiselect from '@vueform/multiselect';

export default defineComponent({
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
            console.log("value selected for " + this.taxonomyName, value);
            this.$emit('termSelected', this.taxonomyName, value);
        }
    },
})
</script>

<!--<style src="vue-multiselect/dist/vue-multiselect.css"></style>-->
<style src="@vueform/multiselect/themes/default.css"></style>
