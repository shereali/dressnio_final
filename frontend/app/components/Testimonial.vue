<script setup>
// Customer words from admin testimonials. Never rendered without real entries.
const props = defineProps({
  testimonials: { type: Array, required: true },
});

const strip = (html) => (html || "").replace(/<[^>]*>/g, " ").replace(/\s+/g, " ").trim();
const initials = (name) =>
  (name || "?")
    .split(/\s+/)
    .slice(0, 2)
    .map((w) => w[0]?.toUpperCase())
    .join("");
</script>

<template>
  <section class="pm-section rv" aria-labelledby="rv-title">
    <div class="pm-wrap">
      <Headline id="rv-title" title="What our customers say" subtitle="Words and photos shared by people who ordered from us." align="left" />

      <ul class="rv-track no-scrollbar" tabindex="0" aria-label="Customer reviews">
        <li v-for="t in props.testimonials" :key="t.id || t.name" class="rv-card">
          <ArtImage v-if="t.image" :src="t.image" :alt="`Photo shared by ${t.name}`" kind="dress" class="rv-photo" />
          <blockquote class="rv-quote">{{ strip(t.description) }}</blockquote>
          <footer class="rv-who">
            <span class="rv-av" aria-hidden="true">{{ initials(t.name) }}</span>
            <span class="rv-name">{{ t.name }}</span>
            <a v-if="t.post_url" :href="t.post_url" target="_blank" rel="noopener" class="rv-src" aria-label="See the original post">
              <Icon name="mdi:instagram" class="w-5 h-5" />
            </a>
          </footer>
        </li>
      </ul>
    </div>
  </section>
</template>

<style scoped>
.rv {
  background: #fff;
}
.rv-track {
  list-style: none;
  display: flex;
  gap: 1.1rem;
  margin-top: 2.2rem;
  overflow-x: auto;
  scroll-snap-type: x mandatory;
  padding-bottom: 0.6rem;
}
.rv-card {
  flex: 0 0 min(84vw, 340px);
  scroll-snap-align: start;
  display: flex;
  flex-direction: column;
  background: var(--paper);
  border: 1px solid var(--line);
  border-radius: 22px;
  overflow: hidden;
}
.rv-photo {
  height: 210px;
  background: var(--paper);
}
.rv-quote {
  flex: 1;
  padding: 1.4rem 1.4rem 1rem;
  font-family: var(--font-display);
  font-size: 1.12rem;
  line-height: 1.55;
  color: var(--brand-deep);
}
.rv-who {
  display: flex;
  align-items: center;
  gap: 0.7rem;
  padding: 0 1.4rem 1.3rem;
}
.rv-av {
  display: grid;
  place-items: center;
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 999px;
  background: var(--brand);
  color: #fff;
  font-size: 0.85rem;
  font-weight: 700;
}
.rv-name {
  flex: 1;
  font-weight: 600;
}
.rv-src {
  color: var(--ink-soft);
}
.rv-src:hover {
  color: var(--brand);
}
</style>
