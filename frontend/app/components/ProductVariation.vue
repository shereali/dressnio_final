<!-- ProductVariation.vue (Stories) -->
<script setup>
import { computed } from "vue";

const props = defineProps({
  story: {
    type: Array,
    default: () => [],
  },
});

const defaultStories = [
  { id: 1, name: "Cotton 3-Piece", slug: "cotton-3-piece", image: "/brand/story-1.jpg" },
  { id: 2, name: "Embroidered Sets", slug: "embroidered-sets", image: "/brand/story-2.jpg" },
  { id: 3, name: "Floral Shirts", slug: "floral-shirts", image: "/brand/story-3.jpg" },
  { id: 4, name: "Georgette Maxis", slug: "georgette-maxis", image: "/brand/story-4.jpg" },
  { id: 5, name: "Party Gowns", slug: "party-gowns", image: "/brand/hero-festive.jpg" },
  { id: 6, name: "Silk Sarees", slug: "silk-sarees", image: "/brand/hero-chic.jpg" },
];

const storyList = computed(() => {
  if (props.story && props.story.length > 0) {
    return props.story.map((s, idx) => ({
      ...s,
      image: s.image || defaultStories[idx % defaultStories.length].image,
    }));
  }
  return defaultStories;
});
</script>

<template>
  <div class="max-w-7xl mx-auto pt-6 pb-6 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-center gap-4 sm:gap-8 overflow-x-auto py-2">
      <NuxtLink
        v-for="item in storyList"
        :to="`/story/${item.slug || item.id}`"
        :key="item.id"
        class="group flex flex-col items-center text-center flex-shrink-0"
      >
        <div
          class="w-16 h-16 sm:w-20 sm:h-20 rounded-full overflow-hidden mb-2 border-2 border-brand p-0.5 group-hover:border-brand-400 transition-all duration-300 ring-2 ring-brand/20 group-hover:ring-brand/50 shadow-sm"
        >
          <img
            :src="item.image"
            :alt="item.name"
            class="w-full h-full object-cover rounded-full transition-transform duration-500 ease-in-out group-hover:scale-110"
          />
        </div>
        <span class="text-xs font-bold text-gray-800 group-hover:text-brand transition-colors whitespace-nowrap">{{ item.name }}</span>
      </NuxtLink>
    </div>
  </div>
</template>
