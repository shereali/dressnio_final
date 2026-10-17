<script setup>
const { freeShippingAbove } = useStoreSettings();

const items = computed(() => [
  {
    icon: "solar:wallet-money-bold-duotone",
    title: "Cash on delivery",
    text: "Pay when the parcel reaches you, anywhere in Bangladesh.",
  },
  {
    icon: "solar:smartphone-2-bold-duotone",
    title: "bKash, Nagad and cards",
    text: "Pay online if you would rather settle before it ships.",
  },
  {
    icon: "solar:delivery-bold-duotone",
    title: `Free delivery over ${formatBDT(freeShippingAbove.value)}`,
    text: `${STORE_POLICY.deliveryInsideCity} inside your city, ${STORE_POLICY.deliveryOutsideCity} elsewhere.`,
  },
  {
    icon: "solar:refresh-circle-bold-duotone",
    title: `${STORE_POLICY.exchangeWindow} size exchange`,
    text: "Wrong fit? Send it back and we swap the size.",
  },
]);
</script>

<template>
  <section class="trust" aria-label="Shopping promises">
    <div class="pm-wrap">
      <ul class="trust-list">
        <li v-for="item in items" :key="item.title" class="trust-item">
          <span class="trust-icon"><Icon :name="item.icon" class="w-7 h-7" /></span>
          <div>
            <p class="trust-title">{{ item.title }}</p>
            <p class="trust-text">{{ item.text }}</p>
          </div>
        </li>
      </ul>
    </div>
  </section>
</template>

<style scoped>
.trust {
  background: var(--brand-deep);
  color: #fff;
}
.trust-list {
  list-style: none;
  display: grid;
  grid-template-columns: repeat(2, 1fr);
}
.trust-item {
  display: flex;
  gap: 0.9rem;
  align-items: flex-start;
  padding: 1.4rem 1rem;
}
.trust-icon {
  flex-shrink: 0;
  width: 2.9rem;
  height: 2.9rem;
  display: grid;
  place-items: center;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.1);
  color: #c8c8fa;
}
.trust-title {
  font-weight: 600;
  font-size: 0.98rem;
  line-height: 1.3;
}
.trust-text {
  margin-top: 0.2rem;
  font-size: 0.85rem;
  line-height: 1.5;
  color: rgba(255, 255, 255, 0.66);
}
@media (min-width: 1024px) {
  .trust-list {
    grid-template-columns: repeat(4, 1fr);
  }
  .trust-item + .trust-item {
    border-left: 1px solid rgba(255, 255, 255, 0.12);
  }
  .trust-item {
    padding: 1.6rem 1.5rem;
  }
}
@media (max-width: 639px) {
  .trust-list {
    grid-template-columns: 1fr;
  }
  .trust-item + .trust-item {
    border-top: 1px solid rgba(255, 255, 255, 0.12);
  }
}
</style>
