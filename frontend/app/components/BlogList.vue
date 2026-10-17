<script setup>
const props = defineProps({
  posts: { type: Array, required: true },
});

const excerpt = (html) => {
  const t = (html || "").replace(/<[^>]*>/g, " ").replace(/\s+/g, " ").trim();
  return t.length > 140 ? `${t.slice(0, 137)}...` : t;
};
</script>

<template>
  <section class="pm-section bl" aria-labelledby="bl-title">
    <div class="pm-wrap">
      <Headline id="bl-title" title="Style notes" subtitle="Styling ideas, fabric care and what to wear for the season." align="left" />

      <ul class="bl-grid">
        <li v-for="(post, i) in props.posts.slice(0, 3)" :key="post.id || post.slug">
          <NuxtLink :to="`/blog/${post.slug}`" class="bl-card">
            <span class="bl-media" :style="{ background: toneFromHex(toneHexAt(i + 3)).tint }">
              <ArtImage :src="post.image" :alt="post.title" :kind="garmentKindFor(post.title)" :color="toneHexAt(i + 3)" />
            </span>
            <span class="bl-title">{{ post.title }}</span>
            <span class="bl-ex">{{ excerpt(post.description) }}</span>
            <span class="bl-more">Read the story <Icon name="solar:arrow-right-linear" class="w-4 h-4" /></span>
          </NuxtLink>
        </li>
      </ul>
    </div>
  </section>
</template>

<style scoped>
.bl {
  background: var(--paper);
}
.bl-grid {
  list-style: none;
  display: grid;
  gap: 1.6rem;
  margin-top: 2.4rem;
}
@media (min-width: 800px) {
  .bl-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}
.bl-card {
  display: flex;
  flex-direction: column;
  gap: 0.7rem;
  height: 100%;
}
.bl-media {
  display: block;
  aspect-ratio: 16 / 11;
  border-radius: 20px;
  overflow: hidden;
}
.bl-media :deep(img) {
  transition: transform 0.5s ease;
}
.bl-card:hover .bl-media :deep(img) {
  transform: scale(1.04);
}
.bl-media :deep(svg) {
  padding: 4% 30% 0;
}
.bl-title {
  margin-top: 0.4rem;
  font-family: var(--font-display);
  font-size: 1.35rem;
  line-height: 1.25;
  font-weight: 500;
  color: var(--brand-deep);
}
.bl-ex {
  color: var(--ink-soft);
  line-height: 1.65;
  font-size: 0.97rem;
}
.bl-more {
  margin-top: auto;
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-weight: 600;
  color: var(--brand);
}
</style>
