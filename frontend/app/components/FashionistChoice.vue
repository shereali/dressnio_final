<script setup>
import { Carousel, Navigation, Pagination, Slide } from "vue3-carousel";
import "vue3-carousel/dist/carousel.css";
import { useCartStore } from "~/stores/cart";
const props = defineProps(["fashionistChoice"]);
const snackbar = useSnackbar();
const snackbarMsg = ($message) => {
  snackbar.add({
    type: "success",
    text: $message,
  });
};

const cartStore = useCartStore();

const addToCart = (product) => {
  if (product.quantity > 0) {
    cartStore.addToCart(product);
    // toast.success("Item added to the cart!");
    snackbarMsg("Item added to the cart!");
  }
};

const settings = ref({
  itemsToShow: 1,
  snapAlign: "center",
});
// breakpoints are mobile first
// any settings not specified will fallback to the carousel settings
const breakpoints = ref({
  // 700px and up
  700: {
    itemsToShow: 3.5,
    snapAlign: "center",
  },
  // 1024 and up
  1024: {
    itemsToShow: 4,
    snapAlign: "center",
  },
});
</script>

<template>
  <div class="bg-gradient-to-br from-brand-600 via-brand to-brand-700 py-20 px-6 md:px-10 relative overflow-hidden">
    <!-- Decorative elements -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-brand-900/20 rounded-full blur-3xl"></div>
    
    <div class="max-w-7xl mx-auto relative z-10">
      <Headline
        title="Fashionist Choice"
        subtitle="Curated collection for the style-conscious"
        class="mb-16 text-white"
      />
    <Carousel
      v-bind="settings"
      :breakpoints="breakpoints"
      :wrapAround="true"
      :transition="500"
    >
      <Slide v-for="(product, index) in props.fashionistChoice" :key="index">
        <div class="carousel__item my-10">
          <ProductCard :product="product" />
        </div>
      </Slide>

      <template #addons>
        <Pagination />
        <Navigation />
      </template>
    </Carousel>
    </div>
  </div>
</template>

<style scoped>
.carousel__slide {
  padding: 5px;
}

.carousel__viewport {
  perspective: 2000px;
}

.carousel__track {
  transform-style: preserve-3d;
}

.carousel__slide--sliding {
  transition: 0.5s;
}

.carousel__slide {
  opacity: 0.9;
  transform: rotateY(-20deg) scale(0.9);
}

.carousel__slide--active ~ .carousel__slide {
  transform: rotateY(20deg) scale(0.9);
}

.carousel__slide--prev {
  opacity: 1;
  transform: rotateY(-10deg) scale(0.95);
}

.carousel__slide--next {
  opacity: 1;
  transform: rotateY(10deg) scale(0.95);
}

.carousel__slide--active {
  opacity: 1;
  transform: rotateY(0) scale(1.1);
}
</style>
