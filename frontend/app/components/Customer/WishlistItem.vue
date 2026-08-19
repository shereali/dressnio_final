<template>
  <div class="bg-white p-4 shadow-md rounded-md">
    <NuxtImg
      :src="item.image"
      alt="Product Image"
      class="w-full h-40 object-cover mb-4"
    />
    <p class="text-lg font-semibold mb-2">{{ item.name }}</p>
    <p class="text-gray-700 mb-2">${{ item.price.toFixed(2) }}</p>
    <button
      @click="removeFromWishlist"
      class="text-red-500 hover:text-red-600 focus:outline-none"
    >
      Remove from Wishlist
    </button>
  </div>
</template>

<script setup>
const { item } = defineProps(["item"]);
const emit = defineEmits();

const removeFromWishlist = () => {
  emit("remove", item.id);
};
</script>
