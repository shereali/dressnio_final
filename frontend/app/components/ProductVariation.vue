<script setup>
// "Shop by style" rail. Uses the stories managed in admin; each ring shows the photo or the drawn garment.
const props = defineProps({
  story: { type: Array, default: () => [] },
});

const defaults = [
  { id: "d1", name: "Cotton 3-piece", slug: "cotton-3-piece", kind: "unstitched" },
  { id: "d2", name: "Embroidered sets", slug: "embroidered-sets", kind: "kurta" },
  { id: "d3", name: "Floral tops", slug: "floral-tops", kind: "top" },
  { id: "d4", name: "Georgette maxis", slug: "georgette-maxis", kind: "maxi" },
  { id: "d5", name: "Party dresses", slug: "party-dresses", kind: "dress" },
  { id: "d6", name: "Silk sarees", slug: "silk-sarees", kind: "saree" },
  { id: "d7", name: "Lehengas", slug: "lehengas", kind: "lehenga" },
  { id: "d8", name: "Shrug sets", slug: "shrug-sets", kind: "shrug" },
];

const items = computed(() => {
  const source = props.story?.length ? props.story : defaults;
  return source.map((s, i) => ({
    ...s,
    kind: s.kind || garmentKindFor(s.name, s.slug),
    color: toneHexAt(i),
    to: `/story/${s.slug || s.id}`,
  }));
});
</script>

<template>
  <section class="styles" aria-label="Shop by style">
    <div class="pm-wrap">
      <ul class="styles-rail no-scrollbar">
        <li v-for="item in items" :key="item.id" class="style-item">
          <NuxtLink :to="item.to" class="style-link">
            <span class="style-ring">
              <span class="style-face" :style="{ background: toneFromHex(item.color).tint }">
                <ArtImage :src="item.image" :alt="item.name" :kind="item.kind" :color="item.color" />
              </span>
            </span>
            <span class="style-name">{{ item.name }}</span>
          </NuxtLink>
        </li>
      </ul>
    </div>
  </section>
</template>

<style scoped>
.styles {
  background: var(--paper);
  border-bottom: 1px solid var(--line);
}
.styles-rail {
  list-style: none;
  display: flex;
  gap: clamp(1rem, 2.4vw, 2.2rem);
  overflow-x: auto;
  padding: 1.4rem 0 1.2rem;
  scroll-snap-type: x proximity;
}
@media (min-width: 1100px) {
  .styles-rail {
    justify-content: center;
  }
}
.style-item {
  flex: 0 0 auto;
  scroll-snap-align: start;
}
.style-link {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.65rem;
  width: 5.6rem;
}
.style-ring {
  width: 5.2rem;
  height: 5.2rem;
  padding: 3px;
  border-radius: 999px;
  background: conic-gradient(from 210deg, #4d4ddb, #8383ed, #cdcdfa, #4d4ddb);
  transition: transform 0.25s ease;
}
.style-face {
  display: block;
  width: 100%;
  height: 100%;
  border-radius: 999px;
  border: 3px solid var(--paper);
  overflow: hidden;
}
.style-face :deep(svg) {
  transform: scale(1.5);
  transform-origin: 50% 24%;
  padding: 0;
}
.style-face :deep(img) {
  object-fit: cover;
}
.style-link:hover .style-ring {
  transform: translateY(-3px);
}
.style-name {
  font-size: 0.82rem;
  font-weight: 500;
  line-height: 1.25;
  text-align: center;
  color: var(--ink);
}
</style>
