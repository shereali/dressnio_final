<script setup>
import { Autoplay, Pagination, Navigation } from "swiper/modules";

const props = defineProps({
  testimonials: {
    type: Array,
    required: true,
  },
});

const { testimonials } = toRefs(props);

watch(testimonials, (newTestimonial, oldTestimonial) => {
  return newTestimonial;
});
// const testimonials = props.testimonials;
// console.log("testimonials", testimonials);
</script>

<template>
  <div class="bg-gradient-to-b from-brand-50 to-white py-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
      <Headline
        title="Customer Reviews"
        subtitle="Hear what our valued customers say about us"
        class="mb-16"
      />
    <swiper
      :modules="[
        SwiperAutoplay,
        Navigation,
        Autoplay,
        Pagination,
        SwiperEffectCreative,
      ]"
      :slidesPerView="1"
      :spaceBetween="10"
      :navigation="true"
      :pagination="{
        clickable: true,
      }"
      :breakpoints="{
        '640': {
          slidesPerView: 2,
          spaceBetween: 20,
        },
        '768': {
          slidesPerView: 4,
          spaceBetween: 30,
        },
        '1024': {
          slidesPerView: 4,
          spaceBetween: 40,
        },
        '1680': {
          slidesPerView: 6,
          spaceBetween: 50,
        },
      }"
      class="mySwiper"
    >
      <swiper-slide v-for="(item, index) in testimonials" :key="index">
        <div class="w-full bg-brand border border-brand-900 text-white rounded-lg overflow-hidden shadow-lg">
          <header
            class="flex justify-between items-center px-5 py-3 border-b border-brand-800 bg-brand-600"
          >
            <h1 class="font-bold">{{ item.name }}</h1>
            <Icon name="mdi:instagram" class="text-white w-5 h-5" />
          </header>
          <div class="px-5 py-4 text-sm text-brand-50 min-h-[80px]">
            <p v-html="item.description"></p>
          </div>
          <NuxtImg v-if="item.image != null" :src="item.image" class="w-full h-48 object-cover" />
        </div>
      </swiper-slide>
    </swiper>
    </div>
  </div>
</template>
