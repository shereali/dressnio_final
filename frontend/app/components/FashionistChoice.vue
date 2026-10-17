<script setup>
// Horizontal, snap-scrolling shelf of staff-picked products.
const props = defineProps({
  fashionistChoice: { type: Array, default: () => [] },
});

const track = ref(null);
const scrollBy = (dir) => {
  const el = track.value;
  if (!el) return;
  el.scrollBy({ left: dir * Math.max(el.clientWidth * 0.8, 260), behavior: "smooth" });
};
</script>

<template>
  <section class="pm-section fc" aria-labelledby="fc-title">
    <div class="pm-wrap">
      <div class="fc-head">
        <Headline
          id="fc-title"
          title="The Dressnio edit"
          subtitle="Pieces our team keeps reaching for this season."
          align="left"
          class="text-white"
        />
        <div class="fc-arrows">
          <button type="button" aria-label="Scroll left" @click="scrollBy(-1)"><Icon name="solar:arrow-left-linear" class="w-5 h-5" /></button>
          <button type="button" aria-label="Scroll right" @click="scrollBy(1)"><Icon name="solar:arrow-right-linear" class="w-5 h-5" /></button>
        </div>
      </div>

      <ul ref="track" class="fc-track no-scrollbar" tabindex="0" aria-label="Staff picks">
        <li v-for="p in props.fashionistChoice" :key="p.id" class="fc-item">
          <ProductCard :product="p" />
        </li>
      </ul>
    </div>
  </section>
</template>

<style scoped>
.fc {
  background: var(--brand-deep);
}
.fc-head {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 1rem;
}
.fc-arrows {
  display: none;
  gap: 0.5rem;
  flex-shrink: 0;
}
.fc-arrows button {
  display: grid;
  place-items: center;
  width: 2.9rem;
  height: 2.9rem;
  border-radius: 999px;
  border: 1.5px solid rgba(255, 255, 255, 0.4);
  color: #fff;
  transition: background-color 0.2s, color 0.2s;
}
.fc-arrows button:hover {
  background: #fff;
  color: var(--brand-deep);
}
@media (min-width: 700px) {
  .fc-arrows {
    display: flex;
  }
}
.fc-track {
  list-style: none;
  display: flex;
  gap: 1.1rem;
  margin-top: 2.2rem;
  overflow-x: auto;
  scroll-snap-type: x mandatory;
  padding-bottom: 0.5rem;
}
.fc-item {
  flex: 0 0 min(72vw, 270px);
  scroll-snap-align: start;
}
</style>
