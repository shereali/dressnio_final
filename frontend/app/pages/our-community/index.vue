<template>
  <div>
    <div class="bg-[#C89DD8] shadow-lg py-10">
      <Headline
        title="Our Customer Review"
        subtitle="You will be impressed by our customer review"
      />
      <swiper
        :effect="'coverflow'"
        :grabCursor="true"
        :centeredSlides="false"
        :slidesPerView="4"
        :navigation="true"
        :pagination="{
          clickable: true,
        }"
        :coverflowEffect="{
          rotate: 50,
          stretch: 0,
          depth: 100,
          modifier: 1,
          slideShadows: true,
        }"
        :modules="[EffectCoverflow, Pagination, Navigation]"
        class="mySwiper"
      >
        <swiper-slide
          ><NuxtImg
            src="https://swiperjs.com/demos/images/nature-1.jpg"
            class="h-full"
          />
        </swiper-slide>
        <swiper-slide
          ><NuxtImg
            src="https://swiperjs.com/demos/images/nature-2.jpg"
            class="h-full" /></swiper-slide
        ><swiper-slide
          ><NuxtImg
            src="https://swiperjs.com/demos/images/nature-3.jpg"
            class="h-full" /></swiper-slide
        ><swiper-slide
          ><NuxtImg
            src="https://swiperjs.com/demos/images/nature-4.jpg"
            class="h-full" /></swiper-slide
        ><swiper-slide
          ><NuxtImg
            src="https://swiperjs.com/demos/images/nature-5.jpg"
            class="h-full" /></swiper-slide
        ><swiper-slide
          ><NuxtImg
            src="https://swiperjs.com/demos/images/nature-6.jpg"
            class="h-full" /></swiper-slide
        ><swiper-slide
          ><NuxtImg
            src="https://swiperjs.com/demos/images/nature-7.jpg"
            class="h-full" /></swiper-slide
        ><swiper-slide
          ><NuxtImg
            src="https://swiperjs.com/demos/images/nature-8.jpg"
            class="h-full" /></swiper-slide
        ><swiper-slide
          ><NuxtImg
            src="https://swiperjs.com/demos/images/nature-9.jpg"
            class="h-full"
        /></swiper-slide>
      </swiper>
    </div>
    <Testimonial />
  </div>
</template>
<script setup>
// Import Swiper styles
import "swiper/css";

import "swiper/css/effect-coverflow";
import "swiper/css/pagination";

// import "./style.css";

// import required modules
import { EffectCoverflow, Pagination, Navigation } from "swiper/modules";
</script>

<style scoped>
.swiper {
  width: 100%;
  padding-top: 50px;
  padding-bottom: 50px;
}

.swiper-slide {
  background-position: center;
  background-size: cover;
  width: 300px;
  height: 460px;
}

.swiper-slide img {
  display: block;
  width: 100%;
}
</style>
