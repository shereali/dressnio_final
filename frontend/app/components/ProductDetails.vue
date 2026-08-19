<script setup>
import { Carousel, Navigation, Slide } from "vue3-carousel";
import "vue3-carousel/dist/carousel.css";

import "vue-inner-image-zoom/lib/vue-inner-image-zoom.css";
import InnerImageZoom from "vue-inner-image-zoom";
import { ref, computed } from "vue";
import { useCartStore } from "~/stores/cart";

const cartStore = useCartStore();
const snackbar = useSnackbar();
const config = useRuntimeConfig();
const route = useRoute();
let activeTab = ref("kurti");
let currentSlide = ref(0);

let product = ref({});

console.log("route.params.slug", route);

const { data } = useLazyFetch(
  `${config.public.apiBase}/client/product/${route.params.slug}`,
  {
    watch: false,
  }
);

const prices = {};
let index = -1;
let quantity = ref(1);
let selectedSize = ref("");
let selectedColor = ref("");

if (data.value) {
  console.log("data", data.value.data);
  product.value = data.value.data;

  for (const [key, value] of Object.entries(product.value.sizes)) {
    index++;
    prices[value.standard] = product.value.prices[index][value.standard];
  }

  selectedSize = ref(product.value.sizes[0]?.standard);
  selectedColor = ref(product.value.colors[0]?.color_code);
}

// console.log("price", product.value?.price?.price);

const formattedPrice = computed(() => {
  if (product.is_size_visible) {
    return (prices[selectedSize.value] * quantity.value).toFixed(2);
  } else {
    if (product.value.hasOwnProperty("price")) {
      return (product.value.price.price * quantity.value).toFixed(2);
    }
  }
});

const incrementQuantity = () => {
  quantity.value++;
  updatePrice();
};

const decrementQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--;
    updatePrice();
  }
};

const updateQuantity = () => {
  updatePrice();
};

const selectSize = (size) => {
  if (prices.hasOwnProperty(size)) {
    selectedSize.value = size;
    updatePrice();
  } else {
    console.error("Invalid selected size.");
  }
};

const selectColor = (color) => {
  selectedColor.value = color;
};

const updatePrice = () => {
  const selectedSizeObj = product.value.sizes.find(
    (size) => size.name === selectedSize.value
  );

  if (selectedSizeObj) {
    product.value.formattedPrice = (
      selectedSizeObj.price * quantity.value
    ).toFixed(2);
  } else {
    product.value.formattedPrice = (
      product.value.price.price * quantity.value
    ).toFixed(2);
  }
};

const addToCart = () => {
  if (
    product.is_size_visible &&
    (!selectedSize.value || !selectedColor.value)
  ) {
    console.error("Please select size and color before adding to cart.");
    return;
  }

  if (product.is_size_visible) {
    const selectedSizeObj = product.value.sizes.find(
      (size) => size.name === selectedSize.value
    );

    if (!selectedSizeObj) {
      console.error("Invalid selected size.");
      return;
    }
  }

  const item = {
    id: product.value.id,
    name: product.value.name,
    image: product.value.image,
    size: product.value.is_size_visible ? selectedSize.value : null,
    color: product.value.is_color_visible ? selectedColor.value : null,
    quantity: quantity.value,
    price: product.value.is_size_visible
      ? formattedPrice.value
      : product.value.price.price,
  };

  cartStore.addToCart(item);
  snackbar.add({
    type: "success",
    text: "Item added to the cart!",
  });
  console.log("Product added to cart:", item);
};

const buyNow = () => {
  console.log("Buy now clicked. Redirect to checkout page.");
  // Implement the logic to proceed to checkout
};

const slideTo = (val) => {
  currentSlide.value = val;
};

watch(formattedPrice, (newFormattedPrice, oldFormattedPrice) => {
  return newFormattedPrice;
});

watch(product, (newProduct, oldProduct) => {
  return newProduct;
});
</script>
<template>
  <div>
    <div class="container mx-auto my-8 py-8 px-10 lg:px-0">
      <div class="flex flex-col lg:flex-row">
        <!-- Product Gallery -->

        <!-- Product Image -->
        <div class="w-full lg:w-1/2">
          <Carousel
            id="gallery"
            :items-to-show="1"
            :wrap-around="false"
            v-model="currentSlide"
          >
            <Slide v-for="(featureImg, index) in product.gallery" :key="index">
              <div class="carousel__item">
                <inner-image-zoom
                  v-if="featureImg.gallery != null"
                  :src="featureImg.gallery"
                  :zoomSrc="featureImg.gallery"
                  zoomPreload="true"
                  zoomType="hover"
                  class="rounded-xl"
                />
              </div>
            </Slide>
          </Carousel>

          <Carousel
            v-if="product.gallery"
            id="thumbnails"
            :items-to-show="product.gallery.length"
            :wrap-around="true"
            v-model="currentSlide"
            ref="carousel"
          >
            <Slide v-for="(galleryImg, index) in product.gallery" :key="index">
              <div class="carousel__item" @click="slideTo(index)">
                <NuxtImg
                  v-if="galleryImg.gallery != null"
                  :src="galleryImg.gallery"
                  :alt="'Product Gallery'"
                  class="h-full object-cover rounded-2xl"
                  :class="product.gallery.length < 5 ? 'w-1/2' : 'w-full'"
                />
              </div>
            </Slide>
            <template #addons>
              <Navigation />
            </template>
          </Carousel>
        </div>

        <!-- Product Details -->
        <div class="w-full lg:w-1/2 lg:pl-8 mt-4 lg:mt-0">
          <h2 class="text-4xl font-extrabold mb-4 text-gray-900">
            {{ product.name }}
          </h2>
          <p
            class="text-lg text-gray-600 mb-6"
            v-html="product.description"
          ></p>

          <!-- <div class="my-8">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <p class="text-sm font-semibold text-gray-700">
                  Model is Wearing:
                </p>
                <p class="text-base">{{ product.modelSize }}</p>
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-700">Model Height:</p>
                <p class="text-base">{{ product.modelHeight }}</p>
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-700">Care:</p>
                <p class="text-base">{{ product.careInstructions }}</p>
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-700">
                  Shipping Info:
                </p>
                <p class="text-base">{{ product.shippingInfo }}</p>
              </div>
            </div>

            <div class="mt-6">
              <div class="mb-4">
                <p class="text-lg font-semibold mb-2">Maxi :</p>
                <ul class="list-disc ml-6">
                  <li>Fabric – {{ product.maxiFabric }}</li>
                  <li>kali cut</li>
                  <li>Length – {{ product.maxiLength }}</li>
                  <li>Front Neck – {{ product.frontNeck }}</li>
                  <li>Back Neck – {{ product.backNeck }}</li>
                  <li>Sleeves – {{ product.sleeves }}</li>
                </ul>
              </div>

              <div class="mb-4">
                <p class="text-lg font-semibold mb-2">Dupatta :</p>
                <ul class="list-disc ml-6">
                  <li>Fabric – {{ product.dupattaFabric }}</li>
                  <li>Length – {{ product.dupattaLength }}</li>
                </ul>
              </div>
            </div>
          </div> -->

          <div class="mb-8">
            <h3 class="text-2xl font-semibold mb-4">Variation:</h3>
            <div class="flex space-x-4">
              <!-- Size Selection -->
              <div>
                <label
                  for="size"
                  class="block text-sm font-semibold text-gray-700"
                  >Size:</label
                >
                <div class="flex space-x-2">
                  <div
                    v-for="size in product.sizes"
                    :key="size.standard"
                    class="cursor-pointer border border-gray-300 rounded-md px-4 py-2 text-base font-semibold transition duration-300 ease-in-out transform hover:scale-105"
                    :class="{
                      'bg-brand text-white':
                        selectedSize === size.standard,
                    }"
                    @click="selectSize(size.standard)"
                  >
                    {{ size.standard }}
                  </div>
                </div>
              </div>

              <!-- Color Selection -->
              <div>
                <label
                  for="color"
                  class="block text-sm font-semibold text-gray-700"
                  >Color:</label
                >
                <div class="flex space-x-2">
                  <div
                    v-for="color in product.colors"
                    :key="color"
                    class="cursor-pointer border border-gray-300 rounded-full w-10 h-10 transition duration-300 ease-in-out transform hover:scale-105"
                    :class="{
                      'border-4 border-brand text-white':
                        selectedColor === color.color_code,
                    }"
                    :style="{ backgroundColor: color.color_code }"
                    @click="selectColor(color.color_code)"
                  ></div>
                </div>
              </div>
            </div>
          </div>

          <div class="flex items-center space-x-4 mb-8">
            <div class="flex items-center border border-gray-900 rounded-md">
              <button
                @click="decrementQuantity(product)"
                class="py-1 px-3 border-r border-gray-900 focus:outline-none"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  class="h-5 w-5"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M20 12H4"
                  ></path>
                </svg>
              </button>
              <input
                type="number"
                v-model="quantity"
                id="quantity"
                @input="updateQuantity(product)"
                class="w-16 px-3 py-1 text-center focus:outline-none"
              />
              <button
                @click="incrementQuantity(product)"
                class="py-1 px-3 border-l border-gray-900 focus:outline-none"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  class="h-5 w-5"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                  ></path>
                </svg>
              </button>
            </div>
            <div>
              <label
                for="price"
                class="block text-sm font-semibold text-gray-700"
                >Price:</label
              >
              <span class="font-bold text-xl">{{ formattedPrice }}</span>
            </div>
          </div>

          <div class="flex space-x-4">
            <button
              @click="addToCart"
              class="bg-brand hover:bg-brand-700 text-white font-bold py-2 px-6 rounded-full focus:outline-none focus:shadow-outline-brand active:bg-brand-800"
            >
              Add to Cart
            </button>
            <button
              @click="buyNow"
              class="bg-slate-900 hover:bg-slate-800 text-white font-bold py-2 px-6 rounded-full focus:outline-none focus:shadow-outline-gray active:bg-slate-950"
            >
              Buy Now
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="container mx-auto my-8">
      <!-- Tabs -->
      <div class="flex mb-4">
        <button
          @click="activeTab = 'kurti'"
          :class="{
            'bg-slate-950 text-white': activeTab === 'kurti',
            'bg-gray-200': activeTab !== 'kurti',
          }"
          class="flex-1 py-2 px-4 focus:outline-none uppercase"
        >
          Kurti Size Chart
        </button>
        <button
          @click="activeTab = 'pant'"
          :class="{
            'bg-slate-950 text-white': activeTab === 'pant',
            'bg-gray-200': activeTab !== 'pant',
          }"
          class="flex-1 py-2 px-4 focus:outline-none uppercase"
        >
          Pant Size Chart
        </button>
      </div>

      <!-- Tab Content -->
      <div v-if="activeTab === 'kurti'">
        <!-- Kurti Size Chart -->
        <table class="w-full border-collapse border border-gray-300">
          <thead>
            <tr>
              <th class="py-2 px-4 border border-gray-300">Size</th>
              <th class="py-2 px-4 border border-gray-300">Chest (inches)</th>
              <th class="py-2 px-4 border border-gray-300">Waist (inches)</th>
              <!-- Add more columns as needed -->
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="py-2 px-4 border border-gray-300">S</td>
              <td class="py-2 px-4 border border-gray-300">36-38</td>
              <td class="py-2 px-4 border border-gray-300">30-32</td>
              <!-- Add more rows as needed -->
            </tr>
            <!-- Add more rows as needed -->
          </tbody>
        </table>
      </div>

      <div v-if="activeTab === 'pant'">
        <!-- Pant Size Chart -->
        <table class="w-full border-collapse border border-gray-300">
          <thead>
            <tr>
              <th class="py-2 px-4 border border-gray-300">Size</th>
              <th class="py-2 px-4 border border-gray-300">Waist (inches)</th>
              <th class="py-2 px-4 border border-gray-300">Hip (inches)</th>
              <!-- Add more columns as needed -->
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="py-2 px-4 border border-gray-300">S</td>
              <td class="py-2 px-4 border border-gray-300">28-30</td>
              <td class="py-2 px-4 border border-gray-300">38-40</td>
              <!-- Add more rows as needed -->
            </tr>
            <!-- Add more rows as needed -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Add custom styles here */
</style>
