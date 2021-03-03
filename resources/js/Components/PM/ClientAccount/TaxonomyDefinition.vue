<template>
    <div>
        <div class="flex flex-col py-2"
             v-for="(taxonomyGroup, index) in taxonomyHierarchy[parentTaxonomy.name].children">

            <template v-for="(taxonomyData, name) in taxonomyGroup">
                <term-definition :terms="taxonomyData.terms"
                                 :taxonomy-name="name"
                                 :taxonomy-id="taxonomyData.id"
                                 :client-account="clientAccount"
                >
                </term-definition>
            </template>

        </div>
    </div>
</template>

<script>
import TermDefinition from "@/Components/PM/ClientAccount/TermDefinition";

export default {
    name: "TaxonomyDefinition",

    components: {
        TermDefinition,
    },

    props: [
        'parentTaxonomy',
        'taxonomyHierarchy',
        'clientAccount'
    ],
}
</script>
