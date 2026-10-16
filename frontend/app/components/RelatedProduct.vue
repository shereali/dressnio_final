<template>
  <section v-if="relatedProducts && relatedProducts.length > 0" class="py-12 bg-stone-50/50 border-t border-stone-200/80">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-8">
        <span class="text-xs font-semibold uppercase tracking-widest text-rose-500">Curated For You</span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-stone-900 mt-1">You May Also Love</h2>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
        <ProductCard
          v-for="item in relatedProducts"
          :key="item.id"
          :product="item"
        />
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();
const slug = computed(() => route.params.slug);

const { data } = await useApiFetch(`/client/related-products/${slug.value}`, {
  key: `related-products-${slug.value}`,
  lazy: true,
  server: false,
});

const relatedProducts = computed(() => {
  const d = data.value?.data;
  return Array.isArray(d) ? d : [];
});
</script>
