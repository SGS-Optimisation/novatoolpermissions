<template>
    <div>
        <jet-action-section>
            <template #title>

                <div :data-id="taxonomyData.id" @click="editTaxonomy(taxonomyData.id, taxonomyName)"
                     class="mt-2 cursor-pointer border-b-2 border-dashed border-transparent hover:border-gray-300
                             transition duration-150 ease-in-out"
                     :class="{'text-red-600': !hasMappings}"
                     :title="mappings"
                >
                    {{ taxonomyName }}
                </div>

                <template v-if="!hasMappings">
                    <a class="text-xs"
                       :href='`/admin/resources/field-mappings/new?viaResource=taxonomies&viaResourceId=${taxonomyData.id}&viaRelationship=mappings`'>
                        Create mapping
                    </a>
                </template>
                <!--                        <div v-if="taxonomyData.taxonomy.mapping && taxonomyData.taxonomy.mapping.id">
                                            <span class="text-xs">
                                                {{ taxonomyData.taxonomy.mapping.api_name }}/{{taxonomyData.taxonomy.mapping.api_action }}//{{ taxonomyData.taxonomy.mapping.field_path }}
                                                </span>
                                        </div>-->


            </template>

            <template #description>
                <i v-if="taxonomyData.terms.length === 0"
                   @click="confirmTaxonomyDeletion(taxonomyData.id, taxonomyName)"
                   class="text-red-600 cursor-pointer hover:text-red-800 p-1 rounded-xl w-5 inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <title>Delete Taxonomy</title>
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2}
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </i>
            </template>

            <template #content>

                <!-- Edit Taxonomy Modal -->
                <jet-dialog-modal :show="editingTaxonomy" @close="cancelEditTaxonomy">
                    <template #title>
                        Edit Vocabulary "{{ taxonomyName }}"
                    </template>

                    <template #content>
                        <div class="mt-4">
                            <jet-input type="text" class="mt-1 block w-3/4"
                                       :value="editForm.name"
                                       v-model="editForm.name"/>

                            <div class="mt-2 flex justify-baseline items-baseline align-bottom">
                                <Checkbox name="search"
                                          inputId="search"
                                          v-model="editForm.use_for_pm_search"
                                          :binary="true"/>
                                <label class="ml-1" for="search">Use for PM Search</label>
                            </div>

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

                <terms-list :initial-terms="taxonomyData.terms"
                            :taxonomy-name="taxonomyName"
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
    </div>
</template>

<script>
import TermsList from "@/Components/RulesLibrary/ClientAccount/TermsList.vue";
import JetActionSection from '@/Jetstream/ActionSection.vue';
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import JetDialogModal from '@/Jetstream/DialogModal.vue';
import JetButton from '@/Jetstream/Button.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetInput from '@/Jetstream/Input.vue'
import JetInputError from '@/Jetstream/InputError.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import Checkbox from 'primevue/checkbox/sfc';

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
        Checkbox,
    },
    props: [
        'initialTaxonomyName',
        'taxonomyData',
        'parentTaxonomy',
        'clientAccount'
    ],
    data() {
        return {
            taxonomyName: this.initialTaxonomyName,

            confirmingTaxonomyDeletion: false,
            deletingTaxonomyId: null,
            deletingTaxonomyName: null,
            deleting: false,

            editingTaxonomy: false,
            editingTaxonomyId: null,
            editingTaxonomyName: null,

            editForm: this.$inertia.form({
                id: null,
                name: this.taxonomyName,
                use_for_pm_search: this.taxonomyData.taxonomy.hasOwnProperty('client_accounts') && this.taxonomyData.taxonomy.client_accounts.length ?
                    this.taxonomyData.taxonomy.client_accounts[0].pivot.use_for_pm_search == 1 : null,
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

    computed: {
        mappings() {
            if (!this.hasMappings) {
                return 'No mapping';
            }
            var mappings_data = [];
            for (let i = 0; i < this.taxonomyData.taxonomy.mappings.length; i++) {
                let mapping = this.taxonomyData.taxonomy.mappings[i];

                mappings_data.push('=>' + mapping.api_name + '/' + mapping.api_action + ':' + mapping.field_path);
            }

            return mappings_data.join("\r\n");
        },

        hasMappings() {
            return this.taxonomyData.taxonomy.mappings.length > 0
                || this.taxonomyData.taxonomy.name === 'Artwork Structure Elements'
                || this.taxonomyData.taxonomy.name === 'PM Section Elements';
        },
    },

    methods: {

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
            var newName = this.editForm.name;
            this.editForm.put(route('library.taxonomies.update', this.editForm.id), {
                preserveScroll: true,
                onSuccess: () => {
                    console.log('taxonomy updated', newName);
                    this.taxonomyName = newName;
                    this.cancelEditTaxonomy();
                }
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

            this.deleteForm.put(route('library.taxonomies.destroy', this.deletingTaxonomyId), {
                preserveScroll: true,
                onSuccess: () => this.resetDeleteTaxonomy(),
            });
        },
    }
}
</script>

<style scoped>

</style>
