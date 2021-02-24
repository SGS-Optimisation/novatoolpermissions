<template>
    <div class="mx-auto sm:px-6 lg:px-8">

        <template>
            <h1 class="text-lg">
                <slot name="title"></slot>
            </h1>
        </template>

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
                        <jet-input-error :message="form.error('name')" class="mt-2"/>
                    </div>

                    <div class="col-span-6 sm:col-span-8">
                        <jet-label for="content" value="Content"/>
                        <vue-editor id="editor" v-model="form.content"
                                    useCustomImageHandler
                                    @imageAdded="handleImageAdded"/>
                    </div>
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
</template>

<script>
import {VueEditor} from "vue2-editor";

import JetButton from '@/Jetstream/Button'
import JetFormSection from '@/Jetstream/FormSection'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetLabel from '@/Jetstream/Label'
import JetActionMessage from '@/Jetstream/ActionMessage'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'

export default {
    name: "RuleForm",

    components: {
        VueEditor,

        JetActionMessage,
        JetButton,
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
        'rule'
    ],

    data() {
        return {
            form: this.$inertia.form({
                name: this.rule.name,
                content: this.rule.content,
            }, {
                bag: 'pushRuleData',
                resetOnSuccess: false,
            }),
        }
    },

    methods: {
        pushRuleData: function () {

            if (this.rule.id) {
                this.form.put(route('rules.update', {clientAccount: this.clientAccount.slug, id: this.rule.id}), {
                    preserveScroll: true
                })
            }
            else {
                this.form.post(route('rules.store', {clientAccount: this.clientAccount.slug}), {
                    preserveScroll: true
                });
            }
        },

        handleImageAdded: function (file, Editor, cursorLocation) {
            // An example of using FormData
            // NOTE: Your key could be different such as:
            // formData.append('file', file)

            console.log('handling image upload');

            var formData = new FormData();
            formData.append("attachment", file);

            axios({
                url: "http://dagobah.test/nova-api/rules/trix-attachment/content",
                method: "POST",
                data: formData,
            })
                .then(result => {
                    let url = result.data.url; // Get url from response
                    Editor.insertEmbed(cursorLocation, "image", url);
                })
                .catch(err => {
                    console.log(err);
                });
        }
    },


}
</script>
