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
                <jet-label value="Team Owner"/>

                <div class="flex items-center mt-2">
                    <img v-if="teamOwner && teamOwner.length" class="w-12 h-12 rounded-full object-cover" :src="teamOwner[0].profile_photo_url"
                         :alt="teamOwner[0].name">
                    <img v-else="user" class="w-12 h-12 rounded-full object-cover" src="https://ui-avatars.com/api/?name=?+?&color=7F9CF5&background=EBF4FF"
                         alt="Unset">

                    <div class="ml-4 leading-tight">
                        <template v-if="$page.props.user_permissions.createTeamsOnBehalfOfUsers">
                            <MultiSelect required v-model="teamOwner" :options="users" :filter="true"
                                         :selectionLimit=1
                                         @change="changeOwner"
                                         optionLabel="name" placeholder="Select Team Owner" />
                        </template>
                        <template v-else>
                            <div>{{ $page.props.user.name }}</div>
                            <div class="text-gray-700 text-sm">{{ $page.props.user.email }}</div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="name" value="Team Name"/>
                <jet-input required id="name" type="text" class="mt-1 block w-full" v-model="form.name" autofocus/>
                <jet-input-error :message="form.errors.name" class="mt-2"/>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="region" value="Region"/>
                <select required id="region" v-model="form.region">
                    <option :value=null disabled>-Select-</option>
                    <option value="APAC">APAC</option>
                    <option value="EMEA">EMEA</option>
                    <option value="LATAM">LATAM</option>
                    <option value="NA">NA</option>
                </select>
                <jet-input-error :message="form.errors.region" class="mt-2"/>
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
import JetActionMessage from '@/Jetstream/ActionMessage.vue'
import JetButton from '@/Jetstream/Button.vue'
import JetFormSection from '@/Jetstream/FormSection.vue'
import JetInput from '@/Jetstream/Input.vue'
import JetInputError from '@/Jetstream/InputError.vue'
import JetLabel from '@/Jetstream/Label.vue'
import MultiSelect from 'primevue/multiselect/sfc'

export default {
    components: {
        JetActionMessage,
        JetButton,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        MultiSelect,
    },

    props: [
        'clientAccount', 'users',
    ],

    data() {
        return {
            teamOwner: null,
            /* [{
                id: this.$page.props.user.id,
                name: this.$page.props.user.name,
                profile_photo_path: this.$page.props.user.profile_photo_path,
                profile_photo_url: this.$page.props.user.profile_photo_url,
            }]*/
            form: this.$inertia.form({
                name: '',
                region: null,
                owner_id: null,
                client_account_id: this.clientAccount.id,
            }, {
                bag: 'createTeam',
                resetOnSuccess: false,
            })
        }
    },

    methods: {
        changeOwner(event){
            this.form.owner_id = event.value.length ? event.value[0].id : null;
        },
        createTeam() {
            this.form.post(route('pm.client-account.teams.store', {clientAccount: this.clientAccount.slug}), {
                preserveScroll: true
            });
        },
    },
}
</script>
