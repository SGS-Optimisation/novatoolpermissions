<template>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden  sm:rounded-lg">
                <div class="pt-4">
                    <jet-form-section @submitted="pushClientData">
                        <template #title>
                            Client info
                        </template>
                        <template #description>

                        </template>
                        <template #form>

                            <div class="col-span-6 sm:col-span-8">
                                <jet-label for="name" value="Name"/>
                                <jet-input id="name" type="text" required
                                           v-model="form.name"
                                           @input="updateName"
                                           autocomplete="off"
                                           class="mt-1 block w-full"/>
                                <jet-input-error :message="form.error('name')" class="mt-2"/>
                            </div>

                            <div class="col-span-6 sm:col-span-8">
                                <jet-label for="slug" value="Slug"/>
                                <jet-input id="slug" type="text" disabled
                                           class="mt-1 block w-full"
                                           v-model="form.slug"
                                />
                                <jet-input-error :message="form.error('slug')" class="mt-2"/>
                            </div>

                            <div class="col-span-6 sm:col-span-8">
                                <jet-label for="alias" value="Alias"/>
                                <textarea class="form-input rounded-md shadow-sm mt-1 block w-full" id="alias"
                                          placeholder="One item per line"
                                          v-model="form.alias"></textarea>
                                <jet-input-error :message="form.error('alias')" class="mt-2"/>
                            </div>

                            <div class="col-span-6 sm:col-span-8">
                                <jet-label for="image" value="Logo"/>
                                <input type="file" id="image" accept="image/*"
                                       @change="addFile($event.target.files);"
                                />
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
        </div>
    </div>
</template>

<script>
import JetButton from '@/Jetstream/Button'
import JetFormSection from '@/Jetstream/FormSection'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetLabel from '@/Jetstream/Label'
import JetActionMessage from '@/Jetstream/ActionMessage'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'

const slugify = text => _.kebabCase(text.replace(/&/g, '-and-'));

export default {
    name: "ClientAccountForm",

    components: {
        JetActionMessage,
        JetButton,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        JetSecondaryButton,
    },

    props: [
        'client',
    ],

    data() {
        return {

            form: this.$inertia.form({
                name: this.client.name,
                slug: this.client.slug,
                alias: this.client.alias,
                image: this.client.image,
            }, {
                bag: 'pushClientData',
                resetOnSuccess: false,
            }),
        }
    },

    methods: {
        updateName: function (name) {
            this.form.slug = slugify(name);
        },

        addFile(files) {
            if (!files.length) return;

            this.form.image = files[0];
        },

        pushClientData: function () {

            if (this.client.id) {
                this.form.post(route('pm.client-account.update', {clientAccount: this.client.slug}), {
                    preserveScroll: true
                })
            }
            else {
                this.form.post(route('pm.client-account.store'), {
                    preserveScroll: true
                })
            }
        },
    },
}
</script>

<style scoped>

</style>
