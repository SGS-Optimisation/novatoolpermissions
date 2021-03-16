<template>
    <div>
        <div class="flex flex-row flex-wrap">
            <div class="text-xs mx-2 border-blue-50 border my-1 bg-yellow-50 rounded-xl px-2 py-1"
                 v-for="termData in _.sortBy(terms, function(term) {return term.name})">

                <i v-if="termData.clientRulesCount === 0"
                   @click="confirmTermDeletion(termData.id, termData.name)"
                   class="text-red-600 fa fa-times cursor-pointer hover:bg-red-800 p-1 rounded-xl">
                </i>

                <span @click="editTerm(termData.id, termData.name)"
                      class="mt-2 cursor-pointer
                          border-b-2 border-dashed border-transparent hover:border-gray-300
                          transition duration-150 ease-in-out">
                        {{ termData.name }}
                </span>

                <span v-if="termData.globalRulesCount" class="text-xs px-2 bg-red-200 text-red-800 rounded-full">
                    <jet-nav-link
                        :href="route('pm.client-account.rules', {clientAccount: clientAccount.slug }) + `?term=${termData.id}`">
                        <span title="Number of rules using this term for this client account. Click to view rules.">
                            {{ termData.clientRulesCount }}
                        </span>
                        <span v-if="termData.clientRulesCount !== termData.globalRulesCount" title="Number of rules using this term across all client accounts. Click to view rules.">
                            &nbsp;({{ termData.globalRulesCount }})
                        </span>
                    </jet-nav-link>
                </span>
            </div>

            <i @click="creatingTerm=true"
               class="cursor-pointer pt-3 align-middle text-blue-400 hover:text-blue-700 fa fa-plus-circle">
            </i>
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


        <!--  Create Term Modal -->
        <jet-dialog-modal :show="creatingTerm" @close="cancelCreateTerm">
            <template #title>
                New Term in {{ taxonomyName }}
            </template>

            <template #content>
                <div class="mt-4">
                    <jet-input type="text" class="mt-1 block w-3/4"
                               :value="createTermForm.name"
                               v-model="createTermForm.name"/>

                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click.native="cancelCreateTerm">
                    Nevermind
                </jet-secondary-button>

                <jet-button class="ml-2" @click.native="storeTerm"
                            :class="{ 'opacity-25': createTermForm.processing }"
                            :disabled="createTermForm.processing">
                    Save
                </jet-button>
            </template>
        </jet-dialog-modal>


        <!-- Edit Term Modal -->
        <jet-dialog-modal :show="editingTerm" @close="cancelEditTerm">
            <template #title>
                Edit Term {{ editingTermName }}
            </template>

            <template #content>
                <div class="mt-4">
                    <jet-input type="text" class="mt-1 block w-3/4"
                               :value="editTermForm.name"
                               v-model="editTermForm.name"/>

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
                                   :class="{ 'opacity-25': deleteTermForm.processing }"
                                   :disabled="deleteTermForm.processing">
                    Delete Term
                </jet-danger-button>
            </template>
        </jet-confirmation-modal>
    </div>
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
            creatingTerm: false,

            confirmingTermDeletion: false,
            deletingTermId: null,
            deletingTermName: null,
            deleting: false,

            editingTerm: false,
            editingTermId: null,
            editingTermName: null,

            createTermForm: this.$inertia.form({
                clientAccountId: this.clientAccount.id,
                taxonomyId: this.taxonomyId,
                name: "",

            }, {
                bag: 'createTerm'
            }),

            editTermForm: this.$inertia.form({
                termId: null,
                clientAccountId: this.clientAccount.id,
                name: null,

            }, {
                bag: 'editTerm'
            }),

            deleteTermForm: this.$inertia.form({
                clientAccountId: this.clientAccount.id,
            }, {
                bag: 'deleteTerm'
            }),
        }
    },

    mounted() {

    },

    methods: {
        createTerm() {
            this.creatingTerm = true;
        },

        cancelCreateTerm() {
            this.creatingTerm = false;
            this.createTermForm.name = null;
        },

        storeTerm() {
            console.log('create term ' + this.createTermForm.name + ' for taxonomy id ' + this.createTermForm.taxonomyId);

            this.createTermForm.post(route('pm.terms.store'), {
                preserveScroll: true
            }).then(() => {
                this.cancelCreateTerm();
            });
        },


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

        deleteTerm() {
            console.log('delete term ' + this.deletingTermId);

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
