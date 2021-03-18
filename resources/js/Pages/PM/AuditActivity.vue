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


                    <ul class="list-group">
                        <li class="list-group-item border-t-2 sm:border-dashed" v-for="(item, index) in audits" :key="index">


                                On {{ item.created_at | date}},
                                {{ item.user.name }} with <strong>IP:</strong>{{item.ip_address}}, {{ item.event }} {{item.auditable_type}}
                                record {{ item.auditable_id}} via  {{ item.url }}



                                <p class="text-blue-400" v-for="(key, value) in item.old_values">
                                    <strong>  {{ value }} </strong> is updated From:  <strong>{{ key }}</strong>>
                                    To:  <strong>{{ item.new_values[value] }}</strong>
                                </p>




                        </li>
                    </ul>
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
