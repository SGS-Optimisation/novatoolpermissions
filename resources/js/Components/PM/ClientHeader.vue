<template>
    <div class="flex flex-row mr-4">
        <div v-if="clientAccount != null && clientAccount.image !== ''">
            <img  :src="'/'+clientAccount.image" style="max-width: 80px">
        </div>
        <div v-else>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <span v-if="clientAccount != null">
                {{ clientAccount.name }}
            </span>
                <span v-else>
                {{ team.name }}
            </span>
            </h2>
        </div>

    </div>

</template>

<script>
export default {
    name: "ClientHeader",
    props: [
        'clientAccount',
    ],
}
</script>

<style scoped>

</style>
