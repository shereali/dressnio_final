<script lang="ts" setup></script>

<template>
  <div>
    <!-- Skeleton Swiper -->
    <div class="relative w-full overflow-hidden grid grid-cols-3 gap-2">
      <!-- Skeleton Slides -->
      <div v-for="index in 3" :key="index" class="animate-pulse">
        <div
          class="w-11/12 mx-auto h-[10rem] md:h-[20rem] lg:h-[30rem] xl:h-[45rem] bg-gray-200 opacity-50 rounded-md mb-4"
        ></div>
      </div>
    </div>
  </div>
</template>

<style scoped></style>
