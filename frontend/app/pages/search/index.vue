<template>
  <div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-8">Product Search</h1>

    <!-- Search Input -->
    <div class="mb-6">
      <label for="search" class="block text-gray-700 text-sm font-bold mb-2"
        >Search Products:</label
      >
      <div class="relative">
        <input
          type="text"
          id="search"
          v-model="searchQuery"
          @input="handleSearch"
          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500"
          placeholder="Enter keywords..."
        />
      </div>
    </div>

    <!-- Search Results -->
    <div v-if="searchResults.length === 0" class="text-gray-700 text-lg">
      No results found.
    </div>
    <div v-else>
      <div
        v-for="product in searchResults"
        :key="product.id"
        class="flex items-center mb-6"
      >
        <div class="flex-shrink-0 w-52 overflow-hidden rounded-md">
          <NuxtImg
            :src="product.image"
            alt="Product Image"
            class="w-full h-full object-cover"
          />
        </div>
        <div class="ml-4 flex-1">
          <h3 class="text-lg font-semibold">{{ product.name }}</h3>
          <p class="text-gray-700">Description: {{ product.description }}</p>
          <p class="text-gray-700">Size: {{ product.size }}</p>
          <p class="text-gray-700">Color: {{ product.color }}</p>
          <div class="flex items-center mt-2 space-x-3">
            <button
              @click="decrementQuantity(product)"
              class="text-gray-500 hover:text-gray-700 focus:outline-none text-2xl"
            >
              <Icon name="mdi:minus" />
            </button>
            <input
              type="number"
              v-model="product.quantity"
              min="1"
              class="w-12 px-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500"
            />
            <button
              @click="incrementQuantity(product)"
              class="text-gray-500 hover:text-gray-700 focus:outline-none text-2xl"
            >
              <Icon name="mdi:plus" />
            </button>
            <button
              @click="addToCart(product)"
              class="ml-4 px-4 py-2 bg-blue-500 text-white rounded-md"
            >
              Add to Cart
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

const products = [
  {
    id: 1,
    name: "Product A",
    size: "M",
    color: "Blue",
    image: "/images/product/p-1.webp",
    quantity: 1,
    description:
      "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam eu ante vel est convallis dignissim.",
  },
  {
    id: 2,
    name: "Product B",
    size: "L",
    color: "Red",
    image: "/images/product/p-2.webp",
    quantity: 1,
    description:
      "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam eu ante vel est convallis dignissim.",
  },
  {
    id: 3,
    name: "Product B",
    size: "L",
    color: "Red",
    image: "/images/product/p-3.webp",
    quantity: 1,
    description:
      "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam eu ante vel est convallis dignissim.",
  },
  // Add more products as needed
];

const searchQuery = ref("");
const searchResults = ref([]);

const handleSearch = () => {
  // Perform live search logic here based on searchQuery
  searchResults.value = products.filter((product) =>
    product.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
};

const incrementQuantity = (product) => {
  product.quantity++;
};

const decrementQuantity = (product) => {
  if (product.quantity > 1) {
    product.quantity--;
  }
};

const addToCart = (product) => {
  // Placeholder logic for adding product to cart
  console.log(
    `Added ${product.name} to cart with quantity ${product.quantity}`
  );
};
</script>
