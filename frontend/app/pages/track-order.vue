<script setup>
// Track a parcel with the order number and the phone number it was placed with.
useSeoMeta({ title: "Track your order | Dressnio", robots: "noindex" });

const route = useRoute();
const config = useRuntimeConfig();
const token = useTokenStore();
const snackbar = useSnackbar();

const orderNo = ref(String(route.query.order_no || ""));
const phone = ref(String(route.query.mobile_no || ""));
const order = ref(null);
const loading = ref(false);
const message = ref("");
const busy = ref("");

const authHeaders = () => (token.isAuthenticated ? { Authorization: `Bearer ${token.getToken}` } : {});
const body = () => ({ mobile_no: phone.value.replace(/\s|-/g, "") });

const steps = [
  { key: "pending", title: "Order placed", icon: "solar:cart-check-bold" },
  { key: "confirmed", title: "Confirmed", icon: "solar:verified-check-bold" },
  { key: "processing", title: "Being packed", icon: "solar:box-bold" },
  { key: "shipped", title: "With the courier", icon: "solar:delivery-bold" },
  { key: "delivered", title: "Delivered", icon: "solar:home-smile-bold" },
];
const rank = { pending: 0, confirmed: 1, processing: 2, shipped: 3, delivered: 4 };
const stopped = computed(() => ["cancelled", "returned"].includes(order.value?.order_status));
const reached = (key) => !stopped.value && (rank[order.value?.order_status] ?? 0) >= rank[key];
const noteFor = (key) => [...(order.value?.delivery_timeline || [])].reverse().find((t) => t.status === key)?.note || "";

const find = async () => {
  message.value = "";
  if (!orderNo.value.trim()) {
    message.value = "Enter your order number.";
    return;
  }
  if (!token.isAuthenticated && !/^(\+?88)?01[3-9]\d{8}$/.test(phone.value.replace(/\s|-/g, ""))) {
    message.value = "Enter the phone number you ordered with.";
    return;
  }
  loading.value = true;
  try {
    const res = await $fetch(`${config.public.apiBase}/client/my-orders/${encodeURIComponent(orderNo.value.trim())}`, {
      params: body(),
      headers: { Accept: "application/json", ...authHeaders() },
    });
    order.value = res?.data || null;
  } catch (e) {
    order.value = null;
    message.value = e?.data?.message || "We could not find that order. Check the order number and phone number.";
  } finally {
    loading.value = false;
  }
};

const post = async (path, key, okText) => {
  busy.value = key;
  try {
    const res = await $fetch(`${config.public.apiBase}/client/my-orders/${order.value.order_no}/${path}`, { method: "POST", body: body(), headers: { Accept: "application/json", ...authHeaders() } });
    if (res?.data?.payment_url) {
      window.location.href = res.data.payment_url;
      return;
    }
    snackbar.add({ type: "success", text: okText });
    await find();
  } catch (e) {
    snackbar.add({ type: "error", text: e?.data?.message || "That did not work. Try again.", duration: 6000 });
  } finally {
    busy.value = "";
  }
};

const cancel = () => {
  if (confirm(`Cancel order #${order.value.order_no}?`)) post("cancel", "cancel", "Order cancelled");
};
const payAgain = () => post("pay", "pay", "Payment started");

const canCancel = computed(() => order.value?.order_status === "pending" && !["paid", "verifying"].includes(order.value?.payment_status));
const canPay = computed(() => order.value?.order_status === "pending" && order.value?.payment_status === "unpaid" && order.value?.payment_method === "card");
const date = (d) => new Date(d).toLocaleString("en-GB", { day: "numeric", month: "short", year: "numeric", hour: "2-digit", minute: "2-digit" });
const payText = { unpaid: "Not paid yet", verifying: "We are checking your payment", paid: "Paid", refunded: "Refunded", failed: "Failed" };

onMounted(() => {
  if (orderNo.value && (phone.value || token.isAuthenticated)) find();
});
</script>

<template>
  <div class="tk">
    <div class="pm-wrap narrow">
      <h1>Track your order</h1>
      <p class="sub">Use the order number from your confirmation and the phone number you ordered with.</p>

      <form class="finder" novalidate @submit.prevent="find">
        <label>
          <span>Order number</span>
          <input v-model="orderNo" type="text" autocomplete="off" placeholder="DN-260824-AB12" />
        </label>
        <label v-if="!token.isAuthenticated">
          <span>Phone number</span>
          <input v-model="phone" type="tel" inputmode="tel" autocomplete="tel-national" placeholder="01712345678" />
        </label>
        <button type="submit" class="pm-btn pm-btn-primary" :disabled="loading">{{ loading ? "Looking" : "Track order" }}</button>
      </form>
      <p v-if="message" class="msg" role="alert">{{ message }}</p>

      <section v-if="order" class="result">
        <header>
          <div>
            <h2>#{{ order.order_no }}</h2>
            <p class="sub">Placed {{ date(order.created_at) }}</p>
          </div>
          <span class="chip" :class="order.order_status">{{ order.order_status }}</span>
        </header>

        <p v-if="stopped" class="stopped">This order was {{ order.order_status }}. {{ noteFor(order.order_status) }}</p>

        <ol v-else class="steps">
          <li v-for="s in steps" :key="s.key" :class="{ on: reached(s.key) }">
            <span class="dot"><Icon :name="s.icon" class="w-5 h-5" /></span>
            <span>
              <b>{{ s.title }}</b>
              <small>{{ reached(s.key) ? noteFor(s.key) : "" }}</small>
            </span>
          </li>
        </ol>

        <p v-if="order.steadfast_tracking_code" class="courier">
          SteadFast tracking code <b>{{ order.steadfast_tracking_code }}</b>
          <a v-if="order.tracking_url" :href="order.tracking_url" target="_blank" rel="noopener">Track with the courier</a>
        </p>

        <div class="box">
          <h3>Items</h3>
          <ul>
            <li v-for="i in order.items" :key="i.id">
              <span>{{ i.quantity }} x {{ i.product_name }} <small v-if="i.size_name">Size {{ i.size_name }}</small></span>
              <b>{{ formatBDT(i.total_price) }}</b>
            </li>
          </ul>
          <p class="tot"><span>Total</span><b>{{ formatBDT(order.total_amount) }}</b></p>
          <p class="pay">Payment: {{ payText[order.payment_status] || order.payment_status }}</p>
        </div>

        <div class="acts">
          <button v-if="canPay" class="pm-btn pm-btn-primary" :disabled="!!busy" @click="payAgain">Pay now</button>
          <button v-if="canCancel" class="pm-btn pm-btn-ghost" :disabled="!!busy" @click="cancel">Cancel order</button>
          <NuxtLink to="/contact" class="pm-btn pm-btn-ghost">Need help?</NuxtLink>
        </div>
      </section>
    </div>
  </div>
</template>

<style scoped>
.tk {
  background: var(--paper);
  padding: 2.5rem 0 4rem;
  min-height: 60vh;
}
.narrow {
  max-width: 44rem;
}
h1 {
  font-size: clamp(2rem, 4vw, 3rem);
  font-weight: 600;
  color: var(--brand-deep);
}
.sub {
  margin-top: 0.5rem;
  color: var(--ink-soft);
}
.finder {
  display: grid;
  gap: 0.9rem;
  margin: 1.6rem 0 0.6rem;
}
@media (min-width: 640px) {
  .finder {
    grid-template-columns: 1fr 1fr auto;
    align-items: end;
  }
}
.finder span {
  display: block;
  margin-bottom: 0.4rem;
  font-size: 0.9rem;
  font-weight: 600;
}
.finder input {
  width: 100%;
  height: 3.2rem;
  padding: 0 1rem;
  border: 1.5px solid var(--line);
  border-radius: 12px;
  background: #fff;
}
.finder input:focus {
  border-color: var(--brand);
  box-shadow: 0 0 0 4px rgba(77, 77, 219, 0.14);
}
.finder .pm-btn {
  min-height: 3.2rem;
}
.msg {
  color: #c0285a;
}
.result {
  margin-top: 2rem;
  padding: 1.6rem;
  border-radius: 22px;
  background: #fff;
  border: 1px solid var(--line);
}
.result header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1rem;
}
.result h2 {
  font-size: 1.7rem;
  color: var(--brand);
}
.chip {
  padding: 0.3rem 0.9rem;
  border-radius: 999px;
  background: var(--brand-tint);
  color: var(--brand);
  font-weight: 600;
  font-size: 0.86rem;
}
.chip.delivered {
  background: #dff5ea;
  color: #0f7a53;
}
.chip.cancelled,
.chip.returned {
  background: #fde7ee;
  color: #b3204f;
}
.steps {
  list-style: none;
  display: grid;
  gap: 0;
  margin: 1.8rem 0 1.2rem;
}
.steps li {
  position: relative;
  display: flex;
  gap: 1rem;
  padding-bottom: 1.4rem;
  color: #9a9ab0;
}
.steps li:not(:last-child)::before {
  content: "";
  position: absolute;
  left: 1.2rem;
  top: 2.5rem;
  bottom: 0.2rem;
  width: 2px;
  background: var(--line);
}
.steps li.on {
  color: var(--ink);
}
.steps li.on:not(:last-child)::before {
  background: var(--brand);
}
.dot {
  flex-shrink: 0;
  display: grid;
  place-items: center;
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 50%;
  background: #eeeef6;
}
.steps li.on .dot {
  background: var(--brand);
  color: #fff;
}
.steps b {
  display: block;
  padding-top: 0.5rem;
}
.steps small {
  display: block;
  margin-top: 0.15rem;
  color: var(--ink-soft);
  font-size: 0.86rem;
}
.stopped {
  margin: 1.2rem 0;
  padding: 1rem;
  border-radius: 12px;
  background: #fde7ee;
  color: #8a1a40;
}
.courier {
  margin-bottom: 1.2rem;
  color: var(--ink-soft);
}
.courier a {
  margin-left: 0.6rem;
  color: var(--brand);
  font-weight: 600;
  text-decoration: underline;
}
.box {
  padding: 1.2rem;
  border-radius: 16px;
  background: var(--paper);
}
.box h3 {
  font-family: var(--font-body);
  font-size: 1rem;
  font-weight: 700;
  margin-bottom: 0.7rem;
}
.box ul {
  list-style: none;
  display: grid;
  gap: 0.5rem;
}
.box li {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
}
.box small {
  color: var(--ink-soft);
}
.tot {
  display: flex;
  justify-content: space-between;
  margin-top: 0.9rem;
  padding-top: 0.8rem;
  border-top: 1px solid var(--line);
  font-size: 1.15rem;
}
.pay {
  margin-top: 0.4rem;
  color: var(--ink-soft);
}
.acts {
  display: flex;
  flex-wrap: wrap;
  gap: 0.6rem;
  margin-top: 1.2rem;
}
</style>
