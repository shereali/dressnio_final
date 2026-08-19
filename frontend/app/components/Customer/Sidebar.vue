<template>
  <nav
    :class="{ 'hidden md:block': !isMobile }"
    class="lg:w-64 md:w-42 bg-[#C0C0F2] h-full"
  >
    <!-- Sidebar Content -->
    <div class="flex flex-col h-full">
      <!-- Logo -->
      <div class="flex items-center justify-center p-4">
        <NuxtImg src="/img/p3.jpg" alt="Logo" class="h-8 w-auto" />
      </div>

      <!-- Navigation Links -->
      <div class="flex-1 overflow-y-auto">
        <ul class="space-y-2">
          <CustomerSidebarItem
            :icon="'mdi:home'"
            :text="'Home'"
            :page_route="'/customer'"
          />
          <CustomerSidebarItem
            :icon="'mdi:user'"
            :text="'Profile'"
            :page_route="'/customer/profile'"
          />
          <CustomerSidebarItem
            :icon="'icon-park-solid:transaction-order'"
            :text="'Order'"
            :page_route="'/customer/order'"
          />
          <CustomerSidebarItem
            :icon="'mdi:shopping-cart'"
            :text="'Cart'"
            :page_route="'/customer/cart'"
          />
          <CustomerSidebarItem
            :icon="'mdi:heart'"
            :text="'Wishlist'"
            :page_route="'/customer/wishlist'"
          />
          <!-- Add more menu items as needed -->
        </ul>
      </div>

      <!-- Logout Button -->
      <div class="p-4">
        <Icon
          v-if="$device.isMobile"
          @click="logout"
          class="text-red-600 font-bold"
          name="uiw:logout"
        />
        <button
          v-if="$device.isDesktop"
          @click="logout"
          class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded"
        >
          Logout
        </button>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";

const isMobile = ref(false);

const handleResize = () => {
  isMobile.value = window.innerWidth < 768;
};

onMounted(() => {
  // Detect mobile view (screen width less than 768px)
  handleResize();

  // Add event listener for window resize
  window.addEventListener("resize", handleResize);
});

onBeforeUnmount(() => {
  // Remove event listener when the component is unmounted
  window.removeEventListener("resize", handleResize);
});

const logout = () => {
  // Logic for logout
};
</script>

<style scoped>
/* Add custom styles for the sidebar */
</style>
