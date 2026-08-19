<!-- layouts/default.vue -->
<template>
  <div class="flex h-full">
    <CustomerSidebar />
    <div class="flex-1">
      <!-- Your page content goes here -->
      <slot></slot>
    </div>
  </div>
</template>

<style scoped>
/* Add custom styles for the layout */
</style>
