<template>
  <div class="flex h-screen bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 transition-colors duration-300 overflow-hidden font-sans">
    <!-- Sidebar Navigation -->
    <AdminSidebar />

    <!-- Content Workspace -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <!-- Top Navbar -->
      <AdminHeader />

      <!-- Main Content Container (Full Width) -->
      <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50/70 dark:bg-gray-950/70 p-4 sm:p-6 lg:p-8 transition-colors duration-300">
        <div class="w-full space-y-6">
          <AdminBreadcrumb />
          <AdminPageTitle />
          <slot />
        </div>
      </main>
    </div>

    <!-- Admin Notification Toasts -->
    <SnackbarToast />
  </div>
</template>

<script setup>
</script>
