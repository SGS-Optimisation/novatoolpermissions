<template>

    <div>
        <span>{{ taxonomyName }}</span>
        <v-select v-model="selection"
                  multiple
                  :appendToBody=true
                  :options="terms"
                  @input="setSelected"/>
    </div>
</template>

<script>
export default {
    name: "Term",
    props: [
        'rule',
        'taxonomyName',
        'terms',
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
        getTaxonomyTermsFromRule()
        {
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
                terms: value
            });
        }
    },
}
</script>

<style scoped>

</style>
