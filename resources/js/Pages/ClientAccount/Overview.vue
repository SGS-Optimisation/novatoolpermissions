<template>
    <div>
        <p>Number of rules: {{ rulesCount }}</p>
    </div>
</template>

<script>
export default {
    name: "Overview",
    "props": [
        "clientAccount",
        "rulesCount"
    ]
}
</script>
