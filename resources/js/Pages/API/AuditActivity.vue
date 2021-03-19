<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Activity Trail
            </h2>
        </template>
        <template>
            <div class="flex lg:pt-36">
                <div class="m-auto w-2/3">
            <div class="container">
                <div class="panel panel-info">
                    <!-- Default panel contents -->
                    <div class="panel-heading font-semibold text-2xl text-cool-gray-600">Recent Activities</div>


                    <ul class="list-group" v-if="typeof audits !== 'undefined'">
                        <li class="list-group-item border-t-2 sm:border-dashed" v-for="(item, index) in audits" :key="index">


                                On {{ item.created_at | date}},
                                {{ item.user.name }} with <strong>IP:</strong>{{item.ip_address}}, {{ item.event }} {{item.auditable_type}}
                                record {{ item.auditable_id}} via  {{ item.url }}



                            <p  v-html="" class="text-blue-400" v-for="(key, value) in item.old_values">
                                <span class="text-red-700"> <strong>  {{ value }} </strong> </span> is updated <span class="text-red-700">From:</span><span v-html="key"></span>

                                <span class="text-red-700">To: </span> <span v-html="item.new_values[value]"></span>
                            </p>




                        </li>
                    </ul>
                    <span v-if="typeof audits!== 'undefined'">No history</span>
                </div>
            </div>
                </div>
            </div>
        </template>

    </app-layout>
</template>

<script>
import AppLayout from './../../Layouts/AppLayout'
export default {
    name: "AuditActivity",
   props: ['audits'],


    components: {
        AppLayout,
    },
}
</script>
