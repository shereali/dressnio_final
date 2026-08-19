<template>
  <div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
      <div v-for="(stat, index) in stats" :key="index">
        <Card :sales="stat.value" :variant="stat.variant">
          <div class="space-y-2">
            <h2 class="text-sm font-bold text-white/80 uppercase tracking-wider">{{ stat.label }}</h2>
            <p class="text-3xl font-extrabold text-white">{{ stat.prefix }}{{ stat.value.toLocaleString() }}</p>
          </div>
        </Card>
      </div>
    </div>

    <!-- User Info Section -->
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-8 transition-colors duration-300">
      <div class="flex items-center gap-6">
        <div class="w-20 h-20 bg-brand-50 dark:bg-brand-900/30 rounded-full flex items-center justify-center text-brand-600 dark:text-brand-400 text-3xl font-black">
          {{ auth.user?.name?.charAt(0) || 'A' }}
        </div>
        <div>
          <h3 class="text-2xl font-black text-gray-900 dark:text-white">Welcome back, {{ auth.user?.name || 'Administrator' }}!</h3>
          <p class="text-gray-500 dark:text-gray-400 font-medium italic">You are logged in as {{ auth.user?.email || 'admin@dressnio.com' }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  middleware: ["auth"],
});

const auth = useAuthStore();
const store = useUtilityStore();

onMounted(() => {
  store.pageTitle = "Dashboard";
});

const stats = [
  { label: "Total Sales", value: 12540, prefix: "$", variant: "indigo" },
  { label: "Total Orders", value: 156, prefix: "", variant: "emerald" },
  { label: "New Customers", value: 48, prefix: "", variant: "blue" },
  { label: "Revenue", value: 18920, prefix: "$", variant: "orange" },
  { label: "Avg. Order", value: 120, prefix: "$", variant: "purple" },
];
</script>

