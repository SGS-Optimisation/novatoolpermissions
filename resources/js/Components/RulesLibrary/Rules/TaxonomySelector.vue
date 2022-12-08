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
import Multiselect from '@vueform/multiselect';

export default defineComponent({
    name: "TaxonomySelector",
    props: [
        'taxonomyName',
        'urlParam',
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
    created() {
        let qp = new URLSearchParams(window.location.search);

        let urlParam = this.urlParam ? this.urlParam : this.taxonomyName;
        let field = qp.get(urlParam);
        if(field) {
            console.log('param loaded from url for ' + this.taxonomyName, field);
            this.selection = qp.get(urlParam);
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
