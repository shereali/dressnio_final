<script setup>
const config = useRuntimeConfig();
const { data, error, pending, refresh } = useApiFetch(
  "/client/unstitched-products",
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

watch(products, (newProducts, oldProducts) => {
  return newProducts;
});

let text_color = ref("");
let thumbnail_view = ref(true);
let list_view = ref(false);
let productView = (listPreview) => {
  if (listPreview == "thumbnail") {
    thumbnail_view.value = true;
    list_view.value = false;
  }
  if (listPreview == "list") {
    list_view.value = true;
    thumbnail_view.value = false;
  }
};
</script>

<template>
  <div v-if="!pending">
    <FeaturedBanner
      title="Hot unstitched collection"
      subtitle="Most people hang with this design"
    />
    <div class="bg-[#C89DD8] shadow-lg py-10">
      <Headline
        title="UNSTITCHED SUIT SETS"
        subtitle="Add Spark to your Special days!"
      />
      <Filter />
      <Product :products="products" />
    </div>
  </div>
  <div v-else>
    <SkeletonProductFilter />
  </div>
</template>
