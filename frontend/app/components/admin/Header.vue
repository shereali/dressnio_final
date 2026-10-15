<!-- components/Header.vue -->
<template>
  <header class="p-4 px-6 flex justify-between items-center bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800 transition-colors">
    <!-- Toggle buttons -->
    <div class="flex items-center gap-3">
      <!-- Mobile hamburger -->
      <button
        @click="sidebarOpen = !sidebarOpen"
        class="lg:hidden p-2 rounded-xl text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors focus:outline-none"
        aria-label="Toggle Mobile Sidebar"
      >
        <Icon name="solar:hamburger-menu-bold" class="w-6 h-6" />
      </button>

      <!-- Desktop collapse toggle -->
      <button
        @click="isCollapsed = !isCollapsed"
        class="hidden lg:flex p-2 rounded-xl text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors focus:outline-none"
        :title="isCollapsed ? 'Expand Sidebar' : 'Collapse Sidebar'"
      >
        <Icon
          :name="isCollapsed ? 'solar:sidebar-minimalistic-bold-duotone' : 'solar:sidebar-minimalistic-bold-duotone'"
          class="w-5 h-5 text-gray-600 dark:text-gray-300"
        />
      </button>

      <NuxtLink to="/" target="_blank" class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 dark:text-gray-400 hover:text-brand dark:hover:text-brand-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors border border-gray-200 dark:border-gray-700">
        <Icon name="solar:globe-bold" class="w-4 h-4 text-brand" />
        <span>View Storefront</span>
      </NuxtLink>
    </div>

    <!-- Right Controls -->
    <div class="flex items-center space-x-3">
      <!-- Dark Mode Toggle -->
      <ClientOnly>
        <button
          @click="isDark = !isDark"
          class="p-2 rounded-xl text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-brand dark:hover:text-brand-400 transition-colors"
          :title="isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
        >
          <Icon
            :name="isDark ? 'solar:sun-2-bold-duotone' : 'solar:moon-bold-duotone'"
            class="w-5 h-5"
          />
        </button>
      </ClientOnly>

      <!-- User Avatar Dropdown -->
      <div class="relative inline-block text-left">
        <button
          type="button"
          class="group flex items-center gap-2.5 p-1.5 pr-3 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200 border border-gray-100 dark:border-gray-800"
          id="user-menu"
          @click="isDropdown = !isDropdown"
        >
          <div class="relative w-8 h-8 rounded-full overflow-hidden ring-2 ring-brand-500/20 group-hover:ring-brand-500 transition-all bg-brand-500/10 flex items-center justify-center">
            <Icon name="solar:user-bold" class="w-5 h-5 text-brand" />
          </div>
          <span class="hidden md:inline-block text-xs font-bold text-gray-700 dark:text-gray-200">Admin</span>
          <Icon name="solar:alt-arrow-down-bold" class="w-3.5 h-3.5 text-gray-400 group-hover:text-gray-600 transition-transform" :class="{ 'rotate-180': isDropdown }" />
        </button>

        <!-- Dropdown menu -->
        <div
          class="origin-top-right absolute right-0 mt-3 w-60 rounded-2xl shadow-2xl bg-white dark:bg-gray-900 ring-1 ring-black/5 dark:ring-gray-800 divide-y divide-gray-100 dark:divide-gray-800 overflow-hidden transform transition-all z-50"
          v-if="isDropdown"
        >
          <div class="px-4 py-3 bg-gray-50/70 dark:bg-gray-800/50">
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Administrator</p>
            <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ auth.user?.email || 'admin@dressnio.com' }}</p>
          </div>
          <div class="py-1.5">
            <NuxtLink
              to="/admin/dashboard"
              @click="isDropdown = false"
              class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-50 dark:hover:bg-brand-900/20 hover:text-brand dark:hover:text-brand-400 transition-colors"
            >
              <Icon name="solar:widget-2-bold-duotone" class="w-4 h-4 text-brand"/>
              Dashboard Overview
            </NuxtLink>
          </div>
          <div class="py-1.5">
            <button
              class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors font-medium"
              @click.prevent="handleLogout"
            >
              <Icon name="solar:logout-bold-duotone" class="w-4 h-4 text-rose-500"/>
              Sign Out
            </button>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
const sidebarOpen = useState('admin_sidebar_open', () => false);
const isCollapsed = useState('admin_sidebar_collapsed', () => false);
const isDropdown = ref(false);
const auth = useAuthStore();
const colorMode = useColorMode();

const isDark = computed({
  get() {
    return colorMode.value === 'dark';
  },
  set() {
    colorMode.preference = colorMode.value === 'dark' ? 'light' : 'dark';
  }
});

const handleLogout = async () => {
  try {
    isDropdown.value = false;
    await auth.logout("/admin/login");
  } catch (error) {
    console.log("error", error?.data?.errors);
    navigateTo("/admin/login");
  }
};
</script>
