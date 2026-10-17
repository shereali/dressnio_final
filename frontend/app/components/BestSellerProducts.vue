<script setup>
defineProps({
  bestSellerProducts: { type: Array, required: true },
});
</script>

<template>
  <div>
    <Product :products="bestSellerProducts || []" empty-text="Best sellers appear here once orders come in." />
    <div v-if="bestSellerProducts?.length" class="more">
      <NuxtLink to="/shop" class="pm-btn pm-btn-ghost">View all products</NuxtLink>
    </div>
  </div>
</template>

<style scoped>
.more {
  display: flex;
  justify-content: center;
  margin-top: 2.5rem;
}
</style>
