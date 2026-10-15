<script setup>
import { computed } from "vue";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Autoplay, Pagination, Navigation, EffectCreative } from "swiper/modules";

// Import Swiper styles
import "swiper/css";
import "swiper/css/pagination";
import "swiper/css/navigation";
import "swiper/css/effect-creative";

const props = defineProps({
  sliders: {
    type: Array,
    default: () => [],
  },
});

const defaultSlides = [
  {
    id: 1,
    title: "Elegant Evening Collection",
    subtitle: "Sophisticated Designs for Every Occasion",
    link: "/shop",
    image: "/brand/hero-evening.jpg",
  },
  {
    id: 2,
    title: "Summer Breeze Edit",
    subtitle: "Light & Colourful Designs for Warm Days",
    link: "/shop",
    image: "/brand/hero-summer.jpg",
  },
  {
    id: 3,
    title: "Festive Glamour",
    subtitle: "Dazzling Outfits for Special Celebrations",
    link: "/category/festive",
    image: "/brand/hero-festive.jpg",
  },
  {
    id: 4,
    title: "Contemporary Chic",
    subtitle: "Modern Styles for the Fashion-Forward",
    link: "/shop",
    image: "/brand/hero-chic.jpg",
  },
];

const slideItems = computed(() => {
  if (props.sliders && props.sliders.length > 0) {
    return props.sliders.map((s, idx) => ({
      ...s,
      image: s.image || defaultSlides[idx % defaultSlides.length].image,
    }));
  }
  return defaultSlides;
});
</script>

<template>
  <div class="relative group/slider overflow-hidden bg-gray-950">
    <Swiper
      :modules="[
        Autoplay,
        Navigation,
        Pagination,
        EffectCreative,
      ]"
      :slides-per-view="1"
      :loop="true"
      :effect="'creative'"
      :navigation="{
        nextEl: '.swiper-button-next-custom',
        prevEl: '.swiper-button-prev-custom',
      }"
      :pagination="{
        clickable: true,
        el: '.swiper-pagination-custom',
        bulletClass: 'custom-bullet',
        bulletActiveClass: 'custom-bullet-active'
      }"
      :autoplay="{
        delay: 5000,
        disableOnInteraction: false,
      }"
      :creative-effect="{
        prev: {
          shadow: true,
          translate: ['-20%', 0, -1],
          opacity: 0,
        },
        next: {
          translate: ['100%', 0, 0],
        },
      }"
      class="hero-swiper"
    >
      <SwiperSlide v-for="(slide, index) in slideItems" :key="slide.id || index" v-slot="{ isActive }">
        <div class="relative w-full h-[60vh] md:h-[70vh] lg:h-[80vh] xl:h-[85vh] min-h-[440px] overflow-hidden">
          <!-- Background Image with Ken Burns Effect -->
          <img
            :src="slide.image"
            :alt="slide.title"
            class="w-full h-full object-cover transition-transform duration-[8000ms] ease-linear"
            :class="isActive ? 'scale-105' : 'scale-100'"
          />
          
          <!-- Modern Gradient Overlays -->
          <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/45 to-transparent z-10"></div>
          <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent z-10"></div>

          <!-- Content Wrapper -->
          <div class="absolute inset-0 z-20 flex items-center px-6 md:px-16 lg:px-28 xl:px-36">
            <div class="max-w-3xl">
              <!-- Subtitle with Animated Line -->
              <div 
                class="flex items-center gap-3 mb-3 md:mb-5 overflow-hidden"
                :class="isActive ? 'opacity-100' : 'opacity-0'"
              >
                <div 
                  class="h-[2px] bg-brand transform transition-all duration-1000 delay-300 origin-left"
                  :class="isActive ? 'w-12 md:w-20 scale-x-100' : 'w-0 scale-x-0'"
                ></div>
                <h4 
                  class="text-brand-300 font-bold tracking-[.25em] uppercase text-xs md:text-sm transition-all duration-1000 delay-400"
                  :class="isActive ? 'translate-x-0 opacity-100' : '-translate-x-10 opacity-0'"
                >
                  {{ slide.subtitle || 'Curated Festive Collection' }}
                </h4>
              </div>

              <!-- Main Title -->
              <h2 
                class="text-white text-3xl sm:text-5xl md:text-6xl lg:text-7xl font-black leading-[1.1] mb-5 md:mb-7 transition-all duration-700 delay-500"
                :class="isActive ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'"
              >
                <span>{{ slide.title }}</span>
              </h2>

              <!-- CTA Button Section -->
              <div 
                class="flex items-center gap-4 transition-all duration-700 delay-700"
                :class="isActive ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0'"
              >
                <NuxtLink 
                  :to="slide.link || '/shop'" 
                  class="inline-flex items-center gap-3 bg-brand hover:bg-brand-600 text-white font-bold py-3.5 px-8 md:py-4 md:px-10 rounded-full transition-all duration-300 shadow-lg shadow-brand/30 hover:-translate-y-0.5 active:translate-y-0 text-xs md:text-sm uppercase tracking-wider group/btn"
                >
                  <span>Explore Collection</span>
                  <Icon name="solar:arrow-right-bold" class="w-4 h-4 transition-transform duration-300 group-hover/btn:translate-x-1"/>
                </NuxtLink>
              </div>
            </div>
          </div>
        </div>
      </SwiperSlide>

      <!-- Custom Pagination Container -->
      <div class="absolute bottom-6 left-0 right-0 z-30 flex justify-center items-center pointer-events-none">
        <div class="swiper-pagination-custom flex items-center gap-2 pointer-events-auto"></div>
      </div>

      <!-- Custom Navigation Buttons -->
      <div class="hidden md:block">
        <button class="swiper-button-prev-custom absolute left-6 top-1/2 -translate-y-1/2 z-30 w-12 h-12 rounded-full border border-white/20 bg-black/30 backdrop-blur-md text-white flex items-center justify-center transition-all duration-300 hover:bg-brand hover:border-brand hover:scale-110 opacity-0 group-hover/slider:opacity-100">
          <Icon name="solar:arrow-left-bold" class="w-6 h-6"/>
        </button>
        <button class="swiper-button-next-custom absolute right-6 top-1/2 -translate-y-1/2 z-30 w-12 h-12 rounded-full border border-white/20 bg-black/30 backdrop-blur-md text-white flex items-center justify-center transition-all duration-300 hover:bg-brand hover:border-brand hover:scale-110 opacity-0 group-hover/slider:opacity-100">
          <Icon name="solar:arrow-right-bold" class="w-6 h-6"/>
        </button>
      </div>
    </Swiper>
  </div>
</template>

<style>
/* Custom Pagination Bullets */
.custom-bullet {
  width: 8px;
  height: 4px;
  background: rgba(255, 255, 255, 0.4);
  border-radius: 9999px;
  cursor: pointer;
  transition: all 0.4s ease;
}

.custom-bullet-active {
  width: 32px;
  background: #ffffff;
  box-shadow: 0 0 12px rgba(255, 255, 255, 0.6);
}

.hero-swiper {
  width: 100%;
}
</style>
