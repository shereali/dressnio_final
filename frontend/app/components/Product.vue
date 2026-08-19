<script setup>
// import products from "~/composables/products";
const props = defineProps({
  products: {
    type: Array,
    required: true,
  },
});

const { products } = toRefs(props);
watch(products, (newProduct, oldProduct) => {
  return newProduct;
});

// console.log("product component", props.products);
</script>
<template>
  <div class="py-8">
    <div class="container mx-auto" v-auto-animate>
      <div
        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"
      >
        <LazyProductCard
          v-for="(product, index) in products"
          :product="product"
        />
      </div>
    </div>
  </div>
</template>
