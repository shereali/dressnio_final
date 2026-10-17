<script setup>
// A style story from the "shop by style" rail: its curated products.
const config = useRuntimeConfig();
const route = useRoute();

const { data, pending } = await useFetch(
  () => `${config.public.apiBase}/client/story-products/${route.params.slug}`,
  { key: `story-${route.params.slug}`, lazy: true }
);

const story = computed(() => data.value?.data || null);
const name = computed(() => story.value?.name || String(route.params.slug).replace(/-/g, " "));

useSeoMeta({ title: () => `${name.value} | Dressnio` });
</script>

<template>
  <div>
    <PageHero :title="name" subtitle="Pieces chosen for this style." :crumbs="[{ label: 'Shop', to: '/shop' }, { label: name }]" :kind="garmentKindFor(name)" />
    <section class="pm-section" style="padding-top: 2rem; background: var(--paper)">
      <div class="pm-wrap">
        <p v-if="pending" style="padding: 3rem 0; color: var(--ink-soft)">Loading...</p>
        <Product v-else :products="story?.products || []" empty-text="Nothing in this style yet. Browse the shop to see everything we have." />
      </div>
    </section>
  </div>
</template>
