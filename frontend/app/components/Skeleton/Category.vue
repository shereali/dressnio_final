<script lang="ts" setup></script>

<template>
  <div class="max-w-max mx-auto py-20 px-10">
    <!-- Headline Skeleton -->
    <div class="mb-10">
      <!-- Title Placeholder -->
      <div
        class="h-8 bg-gray-200 opacity-50 rounded w-3/4 mx-auto animate-pulse"
      ></div>
      <!-- Subtitle Placeholder -->
      <div
        class="h-4 bg-gray-200 opacity-50 rounded w-1/2 mx-auto mt-2 animate-pulse"
      ></div>
    </div>

    <!-- Category Items Skeleton -->
    <div class="flex flex-wrap justify-center gap-4">
      <!-- Skeleton Category Item -->
      <div
        v-for="index in 4"
        :key="index"
        class="flex flex-col items-center mx-auto"
      >
        <!-- Circular Image Placeholder -->
        <div
          class="w-36 h-36 rounded-full bg-gray-200 opacity-50 border-2 border-indigo-400 mb-2 animate-pulse"
        ></div>
        <!-- Text Placeholder -->
        <div
          class="h-4 bg-gray-200 opacity-50 rounded w-20 animate-pulse"
        ></div>
      </div>
    </div>
  </div>
</template>

<style scoped></style>
