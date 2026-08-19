<template>
  <CustomerLayout>
    <div class="container mx-auto p-4">
      <h1 class="text-3xl font-bold mb-4">Wishlist</h1>

      <!-- Wishlist Items -->
      <div v-if="wishlist.length === 0" class="text-gray-500">
        Your wishlist is empty.
      </div>
      <div
        v-else
        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"
      >
        <CustomerWishlistItem
          v-for="item in wishlist"
          :key="item.id"
          :item="item"
          @remove="removeFromWishlist"
        />
      </div>
    </div>
  </CustomerLayout>
</template>

<script setup>
import { ref } from "vue";

const wishlist = ref([
  { id: 1, name: "Product A", price: 29.99, image: "/images/product/p-1.webp" },
  { id: 2, name: "Product B", price: 45.5, image: "/images/product/p-2.webp" },
  { id: 3, name: "Product C", price: 65.5, image: "/images/product/p-2.webp" },
  { id: 4, name: "Product D", price: 90.5, image: "/images/product/p-2.webp" },
  // Add more wishlist items as needed
]);

const removeFromWishlist = (itemId) => {
  wishlist.value = wishlist.value.filter((item) => item.id !== itemId);
};
</script>
