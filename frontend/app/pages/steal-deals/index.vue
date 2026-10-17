<script setup>
// Best sellers and marked-down pieces, loaded from the API.
const config = useRuntimeConfig();
const { data, pending } = await useFetch(`${config.public.apiBase}/client/best-selling-products`, {
  key: "steal-deals",
  lazy: true,
});

const products = computed(() => data.value?.data || []);
const onlyDiscounted = ref(false);
const shown = computed(() =>
  onlyDiscounted.value ? products.value.filter((p) => priceInfo(p).percent > 0) : products.value
);

useSeoMeta({ title: "Steal deals | Dressnio", description: "Best sellers and marked-down pieces from Dressnio while stock lasts." });
</script>

<template>
  <div>
    <PageHero
      title="Steal deals"
      subtitle="Our best sellers and marked-down pieces, while stock lasts."
      :crumbs="[{ label: 'Steal deals' }]"
      kind="maxi"
      color="#B8446B"
    />
    <section class="pm-section" style="padding-top: 2rem; background: var(--paper)">
      <div class="pm-wrap">
        <label class="toggle">
          <input v-model="onlyDiscounted" type="checkbox" />
          <span>Show discounted pieces only</span>
        </label>
        <div v-if="pending" class="skel-row">Loading deals...</div>
        <Product v-else :products="shown" empty-text="No deals right now. New offers land often, so check back soon." />
      </div>
    </section>
  </div>
</template>

<style scoped>
.toggle {
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
  margin-bottom: 1.4rem;
  font-weight: 500;
  cursor: pointer;
}
.toggle input {
  width: 1.15rem;
  height: 1.15rem;
  accent-color: var(--brand);
}
.skel-row {
  padding: 3rem 0;
  color: var(--ink-soft);
}
</style>
