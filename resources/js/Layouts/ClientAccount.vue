<template>
    <app-layout>
        <template #header>
            <div class="flex justify-between">
                <div class="flex flex-row content-center">
                    <client-header :client-account="clientAccount"></client-header>
                    <client-menu :client-account="clientAccount"></client-menu>
                </div>

                <div class="flex flex-col ml-6 justify-end">
                    <action-menu :client-account="clientAccount"></action-menu>
                    <slot name="additionalActions"></slot>
                </div>
            </div>
        </template>

        <slot name="body"></slot>

    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import ClientHeader from "@/Components/PM/ClientHeader.vue";
import ClientMenu from '@/Components/PM/ClientMenu.vue'
import ActionMenu from '@/Components/PM/ActionMenu.vue'
import {useToast} from "vue-toastification";
import {mutate} from 'swrv'

export default {
    props: [
        'clientAccount',
    ],

    components: {
        AppLayout,
        ClientHeader,
        ClientMenu,
        ActionMenu,
    },
    data() {
        return {}
    },
    mounted() {
        Echo.channel(`client-account.${this.clientAccount.slug}`)
            .listen('Rules\\RuleUpdated', (e) => {
                console.log(e);

                if (e.user.id !== this.$page.props.user.id)
                    this.toast(`Rule [${e.rule.dagId}] "${e.rule.name}" updated by ${e.user.name}`, {
                        type: "info",
                        position: "top-right",
                        timeout: 5000,
                        closeOnClick: true,
                        pauseOnFocusLoss: true,
                        pauseOnHover: true,
                        draggable: false,
                        draggablePercent: 0.6,
                        showCloseButtonOnHover: false,
                        hideProgressBar: true,
                        closeButton: "button",
                        icon: true,
                        rtl: false
                    })

                this.prefetchRules();
            });
    },

    beforeUnmount() {
        Echo.leaveChannel(`client-account.${this.clientAccount.slug}`)

    },

    methods: {
        toast: useToast(),

        prefetchRules() {
            const rulesPath = route('api.pm.client-account.rules', [this.clientAccount.slug]);
            mutate(
                rulesPath,
                axios(rulesPath).then((res) => res.data)
            )
            // the second parameter is a Promise
            // SWRV will use the result when it resolves
        },

        message() {
            console.log('messaging');
            this.toast('Is it me you‘re looking for?', {
                type: "info",
                position: "top-right",
                timeout: false,
                closeOnClick: true,
                pauseOnFocusLoss: true,
                pauseOnHover: true,
                draggable: false,
                draggablePercent: 0.6,
                showCloseButtonOnHover: false,
                hideProgressBar: true,
                closeButton: "button",
                icon: true,
                rtl: false
            })
        }
    }
}
</script>
