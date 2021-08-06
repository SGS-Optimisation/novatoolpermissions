<template>
    <div class="w-full h-full flex flex-col justify-center items-center">
        <label class="block font-medium text-sm text-gray-700">{{name}}</label>
        <div class="flex justify-baseline items-baseline align-bottom">
            <!-- Switch Container -->
            <div :class="[{ 'bg-blue-700': state }, 'w-14 h-7 flex items-center bg-gray-300 rounded-full mx-3 px-1']" @click="handleToggleState">
                <!-- Switch -->
                <div :class="[{ 'translate-x-7': state }, 'bg-white w-5 h-5 rounded-full shadow-md transform']"></div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "SwitchFlag",
    props: ['name', 'rightText', 'leftText', 'initialState'],
    data(){
        return {
            state: parseInt(this.initialState) // true = right, false = left
        }
    },
    methods:{
        handleToggleState(){
            this.state = !this.state;
            this.$emit('on-switch', this.state);
        }
    }
}
</script>

<style scoped>

</style>
