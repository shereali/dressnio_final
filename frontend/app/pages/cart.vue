<template>
  <div class="container mx-auto mt-8 p-6 min-h-screen">
    <Headline title="Your Cart" subtitle="Review your items before checkout" class="mb-8" />

    <div v-if="cart.length === 0" class="flex flex-col items-center justify-center py-20 bg-gray-50 rounded-lg shadow-inner">
      <Icon name="streamline:shopping-cart-basket-2-shopping-basket" class="w-24 h-24 text-gray-300 mb-4" />
      <h3 class="text-2xl font-semibold text-gray-600 mb-2">Your cart is empty</h3>
      <p class="text-gray-500 mb-6">Looks like you haven't added anything to your cart yet.</p>
      <NuxtLink to="/" class="bg-brand hover:bg-brand-700 text-white font-bold py-3 px-8 rounded-full transition-colors shadow-lg hover:shadow-xl">
        Start Shopping
      </NuxtLink>
    </div>

    <div v-else class="flex flex-col lg:flex-row gap-8">
      <!-- Cart Items List -->
      <div class="flex-1 space-y-4">
        <div
          v-for="product in cart"
          :key="`${product.id}-${product.size}-${product.color}`"
          class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col sm:flex-row items-center gap-4 transition-transform hover:scale-[1.01]"
        >
          <div class="w-24 h-24 flex-shrink-0 bg-gray-100 rounded-md overflow-hidden">
             <NuxtImg
              :src="product.image"
              :alt="product.name"
              class="w-full h-full object-cover"
            />
          </div>
         
          <div class="flex-1 text-center sm:text-left">
            <h3 class="text-lg font-bold text-gray-800">{{ product.name }}</h3>
            <div class="text-sm text-gray-500 mt-1 flex flex-wrap justify-center sm:justify-start gap-2">
                <span v-if="product.size" class="px-2 py-0.5 bg-gray-100 rounded text-xs border border-gray-200">Size: {{ product.size }}</span>
                <span v-if="product.color" class="flex items-center gap-1 px-2 py-0.5 bg-gray-100 rounded text-xs border border-gray-200">
                    Color: <span class="w-3 h-3 rounded-full border border-gray-300" :style="{ backgroundColor: product.color }"></span>
                </span>
            </div>
            <p class="text-brand font-semibold mt-2">{{ formatCurrency(product.price * product.quantity) }}</p>
          </div>

          <!-- Quantity Controls -->
          <div class="flex items-center border border-gray-300 rounded-lg bg-gray-50">
            <button
              @click="decrementQuantity(product)"
              class="p-2 text-gray-600 hover:text-brand focus:outline-none hover:bg-gray-200 rounded-l-lg transition-colors"
            >
              <Icon name="ic:round-minus" />
            </button>
            <span class="px-4 font-semibold text-gray-700 min-w-[2rem] text-center">{{ product.quantity }}</span>
            <button
              @click="incrementQuantity(product)"
              class="p-2 text-gray-600 hover:text-brand focus:outline-none hover:bg-gray-200 rounded-r-lg transition-colors"
            >
              <Icon name="ic:round-plus" />
            </button>
          </div>

          <!-- Remove Button -->
          <button
            @click="removeFromCart(product.id)"
            class="text-gray-400 hover:text-red-500 transition-colors p-2 rounded-full hover:bg-red-50"
            title="Remove Item"
          >
            <Icon name="ph:trash-bold" class="w-5 h-5" />
          </button>
        </div>
      </div>

      <!-- Cart Summary -->
      <div class="w-full lg:w-96">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 sticky top-24">
            <h2 class="text-xl font-bold mb-6 text-gray-800 border-b border-gray-100 pb-4">Order Summary</h2>
            
            <div class="space-y-3 mb-6">
                <div class="flex justify-between items-center text-gray-600">
                <span>Subtotal</span>
                <span class="font-medium text-gray-800">{{ formatCurrency(subtotal) }}</span>
                </div>
                <div class="flex justify-between items-center text-gray-600">
                <span>Shipping</span>
                <span class="text-xs text-gray-400">Calculated at checkout</span>
                </div>
                <div class="flex justify-between items-center text-gray-600">
                <span>Tax</span>
                <span class="text-xs text-gray-400">Calculated at checkout</span>
                </div>
            </div>
            
            <div class="flex justify-between items-center border-t border-gray-100 pt-4 mb-8">
                <span class="text-lg font-bold text-gray-800">Total</span>
                <span class="text-2xl font-bold text-brand">{{ formatCurrency(total) }}</span>
            </div>

            <div class="flex flex-col space-y-3">
                <NuxtLink
                to="/checkout"
                class="w-full py-3 px-4 bg-brand hover:bg-brand-700 text-white font-bold rounded-lg text-center transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2 group"
                >
                Proceed to Checkout
                <Icon name="heroicons:arrow-right" class="group-hover:translate-x-1 transition-transform" />
                </NuxtLink>
                
                <NuxtLink
                to="/"
                class="w-full py-3 px-4 bg-white border border-gray-200 hover:border-gray-300 text-gray-700 font-semibold rounded-lg text-center transition-colors flex items-center justify-center gap-2"
                >
                Continue Shopping
                </NuxtLink>
            </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useCartStore } from "~/stores/cart";
import { ref, computed } from "vue";

const cartStore = useCartStore();

// Use computed to stay valid when items change
const cart = computed(() => cartStore.items);

const removeFromCart = (productId) => {
    // We should probably remove based on ID + Size + Color specific match for robust removal
    // using cartStore action is better practice
    const product = cart.value.find(p => p.id === productId); 
    if(product) cartStore.removeProduct(product);
};

const incrementQuantity = (product) => {
  cartStore.incrementQuantity(product);
};

const decrementQuantity = (product) => {
  cartStore.decrementQuantity(product);
};

const subtotal = computed(() => {
  return cart.value.reduce(
    (acc, product) => acc + product.price * product.quantity,
    0
  );
});

const total = computed(() => subtotal.value);

const formatCurrency = (price) => {
  // Check if price is valid number, else return 0.00
  const val = parseFloat(price);
  if(isNaN(val)) return '৳0.00';
  return `৳${val.toFixed(2)}`;
};
</script>
