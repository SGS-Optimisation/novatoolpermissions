<template>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="sm:rounded-lg">
                <div class="pt-4">
                    <jet-form-section @submitted="pushClientData">
                        <template #title>
                            Client info
                        </template>
                        <template #description>

                        </template>
                        <template #form>
                            <div v-if="!client.id" class="col-span-3 sm:col-span-4">
                                <jet-label for="name" value="Name"/>
                                <customer-selector @customerSelected="setClient"/>
                            </div>

                            <div v-else class="col-span-3 sm:col-span-4">
                                <jet-label for="name" value="Name"/>
                                <jet-input id="name" type="text" required
                                           v-model="form.name"
                                           @input="updateName"
                                           class="mt-1 block w-full"/>
                                <jet-input-error :message="form.errors.name" class="mt-2"/>
                            </div>

                            <div class="col-span-3 sm:col-span-4">
                                <jet-label for="slug" value="Page address"/>
                                <jet-input id="slug" type="text" disabled
                                           class="mt-1 block w-full"
                                           v-model="form.slug"
                                />
                                <jet-input-error :message="form.errors.slug" class="mt-2"/>
                            </div>

                            <div class="col-span-6 sm:col-span-8">
                                <jet-label for="alias" value="Alias"/>
                                <textarea class="form-input rounded-md shadow-sm mt-1 block w-full" id="alias"
                                          placeholder="One item per line"
                                          v-model="form.alias"></textarea>
                                <jet-input-error :message="form.errors.alias" class="mt-2"/>
                            </div>

                            <div class="col-span-3 sm:col-span-4">
                                <jet-label for="image" value="Logo"/>
                                <input type="file" id="image" accept="image/*"
                                       @change="addFile($event.target.files);"
                                />
                            </div>

                            <div v-if="creatingMode && $page.props.user_permissions.createTeamsOnBehalfOfUsers"
                                 class="col-span-3 sm:col-span-4">
                                <MultiSelect required v-model="teamOwner" :options="users" :filter="true"
                                             :selectionLimit=1
                                             @change="changeOwner"
                                             optionLabel="name" placeholder="Select Team Owner" />
                            </div>

                            <account-structure-selection v-if="accountStructure"
                                                         v-model="form.taxonomy"
                                                         :account-structure="accountStructure"/>

                        </template>

                        <template #actions>
                            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                                Saved.
                            </jet-action-message>

                            <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Save
                            </jet-button>
                        </template>
                    </jet-form-section>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import JetButton from '@/Jetstream/Button'
import JetFormSection from '@/Jetstream/FormSection'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetLabel from '@/Jetstream/Label'
import JetActionMessage from '@/Jetstream/ActionMessage'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import CustomerSelector from "@/Components/PM/ClientAccount/CustomerSelector";
import AccountStructureSelection from "@/Components/PM/ClientAccount/AccountStructureSelection";
import MultiSelect from 'primevue/multiselect/sfc'

export default {
    name: "ClientAccountForm",

    components: {
        JetActionMessage,
        JetButton,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        JetSecondaryButton,
        CustomerSelector,
        AccountStructureSelection,
        MultiSelect,
    },

    props: [
        'client',
        'accountStructure',
        'users',
        'creatingMode'
    ],

    data() {
        return {
            teamOwner: null,

            form: this.$inertia.form({
                name: this.client.name,
                slug: this.client.slug,
                alias: this.client.alias,
                image: this.client.image,
                taxonomy: [],
                owner_id: null,
            }, {
                //bag: 'pushClientData',
                resetOnSuccess: false,
            }),
        }
    },

    methods: {
        changeOwner(event){
            this.form.owner_id = event.value.length ? event.value[0].id : null;
        },

        setClient(name, aliases) {
            this.form.name = name;
            this.form.alias = aliases ? aliases.join("\r\n") : '';
            this.updateName();
        },

        updateName() {
            this.form.slug = _.kebabCase(this.form.name.replace(/&/g, '-and-'));
        },

        addFile(files) {
            if (!files.length) return;
            this.form.image = files[0];
        },

        pushClientData: function () {

            if (this.client.id) {
                this.form.post(route('pm.client-account.update', {clientAccount: this.client.slug}), {
                    preserveScroll: true
                })
            } else {
                this.form.post(route('pm.client-account.store'), {
                    preserveScroll: true
                })
            }
        },
    },
}
</script>

<style scoped>

</style>
