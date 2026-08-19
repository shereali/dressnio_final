<script setup></script>

<template>
  <div class="bg-white py-10">
    <!-- Headline Skeleton -->
    <div class="mb-10">
      <div
        class="h-8 bg-gray-200 opacity-50 rounded w-3/4 mx-auto animate-pulse"
      ></div>
      <div
        class="h-4 bg-gray-200 opacity-50 rounded w-1/2 mx-auto mt-2 animate-pulse"
      ></div>
    </div>

    <!-- Content Skeleton -->
    <div
      class="mx-auto py-8 flex flex-col lg:flex-row items-center lg:items-start gap-8 px-8"
    >
      <!-- Left Column Skeleton -->
      <div class="w-full lg:w-1/2 relative overflow-hidden">
        <div class="h-96 bg-gray-200 opacity-50 rounded-lg animate-pulse"></div>
      </div>

      <!-- Right Column Skeleton -->
      <div
        class="w-full lg:h-screen lg:w-1/2 flex flex-col items-center lg:items-start text-center lg:text-left shadow-lg p-5"
      >
        <!-- Banner Skeleton -->
        <div
          v-if="$device.isDesktop"
          class="bg-gray-200 opacity-50 h-40 w-80 rounded-lg mb-4 animate-pulse"
        ></div>

        <!-- Title Skeleton -->
        <div
          class="h-6 bg-gray-200 opacity-50 rounded w-3/4 mb-4 animate-pulse"
        ></div>

        <!-- Description Skeleton -->
        <div
          class="h-4 bg-gray-200 opacity-50 rounded w-full mb-2 animate-pulse"
        ></div>
        <div
          class="h-4 bg-gray-200 opacity-50 rounded w-5/6 mb-4 animate-pulse"
        ></div>

        <!-- Price Skeleton -->
        <div
          class="h-6 bg-gray-200 opacity-50 rounded w-1/4 mb-4 animate-pulse"
        ></div>

        <!-- Button Skeleton -->
        <div
          class="h-10 bg-gray-200 opacity-50 rounded w-1/3 animate-pulse"
        ></div>
      </div>
    </div>
  </div>
</template>

<style scoped></style>
