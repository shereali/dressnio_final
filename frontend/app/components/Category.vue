<!-- Category.vue -->
<script setup>
const props = defineProps({
  categories: {
    type: Array,
    required: true,
  },
});

const { categories } = toRefs(props);
</script>

<template>
  <section class="bg-gray-50/50 py-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
      <Headline
        title="Browse Categories"
        subtitle="Explore our wide range of collections"
        class="mb-16"
      />
      
      <div v-if="categories && categories.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 md:gap-8">
        <!-- Category Item -->
        <NuxtLink
          v-for="(category, index) in categories"
          :to="`/category/${category.slug}`"
          :key="category.id || index"
          class="group flex flex-col items-center text-center space-y-4"
        >
          <div
            class="w-32 h-32 sm:w-40 sm:h-40 rounded-full overflow-hidden border-4 border-white shadow-lg group-hover:border-brand group-hover:shadow-2xl group-hover:shadow-brand/20 transition-all duration-500 relative"
          >
            <NuxtImg
              v-if="category.image != null"
              :src="category.image"
              :alt="category.name"
              class="w-full h-full object-cover transition-transform duration-700 ease-in-out group-hover:scale-110"
            />
            <div class="absolute inset-0 bg-black/0 group-hover:bg-brand/10 transition-colors duration-500"></div>
          </div>
          <span class="font-black uppercase tracking-tight text-gray-900 group-hover:text-brand transition-colors text-xs md:text-sm">{{ category.name }}</span>
        </NuxtLink>
      </div>
      
      <!-- Empty State -->
      <div v-else class="text-center py-20 bg-white rounded-3xl shadow-sm border border-gray-100">
        <Icon name="solar:bag-smile-outline" class="w-16 h-16 text-gray-200 mx-auto mb-4"/>
        <p class="text-gray-400 font-medium">No categories found at the moment.</p>
      </div>
    </div>
  </section>
</template>

<style scoped>
/* Scoped styles can be removed if using pure Tailwind */
</style>
