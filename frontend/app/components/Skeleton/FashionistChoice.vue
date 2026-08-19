<script lang="ts" setup></script>

<template>
  <div class="max-w-max mx-auto py-16 px-10 bg-[#B19FF9]">
    <!-- Headline Skeleton -->
    <div class="mb-10">
      <div
        class="h-8 bg-gray-200 opacity-50 rounded w-3/4 mx-auto animate-pulse"
      ></div>
      <div
        class="h-4 bg-gray-200 opacity-50 rounded w-1/2 mx-auto mt-2 animate-pulse"
      ></div>
    </div>

    <!-- Carousel Skeleton -->
    <div class="overflow-hidden">
      <div class="flex gap-6">
        <div v-for="index in 5" :key="index" class="flex-shrink-0 w-64">
          <!-- Product Card Skeleton -->
          <div class="bg-white shadow rounded-md p-4 animate-pulse">
            <!-- Image Placeholder -->
            <div class="h-40 bg-gray-200 opacity-50 rounded mb-4"></div>
            <!-- Text Placeholder -->
            <div class="h-4 bg-gray-200 opacity-50 rounded w-3/4 mb-2"></div>
            <div class="h-4 bg-gray-200 opacity-50 rounded w-1/2"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination and Navigation Skeleton -->
    <div class="flex justify-between mt-8">
      <!-- Pagination Dots -->
      <div class="flex space-x-2">
        <div
          v-for="dot in 5"
          :key="dot"
          class="w-3 h-3 bg-gray-200 opacity-50 rounded-full animate-pulse"
        ></div>
      </div>

      <!-- Navigation Arrows -->
      <div class="flex space-x-4">
        <div
          class="w-8 h-8 bg-gray-200 opacity-50 rounded-full animate-pulse"
        ></div>
        <div
          class="w-8 h-8 bg-gray-200 opacity-50 rounded-full animate-pulse"
        ></div>
      </div>
    </div>
  </div>
</template>

<style scoped></style>
