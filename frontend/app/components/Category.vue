<!-- Category.vue -->
<script setup>
import { computed } from "vue";

const props = defineProps({
  categories: {
    type: Array,
    default: () => [],
  },
});

const defaultCategories = [
  { id: 1, name: "Kurta Sets", slug: "kurta-sets", image: "/img/category-kurta.jpg" },
  { id: 2, name: "Shrug Sets", slug: "shrug-sets", image: "/img/category-shrug.jpg" },
  { id: 3, name: "Maxis", slug: "maxis", image: "/img/category-maxi.jpg" },
  { id: 4, name: "Lehengas", slug: "lehengas", image: "/img/category-lehenga.jpg" },
  { id: 5, name: "Suit Sets", slug: "suit-sets", image: "/img/category-suit.jpg" },
  { id: 6, name: "Sarees", slug: "sarees", image: "/img/category-saree.jpg" },
];

const categoryList = computed(() => {
  if (props.categories && props.categories.length > 0) {
    return props.categories.map((c, idx) => ({
      ...c,
      image: c.image || defaultCategories[idx % defaultCategories.length].image,
    }));
  }
  return defaultCategories;
});
</script>

<template>
  <section class="bg-gray-50/50 py-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
      <Headline
        title="Browse Categories"
        subtitle="Explore our wide range of collections"
        class="mb-16"
      />
      
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-6 md:gap-8">
        <!-- Category Item -->
        <NuxtLink
          v-for="category in categoryList"
          :to="`/category/${category.slug}`"
          :key="category.id"
          class="group flex flex-col items-center text-center space-y-4"
        >
          <div
            class="w-32 h-32 sm:w-40 sm:h-40 rounded-full overflow-hidden border-4 border-white shadow-lg group-hover:border-brand group-hover:shadow-2xl group-hover:shadow-brand/20 transition-all duration-500 relative bg-gray-100"
          >
            <img
              :src="category.image"
              :alt="category.name"
              class="w-full h-full object-cover transition-transform duration-700 ease-in-out group-hover:scale-110"
            />
            <div class="absolute inset-0 bg-black/0 group-hover:bg-brand/10 transition-colors duration-500"></div>
          </div>
          <span class="font-black uppercase tracking-tight text-gray-900 group-hover:text-brand transition-colors text-xs md:text-sm">{{ category.name }}</span>
        </NuxtLink>
      </div>
    </div>
  </section>
</template>
