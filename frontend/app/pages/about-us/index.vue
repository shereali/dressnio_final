<script setup>
const { about, address } = useStoreSettings();
useSeoMeta({ title: "About Dressnio", description: "Women's fashion from Dhaka: kurta sets, lehengas, sarees and unstitched suits." });

const promises = [
  { icon: "solar:wallet-money-bold-duotone", title: "Pay on delivery", text: "Cash on delivery across Bangladesh, with bKash and Nagad if you prefer to pay first." },
  { icon: "solar:delivery-bold-duotone", title: "Delivered on time", text: `${STORE_POLICY.deliveryInsideCity} inside your city and ${STORE_POLICY.deliveryOutsideCity} elsewhere.` },
  { icon: "solar:refresh-circle-bold-duotone", title: "Easy size exchange", text: `If it does not fit, you have ${STORE_POLICY.exchangeWindow} to swap the size.` },
];
</script>

<template>
  <div>
    <PageHero title="About Dressnio" :subtitle="about || 'Stitched and unstitched wear for women: kurta sets, lehengas, sarees and more.'" :crumbs="[{ label: 'About us' }]" kind="saree" color="#1F8A70" />
    <section class="pm-section ab">
      <div class="pm-wrap">
        <ul class="ab-grid">
          <li v-for="p in promises" :key="p.title">
            <Icon :name="p.icon" class="w-9 h-9" />
            <h2>{{ p.title }}</h2>
            <p>{{ p.text }}</p>
          </li>
        </ul>
        <p v-if="address" class="ab-addr">Visit us: {{ address }}</p>
        <NuxtLink to="/shop" class="pm-btn pm-btn-primary">Shop the collection</NuxtLink>
      </div>
    </section>
  </div>
</template>

<style scoped>
.ab {
  background: var(--paper);
  padding-top: 2.5rem;
}
.ab-grid {
  list-style: none;
  display: grid;
  gap: 1.2rem;
}
@media (min-width: 800px) {
  .ab-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}
.ab-grid li {
  padding: 1.6rem;
  border-radius: 20px;
  background: #fff;
  border: 1px solid var(--line);
  color: var(--brand);
}
.ab-grid h2 {
  margin: 0.8rem 0 0.4rem;
  font-size: 1.4rem;
  color: var(--brand-deep);
}
.ab-grid p {
  line-height: 1.65;
  color: var(--ink-soft);
}
.ab-addr {
  margin: 2rem 0 1.4rem;
  color: var(--ink-soft);
}
</style>
