<script setup>
import VuePlayerVideo from "vue3-player-video";
// Create array with 500 slides
import { useProductDetails } from "~/stores/product_details";
import { useCartStore } from "~/stores/cart";

const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
  media: {
    type: String,
    required: true,
  },
  itemIndex: {
    type: Number,
    required: true,
  },
});

const { product, media, itemIndex } = toRefs(props);

// const props = defineProps(["product", "media", "itemIndex"]);
// const product = props.product;

const productStore = useProductDetails();
const cartStore = useCartStore();

watch(product, (newProduct, oldProduct) => {
  return newProduct;
});

watch(media, (newMedia, oldMedia) => {
  return newMedia;
});

const prices = {};
const sizes = {};
let index = -1;

for (const [key, value] of Object.entries(product.sizes)) {
  index++;
  prices[value.standard] = product.prices[index][value.standard];
}

let selectedSize = ref(product.sizes[0]?.standard);
let selectedColor = ref(product.colors[0]?.color_code);

let quantity = ref(1);

const snackbar = useSnackbar();
const snackbarMsg = ($message) => {
  snackbar.add({
    type: "success",
    text: $message,
  });
};

const openProductCard = (index) => {
  productStore.openProductCard(index);
};

const closeProductCard = () => {
  productStore.openProductCard();
};

const formattedPrice = computed(() => {
  return (prices[selectedSize.value] * quantity.value).toFixed(2);
});

const incrementQuantity = () => {
  quantity.value++;
  updatePrice();
  addToCart();
};

const decrementQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--;
    updatePrice();
    addToCart();
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
  const selectedSizeObj = product?.sizes.find(
    (size) => size.standard === selectedSize.value
  );

  if (selectedSizeObj) {
    product.formattedPrice = (selectedSizeObj.price * quantity.value).toFixed(
      2
    );
  }
};

const addToCart = () => {
  if (!selectedSize.value || !selectedColor.value) {
    console.error("Please select size and color before adding to cart.");
    return;
  }

  const selectedSizeObj = product?.sizes.find(
    (size) => size.standard === selectedSize.value
  );

  if (!selectedSizeObj) {
    console.error("Invalid selected size.");
    return;
  }

  const item = {
    id: product.id,
    name: product.name,
    image: product.image,
    size: selectedSize.value,
    color: selectedColor.value,
    quantity: quantity.value,
    price: formattedPrice.value, // Use the correct price from the selected size
  };

  cartStore.addToCart(item);
  snackbar.add({
    type: "success",
    text: "Item added to the cart!",
  });
};
</script>

<template>
  <div class="transition-all duration-300 ease-in-out transform">
    <VuePlayerVideo
      theme="gradient"
      :controls="true"
      :src="media"
      class="relative"
    />
    <div class="bottom-4 left-0 w-16 h-16 absolute z-20">
      <NuxtImg
        v-if="product.image != null"
        :src="product.image"
        class="rounded-lg relative"
      />
      <Icon
        name="lucide:maximize"
        @click="openProductCard(product.id)"
        class="absolute top-0 right-0 text-red-700 cursor-pointer hover:text-slate-950 hover:text-xl"
        :v-auto-aminate="{ duration: 10000 }"
      />
    </div>
    <div
      v-if="productStore.isCloseCard == product.id"
      class="absolute top-0 right-0 z-50"
      @click="closeProductCard(product.id)"
    >
      <Icon name="humbleicons:times" class="text-3xl cursor-pointer" />
    </div>
    <div
      v-if="productStore.isCloseCard == product.id"
      class="bottom-0 left-0 w-full absolute z-20 bg-white rounded-lg flex justify-center items-center flex-col h-full"
    >
      <!-- ========== Start Product Details ========== -->
      <div
        class="h-screen flex flex-col text-center overflow-y-hidden transition-all duration-300 ease-in-out transform"
      >
        <div class="flex-1 overflow-y-auto custom-scrollbar">
          <NuxtImg
            v-if="product.image != null"
            :src="product.image"
            class="rounded-lg w-full object-cover"
          />
          <!-- Your product details content -->
          <h1 class="text-2xl font-bold mb-4">
            {{ product.name }}
          </h1>
          <div class="text-left px-3" v-html="product.description"></div>
          <!-- Add more product details as needed -->
        </div>

        <div class="">
          <p class="text-xs font-bold text-brand mt-3">
            ৳{{ formattedPrice }}
          </p>
          <div>
            <!-- <label for="size" class="block text-gray-700">Size:</label> -->

            <div class="flex justify-center items-center space-x-2 py-2">
              <div
                v-for="size in product.sizes"
                :key="size"
                class="cursor-pointer border border-gray-300 rounded-md text-xs px-2 py-1"
                :class="{
                  'bg-brand text-white': selectedSize === size.standard,
                }"
                @click="selectSize(size.standard)"
              >
                {{ size.standard }}
              </div>
            </div>

            <div class="flex justify-center items-center space-x-2 py-2">
              <div
                v-for="color in product.colors"
                :key="color"
                class="cursor-pointer rounded-full w-6 h-6"
                :class="{
                  'border-2 border-brand text-white':
                    selectedColor === color.color_code,
                }"
                :style="{ backgroundColor: color.color_code }"
                @click="selectColor(color.color_code)"
              ></div>
            </div>
            <!-- Quantity and Price -->
            <div class="flex justify-center items-center space-x-4 mb-4">
              <div class="flex items-center border border-gray-900 rounded-md">
                <button
                  @click="decrementQuantity(product)"
                  class="py-1 px-2 border-r border-gray-900 focus:outline-none"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    class="h-4 w-4"
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
                  class="w-16 px-1 text-center focus:outline-none"
                  @click="updateQuantity(product)"
                />
                <button
                  @click="incrementQuantity(product)"
                  class="py-1 px-2 border-l border-gray-900 focus:outline-none"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    class="h-4 w-4"
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
              <!-- <div>
                    <label for="price" class="block text-gray-700"
                      >Price:</label
                    >
                    <span class="font-bold text-xl">{{ formattedPrice }}</span>
                  </div> -->
            </div>
          </div>
          <button
            class="py-1 mb-3 bg-brand hover:bg-brand-700 text-white uppercase px-5 rounded-sm"
            @click="addToCart(product)"
          >
            <Icon
              name="streamline:shopping-cart-basket-2-shopping-basket"
              class="-mt-2"
            />
            Add To Cart
          </button>
        </div>
      </div>

      <!-- ========== End Product Details ========== -->
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

.transition-transform {
  transition-property: transform;
  transition-duration: 0.5s; /* Adjust the duration as needed */
  transition-timing-function: ease-out; /* Adjust the timing function as needed */
}

.translate-y-full {
  transform: translateY(100%);
}

.translate-y-0 {
  transform: translateY(0);
}
</style>
