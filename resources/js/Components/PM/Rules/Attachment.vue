<template>
    <div class="flex flex-row justify-around items-center
    border border-gray-200
    bg-blue-100 shadow-md rounded-l-lg rounded-br-lg
    p-2 mr-1 mb-1">
        <span class="flex flew-row cursor-default mx-auto break-all mr-2" :title="'Size: ' + filesize">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            {{ attachment.filename }}</span>

        <span class="flex flex-row justify-around">
            <a :href="attachment.url" download target="_blank" class="text-blue-500 hover:text-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                </svg>
            </a>
            <a v-if="showDelete" class="button cursor-pointer text-red-500 hover:text-red-700" @click="confirmDeletion">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </a>
        </span>

        <!-- Delete Team Confirmation Modal -->
        <jet-confirmation-modal :show="confirmingDeletion" @close="confirmingDeletion = false">
            <template #title>
                Delete Attachment
            </template>

            <template #content>
                Are you sure you want to delete this attachment?
            </template>

            <template #footer>
                <jet-secondary-button @click.native="confirmingDeletion = false">
                    Nevermind
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click.native="deleteAttachment"
                                   :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Delete Attachment
                </jet-danger-button>
            </template>
        </jet-confirmation-modal>
    </div>
</template>

<script>
import JetConfirmationModal from '@/Jetstream/ConfirmationModal'
import JetButton from '@/Jetstream/Button'
import JetDangerButton from '@/Jetstream/DangerButton'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'

const prettyBytes = require('pretty-bytes');

export default {
    name: "Attachment",
    components: {
        JetButton,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
    },
    props: [
        'attachment',
        'clientAccount',
        'rule',
        'showDelete'
    ],
    data() {
        return {
            confirmingDeletion: false,
            deleting: false,

            form: this.$inertia.form({
                //
            }, {
                bag: 'deleteAttachment'
            })
        }
    },

    methods: {
        confirmDeletion() {
            this.confirmingDeletion = true
        },

        deleteAttachment() {
            const url = route('pm.client-account.rules.attachments.delete', [this.clientAccount.slug, this.rule.id, this.attachment.id]);
            const headers = {
                "content-type": "application/json",
                "Accept": "application/json"
            };

            axios.delete(url, {headers})
                .then((response) => {
                    console.log('deleted');
                    this.confirmingDeletion = false;
                    this.$emit('attachment-deleted', this.attachment);
                });
        }
    },

    computed: {
        filesize() {
            return prettyBytes(this.attachment.filesize);
        }
    }
}
</script>

<style scoped>

</style>
