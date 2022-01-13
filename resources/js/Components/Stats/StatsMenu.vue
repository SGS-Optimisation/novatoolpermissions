<template>
    <div class="flex justify-between">
        <!--                <div class="flex flex-row content-center">
                            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                                Rules activity
                            </h2>
                        </div>

                        <div class="flex ml-6">
                            &lt;!&ndash; right content &ndash;&gt;
                        </div>-->

        <nav class="flex flex-col sm:flex-row">
            <sub-nav-link
                :href="route('stats.rules')"
                :active="route().current('stats.rules')">
                Rules Activity
            </sub-nav-link>

            <sub-nav-link
                :href="route('stats.jobs')"
                :active="route().current('stats.jobs')">
                Job Activity
            </sub-nav-link>
        </nav>
    </div>
</template>

<script>
import SubNavLink from "@/Components/SubNavLink";
export default {
    name: "Menu",
    components: {SubNavLink}
}
</script>

<style scoped>

</style>
