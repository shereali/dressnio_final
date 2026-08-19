<!-- pages/checkout/index.vue -->

<template>
  <div class="container mx-auto p-4 flex flex-col lg:flex-row py-10">
    <!-- Product details (Left side) -->
    <div class="w-full lg:w-1/2 ">
      <h1 class="text-3xl font-semibold mb-8">Product Details</h1>
      <ul>
        <li
          v-for="product in products"
          :key="product.id"
          class="flex flex-row space-x-2 justify-between"
        >
          <!-- Container for [image, title, size, color, price] -->
          <div class="flex items-center space-x-4">
            <!-- Inline Image -->
            <NuxtImg
              :src="product.image"
              alt="Product Image"
              class="w-24 h-24 object-contain rounded-lg my-2"
            />

            <!-- Product Details -->
            <div>
              <h2 class="text-lg font-semibold">{{ product.name }}</h2>
              <div class="flex text-gray-600 space-x-2">
                <div
                  :style="{ background: product.color }"
                  class="cursor-pointer border border-gray-300 rounded-full w-6 h-6"
                ></div
                >
                <div class="place-content-center cursor-pointer border border-gray-300 rounded-md px-2 py-0.5 text-xs">{{ product.size }}</div>
              </div>
              <p class="text-gray-800 font-bold">
                ${{ formatPrice(product.price) }}
              </p>
            </div>
          </div>

          <!-- Container for [increment, decrement, quantity] -->
          <div class="flex items-end justify-end flex-row mt-4 space-x-2">
            <button @click="decrementQuantity(product)" class="px-2"><Icon name="mdi:minus"/></button>
            <span class="px-4">{{ product.quantity }}</span>
            <button @click="incrementQuantity(product)" class="px-2"><Icon name="mdi:plus"/></button>
          </div>
        </li>
      </ul>
    </div>

    <!-- Checkout form (Right side) -->
    <div class="w-full  lg:w-1/2 px-5 mx-10">
      <h1 class="text-3xl font-semibold mb-8">Checkout</h1>
      <!-- Your checkout form goes here -->
      <!-- Checkout form -->
      <p class="text-xl font-bold">Total: ৳{{ formatPrice(totalPrice) }}</p>
      <form @submit.prevent="handleSubmit">
        <!-- Name -->
        <div class="mb-4">
          <label class="block text-sm font-semibold text-gray-600">Name</label>
          <input
            v-model="formData.name"
            type="text"
            class="w-full px-4 py-2 border rounded-md focus:outline-none focus:border-brand"
            placeholder="John Doe"
            required
          />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-semibold text-gray-600"
            >Mobile No.</label
          >
          <input
            v-model="formData.mobile_no"
            type="text"
            class="w-full px-4 py-2 border rounded-md focus:outline-none focus:border-brand"
            placeholder="01749xxxxxx"
            required
          />
        </div>

        <!-- Address -->
        <div class="mb-4">
          <label class="block text-sm font-semibold text-gray-600"
            >Address</label
          >
          <textarea
            v-model="formData.address"
            class="w-full px-4 py-2 border rounded-md focus:outline-none focus:border-brand"
            placeholder="123 Main St, City, Country"
            required
          ></textarea>
        </div>

        <!-- Payment details -->
        <div class="mb-4">
          <label class="block text-sm font-semibold text-gray-600"
            >Card Number</label
          >
          <input
            v-model="formData.cardNumber"
            type="text"
            class="w-full px-4 py-2 border rounded-md focus:outline-none focus:border-brand"
            placeholder="**** **** **** ****"
            required
          />
        </div>

        <!-- Add more input fields as needed -->

        <!-- Submit button -->
        <button
          type="submit"
          class="w-full bg-brand text-white font-bold py-2 px-4 rounded-md hover:bg-brand-700 focus:outline-none focus:bg-brand-700 transition-colors"
        >
          Place Order
        </button>
      </form>
      <!-- Order summary -->
      <div class="mb-8 my-5">
        <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
        <ul>
          <li v-for="product in products" :key="product.id">
            <span class="font-semibold">{{ product.name }}</span> -
            {{ product.quantity }} x ৳{{ formatPrice(product.price) }}
          </li>
        </ul>
        <hr class="my-4" />
        <p class="text-xl font-bold">Total: ৳{{ formatPrice(totalPrice) }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useCartStore } from "~/stores/cart";
import { ref, onMounted } from "vue";
const cartStore = useCartStore();
const products = ref(cartStore.items);

const formatPrice = (price) => {
  if (typeof price === "number") {
    return price.toFixed(2);
  }
  return price;
};

const totalPrice = computed(() => {
  return products.value.reduce((total, product) => {
    return total + product.price * product.quantity;
  }, 0);
});

const decrementQuantity = (product) => {
  if (product.quantity > 1) {
    product.quantity--;
  }
};

const incrementQuantity = (product) => {
  product.quantity++;
};

onMounted(async () => {
  // Fetch product details from the Laravel API
  //   const response = await fetch("/api/product-details");
  //   const data = await response.json();
  //   products.value = data.productDetails.map((product) => ({
  //     ...product,
  //     quantity: 1,
  //   }));
});

// Reactive variables
const formData = ref({
  name: "",
  address: "",
  cardNumber: "",
  // Add more fields as needed
});

// Handle form submission
const handleSubmit = async () => {
  try {
    // Send the form data to the Laravel API for processing
    const response = await fetch("/api/checkout", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(formData.value),
    });

    if (response.ok) {
      // Order placed successfully, you can redirect to a success page or handle as needed
      console.log("Order placed successfully!");
    } else {
      // Handle error responses
      console.error("Failed to place order:", response.statusText);
    }
  } catch (error) {
    console.error("Error while processing order:", error);
  }
};
</script>
