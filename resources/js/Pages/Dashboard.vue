<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ team.name }}
            </h2>
        </template>

        <div class="p-2">
            <div class="col-span-6 sm:col-span-4 flex">
                <jet-input id="name" type="text" class="flex-initial block w-full " v-model="searchJobKey" autofocus/>
                <jet-button v-if="!searching" class="flex-initial ml-1" @click.native="search">
                    Search
                </jet-button>
                <jet-button v-else class="flex-initial ml-1">
                    Searching
                </jet-button>
            </div>

            <div class="flex flex-wrap overflow-hidden sm:-mx-px md:-mx-px lg:-mx-px xl:-mx-px mt-1">

                <div v-for="rule in _.orderBy(searchedRules, 'created_at', 'desc')" class="w-full overflow-hidden sm:my-px sm:px-px sm:w-full md:my-px md:px-px md:w-full lg:my-px lg:px-px lg:w-1/3 xl:my-px xl:px-px xl:w-1/3 shadow-md p-3 rounded">
                    <view-rule-item :rule="rule" @on-click-view="openModal" />
                </div>

            </div>

        </div>


        <div class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="isOpen">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                <div class="fixed inset-0 transition-opacity">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                    <form>

                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="">
                                <div class="mb-4">
                                    <view-rule :rule="currentRule" >
                                        <button @click="closeModal()" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                            Close
                                        </button>
                                    </view-rule>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                                <button @click="closeModal()" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Close
                                </button>
                            </span>
                        </div>

                    </form>

                </div>
            </div>
        </div>

    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Input from "@/Jetstream/Input";
import Button from "@/Jetstream/Button";
import JetButton from '@/Jetstream/Button'
import JetInput from '@/Jetstream/Input'
import ViewRule from '@/Components/PM/Rules/ViewRule'
import ViewRuleItem from "@/Components/PM/Rules/ViewRuleItem";
export default {
    props: [
        'team',
        'jobNumber',
        'rules'
    ],

    data() {
        return {
            searchJobKey: this.jobNumber,
            searching: false,
            searchedRules: [...this.rules],
            isOpen: false,
            currentRule: null
        }
    },

    watch:{
        searchJobKey(){
            if(this.searchJobKey){
                Echo.channel(`rules-filtered.${this.searchJobKey}`)
                    .listen('.rules-updated', (e) => {
                        window.location = window.location+'/'+this.searchJobKey;
                    });
            }
        }
    },

    methods: {
        openModal(rule){
            this.currentRule = rule;
            this.isOpen = true
        },
        closeModal(){
            this.currentRule = null;
            this.isOpen = false;
        },
        search() {
            if (this.searchJobKey && this.searchJobKey !== '') {
                this.searching = true;
                axios({
                    url: "https://dagobah.test/api/rule/search/" + this.searchJobKey,
                    method: "GET",
                })
                    .then(result => {
                        this.searchedRules = result.data;
                        this.searching = false;
                    })
                    .catch(err => {
                        console.log(err);
                        this.searching = false;
                    });
            }
        }
    },

    components: {
        ViewRuleItem,
        Button,
        Input,
        AppLayout,
        JetButton,
        JetInput,
        ViewRule,
    },
}
</script>
