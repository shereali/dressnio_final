<script setup>
// Top strip. Text is managed in admin settings; the fallback is the store's delivery promise.
const { announcement, freeShippingAbove } = useStoreSettings();
const dismissed = useState("announcement-dismissed", () => false);

const text = computed(
  () =>
    announcement.value ||
    `Free delivery on orders over ${formatBDT(freeShippingAbove.value)}. Cash on delivery across Bangladesh.`
);
</script>

<template>
  <div v-if="!dismissed" class="bar" role="region" aria-label="Store announcement">
    <p>{{ text }}</p>
    <button type="button" aria-label="Dismiss announcement" @click="dismissed = true">
      <Icon name="solar:close-circle-linear" class="w-5 h-5" />
    </button>
  </div>
</template>

<style scoped>
.bar {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.55rem 3rem;
  background: var(--brand);
  color: #fff;
  font-size: 0.86rem;
  font-weight: 500;
  text-align: center;
  line-height: 1.4;
}
.bar button {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  display: grid;
  place-items: center;
  width: 2rem;
  height: 2rem;
  color: rgba(255, 255, 255, 0.8);
}
.bar button:hover {
  color: #fff;
}
</style>
