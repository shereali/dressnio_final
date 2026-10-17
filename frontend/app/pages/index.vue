<script setup>
import { computed } from "vue";

// One cached request feeds the whole page (GET /client/homepage).
const { data } = await useApiFetch("/client/homepage", {
  key: "homepage-payload",
  lazy: false,
});

const payload = computed(() => data.value?.data || {});

const story = computed(() => payload.value.story || []);
const sliders = computed(() => payload.value.sliders || []);
const categories = computed(() => payload.value.categories || []);
const watchAndBuy = computed(() => payload.value.watch_n_buy || []);
const latest = computed(() => payload.value.latest || []);
const bestSeller = computed(() => payload.value.best_seller || []);
const fashionistChoice = computed(() => payload.value.fashionist_choice || []);
const highlight = computed(() => payload.value.highlight?.[0] || null);
const testimonials = computed(() => payload.value.testimonials || []);
const posts = computed(() => payload.value.posts || []);

// The hero price tag shows a real product when one exists
const featured = computed(() => latest.value[0] || bestSeller.value[0] || null);

useSeoMeta({
  title: "Dressnio | Kurta sets, sarees and unstitched suits",
  ogTitle: "Dressnio | Kurta sets, sarees and unstitched suits",
  description:
    "Shop kurta sets, lehengas, sarees, maxis and unstitched suits from Dressnio. Cash on delivery across Bangladesh, bKash and Nagad accepted.",
  ogDescription: "Women's fashion with cash on delivery across Bangladesh.",
});
</script>

<template>
  <div>
    <Slider :sliders="sliders" :featured="featured" />
    <TrustStrip />
    <ProductVariation :story="story" />
    <Category :categories="categories" />
    <ProductFilter :latest="latest" :best-seller="bestSeller" />
    <CollectionBento />
    <WatchAndBuy :watch-and-buy="watchAndBuy" />
    <FashionistChoice v-if="fashionistChoice.length" :fashionist-choice="fashionistChoice" />
    <HighlightedProduct :highlight="highlight" />
    <SizeFit />
    <Testimonial v-if="testimonials.length" :testimonials="testimonials" />
    <BlogList v-if="posts.length" :posts="posts" />
  </div>
</template>
