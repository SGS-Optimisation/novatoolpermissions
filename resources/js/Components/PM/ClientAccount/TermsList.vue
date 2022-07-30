<template>
    <div>
        <div class="flex flex-row flex-wrap">
            <div class="text-xs mx-2 border-blue-50 border my-1 bg-yellow-50 rounded-xl px-2 py-1"
                 v-for="termData in displayedTerms">

                <i v-if="termData.clientRulesCount === 0"
                   @click="confirmTermDeletion(termData.id, termData.name)"
                   class="cursor-pointer text-red-500 hover:text-red-800 pr-1 rounded-xl inline-block w-5">
                    <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2}
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </i>

                <span @click="editTerm(termData.id, termData.name)"
                      class="mt-2 cursor-pointer
                          border-b-2 border-dashed border-transparent hover:border-gray-300
                          transition duration-150 ease-in-out">
                        {{ termData.name }}
                </span>

                <span v-if="termData.globalRulesCount" class="text-xs px-2 bg-red-200 text-red-800 rounded-full">
                    <jet-nav-link
                        :href="route('pm.client-account.rules.index', {clientAccount: clientAccount.slug }) + `?term=${termData.id}`">
                        <span title="Number of rules using this term for this client account. Click to view rules.">
                            {{ termData.clientRulesCount }}
                        </span>
                        <span v-if="termData.clientRulesCount !== termData.globalRulesCount"
                              title="Number of rules using this term across all client accounts. Click to view rules.">
                            &nbsp;({{ termData.globalRulesCount }})
                        </span>
                    </jet-nav-link>
                </span>
            </div>
            <div v-if="shouldTruncate && truncateMode" class="w-full text-center">
                <a @click="truncateMode=false" class="cursor-pointer text-white block bg-green-200 hover:bg-green-400">Show
                    all</a>
            </div>
            <div v-if="shouldTruncate && !truncateMode" class="w-full text-center">
                <a @click="truncateMode=true" class="cursor-pointer text-white block bg-green-200 hover:bg-green-400">Show
                    less</a>
            </div>

            <i @click="openCreatingTerm"
               title="Add single term"
               v-if="$page.props.user_permissions.manageTerms"
               class="cursor-pointer pt-3 align-middle text-blue-400 hover:text-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                          clip-rule="evenodd"/>
                </svg>
            </i>

            <i @click="openCreatingMultipleTerm"
               title="Add multiple terms"
               v-if="$page.props.user_permissions.manageTerms"
               class="cursor-pointer pt-3 align-middle text-blue-400 hover:text-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
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
                New {{(creatingMultiple? 'Terms' : 'Term')}} in {{ taxonomyName }}
            </template>

            <template #content>
                <div class="mt-4">

                    <jet-input v-if="!creatingMultiple"
                               type="text" class="mt-1 block w-3/4"
                               autofocus
                               ref="termName"
                               :value="createTermForm.name"
                               v-model="createTermForm.name"/>

                    <template v-if="creatingMultiple">
                        Separate each term by a new line
                    <textarea class="w-full"
                              rows="20"
                               v-model="createTermForm.names"

                    />
                    </template>

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

            <template #content v-if="editingTerm">
                <div class="mt-4">
                    <jet-label for="term-name" value="Name"/>
                    <jet-input id="term-name" type="text" class="mt-1 block w-3/4 disabled:opacity-50"
                               :disabled="!$page.props.user_permissions.manageTerms"
                               :value="editTermForm.name"
                               v-model="editTermForm.name"/>

                </div>
                <div class="mt-4">
                    <jet-label value="Aliases"/>
                    <p class="text-xs">Separate entry with <b>;</b> or by pressing return</p>
                    <vue-tags-input
                        v-model="tag"
                        :tags="tags"
                        :save-on-key="saveOnKey"
                        :add-on-key="saveOnKey"
                        @tags-changed="newTags => tags = newTags"
                    />


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
import JetActionMessage from '@/Jetstream/ActionMessage.vue'
import JetNavLink from "@/Jetstream/NavLink.vue";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import JetDialogModal from '@/Jetstream/DialogModal.vue'
import JetActionSection from '@/Jetstream/ActionSection.vue'
import JetButton from '@/Jetstream/Button.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetFormSection from '@/Jetstream/FormSection.vue'
import JetInput from '@/Jetstream/Input.vue'
import JetInputError from '@/Jetstream/InputError.vue'
import JetLabel from '@/Jetstream/Label.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import VueTagsInput from '@sipec/vue3-tags-input';

export default {
    name: "TermsList",
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
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        JetSecondaryButton,
        VueTagsInput,
    },

    data() {
        return {
            creatingTerm: false,
            creatingMultiple: false,

            confirmingTermDeletion: false,
            deletingTermId: null,
            deletingTermName: null,
            deleting: false,

            truncateMode: false,

            editingTerm: false,
            editingTermId: null,
            editingTermName: null,

            createTermForm: this.$inertia.form({
                clientAccountId: this.clientAccount.id,
                taxonomyId: this.taxonomyId,
                name: "",
                names: "",
                multiple: false,

            }, {
                bag: 'createTerm'
            }),

            tag: '',
            tags: [],
            saveOnKey: [13, ';'],

            editTermForm: this.$inertia.form({
                termId: null,
                clientAccountId: this.clientAccount.id,
                name: null,
                aliases: [],

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

    computed: {
        shouldTruncate() {
            return this.terms.length > 24;
        },

        displayedTerms() {
            if (this.shouldTruncate && this.truncateMode) {
                return _.slice(this.sortedTerms, 0, 24);
            } else {
                return this.sortedTerms;
            }
        },

        sortedTerms() {
            return _.sortBy(this.terms, function (term) {
                return term.name
            });
        }

    },

    mounted() {
        if (this.shouldTruncate) {
            this.truncateMode = true;
        }
    },

    methods: {
        openCreatingTerm() {
            this.creatingTerm = true;
            this.creatingMultiple = false;
            this.createTermForm.multiple = false;
        },

        openCreatingMultipleTerm() {
            this.creatingTerm = true;
            this.creatingMultiple = true;
            this.createTermForm.multiple = true;
        },

        cancelCreateTerm() {
            this.creatingTerm = false;
            this.createTermForm.name = null;
        },

        storeTerm() {
            console.log('create term ' + this.createTermForm.name + ' for taxonomy id ' + this.createTermForm.taxonomyId);

            this.createTermForm.post(route('pm.terms.store'), {
                preserveScroll: true,
                onSuccess: () => this.cancelCreateTerm(),
            });
        },


        editTerm(id, name) {
            const term = _.find(this.terms, {id: id});
            this.editingTerm = true;
            this.editTermForm.termId = id;
            this.editTermForm.name = term.name;
            this.editTermForm.aliases = [];
            this.tags = term.aliases.map(item => {
                return {text: item}
            });
            this.editingTermName = name; //keep original term for display in modal title
        },

        cancelEditTerm() {
            this.editingTerm = false;
            this.editingTermName = null
            this.editTermForm.termId = null;
            this.editTermForm.name = null;
            this.editTermForm.aliases = [];
            this.tags = [];
        },

        updateTerm() {
            console.log('updating term ' + this.editTermForm.termId + ' from ' + this.editingTermName + ' to ' + this.editTermForm.name);

            this.editTermForm.aliases = this.tags.map((item) => item.text);

            this.editTermForm.put(route('pm.terms.update', this.editTermForm.termId), {
                preserveScroll: true,
                onSuccess: () => this.cancelEditTerm(),
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

            this.deleteTermForm.put(route('pm.terms.destroy', this.deletingTermId), {
                preserveScroll: true,
                onSuccess: () => this.cancelDeleteTerm(),
            });
        }
    },
}
</script>

<style scoped>

</style>
