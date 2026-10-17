<script setup>
const config = useRuntimeConfig();
const { data, pending } = await useFetch(`${config.public.apiBase}/client/unstitched-products`, {
  key: "unstitched-products",
  lazy: true,
});
const products = computed(() => data.value?.data || []);

useSeoMeta({
  title: "Unstitched suits | Dressnio",
  description: "Choose your fabric online and have it tailored to your measurements.",
});
</script>

<template>
  <div>
    <PageHero
      title="Unstitched suits"
      subtitle="Pick the fabric here, then have it cut to your own measurements by the tailor you trust."
      :crumbs="[{ label: 'Unstitched suits' }]"
      kind="unstitched"
      color="#D49A1F"
    />
    <section class="pm-section" style="padding-top: 2rem; background: var(--paper)">
      <div class="pm-wrap">
        <p v-if="pending" style="padding: 3rem 0; color: var(--ink-soft)">Loading fabrics...</p>
        <Product v-else :products="products" empty-text="Unstitched suits will appear here soon." />
      </div>
    </section>
    <SizeFit />
  </div>
</template>
