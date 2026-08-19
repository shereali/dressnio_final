<template>
  <li
    class="p-3 cursor-pointer"
    :class="{ 'bg-[#7373E3]': isActive, 'text-center': $device.isMobile }"
    @click="navigateToRoute"
  >
    <Icon :name="icon" class="mr-2" :class="{ 'text-3xl': $device.isMobile }" />
    <span v-if="$device.isDesktop || $device.isTablet">{{ text }}</span>
  </li>
</template>

<script setup>
import { computed, defineProps } from "vue";
import { useRoute, useRouter } from "vue-router";

const props = defineProps(["icon", "text", "page_route"]);
const route = useRoute();
const router = useRouter();

const isActive = computed(() => route.path === props.page_route);

const navigateToRoute = () => {
  // Logic for navigating to the specified route
  router.push(props.page_route);
};
</script>

<style scoped>
/* Add custom styles for the sidebar items */
</style>
