<script setup>
const config = useRuntimeConfig();
const product = ref({});

const { data, error, pending, refresh } = useApiFetch(
  "/client/highlight-product",
  {
    method: "GET",
  }
);

watchEffect(() => {
  if (data.value) {
    product.value = data.value.data || [];
  } else {
    refresh();
  }
});

watch(data, (newData, oldData) => {
  return newData;
});

const prices = {};
let index = -1;
let quantity = ref(1);
let selectedSize = ref("");
let selectedColor = ref("");
// console.log("highlight", data.value.length);
if (data.hasOwnProperty("value")) {
  product.value = data.value.data;

  console.log("highlight", data.value);

  // const props = defineProps({
  //   highlight: {
  //     type: Object,
  //     required: true,
  //   },
  // });

  // // Extract userData from props
  // const { highlight } = toRefs(props);

  for (const [key, value] of Object.entries(product.value.sizes)) {
    index++;
    prices[value.standard] = product.value.prices[index][value.standard];
  }

  selectedSize.value = product.value.sizes[0].standard;
  selectedColor.value = product.value.colors[0].color_code;
}

const formattedPrice = computed(() => {
  return (prices[selectedSize.value] * quantity.value).toFixed(2);
});
</script>

<template>
  <div v-if="!pending" class="bg-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <Headline
        title="Featured Product"
        subtitle="Handpicked just for you"
        class="mb-16"
      />
      <div
        class="flex flex-col lg:flex-row items-center lg:items-start gap-12"
      >
        <!-- Left Column: Product Feature Image -->
        <div class="w-full lg:w-1/2 relative overflow-hidden rounded-2xl shadow-2xl">
          <!-- Background Image Frame -->
          <div class="absolute inset-0 overflow-hidden bg-gradient-to-br from-brand-50 to-brand-100">
            <NuxtImg :src="`/images/highlight-banner.jpg`" class="w-full h-full object-cover opacity-30" />
          </div>

          <!-- Product Image -->
          <div class="flex items-center justify-center relative z-10 p-8">
            <NuxtImg
              v-if="product.image != null"
              :src="product.image"
              alt="Product Feature"
              class="rounded-xl shadow-xl transform hover:scale-105 transition-transform duration-500"
            />
          </div>
        </div>

        <!-- Right Column: Product Details -->
        <div
          class="w-full lg:w-1/2 flex flex-col items-center lg:items-start text-center lg:text-left bg-white rounded-2xl shadow-xl p-8 lg:p-12"
        >
          <!-- Banner -->
          <div v-if="$device.isDesktop" class="bg-gradient-to-r from-brand-50 to-brand-100 p-6 rounded-xl mb-6 text-center w-full">
            <NuxtImg
              :src="`/images/dressnio-tree-offer.png`"
              class="object-contain max-w-[326px] max-h-[245px] mx-auto"
            />
          </div>

          <!-- Product Title -->
          <h2 class="text-3xl lg:text-4xl font-extrabold mb-4 text-gray-900">{{ product.name }}</h2>

          <!-- Short Description -->
          <div class="text-gray-600 mb-6 leading-relaxed" v-html="product.description"></div>

          <!-- Price -->
          <div class="mb-8">
            <p class="text-sm text-gray-500 mb-1">Special Price</p>
            <p class="text-4xl font-extrabold text-brand">
              ৳ {{ formattedPrice }}
            </p>
          </div>

          <!-- View Product Button -->
          <button
            class="bg-brand text-white px-10 py-4 rounded-full font-bold hover:bg-brand-700 focus:outline-none uppercase shadow-xl transition-all duration-300 hover:scale-105 hover:shadow-2xl"
          >
            View Product Details
          </button>
        </div>
      </div>
    </div>
  </div>
  <SkeletonHighlightedProduct v-else />
</template>
