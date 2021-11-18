<template>
    <div class="flex justify-center mb-2">
        <div class="self-end text-xs text-gray-600">
            <a :href="changelogLink" title="View changelog" target="_blank">Dagobah {{ appVersion }}</a> –
            For any issue, raise a ticket on <a target="_blank" href="https://sgsit.service-now.com/serviceportal">ServiceNow</a>
            mentioning <em>Dagobah</em>  or send an email to <a href="mailto:ServiceDesk@SGSCO.com?subject=[Dagobah v2 issue] Short_Issue_Description">ServiceDesk@SGSCO.com</a>
        </div>
    </div>
</template>

<script>

export default {
    name: "About",
    data() {
        return {}
    },
    mounted() {

    },
    methods: {},
    computed: {
        appVersion() {
            return 'v' + document.querySelector('meta[name="app_version"]').content;
        },
        changelogLink() {
            return route('larecipe.show', ['2.0', 'changelog']) + '#' + this.appVersion;
        },
    }
}
</script>

<style scoped>

</style>
