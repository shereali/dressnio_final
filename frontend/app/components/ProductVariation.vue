<!-- ProductVariation.vue (Stories) -->
<script setup>
const props = defineProps({
  story: {
    type: Array,
    required: true,
  },
});

const { story } = toRefs(props);
</script>

<template>
  <div class="max-w-7xl mx-auto pt-4 pb-6 px-4 sm:px-6 lg:px-8">
    <div class="flex flex-wrap justify-center gap-6">
      <NuxtLink
        v-for="(item, index) in story"
        :to="`/story/${item.slug}`"
        :key="index"
        class="group flex flex-col items-center mx-auto text-center"
      >
        <div
          class="w-20 h-20 rounded-full overflow-hidden mb-2 border-4 border-brand group-hover:border-brand-300 transition-all duration-300 ring-2 ring-transparent group-hover:ring-brand-100"
        >
          <NuxtImg
            v-if="item.image != null"
            :src="item.image"
            :alt="item.name"
            class="w-full h-full object-cover transition-transform duration-500 ease-in-out group-hover:scale-110"
          />
        </div>
        <span class="text-xs font-medium text-gray-700 group-hover:text-brand transition-colors">{{ item.name }}</span>
      </NuxtLink>
    </div>
  </div>
</template>

<style scoped>
/* Scoped styles removed */
</style>
