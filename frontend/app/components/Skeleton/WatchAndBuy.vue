<script lang="ts" setup></script>

<template>
  <div class="bg-[#9A76A5] px-10 py-10 mx-auto">
    <!-- Headline Skeleton -->
    <div class="animate-pulse mb-10">
      <div
        class="h-6 bg-gray-200 opacity-50 rounded-md w-3/4 mx-auto mb-2"
      ></div>
      <div class="h-4 bg-gray-200 opacity-50 rounded-md w-1/2 mx-auto"></div>
    </div>

    <!-- Swiper Skeleton -->
    <div class="flex gap-6 overflow-x-auto place-content-center">
      <div
        v-for="index in 5"
        :key="index"
        class="flex-shrink-0 w-60 h-80 bg-gray-200 opacity-50 rounded-md animate-pulse"
      ></div>
    </div>
  </div>
</template>

<style scoped></style>
