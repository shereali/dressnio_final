<script setup>
defineProps({
  latestProducts: { type: Array, required: true },
});
</script>

<template>
  <div>
    <Product :products="latestProducts || []" empty-text="New arrivals are being added. Check back soon." />
    <div v-if="latestProducts?.length" class="more">
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
