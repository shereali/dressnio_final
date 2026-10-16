<template>
  <div class="customer-portal-wrapper min-h-[75vh] py-6 sm:py-10 px-3 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row gap-6 lg:gap-8 items-start">
      <!-- Sidebar Navigation -->
      <CustomerSidebar class="w-full md:w-64 lg:w-72 flex-shrink-0" />
      
      <!-- Main Content Area -->
      <main class="flex-1 w-full bg-white rounded-2xl border border-stone-200/90 shadow-sm p-4 sm:p-6 lg:p-8 min-h-[500px]">
        <slot></slot>
      </main>
    </div>
  </div>
</template>

<script setup>
// Shared customer portal wrapper layout
</script>

<style scoped>
.customer-portal-wrapper {
  animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(6px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
