<script setup>
// import { Carousel, Navigation, Pagination, Slide } from "vue3-carousel";
import { Autoplay, Pagination, Navigation, EffectCreative } from "swiper/modules";
const props = defineProps({
  sliders: {
    type: Array,
    required: true,
  },
});

const { sliders } = toRefs(props);

watch(sliders, (newSlider, oldSlider) => {
  return newSlider;
});

//   const currentSlideIndex = ref(0);

//   const nextSlide = () => {
//     currentSlideIndex.value =
//       (currentSlideIndex.value + 1) % slides.value.length;
//   };

//   // Autoplay functionality
//   const autoplayInterval = 3000; // Set the interval between slides in milliseconds
//   let autoplayTimer;

//   const startAutoplay = () => {
//     autoplayTimer = setInterval(nextSlide, autoplayInterval);
//   };

//   const stopAutoplay = () => {
//     clearInterval(autoplayTimer);
//   };

//   // Watch for changes in currentSlideIndex and restart autoplay on change
// const watchEffect(() => {
//     stopAutoplay(); // Stop the previous autoplay timer
//     startAutoplay(); // Start a new autoplay timer
//   });

//   // Cleanup when the component is unmounted
// const  onBeforeUnmount(() => {
//     stopAutoplay(); // Stop the autoplay timer to prevent memory leaks
//   });
</script>

<template>
  <div class="relative group/slider overflow-hidden">
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
      <SwiperSlide v-for="(slide, index) in sliders" :key="index" v-slot="{ isActive }">
        <div class="relative w-full h-[60vh] md:h-[70vh] lg:h-[85vh] xl:h-[90vh] overflow-hidden">
          <!-- Background Image with Ken Burns Effect -->
          <NuxtImg
            v-if="slide.image != null"
            :src="slide.image"
            class="w-full h-full object-cover transition-transform duration-[10000ms] ease-linear"
            :class="isActive ? 'scale-110' : 'scale-100'"
          />
          
          <!-- Modern Gradient Overlays -->
          <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/40 to-transparent z-10"></div>
          <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent z-10"></div>

          <!-- Content Wrapper -->
          <div class="absolute inset-0 z-20 flex items-center px-6 md:px-16 lg:px-32 xl:px-48">
            <div class="max-w-3xl">
              <!-- Subtitle with Animated Line -->
              <div 
                class="flex items-center gap-3 mb-4 md:mb-6 overflow-hidden"
                :class="isActive ? 'opacity-100' : 'opacity-0'"
              >
                <div 
                  class="h-[2px] bg-brand transform transition-all duration-1000 delay-300 origin-left"
                  :class="isActive ? 'w-12 md:w-20 lg:w-24 scale-x-100' : 'w-0 scale-x-0'"
                ></div>
                <h4 
                  class="text-brand-300 font-bold tracking-[.3em] uppercase text-[10px] md:text-sm transition-all duration-1000 delay-500"
                  :class="isActive ? 'translate-x-0 opacity-100' : '-translate-x-10 opacity-0'"
                >
                  {{ slide.subtitle || 'Premium Collection' }}
                </h4>
              </div>

              <!-- Main Title with Split Animation Feel -->
              <h2 
                class="text-white text-4xl md:text-7xl lg:text-8xl font-black leading-[1.1] mb-6 md:mb-8 transition-all duration-[1000ms] delay-700"
                :class="isActive ? 'translate-y-0 opacity-100 blur-0' : 'translate-y-20 opacity-0 blur-lg'"
              >
                <span class="block">{{ slide.title }}</span>
              </h2>

              <!-- Description/CTA Section -->
              <div 
                class="flex flex-col sm:flex-row items-start sm:items-center gap-6 md:gap-8 transition-all duration-1000 delay-1000"
                :class="isActive ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'"
              >
                <div v-if="slide.link" class="relative group/btn">
                  <NuxtLink 
                    :to="slide.link" 
                    class="relative z-10 inline-flex items-center gap-3 bg-brand text-white font-black py-4 px-10 md:py-5 md:px-14 rounded-full transition-all duration-500 overflow-hidden shadow-[0_10px_40px_-10px_rgba(77,77,219,0.5)] group-hover/btn:shadow-[0_20px_50px_-10px_rgba(77,77,219,0.7)] group-hover/btn:-translate-y-1"
                  >
                    <span class="relative z-10 uppercase tracking-widest text-xs md:text-sm">Explore Collection</span>
                    <Icon name="heroicons:arrow-right-20-solid" class="w-5 h-5 relative z-10 transition-transform duration-500 group-hover/btn:translate-x-2"/>
                    
                    <!-- Button Shine Effect -->
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover/btn:translate-x-full transition-transform duration-1000"></div>
                  </NuxtLink>
                </div>

                <!-- Secondary Info (Optional/Search Attractive) -->
                <div class="hidden md:flex items-center gap-4 text-white/70 italic text-sm">
                  <Icon name="solar:stars-minimalistic-bold" class="w-6 h-6 text-yellow-400 animate-pulse"/>
                  <span>Limited Edition Items Available</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </SwiperSlide>

      <!-- Custom Pagination Container -->
      <div class="absolute bottom-8 left-0 right-0 z-30 flex justify-center items-center pointer-events-none">
        <div class="swiper-pagination-custom flex items-center gap-2 pointer-events-auto"></div>
      </div>

      <!-- Custom Navigation Buttons -->
      <div class="hidden lg:block">
        <button class="swiper-button-prev-custom absolute left-8 top-1/2 -translate-y-1/2 z-30 w-14 h-14 rounded-full border border-white/20 bg-white/5 backdrop-blur-md text-white flex items-center justify-center transition-all duration-300 hover:bg-brand hover:border-brand hover:scale-110 opacity-0 group-hover/slider:opacity-100">
          <Icon name="heroicons:chevron-left-20-solid" class="w-8 h-8"/>
        </button>
        <button class="swiper-button-next-custom absolute right-8 top-1/2 -translate-y-1/2 z-30 w-14 h-14 rounded-full border border-white/20 bg-white/5 backdrop-blur-md text-white flex items-center justify-center transition-all duration-300 hover:bg-brand hover:border-brand hover:scale-110 opacity-0 group-hover/slider:opacity-100">
          <Icon name="heroicons:chevron-right-20-solid" class="w-8 h-8"/>
        </button>
      </div>
    </Swiper>
  </div>
</template>

<style>
/* Custom Pagination Bullets */
.custom-bullet {
  width: 10px;
  height: 4px;
  background: rgba(255, 255, 255, 0.3);
  border-radius: 2px;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.custom-bullet-active {
  width: 40px;
  background: #ffffff;
  box-shadow: 0 0 15px rgba(255,255,255,0.5);
}

/* Ensure Swiper is visible and attractive */
.hero-swiper {
  width: 100%;
  --swiper-theme-color: theme('colors.brand.DEFAULT');
}

@keyframes fade-in-down {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-down {
  animation: fade-in-down 1s ease-out forwards;
}
</style>
