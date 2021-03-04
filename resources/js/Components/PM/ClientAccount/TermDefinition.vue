<template>
    <jet-action-section>
        <template #title>
            {{ taxonomyName }}
        </template>

        <template #description>

        </template>

        <template #content>

            <div class="flex flex-row flex-wrap">
                <div class="text-xs mx-2 border-blue-50 border my-1 bg-yellow-50 rounded-xl px-2 py-1"
                     v-for="termData in terms">

                    <i v-if="termData.rulesCount === 0"
                       @click="confirmTermDeletion(termData.id, termData.name)"
                       class="text-red-600 fa fa-times cursor-pointer hover:bg-red-800 p-1 rounded-xl">
                    </i>

                    <span @click="editTerm(termData.id, termData.name)">
                        {{ termData.name }}
                    </span>

                    <span class="text-xs px-3 bg-red-200 text-red-800 rounded-full">

                        <jet-nav-link v-if="termData.rulesCount" :href="route('pm.client-account.rules', {clientAccount: clientAccount.slug }) + `?term=${termData.id}`">
                            <span title="Number of rules using this term. Click to view rules.">
                                {{termData.rulesCount }}
                            </span>
                        </jet-nav-link>
                        <span v-else title="Number of rules using this term. Click to view rules.">
                            {{termData.rulesCount }}
                        </span>
                    </span>
                </div>
            </div>

            <jet-action-message :on="editTermForm.recentlySuccessful" class="mt-3">
                <div class="flex bg-green-200">
                    <span class="p-1">Saved.</span>
                </div>
            </jet-action-message>

            <jet-action-message :on="deleteTermForm.recentlySuccessful" class="mt-3">
                <div class="flex bg-green-200">
                    <span class="p-1">Deleted.</span>
                </div>
            </jet-action-message>


            <!-- Edit Term Modal -->
            <jet-dialog-modal :show="editingTerm" @close="cancelEditTerm">
                <template #title>
                    Edit Term {{editingTermName}}
                </template>

                <template #content>
                    <div class="mt-4">
                        <jet-input type="text" class="mt-1 block w-3/4"
                                   :value="editTermForm.name"
                                   v-model="editTermForm.name" />

                    </div>
                </template>

                <template #footer>
                    <jet-secondary-button @click.native="cancelEditTerm">
                        Nevermind
                    </jet-secondary-button>

                    <jet-button class="ml-2" @click.native="updateTerm"
                                :class="{ 'opacity-25': editTermForm.processing }"
                                :disabled="editTermForm.processing">
                        Save
                    </jet-button>
                </template>
            </jet-dialog-modal>


            <!-- Delete Term Confirmation Modal -->
            <jet-confirmation-modal :show="confirmingTermDeletion" @close="cancelDeleteTerm">
                <template #title>
                    Delete Term
                </template>

                <template #content>
                    Are you sure you want to delete the term "{{ deletingTermName }}"?
                </template>

                <template #footer>
                    <jet-secondary-button @click.native="cancelDeleteTerm">
                        Nevermind
                    </jet-secondary-button>

                    <jet-danger-button class="ml-2" @click.native="deleteTerm"
                                       :class="{ 'opacity-25': deleteTermForm.processing }" :disabled="deleteTermForm.processing">
                        Delete Term
                    </jet-danger-button>
                </template>
            </jet-confirmation-modal>


        </template>
    </jet-action-section>
</template>

<script>
import JetActionMessage from '@/Jetstream/ActionMessage'
import JetNavLink from "@/Jetstream/NavLink";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal";
import JetDialogModal from '@/Jetstream/DialogModal'
import JetActionSection from '@/Jetstream/ActionSection'
import JetButton from '@/Jetstream/Button'
import JetDangerButton from '@/Jetstream/DangerButton'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'

export default {
    name: "TermDefinition",
    props: [
        'taxonomyName',
        'terms',
        'taxonomyId',
        'clientAccount'
    ],
    components: {
        JetActionMessage,
        JetNavLink,
        JetActionSection,
        JetButton,
        JetConfirmationModal,
        JetDialogModal,
        JetDangerButton,
        JetInput,
        JetInputError,
        JetSecondaryButton,
    },

    data() {
        return {
            confirmingTermDeletion: false,
            deletingTermId: null,
            deletingTermName: null,
            deleting: false,

            editingTerm: false,
            editingTermId: null,
            editingTermName: null,

            deleteTermForm: this.$inertia.form({}, {
                bag: 'deleteTerm'
            }),

            editTermForm: this.$inertia.form({
                termId: null,
                name: null,

            }, {
                bag: 'editTerm'
            })
        }
    },

    mounted() {

    },

    methods: {
        editTerm(id, name) {
            this.editingTerm = true;
            this.editTermForm.termId = id;
            this.editTermForm.name = name;
            this.editingTermName = name; //keep original term for display in modal title
        },

        cancelEditTerm() {
            this.editingTerm = false;
            this.editingTermName = null
            this.editTermForm.termId = null;
            this.editTermForm.name = null;
        },

        updateTerm() {
            console.log('updating term ' + this.editTermForm.termId + ' from ' + this.editingTermName + ' to ' + this.editTermForm.name);

            this.editTermForm.put(route('pm.terms.update', this.editTermForm.termId), {
                preserveScroll: true
            }).then(() => {
                this.cancelEditTerm();
            });
        },


        confirmTermDeletion(id, name) {
            console.log('confirming deleting');
            this.confirmingTermDeletion = true;
            this.deletingTermId = id;
            this.deletingTermName = name;
        },

        cancelDeleteTerm() {
            this.confirmingTermDeletion = false;
            this.deletingTermId = null;
            this.deletingTermName = null;
        },

        deleteTerm(id) {
            console.log('delete term ' + id);

            this.deleteTermForm.delete(route('pm.terms.destroy', this.deletingTermId), {
                preserveScroll: true
            }).then(() => {
                this.cancelDeleteTerm();
            });
        }
    },
}
</script>

<style scoped>

</style>
