<template>
    <div>
        <vue-instant :suggestOnAllWords="true"
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
        </vue-instant>
    </div>
</template>

<script>
export default {
    name: "CustomerSelector",
    components: {

    },

    data() {
        return {
            value: '',
            suggestionAttribute: 'name',
            suggestions: [],
            selectedEvent: ""
        }
    },

    methods: {
        clickInput: function () {
            this.selectedEvent = 'click input'
        },
        clickButton: function () {
            this.selectedEvent = 'click button'
        },
        selected: function () {
            this.selectedEvent = 'selection changed';
            let client = _.find(this.suggestions, {'name': this.value});
            this.$emit('customerSelected', this.value, client ? client.aliases : []);
        },
        enter: function () {
            this.selectedEvent = 'enter'
        },
        keyUp: function () {
            this.selectedEvent = 'keyup pressed'
        },
        keyDown: function () {
            this.selectedEvent = 'keyDown pressed'
        },
        keyRight: function () {
            this.selectedEvent = 'keyRight pressed'
        },
        clear: function () {
            this.selectedEvent = 'clear input'
        },
        escape: function () {
            this.selectedEvent = 'escape'
        },
        changed: _.debounce( function(e) {
            this.getCustomers()
        }, 300),
        getCustomers() {
            this.suggestions = [];
            axios.get(route('api.simplified-customer.index')+ '?query=' + this.value)
                .then((response) => {
                    response.data.results.forEach( (a) =>  {
                        this.suggestions.push(a)
                    })
                })
        }
    },
}
</script>

<style scoped>
/deep/ .sbx-google {
    width: 100%;
}

/deep/ .sbx-google__submit {
    display: none;
}
</style>
