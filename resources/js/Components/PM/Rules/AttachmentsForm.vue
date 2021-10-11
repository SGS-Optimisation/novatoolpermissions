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
            <ul>
                <li v-for="file in files">
                    <div
                        class="relative text-gray-600 flex flew-row justify-between p-2 border border-2 border-dashed border-gray-100 bg-gray-200 rounded-l-lg rounded-br-lg items-center">

                        <div class="flex flex-row">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>

                            <span class="z-50">{{ file.name }} - {{
                                    !file.active ? (file.success ? 'Complete' : 'Pending') : file.progress + '%'
                                }}</span>

                        </div>

                        <a class="flex flex-row button cursor-pointer text-red-500 hover:text-red-700"
                           v-if="!file.success && !file.active"
                           @click.prevent="$refs.upload.remove(file)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </a>

                        <div v-if="file.active || file.success"
                             class="absolute z-40 top-0 left-0 h-full bg-green-400 bg-opacity-75 transition-all ease-out duration-100"
                             :class="{ 'bg-red-800': file.error}"
                             role="progressbar" :style="{width: file.progress + '%'}">&nbsp;
                        </div>
                    </div>
                </li>
            </ul>
            <div class="flex flex-row uploader justify-between my-2">
                <file-upload
                    ref="upload"
                    v-model="files"
                    :headers="headers"
                    :multiple=true
                    :uploadAuto=false
                    :drop=true
                    :dropDirectory=true
                    :post-action="postUrl"
                    @input-file="inputFile"
                    @input-filter="inputFilter"
                >
                <span class="add-file-btn flex flex-row cursor-pointer mb-4 w-1/2">
                    Select files
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd"
                            d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z"
                            clip-rule="evenodd"/>
                    </svg>
                </span>
                </file-upload>
                <div class="flex flex-col ml-6">
                    <button class="flex flex-row bg-green-500 hover:bg-green-700 rounded-lg px-2 py-1 text-white shadow-lg"
                            v-show="!$refs.upload || !$refs.upload.active" @click.prevent="$refs.upload.active=true"
                            type="button">
                        Upload
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                    </button>
                    <button v-show="$refs.upload && $refs.upload.active" @click.prevent="$refs.upload.active=false"
                            type="button">Stop upload
                    </button>
                </div>
            </div>
        </div>


    </div>
</template>

<script>
import {defineComponent} from "vue";
import Attachment from "@/Components/PM/Rules/Attachment";
import VueUploadComponent from 'vue-upload-component';

export default defineComponent({
    name: "Attachments",
    components: {
        Attachment,
        FileUpload: VueUploadComponent,
    },
    props: [
        'clientAccount',
        'rule'
    ],
    data: function () {
        return {
            files: [],
            postUrl: route('pm.client-account.rules.attachments.store', [this.clientAccount.slug, this.rule.id]),
            headers: {
                'X-XSRF-TOKEN': this.xsrf(),
            },
        }
    },
    methods: {
        xsrf() {
            const name = 'XSRF-TOKEN';
            var re = new RegExp(name + "=([^;]+)");
            var value = re.exec(document.cookie);
            return (value != null) ? unescape(value[1]) : null;
        },

        sendingEvent(file, xhr, formData) {
            formData.append('disk', 'azure_docs');
        },

        removeAttachment(attachment) {
            console.log('deleting attachment ' + attachment.id);
            _.remove(this.rule.attachments, {id: attachment.id});
            this.$forceUpdate();
        },

        /**
         * Has changed
         * @param  Object|undefined   newFile   Read only
         * @param  Object|undefined   oldFile   Read only
         * @return undefined
         */
        inputFile: function (newFile, oldFile) {
            if (newFile && oldFile && !newFile.active && oldFile.active) {
                // Get response data
                console.log('response', newFile.response)

                if (newFile.xhr) {
                    //  Get the response status code
                    console.log('status for ' + newFile.name, newFile.xhr.status)
                    this.rule.attachments.push(newFile.response);
                    this.files = _.reject(this.files, (file) => file.success === true);
                }
            }

            /*if (newFile && oldFile && newFile.success && !oldFile.success) {
                this.files = _.reject(this.files, (file)=> file.name === oldFile.name)
            }*/

            // Automatically activate upload
            if (Boolean(newFile) !== Boolean(oldFile) || oldFile.error !== newFile.error) {
                if (this.uploadAuto && !this.$refs.upload.active) {
                    this.$refs.upload.active = true
                }
            }
        },
        /**
         * Pretreatment
         * @param  Object|undefined   newFile   Read and write
         * @param  Object|undefined   oldFile   Read only
         * @param  Function           prevent   Prevent changing
         * @return undefined
         */
        inputFilter: function (newFile, oldFile, prevent) {
            if (newFile && !oldFile) {
                // prevent exes
                if (/\.(exe|dmg|php|html|js|jsx)$/i.test(newFile.name)) {
                    return prevent()
                }

                // Filter system files or hide files
                if (/(\/|^)(Thumbs\.db|desktop\.ini|\..+)$/.test(newFile.name)) {
                    return prevent()
                }
            }

            if (newFile && newFile.error === "" && newFile.file && (!oldFile || newFile.file !== oldFile.file)) {
                // Create a blob field
                newFile.blob = ''
                let URL = (window.URL || window.webkitURL)
                if (URL) {
                    newFile.blob = URL.createObjectURL(newFile.file)
                }
            }
        }
    },

    computed: {},
})
</script>

<style src="vue-upload-component/dist/vue-upload-component.part.css"></style>
<style scoped>
.uploader :deep(label) {
    @apply cursor-pointer;
}

.uploader :deep(.file-uploads-html5) {
    @apply w-1/2 flex-grow;
}

.uploader :deep(.add-file-btn) {
    @apply px-2 py-1 bg-blue-500 hover:bg-blue-700 text-white shadow-lg rounded-lg;
}
</style>
