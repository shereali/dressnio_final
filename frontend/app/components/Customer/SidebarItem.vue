<template>
  <li>
    <NuxtLink
      :to="page_route"
      class="group flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200"
      :class="[
        isActive
          ? 'bg-gradient-to-r from-rose-500 to-rose-600 text-white shadow-sm shadow-rose-200'
          : 'text-stone-700 hover:text-stone-900 hover:bg-stone-100/80'
      ]"
    >
      <div class="flex items-center gap-3">
        <Icon
          :name="icon"
          class="w-5 h-5 transition-transform duration-200 group-hover:scale-110"
          :class="isActive ? 'text-white' : 'text-stone-400 group-hover:text-stone-600'"
        />
        <span>{{ text }}</span>
      </div>
      <Icon
        name="solar:alt-arrow-right-linear"
        class="w-4 h-4 transition-transform duration-200"
        :class="isActive ? 'opacity-80 translate-x-0.5 text-white' : 'opacity-0 -translate-x-1 group-hover:opacity-60 group-hover:translate-x-0 text-stone-400'"
      />
    </NuxtLink>
  </li>
</template>

<script setup>
import { computed } from "vue";
import { useRoute } from "vue-router";

const props = defineProps({
  icon: { type: String, required: true },
  text: { type: String, required: true },
  page_route: { type: String, required: true }
});

const route = useRoute();
const isActive = computed(() => route.path === props.page_route);
</script>
