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
    <LazyProduct :products="latestProducts" />

    <div class="flex justify-center items-center">
      <NuxtLink
        to="#"
        class="border border-brand p-2 px-10 bg-transparent text-brand font-bold hover:bg-brand hover:text-white transition-colors uppercase text-sm tracking-widest"
      >
        VIEW ALL PRODUCTS
      </NuxtLink>
    </div>

    <!-- Add your latest product display logic here -->
  </div>
</template>
