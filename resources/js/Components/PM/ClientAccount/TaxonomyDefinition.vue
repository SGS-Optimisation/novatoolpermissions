<template>
    <div class="px-5 pb-12">
        <span class="cursor-pointer p-1 text-xs rounded-md
        bg-blue-300 hover:bg-blue-400"
              v-if="$page.user_permissions.manageTaxonomies"
              @click="creatingTaxonomy=true"
        >
            <i class="text-white w-5 inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="pt-3 h-6 w-6" viewBox="0 0 18 18" fill="currentColor">
  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
</svg>
            </i> New Category
        </span>

        <div class="flex flex-col py-2"
             v-for="(taxonomyGroup, index) in taxonomyHierarchy[parentTaxonomy.name].children">

            <template v-for="(taxonomyData, name) in taxonomyGroup">
                <jet-action-section>
                    <template #title>

                        <div :data-id="taxonomyData.id" @click="editTaxonomy(taxonomyData.id, name)"
                             class="mt-2 cursor-pointer border-b-2 border-dashed border-transparent hover:border-gray-300
                             transition duration-150 ease-in-out"
                             :class="{'text-red-600': taxonomyData.taxonomy.requiresMapping && !(taxonomyData.taxonomy.mapping && taxonomyData.taxonomy.mapping.id)}"
                             :title="(taxonomyData.taxonomy.mapping && taxonomyData.taxonomy.mapping.id) ?
                        taxonomyData.taxonomy.mapping.api_name +'/'+ taxonomyData.taxonomy.mapping.api_action + '//' +taxonomyData.taxonomy.mapping.field_path: 'No mapping'">
                            {{ name }}
                        </div>
<!--                        <div v-if="taxonomyData.taxonomy.mapping && taxonomyData.taxonomy.mapping.id">
                            <span class="text-xs">
                                {{ taxonomyData.taxonomy.mapping.api_name }}/{{taxonomyData.taxonomy.mapping.api_action }}//{{ taxonomyData.taxonomy.mapping.field_path }}
                                </span>
                        </div>-->


                    </template>

                    <template #description>
                        <i v-if="taxonomyData.terms.length === 0"
                           @click="confirmTaxonomyDeletion(taxonomyData.id, name)"
                           class="text-red-600 cursor-pointer hover:text-red-800 p-1 rounded-xl w-5 inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <title>Delete Taxonomy</title>
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </i>
                    </template>

                    <template #content>

                        <!--  Create Taxonomy Modal -->
                        <jet-dialog-modal :show="creatingTaxonomy" @close="cancelCreateTaxonomy">
                            <template #title>
                                New Category in {{ parentTaxonomy.name }}
                            </template>

                            <template #content>
                                <div class="mt-4">
                                    <jet-input type="text" class="mt-1 block w-3/4"
                                               :value="createForm.name"
                                               v-model="createForm.name"/>

                                </div>
                            </template>

                            <template #footer>
                                <jet-secondary-button @click.native="cancelCreateTaxonomy">
                                    Nevermind
                                </jet-secondary-button>

                                <jet-button class="ml-2" @click.native="storeTaxonomy"
                                            :class="{ 'opacity-25': createForm.processing }"
                                            :disabled="createForm.processing">
                                    Save
                                </jet-button>
                            </template>
                        </jet-dialog-modal>

                        <!-- Edit Taxonomy Modal -->
                        <jet-dialog-modal :show="editingTaxonomy" @close="cancelEditTaxonomy">
                            <template #title>
                                Edit Vocabulary "{{ editingTaxonomyName }}"
                            </template>

                            <template #content>
                                <div class="mt-4">
                                    <jet-input type="text" class="mt-1 block w-3/4"
                                               :value="editForm.name"
                                               v-model="editForm.name"/>

                                </div>
                            </template>

                            <template #footer>
                                <jet-secondary-button @click.native="cancelEditTaxonomy">
                                    Nevermind
                                </jet-secondary-button>

                                <jet-button class="ml-2" @click.native="updateTaxonomy"
                                            :class="{ 'opacity-25': editForm.processing }"
                                            :disabled="editForm.processing">
                                    Save
                                </jet-button>
                            </template>
                        </jet-dialog-modal>

                        <terms-list :terms="taxonomyData.terms"
                                    :taxonomy-name="name"
                                    :taxonomy-id="taxonomyData.id"
                                    :client-account="clientAccount"
                        >
                        </terms-list>

                    </template>
                </jet-action-section>

                <!-- Delete Taxonomy Confirmation Modal -->
                <jet-confirmation-modal :show="confirmingTaxonomyDeletion" @close="resetDeleteTaxonomy">
                    <template #title>
                        Delete Vocabulary
                    </template>

                    <template #content>
                        Are you sure you want to delete the vocabulary "{{ deletingTaxonomyName }}"?
                    </template>

                    <template #footer>
                        <jet-secondary-button @click.native="resetDeleteTaxonomy">
                            Nevermind
                        </jet-secondary-button>

                        <jet-danger-button class="ml-2" @click.native="deleteTaxonomy"
                                           :class="{ 'opacity-25': deleteForm.processing }"
                                           :disabled="deleteForm.processing">
                            Delete
                        </jet-danger-button>
                    </template>
                </jet-confirmation-modal>

            </template>

        </div>
        <span class="cursor-pointer p-1 text-xs rounded-md
        bg-blue-300 hover:bg-blue-400"
              v-if="$page.user_permissions.manageTaxonomies"
              @click="creatingTaxonomy=true"
        >
            <i class="text-white w-5 inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="pt-3 h-6 w-6" viewBox="0 0 18 18" fill="currentColor">
  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
</svg>
            </i> New Category
        </span>
    </div>
</template>

<script>
import TermsList from "@/Components/PM/ClientAccount/TermsList";
import JetActionSection from '@/Jetstream/ActionSection';
import JetConfirmationModal from "@/Jetstream/ConfirmationModal";
import JetDialogModal from '@/Jetstream/DialogModal';
import JetButton from '@/Jetstream/Button'
import JetDangerButton from '@/Jetstream/DangerButton'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'

export default {
    name: "TaxonomyDefinition",

    components: {
        TermsList,
        JetActionSection,
        JetButton,
        JetConfirmationModal,
        JetDialogModal,
        JetDangerButton,
        JetInput,
        JetInputError,
        JetSecondaryButton,
    },

    props: [
        'parentTaxonomy',
        'taxonomyHierarchy',
        'clientAccount'
    ],

    data() {
        return {
            creatingTaxonomy: false,

            confirmingTaxonomyDeletion: false,
            deletingTaxonomyId: null,
            deletingTaxonomyName: null,
            deleting: false,

            editingTaxonomy: false,
            editingTaxonomyId: null,
            editingTaxonomyName: null,

            createForm: this.$inertia.form({
                name: '',
                clientAccountId: this.clientAccount.id,
                parentTaxonomyId: this.parentTaxonomy.id,
            }, {
                bag: 'storeTaxonomy'
            }),

            editForm: this.$inertia.form({
                id: null,
                name: '',
                clientAccountId: this.clientAccount.id,
            }, {
                bag: 'updateTaxonomy'
            }),

            deleteForm: this.$inertia.form({
                clientAccountId: this.clientAccount.id,
            }, {
                bag: 'deleteTaxonomy'
            }),


        }
    },

    methods: {
        storeTaxonomy() {

            console.log('create taxonomy ' + this.createForm.name);

            this.createForm.post(route('pm.taxonomies.store'), {
                preserveScroll: true
            }).then(() => {
                this.cancelCreateTaxonomy();
            });
        },

        cancelCreateTaxonomy() {
            this.creatingTaxonomy = false;
            this.createForm.name = null;
        },

        editTaxonomy(id, name) {
            console.log(`editing taxonomy ${id}: ${name}`);
            this.editingTaxonomy = true;
            this.editForm.id = id;
            this.editForm.name = name;
            this.editingTaxonomyName = name; //keep original taxonomy for display in modal title
        },

        cancelEditTaxonomy() {
            this.editingTaxonomy = false;
            this.editingTaxonomyName = null
            this.editForm.id = null;
            this.editForm.name = null;
        },

        updateTaxonomy() {
            console.log('updating taxonomy ' + this.editForm.id);

            this.editForm.put(route('pm.taxonomies.update', this.editForm.id), {
                preserveScroll: true
            }).then(() => {
                this.cancelEditTaxonomy();
            });
        },

        confirmTaxonomyDeletion(id, name) {
            console.log('confirming deleting ' + id + ' : ' + name);
            this.confirmingTaxonomyDeletion = true;
            this.deletingTaxonomyId = id;
            this.deletingTaxonomyName = name;
        },

        resetDeleteTaxonomy() {
            this.confirmingTaxonomyDeletion = false;
            this.deletingTaxonomyId = null;
            this.deletingTaxonomyName = null;
        },

        deleteTaxonomy() {
            console.log('delete taxonomy ' + this.deletingTaxonomyId);

            this.deleteForm.put(route('pm.taxonomies.destroy', this.deletingTaxonomyId), {
                preserveScroll: true
            }).then(() => {
                this.resetDeleteTaxonomy();
            });
        },
    }
}
</script>
