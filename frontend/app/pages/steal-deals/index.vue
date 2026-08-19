<script setup>
const config = useRuntimeConfig();
const { data, error, pending, refresh } = useApiFetch(
  "/client/best-selling-products",
  { method: "GET" }
);

const products = ref([]);

watchEffect(() => {
  if (data.value) {
    products.value = data.value.data;
  } else {
    refresh();
  }
});
</script>

<template>
  <div v-if="!pending">
    <FeaturedBanner
      title="People busy with"
      subtitle="Trending this week, more colorful, more designful"
    />
    <div class="bg-[#C89DD8] shadow-lg py-10">
      <Headline
        title="Top Sale this week"
        subtitle="Explore our top sale items"
      />
      <Filter />
      <Product :products="products" />
    </div>
  </div>
  <div v-else>
    <SkeletonProductFilter />
  </div>
</template>
