<template>
    <div class="bg-white shadow-md my-2 p-3 rounded" :data-rule-id="rule.id">
        <div class="flex items-center justify-between">
            <div class="description">
                <h2 @click="detailsOpen = !detailsOpen; $emit('toggle')" class="cursor-pointer text-xl font-bold">
                    {{ rule.name }}</h2>
                <div class="flex">
                    <div class="flex-shrink cursor-default align-bottom text-xs border-dashed border-b border-gray-500"
                         :title="date()">
                        Last updated {{ humanDate() }}
                    </div>
                    <nav-link
                        :href="route('pm.client-account.rules.edit',  {clientAccount: clientAccount.slug, id: rule.id })">
                        <i class="fa fa-pen"></i>
                    </nav-link>
                    <div v-if="rule.flagged">
                        <a @click="showFlagReason" class="cursor-pointer"><i class="fa fa-flag text-red-700"></i></a>
                    </div>
                </div>
                <rule-tags :rule="rule"/>
            </div>

            <!-- Button for opening card -->
            <div class="ml-4">
                <div @click="detailsOpen = !detailsOpen; $emit('toggle')"
                     class="flex items-center cursor-pointer px-3 py-2 text-gray-200 hover:text-gray-600"
                     :class="{'transform rotate-180': detailsOpen}">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke="black">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

            </div>

        </div>


        <!-- Collapsed content -->
        <div class="w-full flex flex-col mt-8" :class="{'hidden': !detailsOpen}">
            <hr class="mb-4 border-gray-700">
            <div v-html="rule.content"></div>
        </div>

        <!-- Rule Flag Reason Modal -->
        <jet-dialog-modal :show="isShowingFlagModal" @close="closeFlagModal">
            <template #title>
                Flag Reasons
            </template>
            <template #content>
                <div v-if="rule.metadata && rule.metadata.flag_reason">
                    <div class="flex flew-row"
                         v-for="record in _.orderBy(rule.metadata.flag_reason, 'date', 'desc')">
                        <div>[{{ record.date }}] {{ record.user }}: {{ record.reason }}</div>
                    </div>
                </div>
            </template>
            <template #footer>
                <jet-secondary-button @click.native="closeFlagModal">
                    Close
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click.native="sendUnflagRule"
                                   :class="{ 'opacity-25': flagRuleForm.processing }"
                                   :disabled="flagRuleForm.processing">
                    Remove flag
                </jet-danger-button>
            </template>
        </jet-dialog-modal>

    </div>
</template>

<script>

import RuleTags from "@/Components/PM/Rules/RuleTags";
import JetActionMessage from '@/Jetstream/ActionMessage'
import JetButton from '@/Jetstream/Button'
import JetDangerButton from '@/Jetstream/DangerButton'
import JetDialogModal from '@/Jetstream/DialogModal';
import JetInput from '@/Jetstream/Input'
import JetLabel from '@/Jetstream/Label'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'

const moment = require('moment');

import NavLink from "@/Jetstream/NavLink";

export default {
    name: "Rule",
    props: ['rule', 'clientAccount'],

    components: {
        RuleTags,
        NavLink,
        JetActionMessage,
        JetButton,
        JetDangerButton,
        JetDialogModal,
        JetInput,
        JetLabel,
        JetSecondaryButton,
    },

    data() {
        return {
            detailsOpen: false,
            isShowingFlagModal: false,

            flagRuleForm: this.$inertia.form({
                reason: "",
            }, {
                bag: 'sendUnflagRule'
            }),
        }
    },

    methods: {
        date: function () {
            return moment(this.rule.updated_at).format('MMM DD YYYY, HH:mm:ss');
        },

        humanDate: function () {
            return moment(this.rule.updated_at).fromNow();
        },

        showFlagReason() {
            this.isShowingFlagModal = true;
        },

        closeFlagModal() {
            this.isShowingFlagModal = false;
        },

        sendUnflagRule() {
            console.log('unflagging rule', this.rule);

            this.flagRuleForm.post(route('rule.unflag', this.rule.id), {
                preserveScroll: true
            }).then(() => {
                this.rule.flagged = false;
                this.closeFlagModal();
                this.$emit('unflagged')
            });
        },
    }

}
</script>
