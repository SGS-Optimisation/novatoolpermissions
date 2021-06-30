<template>
    <form @submit.prevent="findJob">
        <div :class="classes">

            <span class="w-auto flex justify-end items-center text-gray-500 p-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                          d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                          clip-rule="evenodd"/>
                </svg>
            </span>

            <input v-model="jobNumber" autofocus class="w-full shadow rounded p-2 focus:outline-none" type="text"
                   :placeholder="placeholder">

            <jet-button :type="'submit'" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Search
            </jet-button>
        </div>
    </form>
</template>

<script>
import JetButton from "@/Jetstream/DangerButton";

export default {
    name: "SearchBar",
    props: {
        classes: {
            type: String,
            default: "bg-white flex p-4",
        },
        placeholder: {
            type: String,
            default: "Enter a MySGS job number"
        }
    },
    components: {
        JetButton,
    },
    data() {
        return {
            jobNumber: "",

            form: this.$inertia.form({}, {
                    //bag: 'findJob'
                }
            ),
        }
    },

    methods: {
        findJob() {
            this.$emit('searching')
            this.form.post(route('job.rules', this.jobNumber));
        }
    },

    watch: {
        jobNumber(newVal) {
            let re = /[^0-9\-]/gi;
            this.$set(this, 'jobNumber', newVal.replace(re, ''));
        }
    }
}
</script>

<style scoped>

</style>
