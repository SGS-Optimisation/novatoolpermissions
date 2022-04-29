<template>
    <div class="mx-auto sm:px-6 lg:px-8"
         :class="{'bg-red-500': rule.deleted_at}">


        <h1 class="text-lg">
            <slot name="title"></slot>
        </h1>


        <div class="pt-4">
            <jet-form-section @submitted="pushRuleData">
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
                        <jet-input-error :message="form.errors.name" class="mt-2"/>
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

                    <section class="col-span-6">
                        <div class="flex flex-col justify-between">
                            <div class="flex flex-row">
                                <div v-for="(state, index) in stateObjects" class="flex flew-row px-4">
                                    <input
                                        v-model="form.state"
                                        :name="'state-'+index"
                                        type="radio"
                                        class="hidden"
                                        :disabled="state.requiresNoError && hasTaggingError"
                                        :value="state.name">
                                    <label :for="'state-'+index"
                                           class="ml-1 flex items-center cursor-pointer disabled:bg-gray-300"
                                           @click="stateChanged(state)">
                                            <span
                                                class="w-8 h-8 inline-block mr-2 rounded-full border border-grey flex-no-shrink"></span>
                                        {{ state.name }}
                                    </label>
                                </div>
                                <InlineMessage v-if="hasTaggingError && refusedChange"
                                               @close="refusedChange=false"
                                               severity="warn">
                                    There are tagging errors
                                </InlineMessage>
                            </div>
                            <div class="flex flex-col mt-3" v-if="requiresAssignees">
                                <h4 class="h4">Reviewers</h4>
                                <multiselect :searchable=true
                                             v-model="form.assignees"
                                             ref="assigneeSelector"
                                             mode="tags"
                                             :options="publishers"
                                             label="label"
                                             valueProp="value"
                                />

                                <div class="flex flex-col mt-2" v-if="suggestedReviewers.length">
                                    <h5 class="h6">Suggested</h5>
                                    <div class="flex flex-row flex-wrap">
                                        <span v-for="user in suggestedReviewers"
                                              class="text-xs rounded-xl m-1 px-1 flex cursor-pointer"
                                              :class="{
                                                    'bg-yellow-100': user.suggestion_level === 1,
                                                    'bg-blue-200': user.suggestion_level > 1,
                                              }"
                                              :title="user.suggestion_level > 1 ? 'Contributed to this rule' :'Is a publisher for the concerned jobteam'"
                                              @click.once="assignSuggestedPublisher(user)">
                                            {{ user.label }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                </template>

                <template #actions>
                    <jet-action-message :on="deleteForm.recentlySuccessful" class="mr-3">Deleted.</jet-action-message>
                    <jet-action-message :on="restoreForm.recentlySuccessful" class="mr-3">Restored.</jet-action-message>
                    <jet-action-message :on="form.recentlySuccessful" class="mr-3">Saved.</jet-action-message>

                    <span v-if="rule.id && !rule.deleted_at"
                          title="Delete Rule"
                          @click="confirmingRuleDeletion=true"
                          class="mr-3 text-red-600 cursor-pointer hover:bg-red-600 hover:text-white rounded p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                  d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </span>

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
import {defineComponent} from "vue";
import {Quill, VueEditor} from "vue3-editor";
import ImageResize from 'quill-image-resize';
import InlineMessage from 'primevue/inlinemessage/sfc';
import Message from 'primevue/message/sfc';
import Multiselect from '@vueform/multiselect';
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

export default defineComponent({
    name: "RuleForm",
    components: {
        VueEditor,
        InlineMessage,
        Message,
        Multiselect,
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
        'stateObjects',
        'publishers',
        'defaultPublishers',
        'initialAssignees',
    ],

    data() {
        return {
            confirmingRuleDeletion: false,
            confirmingRuleRestore: false,

            requiresAssignees: _.find(this.stateObjects, (s) => s.name == this.rule.state).requiresAssignee,

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
                assignees: _.map(this.initialAssignees, 'value'),
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

            refusedChange: false,
        }
    },

    computed: {
        suggestedReviewers() {
            return _.orderBy(_.filter(this.defaultPublishers, (p) => !this.form.assignees.includes(p.value)), 'suggestion_level', 'desc');
        },

        hasTaggingError() {
            let noTerms = (!this.rule.hasOwnProperty('terms')
                || this.rule.terms.length === 0
                || (this.rule.terms.length === 1 && itemElem.terms[0].name === 'No term')
            );

            if (noTerms) {
                return true;
            }

            let hasStructure = _.some(this.rule.terms, function (term) {
                return term.hasOwnProperty('taxonomy') && term.taxonomy.name === 'Artwork Structure Elements';
            });

            let atLeastOneTermPerRootTaxonomy = _.uniq(_.map(this.rule.terms, function (term) {
                return term.hasOwnProperty('taxonomy') && term.taxonomy.parent.name;
            })).length === this.topTaxonomies.length;

            return !hasStructure || !atLeastOneTermPerRootTaxonomy;
        }
    },

    methods: {
        stateChanged(state) {
            if(state.requiresNoError && this.hasTaggingError) {
                this.refusedChange = true;
                return;
            }

            this.form.state = state.name;
            this.requiresAssignees = state.requiresAssignee;
            this.refusedChange = false;
        },

        assignSuggestedPublisher(assignee) {
            console.log('suggested assignee added', assignee.value);
            this.$refs.assigneeSelector.select(assignee.value);
        },

        cancelRestoreRule() {
            this.confirmingRuleRestore = false;
        },

        restoreRule() {
            if (this.rule.id) {
                this.restoreForm.put(route('pm.client-account.rules.restore', {
                    clientAccount: this.clientAccount.slug,
                    id: this.rule.id
                }), {
                    preserveScroll: true,
                    onSuccess: () => this.cancelRestoreRule(),
                });
            }
        },

        cancelDeleteRule() {
            this.confirmingRuleDeletion = false;
        },

        deleteRule() {
            if (this.rule.id) {
                this.deleteForm.delete(route('pm.client-account.rules.delete', {
                    clientAccount: this.clientAccount.slug,
                    id: this.rule.id
                }), {
                    preserveScroll: true
                });
            }
        },

        pushRuleData: function () {

            if (this.rule.id) {
                this.form.put(route('pm.client-account.rules.update', {
                    clientAccount: this.clientAccount.slug,
                    id: this.rule.id
                }), {
                    preserveScroll: true
                })
            } else {
                this.form.post(route('pm.client-account.rules.store', {clientAccount: this.clientAccount.slug}), {
                    preserveScroll: true
                });
            }
        },

        handleImageAdded: function (file, Editor, cursorLocation, resetUploader) {

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


})

function uuidv4() {
    return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, c =>
        (
            c ^
            (crypto.getRandomValues(new Uint8Array(1))[0] & (15 >> (c / 4)))
        ).toString(16)
    )
}
</script>

<style src="@vueform/multiselect/themes/default.css"></style>
<style scoped>

input[type="radio"] + label span {
    transition: background .2s,
    transform .2s;
}

input[type="radio"] + label span:hover,
input[type="radio"] + label:hover span {
    transform: scale(1.2);
}

input[type="radio"]:checked + label span {
    background-color: #3490DC;
    box-shadow: 0px 0px 0px 2px white inset;
}

input[type="radio"]:checked + label {
    color: #3490DC;
}

input[type="radio"] + label.initial {
    color: #0B5EA0;
}
</style>
