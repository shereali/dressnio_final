<script lang="ts" setup></script>

<template>
  <div class="flex h-screen bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 transition-colors duration-300">
    <!-- Sidebar -->
    <AdminSidebar />

    <!-- Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Top Navbar -->
      <header class="bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800 shadow-sm z-10 transition-colors duration-300">
        <!-- Add your top navbar content here -->
        <AdminHeader />
      </header>

      <!-- Main Content -->
      <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 dark:bg-gray-950 p-8 transition-colors duration-300">
        <AdminBreadcrumb />
        <!-- Page Title -->
        <AdminPageTitle />

        <!-- Your page content goes here -->
        <slot />
      </main>
    </div>
  </div>
</template>

<style scoped></style>
