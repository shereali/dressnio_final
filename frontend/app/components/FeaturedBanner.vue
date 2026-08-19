<template>
  <div class="flex justify-center items-center">
    <Headline :title="title" :subtitle="subtitle" class="w-1/5" />

    <div
      v-for="(image, index) in images"
      :key="index"
      class="w-1/5 p-4 transition-transform transform group hover:scale-110"
    >
      <NuxtImg :src="image" alt="Image" class="w-full h-auto rounded-lg" />
    </div>
  </div>
</template>

<script setup>
const props = defineProps(["title", "subtitle"]);
let images = ref([
  "/images/product/p-1.webp",
  "/images/product/p-2.webp",
  "/images/product/p-3.webp",
  "/images/product/p-4.webp",
  // Add more image URLs as needed
]);
</script>

<style scoped>
.group:hover .group-hover\:scale-110 {
  transform: scale(1.1);
}
</style>
