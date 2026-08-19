<script setup>
// Import Swiper styles
import "swiper/css";

import "swiper/css/pagination";
import "swiper/css/navigation";
import "swiper/css/virtual";

const props = defineProps({
  watchAndBuy: {
    type: Array,
    required: true,
  },
});

const { watchAndBuy } = toRefs(props);

watch(watchAndBuy, (newWatchAndBuy, oldWatchAndBuy) => {
  return newWatchAndBuy;
});

// import "./style.css";

// import Swiper core and required modules
import { Pagination, Navigation, Virtual } from "swiper/modules";

// Create array with 500 slides

let swiperRef = null;
let { isMobile } = useDevice();

const setSwiperRef = (swiper) => {
  swiperRef = swiper;
};
const slideTo = (index) => {
  swiperRef.slideTo(index - 1, 0);
};
</script>

<template>
  <div class="bg-gradient-to-br from-brand via-brand-600 to-brand-700 px-6 md:px-10 pt-10 pb-16 md:pb-20 relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-brand-900/30 rounded-full blur-3xl"></div>
    
    <div class="relative z-10">
      <Headline
        title="Watch & Buy"
        subtitle="Discover trending styles through our curated videos"
        class="mb-12 text-white"
      />
    <swiper
      :modules="[Pagination, Navigation, Virtual]"
      :slidesPerView="isMobile ? 1 : 5"
      :spaceBetween="30"
      :centeredSlides="false"
      :navigation="true"
      :virtual="true"
      class="mySwiper"
      @swiper="setSwiperRef"
    >
      <swiper-slide
        v-for="(item, index) in watchAndBuy"
        :key="index"
        :virtualIndex="index"
      >
        <LazyWatchAndBuyProduct
          :product="item.product"
          :media="item?.video"
          :itemIndex="index"
        />
      </swiper-slide>
    </swiper>
    </div>
  </div>
</template>

<style scoped>
/* Customizing scrollbar appearance */
.custom-scrollbar::-webkit-scrollbar {
  width: 0.1px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: white; /* Make thumb transparent */
  border-radius: 3px;
}

.custom-scrollbar:hover::-webkit-scrollbar-thumb {
  background-color: white; /* Change thumb color on hover */
}
</style>
