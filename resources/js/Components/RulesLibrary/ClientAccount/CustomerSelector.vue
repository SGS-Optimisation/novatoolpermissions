<template>
    <div>
        <AutoComplete
            v-model="selectedClient"
            field="name"
            :suggestions="suggestions"
            @complete="getCustomers"
            @item-select="selected($event)"
            placeholder="Search…"/>


        <!--        <vue-instant :suggestOnAllWords="true"
                             :show-autocomplete="true" :autofocus="false"
                             :suggestion-attribute="suggestionAttribute"
                             :suggestions="suggestions"
                             v-model="value" :disabled="false"
                             @input="changed"
                             @click-input="clickInput"
                             @click-button="clickButton"
                             @selected="selected"
                             @enter="enter" @key-up="keyUp" @key-down="keyDown" @key-right="keyRight"
                             @clear="clear" @escape="escape"
                             name="customName" placeholder="Customer search"
                             type="google">
                </vue-instant>-->
    </div>
</template>

<script>
import AutoComplete from 'primevue/autocomplete/sfc';

export default {
    name: "CustomerSelector",
    components: {
        AutoComplete,
    },

    data() {
        return {
            selectedClient: null,
            suggestions: [],
        }
    },

    watch:{
        selectedClient(value){
            const name = this.selectedClient.hasOwnProperty('name') ? this.selectedClient.name : this.selectedClient
            const aliases = this.selectedClient.hasOwnProperty('aliases') ? this.selectedClient.aliases : [];
            this.$emit('customerSelected', name, aliases);
        }
    },

    methods: {
        selected: function (event) {
            this.$emit('customerSelected', this.selectedClient.name, this.selectedClient.aliases);
        },

        getCustomers(event) {
            setTimeout(() => {
                if (!event.query.trim().length) {
                    this.suggestions = [];
                } else {
                    axios.get(route('api.simplified-customer.index') + '?query=' + event.query)
                        .then((response) => {
                            this.suggestions = response.data.results;
                        })
                }
            }, 250);
        },
    },
}
</script>

<style scoped>
</style>
