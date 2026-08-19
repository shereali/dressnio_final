<!-- components/SidebarItem.vue -->
<template>
  <li>
    <NuxtLink
      :to="item.link"
      @click="toggleDropdown(item)"
      class="flex items-center group py-2.5 px-4 cursor-pointer text-sm font-bold text-gray-500 hover:text-brand dark:text-gray-400 dark:hover:text-brand-400 transition-all duration-200"
      active-class="!text-brand dark:!text-brand-400 bg-brand-50/50 dark:bg-brand-900/10"
    >
      <Icon
        class="w-5 h-5 shadow-sm ring-1 ring-gray-100 dark:ring-gray-800 rounded-lg group-hover:bg-brand group-hover:text-white dark:group-hover:bg-brand transition-all duration-200 shadow-brand-500/10"
        :name="item.icon"
      />
      <span class="px-3 truncate">{{ item.label }}</span>
      <span class="ml-auto flex-shrink-0" v-if="item.children">
        <Icon
          class="transition-transform duration-200"
          :class="{ 'rotate-90': item.selected }"
          name="ph:caret-right-bold"
        />
      </span>
    </NuxtLink>
    <ul
      v-if="item.children && item.children.length && item.selected"
      class="mt-1 space-y-1"
    >
      <template
        v-for="child in item.children"
        :key="child.id"
      >
        <div class="pl-4">
          <AdminSidebarItem :item="child" />
        </div>
      </template>
    </ul>
  </li>
</template>

<script setup>
import { ref } from "vue";

const props = defineProps(["item"]);
const openDropdown = ref(null);

const toggleDropdown = (item) => {
  // openDropdown.value = openDropdown.value === itemId ? null : itemId;

  item.selected = !item.selected;
};
</script>

<style scoped>
/* Add your additional styles here */
</style>
