<template>
  <CustomerLayout>
    <div class="space-y-8">
      <!-- Welcome Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-stone-100">
        <div>
          <span class="text-xs font-semibold uppercase tracking-wider text-rose-500">Customer Account</span>
          <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 mt-1">
            Welcome back, {{ customer?.name || 'Valued Shopper' }}
          </h1>
          <p class="text-sm text-stone-500 mt-1">
            Manage your fashion orders, shipping addresses, and personal details.
          </p>
        </div>

        <NuxtLink
          to="/shop"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-stone-900 hover:bg-stone-800 text-white text-sm font-medium transition-colors shadow-sm self-start sm:self-auto"
        >
          <Icon name="solar:bag-3-bold" class="w-4 h-4" />
          <span>Shop New Arrivals</span>
        </NuxtLink>
      </div>

      <!-- Quick Metrics Grid -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Orders -->
        <div class="bg-stone-50 border border-stone-200/80 rounded-2xl p-4 sm:p-5">
          <div class="w-10 h-10 rounded-xl bg-blue-100/70 text-blue-600 flex items-center justify-center mb-3">
            <Icon name="solar:box-minimalistic-bold-duotone" class="w-5 h-5" />
          </div>
          <p class="text-xs font-semibold text-stone-500 uppercase">Total Orders</p>
          <h3 class="text-xl sm:text-2xl font-black text-stone-900 mt-1">
            {{ ordersList.length }}
          </h3>
        </div>

        <!-- Pending Orders -->
        <div class="bg-stone-50 border border-stone-200/80 rounded-2xl p-4 sm:p-5">
          <div class="w-10 h-10 rounded-xl bg-amber-100/70 text-amber-600 flex items-center justify-center mb-3">
            <Icon name="solar:clock-circle-bold-duotone" class="w-5 h-5" />
          </div>
          <p class="text-xs font-semibold text-stone-500 uppercase">In Progress</p>
          <h3 class="text-xl sm:text-2xl font-black text-stone-900 mt-1">
            {{ pendingOrdersCount }}
          </h3>
        </div>

        <!-- Cart Items -->
        <div class="bg-stone-50 border border-stone-200/80 rounded-2xl p-4 sm:p-5">
          <div class="w-10 h-10 rounded-xl bg-rose-100/70 text-rose-600 flex items-center justify-center mb-3">
            <Icon name="solar:cart-large-minimalistic-bold-duotone" class="w-5 h-5" />
          </div>
          <p class="text-xs font-semibold text-stone-500 uppercase">In Cart</p>
          <h3 class="text-xl sm:text-2xl font-black text-stone-900 mt-1">
            {{ cartStore.items.length }}
          </h3>
        </div>

        <!-- Wishlist Items -->
        <div class="bg-stone-50 border border-stone-200/80 rounded-2xl p-4 sm:p-5">
          <div class="w-10 h-10 rounded-xl bg-purple-100/70 text-purple-600 flex items-center justify-center mb-3">
            <Icon name="solar:heart-bold-duotone" class="w-5 h-5" />
          </div>
          <p class="text-xs font-semibold text-stone-500 uppercase">Saved Items</p>
          <h3 class="text-xl sm:text-2xl font-black text-stone-900 mt-1">
            {{ wishlistCount }}
          </h3>
        </div>
      </div>

      <!-- Account Info & Address Snapshot -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Personal Information Card -->
        <div class="border border-stone-200/90 rounded-2xl p-5 sm:p-6 bg-white">
          <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-stone-900 text-base flex items-center gap-2">
              <Icon name="solar:user-bold" class="w-4 h-4 text-rose-500" />
              <span>Personal Details</span>
            </h3>
            <NuxtLink to="/customer/profile" class="text-xs font-semibold text-rose-600 hover:text-rose-700">
              Edit
            </NuxtLink>
          </div>
          <div class="space-y-2.5 text-sm">
            <div class="flex justify-between py-1.5 border-b border-stone-100">
              <span class="text-stone-500">Name</span>
              <span class="font-medium text-stone-900">{{ customer?.name || 'Not provided' }}</span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-stone-100">
              <span class="text-stone-500">Phone</span>
              <span class="font-medium text-stone-900">{{ customer?.mobile_no || 'Not provided' }}</span>
            </div>
            <div class="flex justify-between py-1.5">
              <span class="text-stone-500">Email</span>
              <span class="font-medium text-stone-900">{{ customer?.email || 'Not provided' }}</span>
            </div>
          </div>
        </div>

        <!-- Default Shipping Address -->
        <div class="border border-stone-200/90 rounded-2xl p-5 sm:p-6 bg-white">
          <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-stone-900 text-base flex items-center gap-2">
              <Icon name="solar:map-point-bold" class="w-4 h-4 text-rose-500" />
              <span>Default Shipping Address</span>
            </h3>
            <NuxtLink to="/customer/profile" class="text-xs font-semibold text-rose-600 hover:text-rose-700">
              Edit
            </NuxtLink>
          </div>
          <div class="space-y-2.5 text-sm">
            <div class="flex justify-between py-1.5 border-b border-stone-100">
              <span class="text-stone-500">City / District</span>
              <span class="font-medium text-stone-900">{{ customer?.city || 'Dhaka' }}</span>
            </div>
            <div class="py-1.5">
              <span class="text-stone-500 block text-xs mb-1">Street Address</span>
              <p class="font-medium text-stone-900 bg-stone-50 p-2.5 rounded-xl border border-stone-100">
                {{ customer?.address || 'No address saved yet. Update your profile to speed up checkout.' }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Orders Section -->
      <div>
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-bold text-stone-900 flex items-center gap-2">
            <Icon name="solar:bag-check-bold" class="w-5 h-5 text-rose-500" />
            <span>Recent Orders</span>
          </h2>
          <NuxtLink
            v-if="ordersList.length > 0"
            to="/customer/order"
            class="text-xs font-semibold text-rose-600 hover:text-rose-700 flex items-center gap-1"
          >
            <span>View All</span>
            <Icon name="solar:alt-arrow-right-linear" class="w-3.5 h-3.5" />
          </NuxtLink>
        </div>

        <!-- Loading State -->
        <div v-if="pendingOrders" class="py-12 text-center">
          <Icon name="svg-spinners:180-ring-with-bg" class="w-8 h-8 text-rose-500 mx-auto mb-2" />
          <p class="text-sm text-stone-500">Loading your orders...</p>
        </div>

        <!-- Empty State -->
        <div
          v-else-if="ordersList.length === 0"
          class="text-center py-12 px-4 rounded-2xl bg-stone-50 border border-stone-200/70"
        >
          <div class="w-14 h-14 rounded-2xl bg-rose-100/60 text-rose-500 flex items-center justify-center mx-auto mb-3">
            <Icon name="solar:bag-3-bold" class="w-7 h-7" />
          </div>
          <h4 class="font-bold text-stone-800 text-base">No orders placed yet</h4>
          <p class="text-xs text-stone-500 mt-1 max-w-sm mx-auto">
            Discover our premium embroidered Pakistani suits, festive kurtis, and designer sets.
          </p>
          <NuxtLink
            to="/shop"
            class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-xl bg-stone-900 hover:bg-stone-800 text-white text-xs font-medium transition-colors"
          >
            <span>Start Shopping</span>
            <Icon name="solar:arrow-right-linear" class="w-3.5 h-3.5" />
          </NuxtLink>
        </div>

        <!-- Orders Grid -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <CustomerOrderCard
            v-for="order in recentOrders"
            :key="order.id"
            :order="order"
            @order-cancelled="refreshOrders"
          />
        </div>
      </div>
    </div>
  </CustomerLayout>
</template>

<script setup>
import { computed } from "vue";
import { useCartStore } from "~/stores/cart";

definePageMeta({
  middleware: "auth",
});

const cartStore = useCartStore();

// Customer Profile
const { data: customerData } = await useApiFetch("/client/me", {
  server: false,
  lazy: true,
});
const customer = computed(() => customerData.value?.data || null);

// Orders
const { data: ordersData, pending: pendingOrders, refresh: refreshOrders } = await useApiFetch("/client/my-orders", {
  server: false,
  lazy: true,
});

const ordersList = computed(() => {
  const data = ordersData.value?.data;
  return Array.isArray(data) ? data : [];
});

const recentOrders = computed(() => ordersList.value.slice(0, 4));

const pendingOrdersCount = computed(() => {
  return ordersList.value.filter(
    (o) => o.order_status === "pending" || o.order_status === "processing"
  ).length;
});

// Wishlist
const { data: wishlistData } = await useApiFetch("/client/wishlist", {
  server: false,
  lazy: true,
});

const wishlistCount = computed(() => {
  const data = wishlistData.value?.data;
  return Array.isArray(data) ? data.length : 0;
});
</script>
