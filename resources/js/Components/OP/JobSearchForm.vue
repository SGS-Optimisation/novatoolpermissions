<template>
    <form @submit.prevent="findJob">
        <div :class="classes">

            <span class="w-auto flex justify-end items-center text-gray-500 p-2">
                <i class="fa fa-search fa-2x"></i>
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
                    bag: 'findJob'
                }
            ),
        }
    },

    methods: {
        findJob() {
            this.$emit('searching')
            this.form.post(route('job.rules', this.jobNumber), {
                preserveScroll: true
            }).then(() => {
                this.$emit('loaded')
            });
        }
    },
}
</script>

<style scoped>

</style>
