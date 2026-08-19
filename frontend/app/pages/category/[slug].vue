<script lang="ts" setup>
const config = useRuntimeConfig();
const route = useRoute();
const { data: categoryData, pending, error } = useLazyFetch(
  `${config.public.apiBase}/client/category-details/${route.params.slug}`
);
</script>

<template>
  <div class="bg-gray-50 min-h-screen pb-20">
    <!-- Category Header -->
    <div class="bg-brand py-16 text-white relative overflow-hidden">
      <div class="absolute inset-0 opacity-20">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-brand-900 rounded-full blur-3xl"></div>
      </div>
      
      <div class="container mx-auto px-4 relative z-10 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold uppercase tracking-tight mb-4">
          {{ categoryData?.data?.name || 'Category' }}
        </h1>
        <div class="flex items-center justify-center gap-2 text-brand-100 text-sm md:text-base">
          <NuxtLink to="/" class="hover:text-white transition-colors">Home</NuxtLink>
          <Icon name="heroicons:chevron-right" class="w-4 h-4"/>
          <span class="text-white">{{ categoryData?.data?.name }}</span>
        </div>
      </div>
    </div>

    <!-- Product Grid -->
    <div class="container mx-auto px-4 py-12">
      <div v-if="pending" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div v-for="i in 8" :key="i" class="animate-pulse bg-white rounded-xl h-[350px]"></div>
      </div>

      <div v-else-if="categoryData?.data?.products?.length > 0">
        <div class="flex justify-between items-center mb-8">
          <p class="text-gray-600">Showing <span class="font-bold text-gray-900">{{ categoryData.data.products.length }}</span> products</p>
          <div class="flex items-center gap-4">
            <!-- Basic Sort (Optional) -->
            <select class="bg-white border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand">
              <option>Newest First</option>
              <option>Price: Low to High</option>
              <option>Price: High to Low</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <div v-for="product in categoryData.data.products" :key="product.id">
            <ProductCard :product="product" />
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-20 bg-white rounded-2xl shadow-sm">
        <div class="mb-6 flex justify-center">
          <div class="p-6 bg-brand-50 rounded-full">
            <Icon name="solar:bag-3-outline" class="w-16 h-16 text-brand"/>
          </div>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">No Products Found</h2>
        <p class="text-gray-600 mb-8 px-4">We couldn't find any products in this category at the moment.</p>
        <NuxtLink 
          to="/" 
          class="inline-block bg-brand hover:bg-brand-700 text-white font-bold py-3 px-8 rounded-full transition-all shadow-lg hover:shadow-brand/40"
        >
          Return to Shopping
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<style scoped></style>
