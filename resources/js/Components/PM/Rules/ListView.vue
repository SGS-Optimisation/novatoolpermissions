<template>
    <div class="bg-white shadow-md my-4 p-3 rounded relative" :data-rule-id="rule.id">
        <div class="ribbon z-0">
            <span class="text-xxs text-white font-semibold text-center"
            :class="{
                'bg-indigo-400': rule.state === 'Published',
                'bg-yellow-400' : rule.state !== 'Published'
            }">
                {{rule.state}}
            </span>
        </div>
        <div class="flex items-center justify-between">
            <div class="description">
                <h2 @click="detailsOpen = !detailsOpen; $emit('toggle')" class="cursor-pointer text-xl font-bold">
                    {{ rule.name }}</h2>

                <div class="flex">
                    <a class="inline-flex border-dashed border-b border-gray-500 items-center px-1 pt-1 text-sm font-medium leading-5 text-gray-900 focus:outline-none transition duration-150 ease-in-out"
                        :href="route('pm.client-account.rules.edit',  {clientAccount: clientAccount.slug, id: rule.id })">
                        <div class="flex-shrinkalign-bottom text-xs pr-1"
                             :title="date()">
                            Last updated {{ humanDate() }}
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                    </a>
                    <div v-if="rule.flagged">
                        <a @click="showFlagReason" class="cursor-pointer"><i class="text-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <title>This rule is currently flagged. Click to view reason(s).</title>
                                <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd" />
                            </svg>
                        </i></a>
                    </div>
                </div>
                <rule-tags :rule="rule"/>
            </div>

            <!-- Button for opening card -->
            <div class="ml-4 z-20">
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
        <div class="w-full flex flex-col mt-4">
            <hr class="mb-4 border-gray-300">
            <template v-if="detailsOpen">
                <div v-html="rule.content"/>
            </template>
            <template v-else>
                <div @click="detailsOpen = !detailsOpen; $emit('toggle')"
                     class="cursor-pointer"
                     v-html="excerpt"/>
            </template>

            <div class="text-right" :class="{'hidden' : !detailsOpen}">
                <button @click="flagRule()"
                        class="inline-flex items-center px-1 py-1 bg-gray-800 border border-transparent
                        rounded-md font-semibold text-xs text-white uppercase tracking-widest
                        hover:bg-gray-700 active:bg-gray-900
                        focus:outline-none focus:border-gray-900 focus:shadow-outline-gray
                        transition ease-in-out duration-150">
                    Flag rule?
                </button>
            </div>
        </div>

        <!-- Rule Flag Reason List Modal -->
        <jet-dialog-modal :show="isShowingFlagModal" @close="closeFlagReasonModal">
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
                <jet-secondary-button @click.native="closeFlagReasonModal">
                    Close
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click.native="sendUnflagRule"
                                   :class="{ 'opacity-25': unflagRuleForm.processing }"
                                   :disabled="unflagRuleForm.processing">
                    Remove flag
                </jet-danger-button>
            </template>
        </jet-dialog-modal>

        <!-- Rule Flagging Modal -->
        <jet-dialog-modal :show="isFlaggingRule" @close="closeFlagModal">
            <template #content>
                <div class="mt-4">
                    <jet-label for="reason" value="Flag reason"/>
                    <textarea class="form-input rounded-md shadow-sm mt-1 block w-full" id="reason"
                              placeholder="Please provide a short explanation"
                              v-model="flagRuleForm.reason"/>
                    <!--                    <jet-input type="text" class="mt-1 block w-3/4"
                                                   :value="flagRuleForm.reason"
                                                   v-model="flagRuleForm.reason"/>-->

                </div>
            </template>
            <template #footer>
                <jet-secondary-button @click.native="closeFlagModal">
                    Nevermind
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click.native="sendFlagRule"
                                   :class="{ 'opacity-25': flagRuleForm.processing }"
                                   :disabled="flagRuleForm.processing">
                    Flag
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
import clip from "text-clipper";

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

            unflagRuleForm: this.$inertia.form({
                reason: "",
            }, {
                bag: 'sendUnflagRule'
            }),

            isFlaggingRule: false,
            currentFlaggingRule: null,

            flagRuleForm: this.$inertia.form({
                reason: "",
            }, {
                bag: 'sendFlagRule'
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

        closeFlagReasonModal() {
            this.isShowingFlagModal = false;
        },

        sendUnflagRule() {
            console.log('unflagging rule', this.rule);

            this.flagRuleForm.post(route('rule.unflag', this.rule.id), {
                preserveScroll: true
            }).then(() => {
                this.rule.flagged = false;
                this.rule.metadata['flag_reason'] = [];
                this.closeFlagReasonModal();
                this.$emit('updated')
            });
        },


        flagRule() {
            this.isFlaggingRule = true;
        },

        closeFlagModal() {
            this.isFlaggingRule = false;
        },

        sendFlagRule() {
            console.log('flagging rule', this.rule);
            let reason = this.flagRuleForm.reason;

            this.flagRuleForm.post(route('rule.flag', this.rule.id), {
                preserveScroll: true
            }).then(() => {
                this.rule.flagged = true;

                let reasonEntry = {
                    user: this.$page.user.name,
                    date: moment().format('MMM DD YYYY, HH:mm:ss'),
                    reason: reason,
                };

                if(this.rule.metadata === undefined || this.rule.metadata === null) {
                    this.rule.metadata = {flag_reason: []};
                }

                if (this.rule.metadata['flag_reason'] === undefined) {
                    this.rule.metadata['flag_reason'] = [];
                }
                this.rule.metadata['flag_reason'].push(reasonEntry);

                this.closeFlagModal();
                this.$emit('updated');
            });
        },
    },

    computed: {
        excerpt() {
            return clip(this.rule.content.replace(/<img .*?>/g,''), 120, {html: true, maxLines: 2});
        }
    }

}
</script>
<style scoped>
.text-xxs {
    font-size: 0.5rem;
}
</style>
