<template>
    <div class="pb-36">
        <div v-for="(topTaxonomy, index) in topTaxonomies"
              class="mx-auto sm:px-6 lg:px-8">
            <jet-form-section @submitted="pushRuleMeta" :class="{'mb-4': (index !== topTaxonomies.length - 1)}">
                <template #title>
                    {{ topTaxonomy.name }}
                </template>
                <template #description>
                    Add the appropriate tags
                </template>
                <template #form>
                    <div class="flex flex-row mx-auto sm:px-6 lg:px-8">
                        <div class="flex-grow px-4">
                            <div class="flex flex-col py-2"
                                 v-for="(taxonomyGroup, index) in taxonomyHierarchy[topTaxonomy.name].children">
                                <template v-for="(taxonomyData, name) in taxonomyGroup">
                                    <term :terms="taxonomyData.terms"
                                          :taxonomy-name="name"
                                          :multiple="getMultipleMode(taxonomyData)"
                                          :rule="rule"
                                          @selected="termSelected"
                                    >
                                    </term>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>

                <template #actions v-if="index === topTaxonomies.length - 1">
                    <jet-action-message :on="form.recentlySuccessful" class="mr-3 bg-green-300 text-center flex-grow">
                        Saved.
                    </jet-action-message>

                    <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Save Tagging
                    </jet-button>
                </template>
            </jet-form-section>
        </div>
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
        getMultipleMode(taxonomyData) {
            if(! taxonomyData.taxonomy.config || !taxonomyData.taxonomy.config.hasOwnProperty('multiple')) {
                return true;
            }

            return taxonomyData.taxonomy.config.multiple;
        },

        termSelected(data) {
            console.log('term updated', data);
            this.taxonomy[data.taxonomy] = data.terms;
        },

        pushRuleMeta: function () {
            this.form.taxonomy = this.taxonomy;

            this.form.put(route('pm.client-account.rules.taxonomy.update', {
                clientAccount: this.clientAccount.slug,
                id: this.rule.id
            }), {
                preserveScroll: true
            })
        },
    }
}
</script>

<style scoped>

</style>
