<script setup>
// Plain-language store policies. Delivery and exchange terms come from the shared store policy.
const { freeShippingAbove, shippingFee, shippingFeeOutside, email } = useStoreSettings();
useSeoMeta({ title: "Terms and conditions | Dressnio" });

const sections = computed(() => [
  {
    id: "orders",
    title: "Orders and payment",
    body: [
      "You can order as a guest or with an account. We confirm orders by phone or SMS before dispatch.",
      "Pay by cash on delivery, bKash, Nagad or card. Prices are in Bangladeshi taka (৳) and include any listed discount.",
    ],
  },
  {
    id: "delivery",
    title: "Delivery",
    body: [
      `Delivery takes ${STORE_POLICY.deliveryInsideCity} inside your city and ${STORE_POLICY.deliveryOutsideCity} to other areas.`,
      `Delivery costs ${formatBDT(shippingFee.value)} inside Dhaka and ${formatBDT(shippingFeeOutside.value)} outside Dhaka. Orders over ${formatBDT(freeShippingAbove.value)} ship free.`,
    ],
  },
  {
    id: "exchange",
    title: "Exchange and returns",
    body: [
      `If a size does not fit, tell us within ${STORE_POLICY.exchangeWindow} of delivery and we will swap it, subject to stock.`,
      "Items must be unworn, unwashed and in their original packaging. If you receive a damaged or wrong item, contact us as soon as you can and we will put it right.",
    ],
  },
  {
    id: "privacy",
    title: "Your information",
    body: [
      "We use your mobile number and address only to deliver your order and to sign you in. We send marketing messages only if you subscribe, and you can unsubscribe at any time.",
      "Login codes are sent by SMS and expire after a few minutes. Never share a code with anyone.",
    ],
  },
]);
</script>

<template>
  <div>
    <PageHero title="Terms and conditions" subtitle="The short version of how ordering, delivery and exchanges work." :crumbs="[{ label: 'Terms and conditions' }]" kind="dress" />
    <section class="pm-section tc">
      <div class="pm-wrap tc-grid">
        <nav class="tc-nav" aria-label="On this page">
          <a v-for="s in sections" :key="s.id" :href="`#${s.id}`">{{ s.title }}</a>
        </nav>
        <div class="tc-body">
          <article v-for="s in sections" :id="s.id" :key="s.id">
            <h2>{{ s.title }}</h2>
            <p v-for="line in s.body" :key="line">{{ line }}</p>
          </article>
          <p v-if="email" class="tc-q">Questions? Write to <a :href="`mailto:${email}`">{{ email }}</a>.</p>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
.tc {
  background: var(--paper);
  padding-top: 2.5rem;
}
.tc-grid {
  display: grid;
  gap: 2rem;
}
@media (min-width: 900px) {
  .tc-grid {
    grid-template-columns: 14rem 1fr;
    gap: 4rem;
  }
  .tc-nav {
    position: sticky;
    top: 6rem;
    align-self: start;
  }
}
.tc-nav {
  display: flex;
  flex-wrap: wrap;
  gap: 0.6rem 1.2rem;
}
@media (min-width: 900px) {
  .tc-nav {
    flex-direction: column;
  }
}
.tc-nav a {
  font-weight: 500;
  color: var(--ink-soft);
}
.tc-nav a:hover {
  color: var(--brand);
}
.tc-body {
  max-width: 68ch;
}
article {
  margin-bottom: 2.4rem;
  scroll-margin-top: 6rem;
}
article h2 {
  font-size: 1.7rem;
  color: var(--brand-deep);
}
article p {
  margin-top: 0.8rem;
  line-height: 1.8;
  color: var(--ink-soft);
}
.tc-q a {
  color: var(--brand);
  text-decoration: underline;
}
</style>
