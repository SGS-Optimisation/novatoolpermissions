<template>
    <jet-form-section @submitted="createTeam">
        <template #title>
            Team Details
        </template>

        <template #description>
            Create a new team to collaborate with others on projects.
        </template>

        <template #form>
            <div class="col-span-6">
                <jet-label value="Team Owner" />

                <div class="flex items-center mt-2">
                    <img class="w-12 h-12 rounded-full object-cover" :src="$page.user.profile_photo_url" :alt="$page.user.name">

                    <div class="ml-4 leading-tight">
                        <div>{{ $page.user.name }}</div>
                        <div class="text-gray-700 text-sm">{{ $page.user.email }}</div>
                    </div>
                </div>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="name" value="Team Name" />
                <jet-input required id="name" type="text" class="mt-1 block w-full" v-model="form.name" autofocus />
                <jet-input-error :message="form.error('name')" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="region" value="Region" />
                <select required id="region" v-model="form.region">
                    <option :value=null disabled>-Select-</option>
                    <option value="APAC">APAC</option>
                    <option value="EMEA">EMEA</option>
                    <option value="LATAM">LATAM</option>
                    <option value="NA">NA</option>
                </select>
                <jet-input-error :message="form.error('region')" class="mt-2" />
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
</template>

<script>
    import JetActionMessage from '@/Jetstream/ActionMessage'
    import JetButton from '@/Jetstream/Button'
    import JetFormSection from '@/Jetstream/FormSection'
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label'

    export default {
        components: {
            JetActionMessage,
            JetButton,
            JetFormSection,
            JetInput,
            JetInputError,
            JetLabel,
        },

        props: [
            'clientAccount',
        ],

        data() {
            return {
                form: this.$inertia.form({
                    name: '',
                    region: null,
                    client_account_id: this.clientAccount.id,
                }, {
                    bag: 'createTeam',
                    resetOnSuccess: false,
                })
            }
        },

        methods: {
            createTeam() {
                this.form.post(route('pm.client-account.teams.store', {clientAccount: this.clientAccount.slug}), {
                    preserveScroll: true
                });
            },
        },
    }
</script>
