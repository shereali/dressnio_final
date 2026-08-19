<script lang="ts" setup></script>

<template>
  <div class="max-w-max mx-auto py-16 px-10">
    <!-- Headline Skeleton -->
    <div class="mb-10">
      <div
        class="h-8 bg-gray-200 opacity-50 rounded w-3/4 mx-auto animate-pulse"
      ></div>
      <div
        class="h-4 bg-gray-200 opacity-50 rounded w-1/2 mx-auto mt-2 animate-pulse"
      ></div>
    </div>

    <!-- Tabs Navigation Skeleton -->
    <div class="flex mb-4">
      <div
        class="flex-1 py-4 px-4 bg-gray-200 opacity-50 rounded-l-full animate-pulse"
      ></div>
      <div
        class="flex-1 py-4 px-4 bg-gray-200 opacity-50 rounded-r-full animate-pulse"
      ></div>
    </div>

    <!-- Tab Content Skeleton -->
    <div>
      <!-- Product Card Skeletons -->
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div
          v-for="index in 8"
          :key="index"
          class="flex flex-col items-center animate-pulse"
        >
          <!-- Image Placeholder -->
          <div class="w-36 h-36 bg-gray-200 opacity-50 rounded mb-4"></div>
          <!-- Text Placeholder -->
          <div class="h-4 bg-gray-200 opacity-50 rounded w-28 mb-2"></div>
          <div class="h-4 bg-gray-200 opacity-50 rounded w-20"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped></style>
