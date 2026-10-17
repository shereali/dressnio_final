<template>
  <aside class="w-full">
    <!-- Desktop & Tablet Sidebar Card -->
    <div class="bg-white rounded-2xl border border-stone-200/90 shadow-sm p-4 sm:p-5 flex flex-col justify-between">
      <div>
        <!-- Customer Profile Quick Header -->
        <div class="flex items-center gap-3.5 pb-5 mb-5 border-b border-stone-100">
          <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-brand-500 to-brand-400 text-white flex items-center justify-center font-bold text-lg shadow-sm shadow-brand-200 flex-shrink-0">
            {{ customerInitials }}
          </div>
          <div class="overflow-hidden">
            <h3 class="font-semibold text-stone-900 text-base truncate">
              {{ customer?.name || "Customer" }}
            </h3>
            <p class="text-xs text-stone-500 truncate flex items-center gap-1 mt-0.5">
              <Icon name="solar:phone-linear" class="w-3.5 h-3.5 flex-shrink-0" />
              <span>{{ customer?.mobile_no || "Authenticated" }}</span>
            </p>
          </div>
        </div>

        <!-- Navigation Menu -->
        <nav>
          <!-- Mobile Horizontal Scroll Pills -->
          <ul class="flex md:hidden overflow-x-auto gap-2 pb-2 scrollbar-none">
            <li v-for="item in navItems" :key="item.page_route" class="flex-shrink-0">
              <NuxtLink
                :to="item.page_route"
                class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-medium transition-all"
                :class="route.path === item.page_route ? 'bg-brand-500 text-white shadow-sm' : 'bg-stone-100 text-stone-700'"
              >
                <Icon :name="item.icon" class="w-4 h-4" />
                <span>{{ item.text }}</span>
              </NuxtLink>
            </li>
          </ul>

          <!-- Desktop Vertical Menu -->
          <ul class="hidden md:flex flex-col space-y-1.5">
            <CustomerSidebarItem
              v-for="item in navItems"
              :key="item.page_route"
              :icon="item.icon"
              :text="item.text"
              :page_route="item.page_route"
            />
          </ul>
        </nav>
      </div>

      <!-- Action Footer / Logout -->
      <div class="pt-5 mt-5 border-t border-stone-100 flex items-center justify-between md:block">
        <button
          @click="handleLogout"
          :disabled="isLoggingOut"
          class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium text-brand-600 hover:text-brand-700 bg-brand-50/70 hover:bg-brand-100/70 transition-colors duration-200"
        >
          <Icon v-if="isLoggingOut" name="svg-spinners:180-ring-with-bg" class="w-4 h-4" />
          <Icon v-else name="solar:logout-2-linear" class="w-4 h-4" />
          <span>{{ isLoggingOut ? 'Logging out...' : 'Log Out' }}</span>
        </button>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { ref, computed } from "vue";
import { useRoute } from "vue-router";
import { useAuthStore } from "~/stores/useAuthStore";

const route = useRoute();
const authStore = useAuthStore();
const isLoggingOut = ref(false);

const navItems = [
  { icon: "solar:widget-bold-duotone", text: "Dashboard", page_route: "/customer" },
  { icon: "solar:box-minimalistic-bold-duotone", text: "My Orders", page_route: "/customer/order" },
  { icon: "solar:user-bold-duotone", text: "Profile Settings", page_route: "/customer/profile" },
  { icon: "solar:cart-large-minimalistic-bold-duotone", text: "My Cart", page_route: "/customer/cart" },
  { icon: "solar:heart-bold-duotone", text: "Wishlist", page_route: "/customer/wishlist" },
  { icon: "solar:radar-2-bold-duotone", text: "Track Order", page_route: "/track-order" },
];

const { data: customerData } = await useApiFetch("/client/me", {
  server: false,
  lazy: true,
});

const customer = computed(() => customerData.value?.data || null);

const customerInitials = computed(() => {
  const name = customer.value?.name || "Customer";
  return name.slice(0, 1).toUpperCase();
});

const handleLogout = async () => {
  isLoggingOut.value = true;
  try {
    await authStore.customerLogout("/login");
  } finally {
    isLoggingOut.value = false;
  }
};
</script>
