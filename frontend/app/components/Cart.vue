<!-- Cart.vue - Floating Dropdown Below Navbar -->
<template>
  <div>
    <!-- Backdrop overlay for smooth focus -->
    <div
      v-if="cartStore.isOpen"
      @click="closeCart"
      class="fixed inset-0 z-[940] bg-black/20 backdrop-blur-xs transition-opacity duration-300"
    ></div>

    <!-- Floating Cart Container -->
    <div
      v-if="cartStore.isOpen"
      class="fixed top-14 sm:top-16 lg:top-[93px] right-3 sm:right-6 lg:right-8 z-[950] transform transition-all duration-300 ease-out"
      :class="cartStore.isOpen ? 'translate-y-2 opacity-100 scale-100' : '-translate-y-4 opacity-0 scale-95 pointer-events-none'"
    >
      <!-- Cart Card -->
      <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-800 overflow-hidden w-[90vw] sm:w-[380px] md:w-[410px] max-h-[calc(100vh-110px)] flex flex-col">
        
        <!-- Header -->
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gradient-to-r from-brand-50/70 via-white to-brand-50/40 dark:from-gray-800 dark:to-gray-900">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-brand/10 dark:bg-brand/20 flex items-center justify-center text-brand">
              <Icon name="solar:bag-3-bold" class="w-4.5 h-4.5" />
            </div>
            <div>
              <h2 class="text-sm font-black text-gray-900 dark:text-white tracking-tight">Shopping Bag</h2>
              <p class="text-[11px] font-semibold text-gray-500">{{ cartStore.items.length }} {{ cartStore.items.length === 1 ? 'item' : 'items' }} selected</p>
            </div>
          </div>
          <button 
            @click="closeCart" 
            class="p-2 text-gray-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-xl transition-all"
            title="Close cart"
          >
            <Icon name="solar:close-circle-bold" class="w-5 h-5" />
          </button>
        </div>

        <!-- Body -->
        <div 
          class="overflow-y-auto p-4 transition-all duration-300 custom-scrollbar flex-1"
          :class="cartStore.items.length === 0 ? 'min-h-[160px]' : 'max-h-[440px]'"
        >
          <!-- Empty State -->
          <div v-if="cartStore.items.length === 0" class="flex flex-col items-center justify-center py-8 text-center">
            <div class="w-16 h-16 bg-brand-50 dark:bg-brand-950/30 text-brand rounded-2xl flex items-center justify-center mb-3 shadow-inner">
              <Icon name="solar:bag-smile-outline" class="w-8 h-8" />
            </div>
            <h3 class="text-sm font-black text-gray-800 dark:text-white mb-1">Your bag is empty</h3>
            <p class="text-gray-500 text-xs mb-4 max-w-[200px]">Discover our latest dresses, kurtis & sarees</p>
            <button 
              @click="goToShop" 
              class="px-5 py-2.5 bg-brand hover:bg-brand-600 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-brand/20 hover:-translate-y-0.5 active:translate-y-0"
            >
              Explore Collection
            </button>
          </div>

          <!-- Products List -->
          <div v-else class="space-y-3">
            <div 
              v-for="product in cartStore.items" 
              :key="`${product.id}-${product.size}-${product.color}`"
              class="group relative flex gap-3 p-3 rounded-2xl border border-gray-100 dark:border-gray-800 hover:border-brand-200 dark:hover:border-brand-800/50 bg-gray-50/40 dark:bg-gray-800/30 transition-all hover:shadow-xs"
            >
              <!-- Thumbnail -->
              <div class="w-16 h-20 flex-shrink-0 bg-gray-100 dark:bg-gray-800 rounded-xl overflow-hidden border border-gray-200/60 dark:border-gray-700">
                <img
                  :src="product.image || '/brand/hero-chic.jpg'"
                  :alt="product.name"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
              </div>
          
              <!-- Item Details -->
              <div class="flex-1 min-w-0 flex flex-col justify-between">
                <div>
                  <div class="flex justify-between items-start gap-1">
                    <h3 class="text-xs font-bold text-gray-900 dark:text-white line-clamp-1 pr-1" :title="product.name">
                      {{ product.name }}
                    </h3>
                    <button
                      @click="removeFromCart(product)"
                      class="text-gray-400 hover:text-rose-600 transition-colors p-1 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/40 -mr-1 -mt-1"
                      title="Remove item"
                    >
                      <Icon name="solar:trash-bin-trash-bold" class="w-4 h-4" />
                    </button>
                  </div>
                  
                  <!-- Size & Color Badges -->
                  <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px] text-gray-500">
                    <span v-if="product.size" class="px-2 py-0.5 rounded-md bg-white dark:bg-gray-800 font-semibold border border-gray-200/80 dark:border-gray-700 text-gray-700 dark:text-gray-300">
                      {{ product.size }}
                    </span>
                    <span v-if="product.color" class="flex items-center gap-1 px-2 py-0.5 rounded-md bg-white dark:bg-gray-800 font-semibold border border-gray-200/80 dark:border-gray-700 text-gray-700 dark:text-gray-300">
                      <span class="w-2.5 h-2.5 rounded-full border border-black/10 shadow-xs" :style="{ backgroundColor: product.color }"></span>
                    </span>
                  </div>
                </div>

                <!-- Price & Comfortable Ergonomic Quantity Controller -->
                <div class="flex items-center justify-between mt-2 pt-1 border-t border-gray-200/40 dark:border-gray-700/40">
                  <div class="font-black text-brand text-sm tracking-tight">
                    ৳{{ (product.price * product.quantity).toFixed(2) }}
                  </div>
                  
                  <!-- User-Friendly Large +/- Button Cluster -->
                  <div class="flex items-center gap-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-0.5 shadow-xs">
                    <button 
                      type="button"
                      @click="decrementQuantity(product)" 
                      class="w-7 h-7 rounded-lg bg-gray-50 dark:bg-gray-800 hover:bg-brand-50 hover:text-brand text-gray-700 dark:text-gray-300 flex items-center justify-center transition-all active:scale-90"
                      title="Decrease quantity"
                    >
                      <Icon name="solar:minus-bold" class="w-3.5 h-3.5" />
                    </button>

                    <span class="w-7 text-center text-xs font-black text-gray-900 dark:text-white select-none">
                      {{ product.quantity }}
                    </span>

                    <button 
                      type="button"
                      @click="incrementQuantity(product)" 
                      class="w-7 h-7 rounded-lg bg-gray-50 dark:bg-gray-800 hover:bg-brand-50 hover:text-brand text-gray-700 dark:text-gray-300 flex items-center justify-center transition-all active:scale-90"
                      title="Increase quantity"
                    >
                      <Icon name="solar:add-bold" class="w-3.5 h-3.5" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div v-if="cartStore.items.length > 0" class="p-4 bg-gray-50/80 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800 space-y-3">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Subtotal</span>
            <span class="text-base font-black text-gray-900 dark:text-white tracking-tight">৳{{ calculateTotal() }}</span>
          </div>
          <p class="text-[10px] text-gray-500 text-center">Shipping and delivery calculated at checkout</p>
          
          <button
            @click="checkout"
            class="w-full py-3 bg-brand hover:bg-brand-600 text-white font-bold rounded-xl transition-all shadow-md shadow-brand/25 hover:shadow-lg flex items-center justify-center gap-2 group text-xs tracking-wide"
          >
            <span>Proceed to Checkout</span>
            <Icon name="solar:arrow-right-bold" class="group-hover:translate-x-1 transition-transform w-4 h-4" />
          </button>

          <button 
            type="button"
            class="w-full py-2 text-gray-600 dark:text-gray-400 font-bold hover:text-brand dark:hover:text-white transition-colors text-xs text-center"
            @click="goToCart"
          >
            View Bag & Order Summary
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from "vue-router";
import { useCartStore } from "~/stores/cart";

const router = useRouter();
const cartStore = useCartStore();
const snackbar = useSnackbar();

const updateQuantity = (product) => {
  cartStore.updateQuantity(product);
};

const incrementQuantity = (product) => {
  cartStore.incrementQuantity(product);
};

const decrementQuantity = (product) => {
  cartStore.decrementQuantity(product);
};

const removeFromCart = (product) => {
  cartStore.removeProduct(product);
  snackbar.add({
    type: "info",
    text: "Item removed from bag",
  });
};

const closeCart = () => {
  cartStore.closeCart();
};

const calculateTotal = () => {
  return cartStore.items
    .reduce((total, item) => total + item.price * item.quantity, 0)
    .toFixed(2);
};

const checkout = () => {
  router.push("/checkout");
  cartStore.isOpen = false;
};

const goToCart = () => {
  router.push("/cart");
  cartStore.isOpen = false;
};

const goToShop = () => {
  router.push("/shop");
  cartStore.isOpen = false;
};
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(156, 163, 175, 0.25);
  border-radius: 9999px;
}
</style>
