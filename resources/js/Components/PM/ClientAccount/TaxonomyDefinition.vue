<template>
    <div class="px-5 pb-12">
        <span class="cursor-pointer p-1 text-xs rounded-md
        bg-blue-300 hover:bg-blue-400"
              @click="creatingTaxonomy=true"
        >
            <i class="text-white fa fa-plus-circle"></i> New Category
        </span>

        <div class="flex flex-col py-2"
             v-for="(taxonomyGroup, index) in taxonomyHierarchy[parentTaxonomy.name].children">

            <template v-for="(taxonomyData, name) in taxonomyGroup">
                <jet-action-section>
                    <template #title>

                        <div @click="editTaxonomy(taxonomyData.id, name)"
                             class="mt-2 cursor-pointer border-b-2 border-dashed border-transparent hover:border-gray-300
                             transition duration-150 ease-in-out"
                             :class="{'text-red-600': !(taxonomyData.taxonomy.mapping && taxonomyData.taxonomy.mapping.id)}"
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
                           class="text-red-600 fa fa-times cursor-pointer hover:bg-red-800 p-1 rounded-xl">
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

                        <term-definition :terms="taxonomyData.terms"
                                         :taxonomy-name="name"
                                         :taxonomy-id="taxonomyData.id"
                                         :client-account="clientAccount"
                        >
                        </term-definition>

                    </template>
                </jet-action-section>

                <!-- Delete Taxonomy Confirmation Modal -->
                <jet-confirmation-modal :show="confirmingTaxonomyDeletion" @close="cancelDeleteTaxonomy">
                    <template #title>
                        Delete Vocabulary
                    </template>

                    <template #content>
                        Are you sure you want to delete the vocabulary "{{ deletingTaxonomyName }}"?
                    </template>

                    <template #footer>
                        <jet-secondary-button @click.native="cancelDeleteTaxonomy">
                            Nevermind
                        </jet-secondary-button>

                        <jet-danger-button class="ml-2" @click.native="deleteTaxonomy"
                                           :class="{ 'opacity-25': deleteForm.processing }"
                                           :disabled="deleteForm.processing">
                            Delete Term
                        </jet-danger-button>
                    </template>
                </jet-confirmation-modal>

            </template>

        </div>
        <span class="cursor-pointer p-1 text-xs rounded-md
        bg-blue-300 hover:bg-blue-400"
              @click="creatingTaxonomy=true"
        >
            <i class="text-white fa fa-plus-circle"></i> New Category
        </span>
    </div>
</template>

<script>
import TermDefinition from "@/Components/PM/ClientAccount/TermDefinition";
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
        TermDefinition,
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

        cancelDeleteTaxonomy() {
            this.confirmingTaxonomyDeletion = false;
            this.deletingTaxonomyId = null;
            this.deletingTaxonomyName = null;
        },

        deleteTaxonomy() {
            console.log('delete taxonomy ' + this.deletingTaxonomyId);

            this.deleteForm.delete(route('pm.taxonomies.destroy', this.deletingTaxonomyId), {
                preserveScroll: true
            }).then(() => {
                this.cancelDeleteTaxonomy();
            });
        },
    }
}
</script>
