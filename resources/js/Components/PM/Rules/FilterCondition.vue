<template>
    <div class="w-full h-full flex flex-col justify-center items-center">
        <div class="flex justify-baseline items-baseline align-bottom">
            <span :class="['h-6 w-6', { 'text-gray-500' : !toggleActive},{ 'text-gray-400' : toggleActive}]">
                OR
            </span>
            <!-- Switch Container -->
            <div :class="[{ 'bg-blue-700': toggleActive }, 'w-14 h-7 flex items-center bg-gray-300 rounded-full mx-3 px-1']" @click="handleToggleActive">
                <!-- Switch -->
                <div :class="[{ 'translate-x-7': toggleActive }, 'bg-white w-5 h-5 rounded-full shadow-md transform']"></div>
            </div>
            <span :class="['h-6 w-6', { 'text-gray-500' : toggleActive},{ 'text-gray-400' : !toggleActive}]">
                AND
            </span>
        </div>
    </div>
</template>

<script>
export default {
    name: "FilterCondition",
    data(){
        return {
            toggleActive: false
        }
    },
    methods:{
        handleToggleActive(){
            this.toggleActive = !this.toggleActive;
            this.$emit('on-change-filter-condition', this.toggleActive);
        }
    }
}
</script>

<style scoped>

</style>
