<script setup>
// Responsive product grid with a friendly empty state.
const props = defineProps({
  products: { type: Array, required: true },
  emptyText: { type: String, default: "Nothing here yet. New pieces land often, so check back soon." },
});
</script>

<template>
  <div>
    <ul v-if="props.products.length" v-auto-animate class="pgrid">
      <li v-for="product in props.products" :key="product.id">
        <ProductCard :product="product" />
      </li>
    </ul>
    <div v-else class="pempty">
      <GarmentArt kind="dress" color="#8383ED" class="pempty-art" />
      <p>{{ props.emptyText }}</p>
      <NuxtLink to="/shop" class="pm-btn pm-btn-ghost">Browse the full shop</NuxtLink>
    </div>
  </div>
</template>

<style scoped>
.pgrid {
  list-style: none;
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.9rem;
}
@media (min-width: 800px) {
  .pgrid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1.3rem;
  }
}
@media (min-width: 1100px) {
  .pgrid {
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 1.6rem;
  }
}
.pempty {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.2rem;
  padding: 2.5rem 1rem;
  text-align: center;
  color: var(--ink-soft);
}
.pempty-art {
  width: 130px;
  height: 170px;
}
</style>
