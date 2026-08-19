<template>
  <CustomerLayout>
    <div class="container mx-auto p-4">
      <h1 class="text-3xl font-bold mb-4">Order History</h1>

      <!-- Responsive Order List -->
      <div
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4"
      >
        <CustomerOrderCard
          v-for="order in orders"
          :key="order.id"
          :order="order"
        />
      </div>
    </div>
  </CustomerLayout>
</template>

<script setup>
const orders = [
  {
    id: 1,
    date: "2023-01-15",
    total: 120.5,
    status: "Delivered",
    product: {
      name: "Product A",
      size: "Medium",
      color: "Blue",
      image: "/images/product/p-1.webp",
    },
  },
  {
    id: 2,
    date: "2023-01-15",
    total: 120.5,
    status: "Pending",
    product: {
      name: "Product A",
      size: "Medium",
      color: "Blue",
      image: "/images/product/p-2.webp",
    },
  },
  {
    id: 3,
    date: "2023-01-15",
    total: 120.5,
    status: "Shipped",
    product: {
      name: "Product A",
      size: "Medium",
      color: "Blue",
      image: "/images/product/p-3.webp",
    },
  },
  {
    id: 4,
    date: "2023-01-15",
    total: 120.5,
    status: "Shipped",
    product: {
      name: "Product A",
      size: "Medium",
      color: "Blue",
      image: "/images/product/p-4.webp",
    },
  },
  {
    id: 5,
    date: "2023-01-15",
    total: 120.5,
    status: "Shipped",
    product: {
      name: "Product A",
      size: "Medium",
      color: "Blue",
      image: "/images/product/p-5.webp",
    },
  },
  {
    id: 6,
    date: "2023-01-15",
    total: 120.5,
    status: "Shipped",
    product: {
      name: "Product A",
      size: "Medium",
      color: "Blue",
      image: "/images/product/p-6.webp",
    },
  },
  {
    id: 7,
    date: "2023-01-15",
    total: 120.5,
    status: "Shipped",
    product: {
      name: "Product A",
      size: "Medium",
      color: "Blue",
      image: "/images/product/p-7.webp",
    },
  },
  {
    id: 8,
    date: "2023-01-15",
    total: 120.5,
    status: "Shipped",
    product: {
      name: "Product A",
      size: "Medium",
      color: "Blue",
      image: "/images/product/p-8.webp",
    },
  },
  {
    id: 9,
    date: "2023-01-15",
    total: 120.5,
    status: "Shipped",
    product: {
      name: "Product A",
      size: "Medium",
      color: "Blue",
      image: "/images/product/p-9.webp",
    },
  },
  {
    id: 10,
    date: "2023-01-15",
    total: 120.5,
    status: "Shipped",
    product: {
      name: "Product A",
      size: "Medium",
      color: "Blue",
      image: "/images/product/p-10.webp",
    },
  },
  {
    id: 11,
    date: "2023-01-15",
    total: 120.5,
    status: "Shipped",
    product: {
      name: "Product A",
      size: "Medium",
      color: "Blue",
      image: "/images/product/p-11.webp",
    },
  },
  // Add more orders as needed
];
</script>
