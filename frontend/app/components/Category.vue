<script setup>
// Category arches. Photos come from admin; categories without a photo are drawn from their garment type.
const props = defineProps({
  categories: { type: Array, default: () => [] },
});

const defaults = [
  "Kurta Sets", "Shrug Sets", "Maxis", "Lehengas", "Suit Sets",
  "Dresses", "Tops & Co-ord Sets", "Sarees", "Raw Fabrics", "Unstitched Suits",
].map((name, i) => ({ id: `d${i}`, name, slug: name.toLowerCase().replace(/&/g, "and").replace(/[^a-z0-9]+/g, "-").replace(/-$/, "") }));

const items = computed(() => {
  const source = props.categories?.length ? props.categories : defaults;
  return source.slice(0, 10).map((c, i) => ({
    ...c,
    kind: garmentKindFor(c.name, c.slug),
    color: toneHexAt(i),
  }));
});
</script>

<template>
  <section class="pm-section cats" aria-labelledby="cats-title">
    <div class="pm-wrap">
      <Headline
        id="cats-title"
        title="Shop by category"
        subtitle="From everyday kurta sets to wedding lehengas, start with the piece you have in mind."
        align="left"
        action-label="All products"
        action-to="/shop"
      />

      <ul class="cats-grid">
        <li v-for="c in items" :key="c.id" class="cat">
          <NuxtLink :to="`/category/${c.slug}`" class="cat-link">
            <span class="cat-arch arch" :style="{ background: toneFromHex(c.color).tint }">
              <span class="cat-ground" :style="{ background: toneFromHex(c.color).tintDeep }"></span>
              <ArtImage :src="c.image" :alt="c.name" :kind="c.kind" :color="c.color" class="cat-art" />
            </span>
            <span class="cat-name">
              {{ c.name }}
              <Icon name="solar:arrow-right-up-linear" class="cat-arrow w-4 h-4" />
            </span>
          </NuxtLink>
        </li>
      </ul>
    </div>
  </section>
</template>

<style scoped>
.cats {
  background: #fff;
}
.cats-grid {
  list-style: none;
  margin-top: clamp(2rem, 4vw, 3.2rem);
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.6rem 1rem;
}
@media (min-width: 700px) {
  .cats-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}
@media (min-width: 1000px) {
  .cats-grid {
    grid-template-columns: repeat(5, 1fr);
    gap: 2.4rem 1.4rem;
  }
}
.cat-link {
  display: block;
}
.cat-arch {
  position: relative;
  display: block;
  aspect-ratio: 3 / 4;
  transition: filter 0.3s ease;
}
.cat-ground {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  height: 14%;
}
.cat-art {
  position: absolute;
  inset: 0;
  transition: transform 0.45s cubic-bezier(0.2, 0.7, 0.2, 1);
}
.cat-art :deep(svg) {
  padding: 12% 10% 6%;
}
.cat-link:hover .cat-art {
  transform: translateY(-8px) scale(1.03);
}
.cat-link:hover .cat-arch {
  filter: saturate(1.15);
}
.cat-name {
  margin-top: 0.85rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.5rem;
  font-family: var(--font-display);
  font-size: 1.18rem;
  font-weight: 500;
  color: var(--brand-deep);
}
.cat-arrow {
  flex-shrink: 0;
  color: var(--brand);
  opacity: 0;
  transform: translate(-4px, 4px);
  transition: opacity 0.25s, transform 0.25s;
}
.cat-link:hover .cat-arrow,
.cat-link:focus-visible .cat-arrow {
  opacity: 1;
  transform: none;
}
@media (hover: none) {
  .cat-arrow {
    opacity: 1;
    transform: none;
  }
}
</style>
