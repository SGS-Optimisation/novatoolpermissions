<template>
    <div class="mx-auto sm:px-6 lg:px-8" id="rule-metadata">
        <jet-form-section @submitted="pushRuleMeta">
            <template #title>
                Rule metadata
            </template>
            <template #description>
                Add the appropriate tags
            </template>
            <template #form>
                <div class="flex flex-row mx-auto sm:px-6 lg:px-8">
                    <div class="flex-grow px-4" v-for="topTaxonomy in topTaxonomies">
                        <div>
                            <h3 class="text-xs uppercase font-bold">{{ topTaxonomy.name }}</h3>

                            <div class="flex flex-col py-2"
                                 v-for="(taxonomyGroup, index) in taxonomyHierarchy[topTaxonomy.name].children">
                                <template v-for="(taxonomyData, name) in taxonomyGroup">
                                    <term :terms="taxonomyData.terms"
                                          :taxonomy-name="name"
                                          :rule="rule"
                                          @selected="termSelected"
                                    >
                                    </term>
                                </template>
                            </div>
                        </div>
                    </div>
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
</template>

<script>
import JetButton from '@/Jetstream/Button'
import JetFormSection from '@/Jetstream/FormSectionNoGrid'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetLabel from '@/Jetstream/Label'
import JetActionMessage from '@/Jetstream/ActionMessage'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import {VueEditor} from "vue2-editor";
import Term from "@/Components/PM/Rules/Term";

export default {
    name: "Meta",

    components: {
        JetActionMessage,
        JetButton,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        JetSecondaryButton,
        Term,
    },
    props: [
        'clientAccount',
        'taxonomyHierarchy',
        'topTaxonomies',
        'rule'
    ],

    data() {
        return {

            taxonomy: {},

            form: this.$inertia.form({
                taxonomy: this.taxonomy
            }, {
                bag: 'pushRuleMeta',
                resetOnSuccess: false,
            }),
        }
    },

    mounted() {
        console.log(this.taxonomyHierarchy['Account Structure'].children)
    },

    methods: {
        termSelected(data) {
            console.log('term updated', data);
            this.taxonomy[data.taxonomy] = data.terms;
        },

        pushRuleMeta: function () {
            this.form.taxonomy = this.taxonomy;

            this.form.put(route('rules.taxonomy.update', {clientAccount: this.clientAccount.slug, id: this.rule.id}), {
                preserveScroll: true
            })
        },
    }
}
</script>

<style scoped>

</style>
