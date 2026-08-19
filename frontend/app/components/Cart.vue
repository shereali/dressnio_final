<!-- Cart.vue - Floating Design Below Navbar -->
<template>
  <div>
    <!-- Floating Cart Container -->
    <div
        v-if="cartStore.isOpen"
        class="fixed top-14 sm:top-16 lg:top-[93px] right-3 sm:right-4 lg:right-0 z-[950] transform transition-all duration-300 ease-out"
        :class="cartStore.isOpen ? 'translate-y-2 opacity-100' : '-translate-y-4 opacity-0 pointer-events-none'"
    >
      <!-- Cart Card - Dynamic Height -->
      <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden w-[85vw] sm:w-[340px] md:w-[360px] max-h-[calc(100vh-80px)] flex flex-col">
        
        <!-- Header - Compact -->
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-brand-50 to-white">
            <h2 class="text-sm font-bold text-gray-800 flex items-center gap-1.5">
                <Icon name="solar:bag-3-bold" class="text-brand w-4 h-4" />
                Cart
                <span class="text-xs font-normal text-gray-500">({{ cartStore.items.length }})</span>
            </h2>
            <button 
                @click="closeCart" 
                class="p-1.5 text-gray-400 hover:text-rose-500 hover:bg-rose-50 rounded-full transition-colors focus:outline-none"
            >
                <Icon name="solar:close-circle-bold" class="w-5 h-5" />
            </button>
        </div>

        <!-- Body - Dynamic Height -->
        <div 
          class="overflow-y-auto p-3 transition-all duration-300"
          :class="cartStore.items.length === 0 ? 'min-h-[120px]' : 'max-h-[400px]'"
        >
            <!-- Empty State - Minimal -->
            <div v-if="cartStore.items.length === 0" class="flex flex-col items-center justify-center py-4 text-center">
                <div class="w-14 h-14 bg-gray-50 rounded-full flex items-center justify-center mb-2">
                     <Icon name="solar:bag-smile-outline" class="w-7 h-7 text-gray-300" />
                </div>
                <h3 class="text-sm font-semibold text-gray-700 mb-1">Cart is empty</h3>
                <p class="text-gray-500 text-xs mb-3 max-w-[160px]">Add items to get started</p>
                <button 
                    @click="goToShop" 
                    class="px-4 py-1.5 bg-brand text-white rounded-lg text-xs font-medium hover:bg-brand-700 transition-all shadow-sm hover:shadow-md focus:outline-none"
                >
                    Start Shopping
                </button>
            </div>

            <!-- Products List - Auto Height -->
            <div v-else class="space-y-2">
                <div 
                    v-for="product in cartStore.items" 
                    :key="`${product.id}-${product.size}-${product.color}`"
                    class="group relative flex gap-2 p-2 rounded-lg border border-gray-100 hover:border-brand-100 bg-white transition-all hover:shadow-sm"
                >
                    <!-- Image - Smaller -->
                    <div class="w-14 h-16 flex-shrink-0 bg-gray-50 rounded-md overflow-hidden border border-gray-100">
                        <img
                            :src="product.image"
                            :alt="product.name"
                            class="w-full h-full object-cover"
                        />
                    </div>
                
                    <!-- Content - Compact -->
                    <div class="flex-1 min-w-0 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start gap-1">
                                <h3 class="text-xs font-bold text-gray-800 line-clamp-1 pr-1">{{ product.name }}</h3>
                                <!-- Remove Button -->
                                <button
                                    @click="removeFromCart(product)"
                                    class="text-gray-400 hover:text-rose-500 transition-colors p-0.5"
                                    title="Remove"
                                >
                                    <Icon name="solar:trash-bin-minimalistic-bold" class="w-3.5 h-3.5" />
                                </button>
                            </div>
                            
                            <!-- Attributes - Compact -->
                            <div class="mt-0.5 flex flex-wrap gap-1 text-[9px] text-gray-500">
                                <span v-if="product.size" class="flex items-center bg-gray-50 px-1 py-0.5 rounded">
                                    {{ product.size }}
                                </span>
                                <span v-if="product.color" class="flex items-center gap-0.5 bg-gray-50 px-1 py-0.5 rounded">
                                    <span class="w-2 h-2 rounded-full border border-gray-200" :style="{ backgroundColor: product.color }"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Price & Quantity - Compact -->
                        <div class="flex items-center justify-between mt-1">
                             <div class="font-bold text-brand text-xs">৳{{ (product.price * product.quantity).toFixed(2) }}</div>
                             
                             <div class="flex items-center border border-gray-200 rounded-md bg-white shadow-sm overflow-hidden h-5">
                                <button @click="decrementQuantity(product)" class="px-1 h-full hover:bg-gray-50 text-gray-600 transition-colors border-r border-gray-100">
                                    <Icon name="ic:round-minus" class="w-2 h-2" />
                                </button>
                                <input
                                    type="number"
                                    v-model="product.quantity"
                                    class="w-6 text-center text-[9px] font-semibold focus:outline-none text-gray-700"
                                    @input="updateQuantity(product)"
                                />
                                <button @click="incrementQuantity(product)" class="px-1 h-full hover:bg-gray-50 text-gray-600 transition-colors border-l border-gray-100">
                                    <Icon name="ic:round-plus" class="w-2 h-2" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer - Compact (Only show when items exist) -->
        <div v-if="cartStore.items.length > 0" class="p-3 bg-gray-50 border-t border-gray-100 space-y-2">
             <div class="flex items-center justify-between text-sm font-bold text-gray-800">
                <span>Subtotal</span>
                <span>৳{{ calculateTotal() }}</span>
             </div>
             <p class="text-[9px] text-gray-500 text-center">Shipping & taxes at checkout</p>
             
            <button
                @click="checkout"
                class="w-full py-2 bg-brand text-white font-bold rounded-lg hover:bg-brand-700 transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-1.5 group text-xs"
            >
                Checkout
                <Icon name="solar:arrow-right-bold" class="group-hover:translate-x-1 transition-transform w-3.5 h-3.5" />
            </button>
            <button 
                class="w-full py-1.5 text-gray-600 font-semibold hover:text-brand transition-colors text-[10px]"
                @click="goToCart"
            >
                View Full Cart
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
    text: "Item removed from cart",
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
  cartStore.isOpen = false
};

const goToCart = () => {
  router.push("/cart");
  cartStore.isOpen = false
};

const goToShop = () => {
  router.push("/shop"); // Assuming /shop path exists, otherwise /
  cartStore.isOpen = false
};
</script>

<style scoped>
/* No Logic needed for transition if we use classes */
</style>
