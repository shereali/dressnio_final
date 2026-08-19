<!-- src/components/OrderCard.vue -->
<template>
  <div class="bg-white p-4 shadow-md rounded-md">
    <p class="text-sm text-gray-500 mb-2">{{ formatDate(order.date) }}</p>
    <p class="text-lg font-semibold mb-2">Order #{{ order.id }}</p>

    <!-- Product Details with Image -->
    <div class="flex items-center mb-2">
      <div class="w-16 h-16 overflow-hidden rounded-full">
        <NuxtImg
          :src="order.product.image"
          alt="Product Image"
          class="w-full h-full object-contain"
        />
      </div>
      <div class="ml-4">
        <p class="text-gray-700">{{ order.product.name }}</p>
        <p class="text-gray-700">Size: {{ order.product.size }}</p>
        <p class="text-gray-700">Color: {{ order.product.color }}</p>
      </div>
    </div>

    <p class="text-gray-700 mb-2">Total: ${{ order.total.toFixed(2) }}</p>
    <p :class="getStatusClass(order.status)">{{ order.status }}</p>
  </div>
</template>

<script setup>
const { order } = defineProps(["order"]);

const formatDate = (dateString) => {
  const options = { year: "numeric", month: "long", day: "numeric" };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

const getStatusClass = (status) => {
  return {
    "text-brand": status === "Delivered",
    "text-yellow-600": status === "Pending",
    "text-blue-600": status === "Shipped",
    // Add more status classes as needed
  };
};
</script>
