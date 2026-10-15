<template>
   <suspense timeout="0">
    <template #default>
      <component :is="layout">
        <router-view />
      </component>    
    </template>
    <template #fallback>
      <Preloader />
    </template>
  </suspense>
</template>

<script lang="ts">
import { defineComponent, computed } from "vue";
import { useRouter } from "vue-router";
import Preloader from "./components/Preloader.vue";

const defaultLayout = "default";

export default defineComponent({
    setup() {
        const { currentRoute } = useRouter();
        const layout = computed(() => `${currentRoute.value.meta.layout || defaultLayout}-layout`);
        return {
            layout,
        };
    },
    components: { Preloader }
});
</script>