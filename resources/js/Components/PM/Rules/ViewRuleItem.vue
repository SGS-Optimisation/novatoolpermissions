<template>
    <div class="mx-4 flex">
        <div class="w-full">
            <div class="text-sm font-medium text-gray-900">
                {{ rule.name }}
            </div>
            <div class="text-sm text-gray-500">
                {{ moment(rule.created_at).fromNow() }}
            </div>
        </div>
        <div class="flex-row">
            <a href="#" @click="$emit('on-click-view', rule)"
               class="text-indigo-600 hover:text-indigo-900 float-right justify-center -align-center">View</a>
        </div>
    </div>
</template>

<script>

export default {
    name: "ViewRuleItem",
    props: ['rule']
}

</script>
