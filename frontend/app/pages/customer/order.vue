<template>
  <CustomerLayout>
    <div class="space-y-6">
      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-stone-100">
        <div>
          <h1 class="text-2xl font-extrabold text-stone-900">Order History</h1>
          <p class="text-xs sm:text-sm text-stone-500 mt-1">
            Track your parcel status, view invoices, or cancel pending orders.
          </p>
        </div>

        <NuxtLink
          to="/track-order"
          class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs sm:text-sm font-semibold transition-colors self-start sm:self-auto"
        >
          <Icon name="solar:radar-2-bold" class="w-4 h-4 text-brand-500" />
          <span>Quick Parcel Tracker</span>
        </NuxtLink>
      </div>

      <!-- Filter Tabs -->
      <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
        <button
          v-for="tab in filterTabs"
          :key="tab.value"
          @click="activeTab = tab.value"
          class="px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition-all whitespace-nowrap"
          :class="[
            activeTab === tab.value
              ? 'bg-stone-900 text-white shadow-sm'
              : 'bg-stone-100 text-stone-600 hover:bg-stone-200/80 hover:text-stone-900'
          ]"
        >
          {{ tab.label }}
          <span
            v-if="tab.count !== null"
            class="ml-1.5 px-1.5 py-0.5 rounded-full text-[10px] font-bold"
            :class="activeTab === tab.value ? 'bg-white/20 text-white' : 'bg-stone-200 text-stone-700'"
          >
            {{ tab.count }}
          </span>
        </button>
      </div>

      <!-- Loading State -->
      <div v-if="pending" class="py-16 text-center">
        <Icon name="svg-spinners:180-ring-with-bg" class="w-8 h-8 text-brand-500 mx-auto mb-2" />
        <p class="text-sm text-stone-500">Fetching your orders...</p>
      </div>

      <!-- Empty State -->
      <div
        v-else-if="filteredOrders.length === 0"
        class="text-center py-16 px-4 rounded-2xl bg-stone-50 border border-stone-200/70"
      >
        <div class="w-16 h-16 rounded-2xl bg-brand-100/60 text-brand-500 flex items-center justify-center mx-auto mb-3">
          <Icon name="solar:box-minimalistic-bold" class="w-8 h-8" />
        </div>
        <h3 class="font-bold text-stone-800 text-lg">No orders found</h3>
        <p class="text-xs text-stone-500 mt-1 max-w-sm mx-auto">
          {{ activeTab === 'all' ? "You haven't placed any orders yet. Discover our latest fashion collection." : "No orders found in this filter category." }}
        </p>
        <NuxtLink
          to="/shop"
          class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 rounded-xl bg-stone-900 hover:bg-stone-800 text-white text-xs font-medium transition-colors shadow-sm"
        >
          <span>Explore Catalog</span>
          <Icon name="solar:arrow-right-linear" class="w-4 h-4" />
        </NuxtLink>
      </div>

      <!-- Orders Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <CustomerOrderCard
          v-for="order in filteredOrders"
          :key="order.id"
          :order="order"
          @order-cancelled="refresh"
        />
      </div>
    </div>
  </CustomerLayout>
</template>

<script setup>
import { ref, computed } from "vue";

definePageMeta({
  middleware: "auth",
});

const activeTab = ref("all");

const { data: ordersResponse, pending, refresh } = await useApiFetch("/client/my-orders", {
  server: false,
  lazy: true,
});

const orders = computed(() => {
  const d = ordersResponse.value?.data;
  return Array.isArray(d) ? d : [];
});

const filteredOrders = computed(() => {
  if (activeTab.value === "all") return orders.value;
  return orders.value.filter((o) => (o.order_status || "").toLowerCase() === activeTab.value);
});

const filterTabs = computed(() => [
  { label: "All Orders", value: "all", count: orders.value.length },
  {
    label: "Pending",
    value: "pending",
    count: orders.value.filter((o) => (o.order_status || "").toLowerCase() === "pending").length,
  },
  {
    label: "Processing",
    value: "processing",
    count: orders.value.filter((o) => (o.order_status || "").toLowerCase() === "processing").length,
  },
  {
    label: "Shipped",
    value: "shipped",
    count: orders.value.filter((o) => (o.order_status || "").toLowerCase() === "shipped").length,
  },
  {
    label: "Delivered",
    value: "delivered",
    count: orders.value.filter((o) => (o.order_status || "").toLowerCase() === "delivered").length,
  },
]);
</script>
