<script lang="ts" setup>
const store = useUtilityStore();
</script>

<template>
  <div>
    <div class="mb-8">
      <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">{{ store.pageTitle || 'Dashboard' }}</h2>
      <p class="text-gray-500 dark:text-gray-400 mt-1 font-medium italic">Manage your {{ (store.pageTitle || 'Admin').toLowerCase() }} here.</p>
    </div>
  </div>
</template>

<style scoped></style>
