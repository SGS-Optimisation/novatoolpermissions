<template>
    <div class="mx-auto sm:px-6 lg:px-8"
    :class="{'bg-red-500': rule.deleted_at}">

        <template>
            <h1 class="text-lg">
                <slot name="title"></slot>
            </h1>
        </template>

        <div class="pt-4">
            <jet-form-section @submitted="pushRuleData" >
                <template #title>
                    Rule definition
                </template>
                <template #description>
                    Write the content of the rule here
                </template>
                <template #form>

                    <div class="col-span-6 sm:col-span-8">
                        <jet-label for="name" value="Name"/>
                        <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name"
                                   autocomplete="name"/>
                        <jet-input-error :message="form.error('name')" class="mt-2"/>
                    </div>

                    <div class="col-span-6 sm:col-span-8">
                        <jet-label for="content" value="Content"/>
                        <vue-editor id="editor"
                                    :editorOptions="editorSettings"
                                    v-model="form.content"
                                    useCustomImageHandler
                                    @image-added="handleImageAdded"/>
                    </div>

                    <input type="hidden" v-model="form.ContentDraftId">

                    <section>

                        <div class="flex flex-row">
                            <div class="flex flex-row px-4">
                                <input id="current-state" v-model="form.state" type="radio" class="hidden" :value="rule.state">
                                <label for="current-state" @click="form.state=rule.state" class="ml-1 initial flex items-center cursor-pointer">
                                    <span class="w-8 h-8 inline-block mr-2 rounded-full border border-grey flex-no-shrink"></span>
                                    {{rule.state}}
                                </label>

                            </div>
                            <div v-for="(state, index) in allowedStates" class="flex flew-row px-4">
                                <template v-if="state !== rule.state">
                                    <input
                                        v-model="form.state"
                                        :name="'state-'+index"
                                        type="radio"
                                        class="hidden"
                                        :value="state">
                                    <label class="ml-1 flex items-center cursor-pointer" :for="'state-'+index"
                                           @click="form.state=state">
                                        <span class="w-8 h-8 inline-block mr-2 rounded-full border border-grey flex-no-shrink"></span>
                                        {{ state }}
                                    </label>
                                </template>
                            </div>
                        </div>
                    </section>

                </template>

                <template #actions>
                    <jet-action-message :on="deleteForm.recentlySuccessful" class="mr-3">Deleted.</jet-action-message>
                    <jet-action-message :on="restoreForm.recentlySuccessful" class="mr-3">Restored.</jet-action-message>
                    <jet-action-message :on="form.recentlySuccessful" class="mr-3">Saved.</jet-action-message>

                    <i v-if="rule.id && !rule.deleted_at"
                       title="Delete Rule"
                       @click="confirmingRuleDeletion=true"
                       class="mr-3 text-red-600 fa fa-trash cursor-pointer hover:bg-red-600 hover:text-white rounded p-2"/>

                    <i v-if="rule.id && rule.deleted_at"
                       title="Restore from Trash"
                       @click="confirmingRuleRestore=true"
                       class="mr-3 text-green-600 fas fa-trash-restore cursor-pointer hover:bg-green-600 hover:text-white rounded p-2"/>

                    <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Save Content
                    </jet-button>
                </template>
            </jet-form-section>
        </div>

        <!-- Delete Rule Confirmation Modal -->
        <jet-confirmation-modal :show="confirmingRuleDeletion" @close="cancelDeleteRule">
            <template #title>
                Delete Rule
            </template>

            <template #content>
                Are you sure you want to delete this rule?
            </template>

            <template #footer>
                <jet-secondary-button @click.native="cancelDeleteRule">
                    Nevermind
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click.native="deleteRule"
                                   :class="{ 'opacity-25': deleteForm.processing }"
                                   :disabled="deleteForm.processing">
                    Delete
                </jet-danger-button>
            </template>
        </jet-confirmation-modal>

        <!-- Restore Rule Confirmation Modal -->
        <jet-confirmation-modal :show="confirmingRuleRestore" @close="cancelRestoreRule">
            <template #title>
                Restore Rule
            </template>

            <template #content>
                Are you sure you want to restore this rule?
            </template>

            <template #footer>
                <jet-secondary-button @click.native="cancelRestoreRule">
                    Nevermind
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click.native="restoreRule"
                                   :class="{ 'opacity-25': restoreForm.processing }"
                                   :disabled="restoreForm.processing">
                    Restore
                </jet-danger-button>
            </template>
        </jet-confirmation-modal>

    </div>
</template>

<script>
import {Quill, VueEditor} from "vue2-editor";
import ImageResize from 'quill-image-resize';

import JetButton from '@/Jetstream/Button'
import JetConfirmationModal from "@/Jetstream/ConfirmationModal";
import JetDangerButton from '@/Jetstream/DangerButton'
import JetFormSection from '@/Jetstream/FormSection'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetLabel from '@/Jetstream/Label'
import JetActionMessage from '@/Jetstream/ActionMessage'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'

Quill.register("modules/imageResize", ImageResize);

export default {
    name: "RuleForm",

    components: {
        VueEditor,

        JetActionMessage,
        JetButton,
        JetConfirmationModal,
        JetDangerButton,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        JetSecondaryButton,
    },

    props: [
        'clientAccount',
        'taxonomyHierarchy',
        'topTaxonomies',
        'rule',
        'states',
        'allowedStates',
    ],

    data() {
        return {
            confirmingRuleDeletion: false,
            confirmingRuleRestore: false,

            editorSettings: {
                modules: {
                    imageResize: {}
                }
            },

            form: this.$inertia.form({
                name: this.rule.name,
                content: this.rule.content,
                ContentDraftId: uuidv4(),
                state: this.rule.state,
            }, {
                bag: 'pushRuleData',
                resetOnSuccess: false,
            }),

            deleteForm: this.$inertia.form({
                id: this.rule.id,
            }, {
                bag: 'deleteRule',
                resetOnSuccess: false,
            }),

            restoreForm: this.$inertia.form({
                id: this.rule.id,
            }, {
                bag: 'restoreRule',
                resetOnSuccess: false,
            }),
        }
    },

    methods: {
        cancelRestoreRule() {
            this.confirmingRuleRestore = false;
        },

        restoreRule() {
            if(this.rule.id) {
                this.restoreForm.put(route('pm.client-account.rules.restore', {clientAccount: this.clientAccount.slug, id: this.rule.id}), {
                    preserveScroll: true
                }).then(() => {
                    this.cancelRestoreRule();
                });
            }
        },

        cancelDeleteRule() {
            this.confirmingRuleDeletion = false;
        },

        deleteRule() {
            if(this.rule.id) {
                this.deleteForm.delete(route('pm.client-account.rules.delete', {clientAccount: this.clientAccount.slug, id: this.rule.id}), {
                    preserveScroll: true
                }).then(() => {
                    this.cancelDeleteRule();
                });
            }
        },

        pushRuleData: function () {

            if (this.rule.id) {
                this.form.put(route('pm.client-account.rules.update', {clientAccount: this.clientAccount.slug, id: this.rule.id}), {
                    preserveScroll: true
                })
            }
            else {
                this.form.post(route('pm.client-account.rules.store', {clientAccount: this.clientAccount.slug}), {
                    preserveScroll: true
                });
            }
        },

        handleImageAdded: function(file, Editor, cursorLocation, resetUploader) {

            console.log('handling image upload');

            var formData = new FormData();
            formData.append('attachment', file);
            formData.append('draftId', this.form.ContentDraftId);

            axios({
                url: "/nova-api/rules/trix-attachment/content",
                method: "POST",
                data: formData,
            })
            .then(result => {
                console.log(result)
                let url = result.data.url; // Get url from response
                Editor.insertEmbed(cursorLocation, "image", url);
            })
            .catch(err => {
                console.log(err);
            });
        }
    },


}

function uuidv4() {
    return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, c =>
        (
            c ^
            (crypto.getRandomValues(new Uint8Array(1))[0] & (15 >> (c / 4)))
        ).toString(16)
    )
}
</script>

<style scoped>

input[type="radio"] + label span {
    transition: background .2s,
    transform .2s;
}

input[type="radio"] + label span:hover,
input[type="radio"] + label:hover span{
    transform: scale(1.2);
}

input[type="radio"]:checked + label span {
    background-color: #3490DC;
    box-shadow: 0px 0px 0px 2px white inset;
}

input[type="radio"]:checked + label{
    color: #3490DC;
}
input[type="radio"] + label.initial{
    color: #0B5EA0;
}
</style>
