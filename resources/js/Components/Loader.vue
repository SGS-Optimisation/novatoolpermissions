<template>
    <div class="flex flex-col items-center justify-center">
        <div class="mt-1/5 loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-12 w-12 mb-4"></div>
        <h2 class="text-center text-gray-800 text-xl font-semibold">Loading...</h2>
        <p class="w-1/3 text-center text-gray-800">
            <slot>
                Patience you must have.<br>Worth it will be.
            </slot>
        </p>
    </div>
</template>

<script>
export default {
    name: "Loader"
}
</script>

<style scoped>
.loader {
    border-top-color: #3498db;
    -webkit-animation: spinner 1.5s linear infinite;
    animation: spinner 1.5s linear infinite;
}

@-webkit-keyframes spinner {
    0% {
        -webkit-transform: rotate(0deg);
    }
    100% {
        -webkit-transform: rotate(360deg);
    }
}

@keyframes spinner {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>
