<script setup>
const props = defineProps(["latest", "bestSeller"]);

const tabs = [
  { key: "latest", label: "New arrivals" },
  { key: "bestSeller", label: "Best sellers" },
];
const active = ref("latest");
const counts = computed(() => ({
  latest: props.latest?.length || 0,
  bestSeller: props.bestSeller?.length || 0,
}));

// Open on best sellers when there are no new arrivals to show
onMounted(() => {
  if (!counts.value.latest && counts.value.bestSeller) active.value = "bestSeller";
});
</script>

<template>
  <section class="pm-section pf" aria-labelledby="pf-title">
    <div class="pm-wrap">
      <Headline id="pf-title" title="Fresh from the studio" subtitle="Pick a colour, choose a size, and add it straight to your bag." align="left" action-label="Shop everything" action-to="/shop" />

      <div class="pf-tabs" role="tablist" aria-label="Product lists">
        <button
          v-for="t in tabs"
          :key="t.key"
          role="tab"
          :aria-selected="active === t.key"
          class="pf-tab"
          :class="{ on: active === t.key }"
          @click="active = t.key"
        >
          {{ t.label }}
        </button>
      </div>

      <div class="pf-panel" role="tabpanel">
        <LatestProducts v-if="active === 'latest'" :latest-products="latest || []" />
        <BestSellerProducts v-else :best-seller-products="bestSeller || []" />
      </div>
    </div>
  </section>
</template>

<style scoped>
.pf {
  background: var(--paper);
}
.pf-tabs {
  display: inline-flex;
  gap: 0.3rem;
  margin: 2rem 0 1.6rem;
  padding: 0.3rem;
  border-radius: 999px;
  background: var(--brand-tint);
}
.pf-tab {
  min-height: 2.6rem;
  padding: 0 1.3rem;
  border-radius: 999px;
  font-weight: 600;
  font-size: 0.92rem;
  color: var(--brand-deep);
  transition: background-color 0.2s, color 0.2s;
}
.pf-tab.on {
  background: var(--brand);
  color: #fff;
}
</style>
