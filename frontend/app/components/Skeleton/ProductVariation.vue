<script lang="ts" setup></script>

<template>
  <div class="max-w-max mx-auto py-5 px-10 relative">
    <div class="flex flex-wrap justify-center gap-5">
      <!-- Skeleton story items -->
      <div
        v-for="index in 6"
        :key="index"
        class="flex flex-col items-center mx-auto text-center animate-pulse"
      >
        <div
          class="w-20 h-20 rounded-full bg-gray-200 opacity-50 mb-2 border-4 border-gray-300"
        ></div>
        <span class="w-12 h-4 bg-gray-200 opacity-50 rounded-md"></span>
      </div>
    </div>
  </div>
</template>

<style scoped></style>
