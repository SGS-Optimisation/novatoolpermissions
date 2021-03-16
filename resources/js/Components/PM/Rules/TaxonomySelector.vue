<template>
    <div>
        <label id="listbox-label" class="block text-xs font-medium text-gray-700">
            {{ taxonomyName }}
        </label>

        <v-select v-model="selection"
                  :options="terms"
                  @input="setSelected"
        >

        </v-select>
    </div>
</template>

<script>

export default {
    name: "TaxonomySelector",
    props: [
        'taxonomyName',
        'terms',
        'filters',
    ],
    data() {
        return {
            selection: null,
        }
    },
    methods: {
        clearSelected: function () {
            this.selection = null;
        },

        setSelected: function (value) {
            console.log("value selected for " + this.taxonomyName, value);
            this.$emit('brandSelected', this.taxonomyName, value);
        }
    },
}
</script>

<style scoped>

</style>
