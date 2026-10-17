<script setup>
const route = useRoute();
const config = useRuntimeConfig();

const { data } = await useFetch(() => `${config.public.apiBase}/client/related-products/${route.params.slug}`, {
  key: `related-products-${route.params.slug}`,
  lazy: true,
});

const related = computed(() => (Array.isArray(data.value?.data) ? data.value.data.slice(0, 4) : []));
</script>

<template>
  <section v-if="related.length" class="pm-section rel">
    <div class="pm-wrap">
      <Headline title="You may also like" subtitle="More pieces in the same spirit." align="left" action-label="Shop everything" action-to="/shop" />
      <div style="margin-top: 2rem">
        <Product :products="related" />
      </div>
    </div>
  </section>
</template>

<style scoped>
.rel {
  background: #fff;
  border-top: 1px solid var(--line);
}
</style>
