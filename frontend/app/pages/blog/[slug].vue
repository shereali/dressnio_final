<script setup>
const config = useRuntimeConfig();
const route = useRoute();

const { data, pending } = await useFetch(() => `${config.public.apiBase}/client/posts/${route.params.slug}`, {
  key: `post-${route.params.slug}`,
  lazy: true,
});
const post = computed(() => data.value?.data || null);

useSeoMeta({
  title: () => (post.value ? `${post.value.title} | Dressnio` : "Style notes | Dressnio"),
});
</script>

<template>
  <div>
    <PageHero :title="post?.title || 'Style notes'" :crumbs="[{ label: 'Style notes' }, ...(post ? [{ label: post.title }] : [])]" kind="kurta" />
    <section class="pm-section" style="padding-top: 2rem; background: var(--paper)">
      <div class="pm-wrap">
        <p v-if="pending" style="color: var(--ink-soft)">Loading...</p>
        <article v-else-if="post" class="post">
          <ArtImage v-if="post.image" :src="post.image" :alt="post.title" kind="dress" class="post-img" />
          <div class="post-body" v-html="post.description"></div>
        </article>
        <div v-else class="post-missing">
          <p>We could not find that story.</p>
          <NuxtLink to="/" class="pm-btn pm-btn-ghost">Back to the homepage</NuxtLink>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
.post {
  max-width: 70ch;
}
.post-img {
  aspect-ratio: 16 / 9;
  margin-bottom: 1.6rem;
  border-radius: 20px;
  overflow: hidden;
}
.post-body {
  line-height: 1.85;
  font-size: 1.05rem;
  color: var(--ink);
}
.post-body :deep(p) {
  margin-bottom: 1.1rem;
}
.post-body :deep(h2),
.post-body :deep(h3) {
  margin: 1.8rem 0 0.8rem;
  font-size: 1.6rem;
  color: var(--brand-deep);
}
.post-body :deep(a) {
  color: var(--brand);
  text-decoration: underline;
}
.post-missing {
  display: grid;
  gap: 1rem;
  justify-items: start;
  color: var(--ink-soft);
}
</style>
