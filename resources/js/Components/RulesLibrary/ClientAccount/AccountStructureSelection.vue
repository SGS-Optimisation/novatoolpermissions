<template>
    <div class="col-span-6">
        <h3>Categories</h3>
        <div class="flex flex-wrap">
            <div class="w-1/3 py-1" v-for="taxonomy of accountStructure" :key="taxonomy.id">
                <div class="p-field-checkbox">
                    <Checkbox :id="taxonomy.id" name="taxonomy" :value="taxonomy.id"
                              v-model="selectedTaxonomy" @change="onInput"
                    />
                    <label v-tooltip="mappings(taxonomy)" class="pl-2" :for="taxonomy.id">{{ taxonomy.name }}</label>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import Checkbox from 'primevue/checkbox/sfc';


export default {
    name: "AccountStructureSelection",
    props: {
        accountStructure: {
            type: Object,
            default: {},
            required: true,
        },
        modelValue: {
            type: Array,
            default: [],
            required: true
        }
    },
    components: {
        Checkbox,
    },
    data() {
        return {
            checked: false,
            selectedTaxonomy: []
        }
    },
    methods: {
        mappings(taxonomy) {
            var mappings_data = [];
            for (let i = 0; i < taxonomy.mappings.length; i++) {
                let mapping = taxonomy.mappings[i];

                mappings_data.push('=>' + mapping.api_name + '/' + mapping.api_action + ':' + mapping.field_path);
            }

            return mappings_data.join("\r\n");
        },

        onInput(event) {
            this.$emit('update:modelValue', this.selectedTaxonomy);
        }
    },

}
</script>

<style scoped>

</style>
