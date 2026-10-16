<template>
  <CustomerLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-stone-100">
        <div>
          <h1 class="text-2xl font-extrabold text-stone-900">Shopping Cart</h1>
          <p class="text-xs sm:text-sm text-stone-500 mt-1">
            Review the items you have selected before proceeding to checkout.
          </p>
        </div>

        <NuxtLink
          to="/shop"
          class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs sm:text-sm font-semibold transition-colors self-start sm:self-auto"
        >
          <Icon name="solar:bag-3-bold" class="w-4 h-4 text-rose-500" />
          <span>Continue Shopping</span>
        </NuxtLink>
      </div>

      <!-- Empty State -->
      <div
        v-if="cartStore.items.length === 0"
        class="text-center py-16 px-4 rounded-2xl bg-stone-50 border border-stone-200/70"
      >
        <div class="w-16 h-16 rounded-2xl bg-rose-100/60 text-rose-500 flex items-center justify-center mx-auto mb-3">
          <Icon name="solar:cart-large-minimalistic-bold" class="w-8 h-8" />
        </div>
        <h3 class="font-bold text-stone-800 text-lg">Your cart is empty</h3>
        <p class="text-xs text-stone-500 mt-1 max-w-sm mx-auto">
          Explore our designer collections and add your favourite pieces to your cart.
        </p>
        <NuxtLink
          to="/shop"
          class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 rounded-xl bg-stone-900 hover:bg-stone-800 text-white text-xs font-medium transition-colors shadow-sm"
        >
          <span>Explore Catalog</span>
          <Icon name="solar:arrow-right-linear" class="w-4 h-4" />
        </NuxtLink>
      </div>

      <!-- Cart Items & Summary Grid -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <!-- Items List -->
        <div class="lg:col-span-2 space-y-3">
          <div
            v-for="item in cartStore.items"
            :key="item.id + (item.size || '') + (item.color || '')"
            class="bg-white border border-stone-200/90 rounded-2xl p-4 flex items-center gap-4 shadow-sm"
          >
            <!-- Thumbnail -->
            <div class="w-20 h-24 rounded-xl bg-stone-100 overflow-hidden flex-shrink-0">
              <img
                :src="item.image || '/img/p3.jpg'"
                :alt="item.name"
                class="w-full h-full object-cover"
                @error="$event.target.src = '/img/p3.jpg'"
              />
            </div>

            <!-- Details -->
            <div class="flex-1 min-w-0">
              <h4 class="font-bold text-stone-900 text-sm sm:text-base truncate">
                {{ item.name }}
              </h4>
              <div class="flex items-center gap-3 text-xs text-stone-500 mt-1">
                <span v-if="item.size" class="bg-stone-100 px-2 py-0.5 rounded-md font-medium">
                  Size: {{ item.size }}
                </span>
                <span v-if="item.color" class="bg-stone-100 px-2 py-0.5 rounded-md font-medium">
                  Color: {{ item.color }}
                </span>
              </div>
              <p class="font-bold text-rose-600 text-sm sm:text-base mt-2">
                ৳{{ Number(item.price).toFixed(2) }}
              </p>
            </div>

            <!-- Quantity Control -->
            <div class="flex items-center border border-stone-200 rounded-xl overflow-hidden bg-stone-50">
              <button
                @click="cartStore.decrementQuantity(item)"
                class="w-8 h-8 flex items-center justify-center text-stone-600 hover:bg-stone-200 transition-colors"
              >
                <Icon name="solar:minus-linear" class="w-3.5 h-3.5" />
              </button>
              <span class="w-8 text-center text-xs font-bold text-stone-900">{{ item.quantity }}</span>
              <button
                @click="cartStore.incrementQuantity(item)"
                class="w-8 h-8 flex items-center justify-center text-stone-600 hover:bg-stone-200 transition-colors"
              >
                <Icon name="solar:add-linear" class="w-3.5 h-3.5" />
              </button>
            </div>

            <!-- Remove Button -->
            <button
              @click="cartStore.removeProduct(item)"
              class="w-8 h-8 rounded-xl text-stone-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-colors"
              title="Remove item"
            >
              <Icon name="solar:trash-bin-trash-bold" class="w-4 h-4" />
            </button>
          </div>
        </div>

        <!-- Order Summary Card -->
        <div class="border border-stone-200/90 rounded-2xl p-5 bg-stone-50/80 sticky top-20">
          <h3 class="font-bold text-stone-900 text-base mb-4 pb-3 border-b border-stone-200">
            Order Summary
          </h3>

          <div class="space-y-2.5 text-sm">
            <div class="flex justify-between text-stone-600">
              <span>Subtotal</span>
              <span class="font-semibold text-stone-900">৳{{ subtotal.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between text-stone-600">
              <span>Shipping</span>
              <span class="font-semibold text-stone-900">
                {{ subtotal >= 2000 ? 'Free' : 'Calculated at checkout' }}
              </span>
            </div>
            <div v-if="subtotal < 2000" class="text-xs text-rose-500 bg-rose-50/80 p-2 rounded-lg">
              Add ৳{{ (2000 - subtotal).toFixed(2) }} more for FREE delivery across Bangladesh!
            </div>
          </div>

          <div class="border-t border-stone-200 pt-3 mt-4 mb-5 flex justify-between items-baseline">
            <span class="font-bold text-stone-900 text-base">Estimated Total</span>
            <span class="font-extrabold text-xl text-stone-900">৳{{ subtotal.toFixed(2) }}</span>
          </div>

          <NuxtLink
            to="/checkout"
            class="w-full py-3 px-4 rounded-xl bg-stone-900 hover:bg-stone-800 text-white font-bold text-sm text-center transition-all inline-flex items-center justify-center gap-2 shadow-sm shadow-stone-900/10"
          >
            <span>Proceed to Checkout</span>
            <Icon name="solar:card-2-bold" class="w-4 h-4" />
          </NuxtLink>
        </div>
      </div>
    </div>
  </CustomerLayout>
</template>

<script setup>
import { computed } from "vue";
import { useCartStore } from "~/stores/cart";

definePageMeta({
  middleware: "auth",
});

const cartStore = useCartStore();

const subtotal = computed(() => {
  return cartStore.items.reduce((acc, item) => acc + (Number(item.price) || 0) * item.quantity, 0);
});
</script>
