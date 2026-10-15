<template>
  <div class="space-y-6">
    <!-- Real-time Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div>
        <Card :sales="overview.total_revenue" variant="indigo">
          <div class="space-y-2">
            <h2 class="text-sm font-bold text-white/80 uppercase tracking-wider">Total Revenue</h2>
            <p class="text-3xl font-extrabold text-white">৳{{ (overview.total_revenue || 0).toLocaleString() }}</p>
          </div>
        </Card>
      </div>

      <div>
        <Card :sales="overview.total_orders" variant="emerald">
          <div class="space-y-2">
            <h2 class="text-sm font-bold text-white/80 uppercase tracking-wider">Total Orders</h2>
            <p class="text-3xl font-extrabold text-white">{{ overview.total_orders || 0 }}</p>
          </div>
        </Card>
      </div>

      <div>
        <Card :sales="overview.total_customers" variant="blue">
          <div class="space-y-2">
            <h2 class="text-sm font-bold text-white/80 uppercase tracking-wider">Shoppers</h2>
            <p class="text-3xl font-extrabold text-white">{{ overview.total_customers || 0 }}</p>
          </div>
        </Card>
      </div>

      <div>
        <Card :sales="overview.active_products" variant="purple">
          <div class="space-y-2">
            <h2 class="text-sm font-bold text-white/80 uppercase tracking-wider">Active Products</h2>
            <p class="text-3xl font-extrabold text-white">{{ overview.active_products || 0 }}</p>
          </div>
        </Card>
      </div>
    </div>

    <!-- Admin Welcome & Status -->
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-6 flex items-center justify-between flex-wrap gap-4">
      <div class="flex items-center gap-4">
        <div class="w-16 h-16 bg-brand-50 dark:bg-brand-900/30 rounded-full flex items-center justify-center text-brand-600 dark:text-brand-400 text-2xl font-black">
          {{ auth.user?.name?.charAt(0) || 'A' }}
        </div>
        <div>
          <h3 class="text-xl font-black text-gray-900 dark:text-white">Welcome back, {{ auth.user?.name || 'Administrator' }}!</h3>
          <p class="text-gray-500 text-sm">Dressnio Ecommerce Engine • All APIs Active & Synchronized</p>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <NuxtLink to="/admin/products/create" class="dn-btn dn-btn-primary dn-btn-sm">
          <Icon name="solar:add-circle-bold" />
          <span>Add New Product</span>
        </NuxtLink>
        <NuxtLink to="/admin/order" class="dn-btn dn-btn-dark dn-btn-sm">
          <Icon name="solar:cart-check-bold" />
          <span>Manage Orders ({{ overview.pending_orders || 0 }} Pending)</span>
        </NuxtLink>
      </div>
    </div>

    <!-- Recent Orders & Low Stock Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Recent Orders -->
      <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-6">
        <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
          <h4 class="font-extrabold text-lg text-gray-900 dark:text-white flex items-center gap-2">
            <Icon name="solar:cart-check-bold" class="text-brand" />
            <span>Recent Orders</span>
          </h4>
          <NuxtLink to="/admin/order" class="text-xs text-brand font-bold hover:underline">View All</NuxtLink>
        </div>

        <div v-if="recentOrders.length === 0" class="text-sm text-gray-400 py-6 text-center">
          No orders received yet.
        </div>

        <div v-else class="space-y-3">
          <div
            v-for="order in recentOrders"
            :key="order.id"
            class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50 hover:bg-gray-100 transition-colors text-sm"
          >
            <div>
              <strong class="text-brand font-bold">#{{ order.order_no }}</strong>
              <div class="text-xs text-gray-500">{{ order.customer_name }} • {{ order.customer_phone }}</div>
            </div>
            <div class="text-right">
              <div class="font-bold text-gray-900 dark:text-white">৳{{ order.total_amount }}</div>
              <span class="dn-status-badge" :class="`dn-status-${order.order_status}`">
                {{ order.order_status }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Low Stock Alerts -->
      <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-6">
        <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
          <h4 class="font-extrabold text-lg text-gray-900 dark:text-white flex items-center gap-2">
            <Icon name="solar:danger-triangle-bold" class="text-amber-500" />
            <span>Low Stock Alerts</span>
          </h4>
          <span class="text-xs text-amber-600 font-bold bg-amber-50 px-2 py-1 rounded-md">{{ lowStockItems.length }} alerts</span>
        </div>

        <div v-if="lowStockItems.length === 0" class="text-sm text-gray-400 py-6 text-center">
          All products have sufficient inventory.
        </div>

        <div v-else class="space-y-3">
          <div
            v-for="item in lowStockItems"
            :key="item.id"
            class="flex items-center justify-between p-3 rounded-xl bg-amber-50/50 border border-amber-100 text-sm"
          >
            <div>
              <strong class="text-gray-900">{{ item.product?.name || 'Garment Item' }}</strong>
              <div class="text-xs text-gray-500">Size: {{ item.size?.standard || 'Standard' }}</div>
            </div>
            <span class="font-bold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-md text-xs">
              {{ item.quantity }} left
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";

definePageMeta({
  middleware: ["auth"],
});

const auth = useAuthStore();
const store = useUtilityStore();
const config = useRuntimeConfig();

onMounted(() => {
  store.pageTitle = "Dashboard";
});

const { data: dashboardData } = await useApiFetch("/dashboard/stats");

const overview = computed(() => dashboardData.value?.data?.overview || {});
const recentOrders = computed(() => dashboardData.value?.data?.recent_orders || []);
const lowStockItems = computed(() => dashboardData.value?.data?.low_stock_items || []);
</script>
