<script setup>
import { useCartStore } from "~/stores/cart";
const props = defineProps(["product"]);

const product = props.product;
// const product = computed(() => props.product);

// watch(product, (newValue, oldValue) => {
//   console.log(newValue, oldValue);
// });

const cartStore = useCartStore();
const snackbar = useSnackbar();

const prices = {};
let index = -1;

for (const [key, value] of Object.entries(product.sizes)) {
  index++;
  prices[value.standard] = product.prices[index][value.standard];
}

let selectedSize = ref(product.sizes[0]?.standard);
let selectedColor = ref(product.colors[0]?.color_code);
let quantity = ref(1);

const formattedPrice = computed(() => {
  if (product.is_size_visible) {
    return (prices[selectedSize.value] * quantity.value).toFixed(2);
  } else {
    return (product.price.price * quantity.value).toFixed(2);
  }
});

const incrementQuantity = () => {
  quantity.value++;
  updatePrice();
};

const decrementQuantity = (product) => {
  if (quantity.value > 1) {
    quantity.value--;
    updatePrice();
  }
  cartStore.decrementQuantity(product);
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
  } else {
    product.formattedPrice = (product.price.price * quantity.value).toFixed(2);
  }
};

const addToCart = () => {
  if (
    product.is_size_visible &&
    (!selectedSize.value || !selectedColor.value)
  ) {
    console.error("Please select size and color before adding to cart.");
    // Ideally show a user-visible error here
    snackbar.add({
        type: "error",
        text: "Please select size and color."
    });
    return;
  }

  let unitPrice = 0;

  if (product.is_size_visible) {
    const selectedSizeObj = product?.sizes.find(
      (size) => size.standard === selectedSize.value
    );

    if (!selectedSizeObj) {
      console.error("Invalid selected size.");
      return;
    }
    // Logic to get price from prices map which was populated earlier
    // prices[selectedSize.value] holds the price for that size
    unitPrice = parseFloat(prices[selectedSize.value]); 
  } else {
    unitPrice = parseFloat(product.price.price);
  }

  const item = {
    id: product.id,
    name: product.name,
    image: product.image,
    size: product.is_size_visible ? selectedSize.value : null,
    color: product.is_color_visible ? selectedColor.value : null,
    quantity: quantity.value,
    price: unitPrice, 
  };

  cartStore.addToCart(item);
  snackbar.add({
    type: "success",
    text: "Item added to the cart!",
  });
  console.log("Product added to cart:", item);
};
</script>

<template>
  <div>
    <div
      class="group flex flex-col justify-between items-center shadow-lg hover:shadow-2xl transition-all duration-300 ease-in-out transform hover:-translate-y-1 relative bg-white rounded-lg overflow-hidden h-full"
    >
      <nuxt-link :to="`/product/${product.slug}`" class="w-full relative overflow-hidden aspect-[3/4]">
        <NuxtImg
          v-if="product.image != null"
          class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
          :src="product.image"
          :alt="product.name"
        />
        <!-- Overlay for quick actions could go here -->
      </nuxt-link>
      
      <div class="flex flex-col w-full p-4">
        <!-- Rating -->
        <div
          class="flex flex-row items-center justify-center space-x-1 mb-2"
        >
          <Icon name="iconamoon:star-fill" class="text-secondary w-4 h-4" />
          <Icon name="iconamoon:star-fill" class="text-brand w-4 h-4" />
          <Icon name="iconamoon:star-fill" class="text-brand w-4 h-4" />
          <Icon name="iconamoon:star-fill" class="text-brand w-4 h-4" />
          <Icon name="iconamoon:star-fill" class="text-brand w-4 h-4" />
          <!-- <span class="text-xs text-gray-500 ml-1">(5)</span> -->
        </div>

        <!-- Name -->
        <div class="text-center mb-2">
            <nuxt-link
                :to="`/product/${product.slug}`"
                class="font-bold text-lg text-gray-800 hover:text-brand transition-colors line-clamp-1"
            >
                {{ product.name }}
            </nuxt-link>
        </div>

        <!-- Price -->
         <div class="text-center font-semibold text-brand text-lg mb-4">৳{{ formattedPrice }}</div>
      </div>

      <!-- Hover Overlay for Add to Cart / Options -->
      <div
        class="absolute inset-x-0 bottom-0 bg-white/95 backdrop-blur-sm p-4 translate-y-full group-hover:translate-y-0 transition-transform duration-300 border-t border-gray-100 flex flex-col items-center justify-center"
      >
        <!-- Product Variation -->
        <div v-if="product.is_size_visible || product.is_color_visible" class="flex flex-col items-center space-y-3 mb-4 w-full">
            
          <!-- Size Selection -->
          <div v-if="product.is_size_visible" class="flex flex-wrap justify-center gap-2">
              <div
                v-for="size in product.sizes"
                :key="size.standard"
                class="cursor-pointer border text-xs font-medium rounded-md px-2 py-1 transition-colors"
                :class="selectedSize === size.standard ? 'bg-brand text-white border-brand' : 'bg-white text-gray-700 border-gray-200 hover:border-brand-300'"
                @click="selectSize(size.standard)"
              >
                {{ size.standard }}
              </div>
          </div>

          <!-- Color Selection -->
          <div v-if="product.is_color_visible" class="flex flex-wrap justify-center gap-2">
              <div
                v-for="color in product.colors"
                :key="color"
                class="cursor-pointer rounded-full w-5 h-5 shadow-sm ring-1 ring-gray-200 ring-offset-1"
                :class="{'ring-2 ring-brand ring-offset-2': selectedColor === color.color_code}"
                :style="{ backgroundColor: color.color_code }"
                @click="selectColor(color.color_code)"
              ></div>
          </div>
        </div>

        <!-- Quantity and Actions -->
        <div class="flex items-center justify-between w-full space-x-3">
            <!-- Quantity -->
             <div class="flex items-center border border-gray-300 rounded-md bg-white">
              <button
                @click="decrementQuantity(product)"
                class="p-2 hover:bg-gray-100 text-gray-600 transition-colors rounded-l-md"
              >
                <Icon name="ic:round-minus" class="w-3 h-3" />
              </button>
              <input
                type="number"
                v-model="quantity"
                @input="updateQuantity(product)"
                class="w-8 py-1 text-sm text-center focus:outline-none text-gray-800"
              />
              <button
                @click="incrementQuantity(product)"
                class="p-2 hover:bg-gray-100 text-gray-600 transition-colors rounded-r-md"
              >
               <Icon name="ic:round-plus" class="w-3 h-3" />
              </button>
            </div>

            <!-- Add to Cart Button -->
            <button
                class="flex-1 bg-brand hover:bg-brand-700 text-white text-sm font-semibold py-2 px-4 rounded-md shadow-sm transition-colors flex items-center justify-center gap-2"
                @click="addToCart"
            >
                <Icon name="streamline:shopping-cart-basket-2-shopping-basket" class="w-4 h-4" />
                Add
            </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Add custom styles here */
</style>
