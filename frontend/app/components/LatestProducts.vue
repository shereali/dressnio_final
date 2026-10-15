<!-- LatestProducts.vue -->
<script lang="ts" setup>
// Use defineProps to define props
const props = defineProps({
  latestProducts: {
    type: Array,
    required: true,
  },
});

const { latestProducts } = toRefs(props);
watch(latestProducts, (newLatestProducts, oldLatestProducts) => {
  return newLatestProducts;
});
</script>

<template>
  <div>
    <h3 class="text-xl font-semibold mb-2">Latest Products</h3>
    <!-- Display Latest Products -->
    <Product :products="props.latestProducts || []" />

    <div class="flex justify-center items-center mt-6">
      <NuxtLink
        to="/shop"
        class="dn-btn dn-btn-outline"
      >
        VIEW ALL PRODUCTS
      </NuxtLink>
    </div>

    <!-- Add your latest product display logic here -->
  </div>
</template>
