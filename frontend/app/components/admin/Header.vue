<!-- components/Header.vue -->
<template>
  <header class="header p-4 flex justify-between items-center">
    <!-- Toggle button for mobile view -->
    <button @click="toggleSidebar" class="lg:hidden">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        class="h-6 w-6"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M4 6h16M4 12h16m-7 6h7"
        ></path>
      </svg>
    </button>

    <!-- Logo or site title (Removed redundant text) -->
    <div class="flex-1"></div>

    <!-- User information or other header content -->
    <div class="flex items-center space-x-4">
      <!-- Dark Mode Toggle -->
      <ClientOnly>
        <UButton
          :icon="isDark ? 'i-heroicons-moon-20-solid' : 'i-heroicons-sun-20-solid'"
          color="gray"
          variant="ghost"
          aria-label="Theme"
          class="rounded-full"
          @click="isDark = !isDark"
        />
        <template #fallback>
          <div class="w-8 h-8" />
        </template>
      </ClientOnly>

      <!-- User Avatar Dropdown -->
      <div class="relative inline-block text-left">
        <div>
          <button
            type="button"
            class="group flex items-center p-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200"
            id="user-menu"
            @click="isDropdown = !isDropdown"
          >
            <div class="relative w-8 h-8 rounded-full overflow-hidden ring-2 ring-transparent group-hover:ring-brand-500 transition-all">
              <img
                class="w-full h-full object-cover"
                src="/images/avatar.jpg"
                alt="Admin"
              />
            </div>
            <Icon name="ph:caret-down-bold" class="ml-2 w-4 h-4 text-gray-400 group-hover:text-gray-600" />
          </button>
        </div>

        <!-- Dropdown menu, show/hide based on menu state -->
        <div
          class="origin-top-right absolute right-0 mt-3 w-56 rounded-2xl shadow-2xl bg-white dark:bg-gray-900 ring-1 ring-black ring-opacity-5 dark:ring-gray-800 divide-y divide-gray-100 dark:divide-gray-800 overflow-hidden transform transition-all"
          v-if="isDropdown"
        >
          <div class="px-4 py-3 bg-gray-50/50 dark:bg-gray-800/50">
            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Signed in as</p>
            <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ auth.user?.email || 'admin@dressnio.com' }}</p>
          </div>
          <div class="py-1">
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-50 dark:hover:bg-brand-900/20 hover:text-brand dark:hover:text-brand-400 transition-colors">
              <Icon name="solar:user-bold" class="w-5 h-5"/>
              Account Settings
            </a>
          </div>
          <div class="py-1">
            <button
              class="flex w-full items-center gap-3 px-4 py-3 text-sm text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors"
              @click.prevent="handleLogout"
            >
              <Icon name="solar:logout-bold" class="w-5 h-5"/>
              Sign out
            </button>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
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
    await auth.logout("/admin/login");
  } catch (error) {
    console.log("error", error.data?.errors);
  }
};
</script>

<style scoped>
/* Add any additional styles specific to the Header component here */
</style>
