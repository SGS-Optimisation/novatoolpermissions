<template>
    <div>
        <p>Number of rules: {{ rules.length }}</p>
    </div>
</template>

<script>
export default {
    name: "Overview",
    "props": [
        "clientAccount",
        "rules"
    ]
}
</script>
