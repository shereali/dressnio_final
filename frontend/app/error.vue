<script setup>
const error = useError();
</script>
<template>
  <div class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="text-center">
      <h1 class="text-6xl font-bold text-red-500">{{ error.statusCode }}</h1>
      <p class="text-2xl text-gray-800">{{ error.message }}</p>
      <p class="mt-4 text-lg text-gray-600">{{ error.description }}</p>
      <NuxtLink
        to="/"
        class="mt-8 inline-block bg-blue-500 text-white px-6 py-3 rounded hover:bg-blue-600"
        >Go Home</NuxtLink
      >
    </div>
  </div>
</template>
