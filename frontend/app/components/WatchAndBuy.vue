<script setup>
import { computed } from "vue";
// Import Swiper styles
import "swiper/css";
import "swiper/css/pagination";
import "swiper/css/navigation";

import { Swiper, SwiperSlide } from "swiper/vue";
import { Pagination, Navigation, Autoplay } from "swiper/modules";

const props = defineProps({
  watchAndBuy: {
    type: Array,
    default: () => [],
  },
});

let { isMobile } = useDevice();

const defaultItems = [
  {
    id: 1,
    video: "/brand/hero-evening.jpg",
    product: {
      id: 1,
      name: "Embroidered Cotton 3-Piece Set",
      slug: "embroidered-cotton-3-piece-set",
      base_price: 2150,
      price: { price: 2150, discount_price: 1850 },
      media: [{ media_url: "/brand/hero-evening.jpg" }],
    },
  },
  {
    id: 2,
    video: "/brand/hero-summer.jpg",
    product: {
      id: 2,
      name: "Floral Print Chiffon Maxi",
      slug: "floral-print-chiffon-maxi",
      base_price: 1850,
      price: { price: 1850, discount_price: 1550 },
      media: [{ media_url: "/brand/hero-summer.jpg" }],
    },
  },
  {
    id: 3,
    video: "/brand/hero-chic.jpg",
    product: {
      id: 3,
      name: "Pure Georgette Party Kurti",
      slug: "pure-georgette-party-kurti",
      base_price: 1650,
      price: { price: 1650, discount_price: 1350 },
      media: [{ media_url: "/brand/hero-chic.jpg" }],
    },
  },
  {
    id: 4,
    video: "/brand/hero-festive.jpg",
    product: {
      id: 4,
      name: "Festive Embroidered Silk Kameez",
      slug: "festive-embroidered-silk-kameez",
      base_price: 2750,
      price: { price: 2750, discount_price: 2450 },
      media: [{ media_url: "/brand/hero-festive.jpg" }],
    },
  },
];

const videoItems = computed(() => {
  if (props.watchAndBuy && props.watchAndBuy.length > 0) {
    return props.watchAndBuy;
  }
  return defaultItems;
});
</script>

<template>
  <div class="bg-gradient-to-br from-brand via-brand-600 to-brand-700 px-4 sm:px-6 md:px-10 pt-10 pb-16 md:pb-20 relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-brand-900/30 rounded-full blur-3xl pointer-events-none"></div>
    
    <div class="relative z-10 max-w-7xl mx-auto">
      <Headline
        title="Watch & Buy"
        subtitle="Discover trending boutique styles through our curated lookbooks"
        class="mb-10 text-white"
      />

      <Swiper
        :modules="[Pagination, Navigation, Autoplay]"
        :slides-per-view="isMobile ? 1.2 : 4"
        :space-between="20"
        :navigation="true"
        :autoplay="{ delay: 4000, disableOnInteraction: false }"
        class="lookbook-swiper"
      >
        <SwiperSlide
          v-for="(item, index) in videoItems"
          :key="item.id || index"
        >
          <WatchAndBuyProduct
            :product="item.product"
            :media="item.video"
            :itemIndex="index"
          />
        </SwiperSlide>
      </Swiper>
    </div>
  </div>
</template>

<style scoped>
.lookbook-swiper {
  width: 100%;
  padding-bottom: 2rem;
}
</style>
