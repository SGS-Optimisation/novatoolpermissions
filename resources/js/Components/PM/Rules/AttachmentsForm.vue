<template>
    <div class="md:grid md:grid-cols-3 md:gap-6 mx-auto sm:px-6 lg:px-8">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0"></div>
            <h3 class="text-lg font-medium text-gray-900">Attachments</h3>
        </div>
        <div class="mt-5 md:mt-0 md:col-span-2">
            <div class="flex flex-wrap mb-3">
                <template class="w-16" v-for="attachment in rule.attachments">
                    <attachment
                        :attachment="attachment"
                        :client-account="clientAccount"
                        :rule="rule"
                        :show-delete="true"
                        @attachment-deleted="removeAttachment"
                    />
                </template>
            </div>

            <vue-dropzone ref="myDropzone" id="dropzone"
                          :options="dropzoneOptions"
                          @vdropzone-sending="sendingEvent"
                          @vdropzone-success="fileUploaded"
            />
        </div>


    </div>
</template>

<script>
import vue2Dropzone from 'vue2-dropzone';
import 'vue2-dropzone/dist/vue2Dropzone.min.css'
import Attachment from "@/Components/PM/Rules/Attachment";

export default {
    name: "Attachments",
    components: {
        Attachment,
        vueDropzone: vue2Dropzone
    },
    props: [
        'clientAccount',
        'rule'
    ],
    data: function () {
        return {}
    },
    methods: {
        sendingEvent(file, xhr, formData) {
            formData.append('disk', 'azure_docs');
        },

        removeAttachment(attachment) {
            console.log('deleting attachment ' + attachment.id);
            _.remove(this.rule.attachments, {id: attachment.id});
            this.$forceUpdate();
        },

        fileUploaded(file, response) {
            this.$refs.myDropzone.removeFile(file);
            console.log(response);
            this.rule.attachments.push(response);
        }
    },

    computed: {
        dropzoneOptions() {
            return {
                url: route('pm.client-account.rules.attachments.store', [this.clientAccount.slug, this.rule.id]),
                autoProcessQueue: true,
                thumbnailWidth: 150,
                maxFilesize: 100,
                maxThumbnailFilesize: 0.5,
                timeout: 0,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").getAttribute('content')
                },
                dictDefaultMessage: '<div class="flex justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">\n' +
                    '  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />\n' +
                    '</svg> ' + this.uploadMessage + '</div>',
            }
        },

        uploadMessage() {
            return this.rule.attachments.length ?
                'Drag and drop another file to upload'
                : 'Drag and drop file to upload';
        },
    },
}
</script>

<style scoped>

</style>
