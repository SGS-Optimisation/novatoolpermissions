<template>
    <div>
        <div class="px-5 pb-12">
            <span class="cursor-pointer p-1 text-xs rounded-md bg-blue-300 hover:bg-blue-400"
                  v-if="$page.props.user_permissions.manageTaxonomies"
                  @click="creatingTaxonomy=true"
            >
                <i class="text-white w-5 inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="pt-3 h-6 w-6" viewBox="0 0 18 18" fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                              clip-rule="evenodd"/></svg>
                </i> New Category
            </span>

                <div class="flex flex-col py-2"
                     v-for="(taxonomyGroup, index) in taxonomyHierarchy[parentTaxonomy.name].children">

                    <template v-for="(taxonomyData, name) in taxonomyGroup">
                        <taxonomy-definition
                            :taxonomy-name="name"
                            :taxonomy-data="taxonomyData"
                            :client-account="clientAccount"
                            :parent-taxonomy="parentTaxonomy"
                        />

                    </template>

                </div>

                <span class="cursor-pointer p-1 text-xs rounded-md bg-blue-300 hover:bg-blue-400"
                      v-if="$page.props.user_permissions.manageTaxonomies"
                      @click="creatingTaxonomy=true"
                >
                <i class="text-white w-5 inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="pt-3 h-6 w-6" viewBox="0 0 18 18" fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                              clip-rule="evenodd"/>
                    </svg>
                </i> New Category
            </span>
        </div>

        <!--  Create Taxonomy Modal -->
        <jet-dialog-modal :show="creatingTaxonomy" @close="cancelCreateTaxonomy">
            <template #title>
                New Category in {{ parentTaxonomy.name }}
            </template>

            <template #content>
                <div class="mt-4">
                    <jet-input type="text" class="mt-1 block w-3/4"
                               autofocus
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
    </div>
</template>

<script>
import JetActionSection from '@/Jetstream/ActionSection';
import JetConfirmationModal from "@/Jetstream/ConfirmationModal";
import JetDialogModal from '@/Jetstream/DialogModal';
import JetButton from '@/Jetstream/Button'
import JetDangerButton from '@/Jetstream/DangerButton'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import TaxonomyDefinition from "./TaxonomyDefinition";

export default {
    name: "ChildTaxonomiesSection",

    components: {
        TaxonomyDefinition,
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
                preserveScroll: true,
                onSuccess: () => this.cancelCreateTaxonomy(),
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
                preserveScroll: true,
                onSuccess: () => this.cancelEditTaxonomy(),
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
                preserveScroll: true,
                onSuccess: () => this.resetDeleteTaxonomy(),
            });
        },
    }
}
</script>
