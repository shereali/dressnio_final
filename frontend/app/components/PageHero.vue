<script setup>
// Compact header for inner pages: breadcrumb, title, short line and one drawn garment in an arch.
defineProps({
  title: { type: String, required: true },
  subtitle: { type: String, default: "" },
  crumbs: { type: Array, default: () => [] },
  kind: { type: String, default: "dress" },
  color: { type: String, default: "#4D4DDB" },
});
</script>

<template>
  <section class="ph">
    <div class="pm-wrap ph-in">
      <div class="ph-copy">
        <nav v-if="crumbs.length" aria-label="Breadcrumb" class="ph-crumbs">
          <NuxtLink to="/">Home</NuxtLink>
          <template v-for="c in crumbs" :key="c.label">
            <Icon name="solar:alt-arrow-right-linear" class="w-3.5 h-3.5" />
            <NuxtLink v-if="c.to" :to="c.to">{{ c.label }}</NuxtLink>
            <span v-else aria-current="page">{{ c.label }}</span>
          </template>
        </nav>
        <h1 class="ph-title">{{ title }}</h1>
        <p v-if="subtitle" class="ph-sub">{{ subtitle }}</p>
        <slot />
      </div>
      <div class="ph-arch arch" aria-hidden="true">
        <GarmentArt :kind="kind" :color="color" />
      </div>
    </div>
  </section>
</template>

<style scoped>
.ph {
  background: linear-gradient(180deg, var(--brand-tint), var(--paper));
  border-bottom: 1px solid var(--line);
}
.ph-in {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 2rem;
  padding-top: clamp(1.8rem, 4vw, 3rem);
}
.ph-copy {
  padding-bottom: clamp(1.8rem, 4vw, 3rem);
  min-width: 0;
}
.ph-crumbs {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 0.35rem;
  margin-bottom: 1rem;
  font-size: 0.86rem;
  color: var(--ink-soft);
}
.ph-crumbs a:hover {
  color: var(--brand);
}
.ph-crumbs span {
  color: var(--brand-deep);
  font-weight: 600;
}
.ph-title {
  font-size: clamp(2rem, 4.6vw, 3.6rem);
  line-height: 1.08;
  font-weight: 600;
  color: var(--brand-deep);
  text-wrap: balance;
}
.ph-sub {
  margin-top: 0.9rem;
  max-width: 52ch;
  line-height: 1.65;
  color: var(--ink-soft);
}
.ph-arch {
  display: none;
  flex-shrink: 0;
  width: 170px;
  height: 200px;
  background: #fff;
  border: 1px solid var(--line);
  border-bottom: 0;
}
.ph-arch :deep(svg) {
  padding: 18% 8% 0;
}
@media (min-width: 700px) {
  .ph-arch {
    display: block;
  }
}
</style>
