<template>
  <li class="my-0.5">
    <!-- Single Link Item -->
    <NuxtLink
      v-if="!item.children || item.children.length === 0"
      :to="item.link"
      @click="handleItemClick"
      class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-gray-600 dark:text-gray-400 hover:text-brand dark:hover:text-brand-400 hover:bg-brand-50/60 dark:hover:bg-brand-950/30 transition-all duration-200 group"
      active-class="!bg-brand-500 !text-white shadow-md shadow-brand-500/20 dark:!bg-brand-600 font-bold"
    >
      <Icon
        v-if="item.icon"
        :name="item.icon"
        class="w-4.5 h-4.5 flex-shrink-0 transition-transform group-hover:scale-110"
      />
      <span class="truncate">{{ item.label }}</span>
    </NuxtLink>

    <!-- Parent Dropdown Item -->
    <div v-else>
      <button
        type="button"
        @click="isOpen = !isOpen"
        class="w-full flex items-center justify-between gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-gray-600 dark:text-gray-400 hover:text-brand dark:hover:text-brand-400 hover:bg-brand-50/40 dark:hover:bg-brand-950/20 transition-all duration-200 group"
        :class="{ 'text-brand dark:text-brand-400 font-bold': isChildActive }"
      >
        <div class="flex items-center gap-3 truncate">
          <Icon
            v-if="item.icon"
            :name="item.icon"
            class="w-4.5 h-4.5 flex-shrink-0 transition-transform group-hover:scale-110"
            :class="{ 'text-brand dark:text-brand-400': isChildActive }"
          />
          <span class="truncate">{{ item.label }}</span>
        </div>
        <Icon
          name="solar:alt-arrow-down-bold"
          class="w-3.5 h-3.5 flex-shrink-0 text-gray-400 transition-transform duration-200"
          :class="{ 'rotate-180 text-brand': isOpen }"
        />
      </button>

      <!-- Sub-menu list with smooth expand -->
      <ul
        v-show="isOpen"
        class="mt-1 ml-4 pl-3 border-l-2 border-gray-100 dark:border-gray-800 space-y-0.5 transition-all"
      >
        <SidebarItem
          v-for="(child, cIdx) in item.children"
          :key="child.id || cIdx"
          :item="child"
        />
      </ul>
    </div>
  </li>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
});

const route = useRoute();
const sidebarOpen = useState('admin_sidebar_open', () => false);

// Check if any child route is active
const isChildActive = computed(() => {
  if (!props.item.children) return false;
  return checkActiveRecursive(props.item.children);
});

function checkActiveRecursive(items) {
  for (const child of items) {
    if (child.link && route.path.startsWith(child.link)) return true;
    if (child.children && checkActiveRecursive(child.children)) return true;
  }
  return false;
}

const isOpen = ref(isChildActive.value);

watch(
  () => route.path,
  () => {
    if (isChildActive.value) {
      isOpen.value = true;
    }
  }
);

function handleItemClick() {
  // On mobile, close the drawer after clicking a link
  if (process.client && window.innerWidth < 1024) {
    sidebarOpen.value = false;
  }
}
</script>
