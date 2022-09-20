<template>
    <div v-if="$page.props.features.allow_force_account" class="">
        <span class="px-2">
            <span class="font-bold text-red-500" v-if="initialSelection">
                Forced account selection
            </span>
            <span v-else>Manual account selection</span>
        </span>
        <Dropdown
            class="mx-2"
            v-model="selected"
            :options="clientAccounts"
            optionValue="slug"
            optionLabel="label"
            :filter="true"
        />
        <Button :label="initialSelection ? 'Change' : 'Go'"
                :disabled="!selected"
                class="pl-2"
                @click="handleClick($event)"></Button>

    </div>
</template>

<script>
import Dropdown from 'primevue/dropdown/sfc';
import Button from "primevue/button/sfc";

export default {
    name: "ManualAccountSelection",
    components: {
        Dropdown,
        Button,
    },
    props: {
        initialSelection: {
            type: String,
            default: null,
        },
        jobNumber: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            clientAccounts: Object.keys(this.$page.props.client_accounts).map((key) => {
                return {slug: key, label: this.$page.props.client_accounts[key]}
            }),
            selected: this.initialSelection ? this.initialSelection : null,
        }
    },
    methods: {
        handleClick($event) {
            console.log('go');
            this.$inertia.get(route('job.rules.force-account', [this.selected, this.jobNumber]))
        }
    }
}
</script>

<style scoped>

</style>
