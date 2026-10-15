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
    <Product :products="props.bestSellerProducts || []" />
    <div class="flex justify-center items-center mt-6">
      <NuxtLink
        to="/shop"
        class="dn-btn dn-btn-outline"
      >
        VIEW ALL PRODUCTS
      </NuxtLink>
    </div>
    <!-- Add your best seller product display logic here -->
  </div>
</template>
