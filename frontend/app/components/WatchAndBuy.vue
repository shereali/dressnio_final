<script setup>
// Lookbook reels managed in admin. Hidden when there are none, so the page never shows made-up content.
import "swiper/css";
import "swiper/css/navigation";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Navigation, A11y } from "swiper/modules";

const props = defineProps({
  watchAndBuy: { type: Array, default: () => [] },
});

const items = computed(() => (props.watchAndBuy || []).filter((i) => i?.product));
</script>

<template>
  <section v-if="items.length" class="pm-section wb" aria-labelledby="wb-title">
    <div class="pm-wrap">
      <Headline
        id="wb-title"
        title="Watch and shop the look"
        subtitle="See each piece in motion, then add it to your bag without leaving the page."
        align="left"
        class="text-white"
      />

      <div class="wb-track">
        <Swiper
          :modules="[Navigation, A11y]"
          :slides-per-view="1.25"
          :space-between="16"
          :navigation="true"
          :breakpoints="{
            640: { slidesPerView: 2.3, spaceBetween: 18 },
            1000: { slidesPerView: 3.4, spaceBetween: 20 },
            1280: { slidesPerView: 4, spaceBetween: 22 },
          }"
          class="wb-swiper"
        >
          <SwiperSlide v-for="(item, index) in items" :key="item.id || index">
            <WatchAndBuyProduct :product="item.product" :media="item.video || item.media || ''" :item-index="index" />
          </SwiperSlide>
        </Swiper>
      </div>
    </div>
  </section>
</template>

<style scoped>
.wb {
  background: linear-gradient(160deg, #3e3ebe 0%, #4d4ddb 45%, #2a2ad1 100%);
}
.wb-track {
  margin-top: 2.4rem;
}
.wb-swiper {
  --swiper-navigation-color: #fff;
  --swiper-navigation-size: 20px;
}
.wb-swiper :deep(.swiper-button-prev),
.wb-swiper :deep(.swiper-button-next) {
  width: 2.8rem;
  height: 2.8rem;
  border-radius: 999px;
  background: rgba(21, 21, 82, 0.55);
  backdrop-filter: blur(6px);
}
</style>
