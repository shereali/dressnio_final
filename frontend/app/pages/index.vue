<script setup>
import { ref } from "vue";

const story = ref([]);
const sliders = ref([]);
const categories = ref([]);
const latest = ref([]);
const best_seller = ref([]);
const fashionist_choice = ref([]);
const watch_n_buy = ref([]);
const testimonials = ref([]);
const posts = ref([]);

const { data, pending, error, refresh } = useApiFetch("/client/homepage", {
  method: "GET",
});

watchEffect(() => {
  if (data.value) {
    story.value = data.value.story?.data || data.value.story || [];
    sliders.value = data.value.sliders?.data || data.value.sliders || [];
    categories.value = data.value.categories?.data || data.value.categories || [];
    watch_n_buy.value = data.value.watch_n_buy?.data || data.value.watch_n_buy || [];
    latest.value = data.value.latest?.data || data.value.latest || [];
    best_seller.value = data.value.best_seller?.data || data.value.best_seller || [];
    fashionist_choice.value = data.value.fashionist_choice?.data || data.value.fashionist_choice || [];
    testimonials.value = data.value.testimonials?.data || data.value.testimonials || [];
    posts.value = data.value.posts?.data || data.value.posts || [];
  } else {
    refresh();
  }
});

if (error.value) {
  console.error("API Error:", error.value);
}
</script>

<template>
  <div>
    <LazySlider v-if="!pending && data" :sliders="sliders" />
    <SkeletonSlider v-else />

    <LazyProductVariation v-if="!pending && data" :story="story" />
    <SkeletonProductVariation v-else />

    <LazyCategory v-if="!pending && data" :categories="categories" />
    <SkeletonCategory v-else />

    <LazyWatchAndBuy v-if="!pending && data" :watchAndBuy="watch_n_buy" />
    <SkeletonWatchAndBuy v-else />

    <ProductFilter
      v-if="!pending && data"
      :latest="latest"
      :bestSeller="best_seller"
    />
    <SkeletonProductFilter v-else />

    <LazyFashionistChoice
      v-if="!pending && data"
      :fashionistChoice="fashionist_choice"
    />

    <SkeletonFashionistChoice v-else />

    <HighlightedProduct />

    <LazyTestimonial :testimonials="testimonials" />

    <LazyBlogList :posts="posts" />
  </div>
</template>
