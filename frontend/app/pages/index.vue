<script setup>
import { computed } from "vue";

const { data, pending, error } = await useApiFetch("/client/homepage", {
  key: "homepage-payload",
  lazy: false,
});

const payload = computed(() => data.value?.data || {});

const story = computed(() => payload.value.story || []);
const sliders = computed(() => payload.value.sliders || []);
const categories = computed(() => payload.value.categories || []);
const watch_n_buy = computed(() => payload.value.watch_n_buy || []);
const latest = computed(() => payload.value.latest || []);
const best_seller = computed(() => payload.value.best_seller || []);
const fashionist_choice = computed(() => payload.value.fashionist_choice || []);
const testimonials = computed(() => payload.value.testimonials || []);
const posts = computed(() => payload.value.posts || []);
</script>

<template>
  <div>
    <!-- Stories / Reels Highlights -->
    <ProductVariation :story="story" />

    <!-- Hero Slider Showcase -->
    <Slider :sliders="sliders" />

    <!-- Categories Grid -->
    <Category :categories="categories" />

    <!-- Watch & Buy Video Lookbooks -->
    <WatchAndBuy :watchAndBuy="watch_n_buy" />

    <!-- New Arrivals & Best Sellers Filter -->
    <ProductFilter
      :latest="latest"
      :bestSeller="best_seller"
    />

    <!-- Fashionist Choice Carousel -->
    <FashionistChoice
      v-if="fashionist_choice.length > 0"
      :fashionistChoice="fashionist_choice"
    />

    <!-- Spotlight Hero Product -->
    <HighlightedProduct />

    <!-- Customer Reviews -->
    <Testimonial v-if="testimonials.length > 0" :testimonials="testimonials" />

    <!-- Blog & Guides -->
    <BlogList v-if="posts.length > 0" :posts="posts" />
  </div>
</template>
