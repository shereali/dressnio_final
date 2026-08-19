<!-- BestSellerProducts.vue -->
<script lang="ts" setup>
const props = defineProps({
  bestSellerProducts: {
    type: Array,
    required: true,
  },
});

const { bestSellerProducts } = toRefs(props);
watch(bestSellerProducts, (newBestSellerProducts, oldBestSellerProducts) => {
  return newBestSellerProducts;
});
</script>
<template>
  <div>
    <h3 class="text-xl font-semibold mb-2">Best Seller Products</h3>
    <!-- Display Best Seller Products -->
    <LazyProduct :products="bestSellerProducts" />
    <div class="flex justify-center items-center">
      <NuxtLink
        to="#"
        class="border border-brand p-2 px-10 bg-transparent text-brand font-bold hover:bg-brand hover:text-white transition-colors uppercase text-sm tracking-widest"
      >
        VIEW ALL PRODUCTS
      </NuxtLink>
    </div>
    <!-- Add your best seller product display logic here -->
  </div>
</template>
