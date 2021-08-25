<template>

    <div>
        <span>{{ taxonomyName }}</span>
        <div class="flex flex-row items-center">
            <div class="flex-grow">
                <multiselect v-model="selection"
                          ref="select"
                          :multiple="multiple"
                          :close-on-select="!multiple"
                          :appendToBody=true
                          :options="terms"
                          @input="setSelected"/>
            </div>
            <div class="flex flex-col flex-shrink ml-3">
                <button type="button" @click="selectAll"
                        class="px-1 text-xs border rounded-full border-gray-50 bg-gray-100 hover:bg-gray-200"
                        v-if="multiple">
                    Select&nbsp;All
                </button>
                <button type="button" @click="removeAll"
                        class="px-1 text-xs border rounded-full border-gray-50 bg-gray-100 hover:bg-gray-200"
                        v-if="multiple"
                        :disabled="terms.length===0">
                    Remove&nbsp;All
                </button>
                <button type="button" @click="getTaxonomyTermsFromRule"
                        class="px-1 text-xs border rounded-full border-gray-50 bg-gray-100 hover:bg-gray-200"
                        >
                    Reset
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import JetButton from "@/Jetstream/Button";

export default {
    name: "Term",
    components: {
        JetButton,
    },
    props: [
        'rule',
        'taxonomyName',
        'terms',
        'multiple'
    ],

    data() {
        return {
            selection: [],
        }
    },

    mounted() {
        this.getTaxonomyTermsFromRule();
    },

    methods: {
        selectAll() {
            this.selection = this.terms;
            this.setSelected(this.selection);
        },

        removeAll() {
            console.log('removing all');
            this.selection = [];
            this.setSelected(this.selection);
        },

        getTaxonomyTermsFromRule() {
            this.selection = _.filter(_.map(this.rule.terms, (term, key) => {
                if (term.taxonomy.name == this.taxonomyName) {
                    return term.name;
                }
            }));

            /*
             * hack to populate parent
             */
            this.$emit('selected', {
                taxonomy: this.taxonomyName,
                terms: this.selection
            });
        },

        setSelected: function (value) {
            console.log("value selected for " + this.taxonomyName, value);

            this.$emit('selected', {
                taxonomy: this.taxonomyName,
                terms: this.selection
            });
        }
    },
}
</script>

<style scoped>

</style>
