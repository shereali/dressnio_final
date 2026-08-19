<template>
  <CustomerLayout>
    <div class="container mx-auto mt-8 p-4">
      <!-- Cart Items -->

      <div
        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-2 gap-4"
      >
        <div
          v-for="product in cart"
          :key="product.id"
          class="bg-[#C0C0F2] px-4 py-2 rounded-md shadow-md flex items-center justify-between"
        >
          <NuxtImg
            :src="product.image"
            :alt="product.name"
            class="w-16 h-20 rounded-md object-contain"
          />
          <div class="flex-1 ml-4">
            <h3 class="text-lg font-semibold">{{ product.name }}</h3>
            <p class="text-gray-500">{{ formatCurrency(product.price) }}</p>
          </div>
          <div class="flex items-center px-4">
            <button
              @click="decrementQuantity(product)"
              class="text-gray-500 focus:outline-none"
            >
              <Icon name="icon-park:minus" />
            </button>
            <span class="mx-2">{{ product.quantity }}</span>
            <button
              @click="incrementQuantity(product)"
              class="text-gray-500 focus:outline-none"
            >
              <Icon name="icon-park:plus" />
            </button>
          </div>
          <button
            @click="removeFromCart(product.id)"
            class="text-red-500 focus:outline-none px-2"
          >
            <Icon name="icon-park:close" />
          </button>
        </div>
      </div>

      <!-- Cart Summary -->
      <div class="mt-8 flex flex-col shadow-2xl px-5 py-10">
        <div class="w-full lg:w-1/4 mb-5 border-b border-slate-950">
          <h2 class="text-2xl font-bold mb-4">Cart Summary</h2>
          <div class="flex justify-between items-center mb-4">
            <span class="text-gray-600">Subtotal:</span>
            <span class="font-bold">{{ formatCurrency(subtotal) }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-gray-600">Total:</span>
            <span class="font-bold">{{ formatCurrency(total) }}</span>
          </div>
        </div>

        <div class="flex flex-col lg:flex-row items-start lg:space-x-2">
          <NuxtLink
            to="/checkout"
            class="py-2 my-1 px-5 bg-slate-800 hover:bg-slate-950 text-white"
          >
            Proceed to checkout <Icon name="mdi:payment" />
          </NuxtLink>
          <NuxtLink
            to="/shop"
            class="py-2 my-1 px-5 bg-slate-800 hover:bg-slate-950 text-white"
          >
            Continue to shopping <Icon name="mdi:shopping-cart" />
          </NuxtLink>
        </div>
      </div>
    </div>
  </CustomerLayout>
</template>

<script setup>
import { useCartStore } from "~/stores/cart";
import { ref, computed } from "vue";

const cartStore = useCartStore();

const cart = ref(cartStore.items);

const removeFromCart = (productId) => {
  const index = cart.value.findIndex((p) => p.id === productId);
  if (index !== -1) {
    cart.value.splice(index, 1);
  }
};

const incrementQuantity = (product) => {
  product.quantity++;
};

const decrementQuantity = (product) => {
  if (product.quantity > 1) {
    product.quantity--;
  }
};

const subtotal = computed(() => {
  return cart.value.reduce(
    (acc, product) => acc + product.price * product.quantity,
    0
  );
});

const total = computed(() => subtotal.value);

const formatCurrency = (price) => {
  return `৳${price}`;
};
</script>
