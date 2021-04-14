<template>
    <jet-nav-link
        class="hover:no-underline border-none block relative"
        :href="route('pm.client-account.dashboard', {clientAccount: team.client_account.slug })">

        <img v-if="team.client_account.image"
             :src="'/storage/'+ team.client_account.image"
             :alt="team.client_account.name"
             :title="team.client_account.name"
             class="client-logo">

        <span v-else class="font-bold">{{ team.client_account.name }}</span>

        <span class="num-rules"
              :class="{'bg-green-100': hasRules,'bg-pink-100': !hasRules}"
              title="Number of rules">
            {{ team.client_account.rules_count }}
        </span>
    </jet-nav-link>
</template>

<script>
import JetNavLink from "@/Jetstream/NavLink";

export default {
    name: "ClientAccountLink",
    props: ['team'],
    components: {
        JetNavLink,
    },
    computed: {
        hasRules: function () {
            return this.team.client_account.rules_count > 0;
        },
    }
}
</script>

<style scoped>
.client-logo {
    max-width: 120px;
    max-height: 75px;
}

.num-rules {
    font-size: 8px;
    height: 20px;
    width: 20px;
    top: -10px;
    right: -10px;
    @apply bg-opacity-50 rounded-xl text-center absolute
}
</style>
