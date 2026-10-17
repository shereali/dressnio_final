<script setup>
// Where SSLCommerz sends the customer back: paid, failed or cancelled.
useSeoMeta({ title: "Payment result | Dressnio", robots: "noindex" });

const route = useRoute();
const config = useRuntimeConfig();
const token = useTokenStore();
const snackbar = useSnackbar();

const order = computed(() => String(route.query.order || ""));
const status = computed(() => String(route.query.status || "failed"));
const phone = ref("");
const retrying = ref(false);
const phoneError = ref("");

const copy = computed(() => {
  if (status.value === "paid")
    return { icon: "solar:check-circle-bold", tone: "ok", title: "Payment received", text: "Thank you. Your order is confirmed for packing and we will update you by SMS." };
  if (status.value === "cancelled")
    return { icon: "solar:close-circle-bold", tone: "warn", title: "Payment cancelled", text: "No money was taken. Your order is saved, so you can pay now or choose cash on delivery by contacting us." };
  return { icon: "solar:danger-circle-bold", tone: "bad", title: "Payment did not go through", text: "No money was taken, or the bank has not confirmed it yet. Your order is saved. Try again or contact us if money left your account." };
});

const retry = async () => {
  phoneError.value = "";
  if (!token.isAuthenticated && !/^(\+?88)?01[3-9]\d{8}$/.test(phone.value.replace(/\s|-/g, ""))) {
    phoneError.value = "Enter the phone number you ordered with.";
    return;
  }
  retrying.value = true;
  try {
    const res = await $fetch(`${config.public.apiBase}/client/my-orders/${order.value}/pay`, {
      method: "POST",
      headers: { Accept: "application/json", ...(token.isAuthenticated ? { Authorization: `Bearer ${token.getToken}` } : {}) },
      body: { mobile_no: phone.value.replace(/\s|-/g, "") },
    });
    window.location.href = res.data.payment_url;
  } catch (e) {
    snackbar.add({ type: "error", text: e?.data?.message || "We could not open the payment page. Try again.", duration: 6000 });
    retrying.value = false;
  }
};
</script>

<template>
  <div class="rs">
    <div class="box" role="status">
      <Icon :name="copy.icon" class="w-14 h-14" :class="copy.tone" />
      <h1>{{ copy.title }}</h1>
      <p>{{ copy.text }}</p>
      <p v-if="order" class="ord">Order <b>#{{ order }}</b></p>

      <div v-if="status !== 'paid' && order" class="retry">
        <label v-if="!token.isAuthenticated">
          <span>Phone number on the order</span>
          <input v-model="phone" type="tel" inputmode="tel" placeholder="01712345678" :aria-invalid="!!phoneError" />
          <small v-if="phoneError">{{ phoneError }}</small>
        </label>
        <button class="pm-btn pm-btn-primary" :disabled="retrying" @click="retry">{{ retrying ? "Opening payment page" : "Try payment again" }}</button>
      </div>

      <div class="links">
        <NuxtLink v-if="order" :to="{ path: '/track-order', query: { order_no: order } }" class="pm-btn pm-btn-ghost">Track this order</NuxtLink>
        <NuxtLink to="/shop" class="pm-btn pm-btn-ghost">Keep shopping</NuxtLink>
        <NuxtLink v-if="status !== 'paid'" to="/contact" class="pm-btn pm-btn-ghost">Contact us</NuxtLink>
      </div>
    </div>
  </div>
</template>

<style scoped>
.rs {
  display: grid;
  place-items: center;
  min-height: 60vh;
  padding: 3rem 1rem;
  background: var(--paper);
}
.box {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.9rem;
  max-width: 34rem;
  text-align: center;
}
.box h1 {
  font-size: clamp(1.9rem, 4vw, 2.6rem);
  color: var(--brand-deep);
}
.box p {
  line-height: 1.65;
  color: var(--ink-soft);
}
.ok {
  color: #0f8a5f;
}
.warn {
  color: #b86e00;
}
.bad {
  color: #c0285a;
}
.ord b {
  color: var(--ink);
}
.retry {
  display: grid;
  gap: 0.8rem;
  width: 100%;
  max-width: 22rem;
  margin-top: 0.6rem;
}
.retry label span {
  display: block;
  margin-bottom: 0.4rem;
  font-weight: 600;
  text-align: left;
}
.retry input {
  width: 100%;
  height: 3rem;
  padding: 0 1rem;
  border: 1.5px solid var(--line);
  border-radius: 12px;
  background: #fff;
}
.retry small {
  display: block;
  margin-top: 0.3rem;
  text-align: left;
  color: #c0285a;
}
.links {
  display: flex;
  flex-wrap: wrap;
  gap: 0.6rem;
  justify-content: center;
  margin-top: 1rem;
}
</style>
