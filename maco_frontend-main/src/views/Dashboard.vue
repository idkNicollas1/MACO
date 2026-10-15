<template>
    <div class="mt-2 mb-5" v-if="roleUser == '1:ADMIN'">
        <DashboardAdmin />
    </div>
    <div class="mt-2 mb-5" v-else-if="roleUser == '2:ADVISOR'">
        <DashboardAdvisor />
    </div>
    <div class="mt-2 mb-5" v-else-if="roleUser == '3:AUTHOR' || roleUser == '4:EXTERNAL'">
        <DashboardAuthor />
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, reactive, toRefs } from "vue"
import Spinner from "../components/Spinner.vue"
import DashboardAdmin from "../components/dashboards/DashboardAdmin.vue"
import DashboardAuthor from "../components/dashboards/DashboardAuthor.vue"
import DashboardAdvisor from "../components/dashboards/DashboardAdvisor.vue"
import { getUserRole } from "../hooks/useAuth";

export default defineComponent({
    async setup() {
        const infoLoaded = ref(false)
        const roleUser = ref()
        roleUser.value = getUserRole()
        infoLoaded.value = true
        return {
            infoLoaded,
            roleUser,
        }
    },
    components: { Spinner, DashboardAdmin, DashboardAuthor, DashboardAdvisor }
})
</script>