<script setup>
// Guest-friendly checkout: delivery details, a payment method from admin settings, then the order.
import { useCartStore } from "~/stores/cart";

useSeoMeta({ title: "Checkout | Dressnio", robots: "noindex" });

const cartStore = useCartStore();
const token = useTokenStore();
const config = useRuntimeConfig();
const snackbar = useSnackbar();
const { freeShippingAbove, shippingFee, shippingFeeOutside, paymentMethods } = useStoreSettings();

const items = computed(() => cartStore.items);

const form = reactive({ name: "", mobile_no: "", email: "", address: "", delivery_notes: "", trx_id: "", sender_number: "" });
const region = ref("Dhaka");
const method = ref("");
const errors = ref({});
const submitting = ref(false);
const placed = ref(null);
const copied = ref(false);

// Signed-in customers start with their saved details
if (token.isAuthenticated) {
  $fetch(`${config.public.apiBase}/client/me`, { headers: { Authorization: `Bearer ${token.getToken}`, Accept: "application/json" } })
    .then((res) => {
      const c = res?.data;
      if (!c) return;
      form.name ||= c.name && !/^Customer \d{4}$/.test(c.name) ? c.name : "";
      form.mobile_no ||= c.mobile_no || "";
      form.email ||= c.email || "";
      form.address ||= c.address || "";
      if (c.city) region.value = c.city.toLowerCase().includes("dhaka") && !c.city.toLowerCase().includes("outside") ? "Dhaka" : "Outside Dhaka";
    })
    .catch(() => {});
}

const methodList = computed(() => Object.entries(paymentMethods.value).map(([code, m]) => ({ code, ...m })));
watchEffect(() => {
  if (!method.value || !paymentMethods.value[method.value]) method.value = methodList.value[0]?.code || "cod";
});
const current = computed(() => paymentMethods.value[method.value] || {});
const manual = computed(() => !!current.value.manual);

const subtotal = computed(() => items.value.reduce((t, i) => t + i.price * i.quantity, 0));
const delivery = computed(() => (subtotal.value >= freeShippingAbove.value ? 0 : region.value === "Dhaka" ? shippingFee.value : shippingFeeOutside.value));
const total = computed(() => subtotal.value + delivery.value);
const toFree = computed(() => Math.max(0, freeShippingAbove.value - subtotal.value));

const field = (k) => errors.value[k]?.[0] || "";

const validate = () => {
  const e = {};
  if (!form.name.trim()) e.name = ["Enter your name."];
  if (!/^(\+?88)?01[3-9]\d{8}$/.test(form.mobile_no.replace(/\s|-/g, ""))) e.mobile_no = ["Enter a valid mobile number, like 01712345678."];
  if (form.email && !/^\S+@\S+\.\S+$/.test(form.email)) e.email = ["Enter a valid email address or leave it empty."];
  if (form.address.trim().length < 5) e.address = ["Enter your full delivery address."];
  if (manual.value) {
    if (!/^[A-Za-z0-9]{6,20}$/.test(form.trx_id.trim())) e.trx_id = ["Enter the TrxID from your payment SMS (6 to 20 letters and numbers)."];
    if (!/^(\+?88)?01[3-9]\d{8}$/.test(form.sender_number.replace(/\s|-/g, ""))) e.sender_number = ["Enter the number you paid from."];
  }
  errors.value = e;
  return !Object.keys(e).length;
};

const copyNumber = async () => {
  try {
    await navigator.clipboard.writeText(current.value.number);
    copied.value = true;
    setTimeout(() => (copied.value = false), 2000);
  } catch (e) {
    /* the number is shown on screen anyway */
  }
};

const place = async () => {
  if (submitting.value || !items.value.length) return;
  if (!validate()) {
    await nextTick();
    document.querySelector("[aria-invalid='true']")?.focus();
    return;
  }
  submitting.value = true;
  try {
    const res = await $fetch(`${config.public.apiBase}/client/checkout`, {
      method: "POST",
      headers: { Accept: "application/json", ...(token.isAuthenticated ? { Authorization: `Bearer ${token.getToken}` } : {}) },
      body: {
        name: form.name.trim(),
        mobile_no: form.mobile_no.replace(/\s|-/g, ""),
        email: form.email.trim() || null,
        address: form.address.trim(),
        city: region.value,
        delivery_notes: form.delivery_notes.trim() || null,
        payment_method: method.value,
        trx_id: manual.value ? form.trx_id.trim() : undefined,
        sender_number: manual.value ? form.sender_number.replace(/\s|-/g, "") : undefined,
        items: items.value.map((i) => ({ id: i.id, size: i.size, color: i.color, quantity: i.quantity, image: i.image })),
      },
    });
    const order = res?.data;
    cartStore.checkout();
    if (order?.payment_url) {
      window.location.href = order.payment_url;
      return;
    }
    placed.value = order;
    window.scrollTo({ top: 0, behavior: "smooth" });
  } catch (e) {
    errors.value = e?.data?.errors && typeof e.data.errors === "object" ? e.data.errors : {};
    snackbar.add({ type: "error", text: Object.values(errors.value).flat()[0] || e?.data?.message || "We could not place your order. Check your details and try again.", duration: 7000 });
  } finally {
    submitting.value = false;
  }
};
</script>

<template>
  <div class="co">
    <div class="pm-wrap">
      <!-- Placed -->
      <section v-if="placed" class="done" role="status">
        <Icon name="solar:check-circle-bold" class="w-14 h-14 tick" />
        <h1>Thank you, {{ placed.customer_name.split(" ")[0] }}</h1>
        <p class="lead">Order <b>#{{ placed.order_no }}</b> is placed. We will confirm it by phone or SMS before it ships.</p>
        <p v-if="placed.payment_status === 'verifying'" class="note">We have your {{ placed.payment_method === "bkash" ? "bKash" : "Nagad" }} details and will check the TrxID shortly.</p>
        <p v-else-if="placed.payment_method === 'card'" class="note warn">Your payment page did not open. Open your order to try the payment again.</p>
        <dl class="recap">
          <div><dt>Deliver to</dt><dd>{{ placed.delivery_address }}, {{ placed.delivery_city }}</dd></div>
          <div><dt>Total</dt><dd>{{ formatBDT(placed.total_amount) }}</dd></div>
          <div><dt>Payment</dt><dd>{{ paymentMethods[placed.payment_method]?.label || placed.payment_method }}</dd></div>
        </dl>
        <div class="done-actions">
          <NuxtLink :to="{ path: '/track-order', query: { order_no: placed.order_no, mobile_no: placed.customer_phone } }" class="pm-btn pm-btn-primary">Track this order</NuxtLink>
          <NuxtLink to="/shop" class="pm-btn pm-btn-ghost">Keep shopping</NuxtLink>
        </div>
      </section>

      <!-- Empty -->
      <section v-else-if="!items.length" class="done">
        <GarmentArt kind="dress" color="#8383ED" class="empty-art" />
        <h1>Your bag is empty</h1>
        <p class="lead">Add something you love, then come back to check out.</p>
        <NuxtLink to="/shop" class="pm-btn pm-btn-primary">Start shopping</NuxtLink>
      </section>

      <!-- Form -->
      <form v-else class="grid" novalidate @submit.prevent="place">
        <div class="left">
          <h1>Checkout</h1>
          <p v-if="!token.isAuthenticated" class="hint">Have an account? <NuxtLink :to="{ path: '/login', query: { redirect: '/checkout' } }">Sign in</NuxtLink> to fill this in for you. You can also order as a guest.</p>

          <fieldset>
            <legend>Your details</legend>
            <label class="f">
              <span>Full name</span>
              <input v-model="form.name" type="text" autocomplete="name" :aria-invalid="!!field('name')" />
              <small v-if="field('name')" class="err">{{ field('name') }}</small>
            </label>
            <div class="two">
              <label class="f">
                <span>Mobile number</span>
                <input v-model="form.mobile_no" type="tel" inputmode="tel" autocomplete="tel-national" placeholder="01712345678" :aria-invalid="!!field('mobile_no')" />
                <small v-if="field('mobile_no')" class="err">{{ field('mobile_no') }}</small>
                <small v-else>The courier calls this number.</small>
              </label>
              <label class="f">
                <span>Email (optional)</span>
                <input v-model="form.email" type="email" autocomplete="email" :aria-invalid="!!field('email')" />
                <small v-if="field('email')" class="err">{{ field('email') }}</small>
              </label>
            </div>
          </fieldset>

          <fieldset>
            <legend>Delivery</legend>
            <div class="radios" role="radiogroup" aria-label="Delivery area">
              <label :class="{ on: region === 'Dhaka' }">
                <input v-model="region" type="radio" value="Dhaka" />
                <span><b>Inside Dhaka</b><small>{{ STORE_POLICY.deliveryInsideCity }} · {{ delivery === 0 && subtotal >= freeShippingAbove ? "Free" : formatBDT(shippingFee) }}</small></span>
              </label>
              <label :class="{ on: region === 'Outside Dhaka' }">
                <input v-model="region" type="radio" value="Outside Dhaka" />
                <span><b>Outside Dhaka</b><small>{{ STORE_POLICY.deliveryOutsideCity }} · {{ subtotal >= freeShippingAbove ? "Free" : formatBDT(shippingFeeOutside) }}</small></span>
              </label>
            </div>
            <label class="f">
              <span>Full address</span>
              <textarea v-model="form.address" rows="3" autocomplete="street-address" placeholder="House, road, area, thana and district" :aria-invalid="!!field('address')"></textarea>
              <small v-if="field('address')" class="err">{{ field('address') }}</small>
            </label>
            <label class="f">
              <span>Note for the courier (optional)</span>
              <input v-model="form.delivery_notes" type="text" maxlength="500" placeholder="Landmark, preferred time" />
            </label>
          </fieldset>

          <fieldset>
            <legend>Payment</legend>
            <div class="methods" role="radiogroup" aria-label="Payment method">
              <label v-for="m in methodList" :key="m.code" :class="{ on: method === m.code }">
                <input v-model="method" type="radio" :value="m.code" />
                <Icon :name="m.code === 'cod' ? 'solar:wallet-money-bold-duotone' : m.code === 'card' ? 'solar:card-bold-duotone' : 'solar:smartphone-2-bold-duotone'" class="w-6 h-6" />
                <span><b>{{ m.label }}</b><small>{{ m.code === "cod" ? "Pay when it arrives" : m.code === "card" ? "Pay securely online" : "Send money, then enter the TrxID" }}</small></span>
              </label>
            </div>

            <div v-if="manual" class="manual">
              <ol>
                <li>Open {{ current.label }} and choose <b>Send Money</b>.</li>
                <li>
                  Send <b>{{ formatBDT(total) }}</b> to
                  <button type="button" class="num" @click="copyNumber">{{ current.number }} <Icon :name="copied ? 'solar:check-circle-bold' : 'solar:copy-linear'" class="w-4 h-4" /></button>
                  <small>{{ copied ? "Copied" : "Personal number. Tap to copy." }}</small>
                </li>
                <li>Enter the TrxID from the confirmation SMS below.</li>
              </ol>
              <div class="two">
                <label class="f">
                  <span>TrxID</span>
                  <input v-model="form.trx_id" type="text" autocomplete="off" autocapitalize="characters" placeholder="e.g. 9H7K2LMN4P" :aria-invalid="!!field('trx_id')" />
                  <small v-if="field('trx_id')" class="err">{{ field('trx_id') }}</small>
                </label>
                <label class="f">
                  <span>Number you paid from</span>
                  <input v-model="form.sender_number" type="tel" inputmode="tel" placeholder="01712345678" :aria-invalid="!!field('sender_number')" />
                  <small v-if="field('sender_number')" class="err">{{ field('sender_number') }}</small>
                </label>
              </div>
            </div>
            <p v-else-if="method === 'card'" class="manual-note">You will pay on SSLCommerz's secure page with a card, bKash, Nagad or net banking. Your order is held while you pay.</p>
            <p v-else class="manual-note">Pay the courier {{ formatBDT(total) }} in cash when your parcel arrives.</p>
          </fieldset>
        </div>

        <aside class="right">
          <div class="sum">
            <h2>Order summary</h2>
            <ul class="lines">
              <li v-for="i in items" :key="`${i.id}-${i.size}-${i.color}`">
                <span class="thumb"><ArtImage :src="i.image" :alt="i.name" :kind="garmentKindFor(i.name)" :color="i.color || toneHexAt(i.id)" /></span>
                <span class="meta">
                  <b>{{ i.name }}</b>
                  <small>{{ [i.size && `Size ${i.size}`, `Qty ${i.quantity}`].filter(Boolean).join(" · ") }}</small>
                </span>
                <span class="amt">{{ formatBDT(i.price * i.quantity) }}</span>
              </li>
            </ul>
            <dl>
              <div><dt>Subtotal</dt><dd>{{ formatBDT(subtotal) }}</dd></div>
              <div><dt>Delivery</dt><dd>{{ delivery ? formatBDT(delivery) : "Free" }}</dd></div>
              <div class="total"><dt>Total</dt><dd>{{ formatBDT(total) }}</dd></div>
            </dl>
            <p v-if="toFree > 0" class="free">Add {{ formatBDT(toFree) }} more for free delivery.</p>
            <button type="submit" class="pm-btn pm-btn-primary go" :disabled="submitting">
              {{ submitting ? "Placing your order" : method === "card" ? `Continue to payment, ${formatBDT(total)}` : `Place order, ${formatBDT(total)}` }}
            </button>
            <p class="fine">{{ STORE_POLICY.exchangeWindow }} size exchange on every order.</p>
          </div>
        </aside>
      </form>
    </div>
  </div>
</template>

<style scoped>
.co {
  background: var(--paper);
  padding: 2rem 0 4rem;
}
h1 {
  font-size: clamp(2rem, 4vw, 3rem);
  font-weight: 600;
  color: var(--brand-deep);
}
.hint {
  margin: 0.6rem 0 1.6rem;
  color: var(--ink-soft);
}
.hint a {
  color: var(--brand);
  font-weight: 600;
  text-decoration: underline;
}
.grid {
  display: grid;
  gap: 2rem;
}
@media (min-width: 960px) {
  .grid {
    grid-template-columns: 1.4fr 1fr;
    gap: 3.5rem;
    align-items: start;
  }
  .right {
    position: sticky;
    top: 6rem;
  }
}
fieldset {
  display: grid;
  gap: 1rem;
  margin: 0 0 1.8rem;
  padding: 0;
  border: 0;
}
legend {
  margin-bottom: 0.9rem;
  font-family: var(--font-display);
  font-size: 1.5rem;
  color: var(--brand-deep);
}
.two {
  display: grid;
  gap: 1rem;
}
@media (min-width: 640px) {
  .two {
    grid-template-columns: 1fr 1fr;
  }
}
.f span {
  display: block;
  margin-bottom: 0.4rem;
  font-size: 0.9rem;
  font-weight: 600;
}
.f input,
.f textarea {
  width: 100%;
  padding: 0.85rem 1rem;
  border: 1.5px solid var(--line);
  border-radius: 12px;
  background: #fff;
  font-size: 1rem;
}
.f input:focus,
.f textarea:focus {
  border-color: var(--brand);
  box-shadow: 0 0 0 4px rgba(77, 77, 219, 0.14);
}
.f [aria-invalid="true"] {
  border-color: #d63a6a;
}
.f small {
  display: block;
  margin-top: 0.35rem;
  font-size: 0.84rem;
  color: var(--ink-soft);
}
.f small.err {
  color: #c0285a;
}
.radios,
.methods {
  display: grid;
  gap: 0.7rem;
}
@media (min-width: 640px) {
  .radios {
    grid-template-columns: 1fr 1fr;
  }
}
.radios label,
.methods label {
  position: relative;
  display: flex;
  align-items: center;
  gap: 0.8rem;
  padding: 1rem;
  border: 1.5px solid var(--line);
  border-radius: 14px;
  background: #fff;
  cursor: pointer;
  transition: border-color 0.15s, background-color 0.15s;
}
.radios label.on,
.methods label.on {
  border-color: var(--brand);
  background: var(--brand-tint);
}
.radios input,
.methods input {
  position: absolute;
  opacity: 0;
}
.radios input:focus-visible + span,
.methods input:focus-visible ~ span {
  outline: 2px solid var(--brand);
  outline-offset: 4px;
  border-radius: 6px;
}
.radios b,
.methods b {
  display: block;
}
.radios small,
.methods small {
  color: var(--ink-soft);
  font-size: 0.84rem;
}
.methods svg {
  flex-shrink: 0;
  color: var(--brand);
}
.manual {
  display: grid;
  gap: 1rem;
  padding: 1.2rem;
  border-radius: 14px;
  background: #fff;
  border: 1px dashed #b9b9ee;
}
.manual ol {
  display: grid;
  gap: 0.8rem;
  padding-left: 1.2rem;
  line-height: 1.55;
}
.num {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.2rem 0.7rem;
  border-radius: 999px;
  background: var(--brand-tint);
  font-weight: 700;
  color: var(--brand);
}
.manual li small {
  display: block;
  color: var(--ink-soft);
}
.manual-note {
  padding: 1rem;
  border-radius: 12px;
  background: #fff;
  border: 1px solid var(--line);
  color: var(--ink-soft);
  line-height: 1.6;
}
.sum {
  padding: 1.5rem;
  border-radius: 22px;
  background: #fff;
  border: 1px solid var(--line);
  box-shadow: 0 30px 60px -40px rgba(21, 21, 82, 0.4);
}
.sum h2 {
  font-size: 1.5rem;
  color: var(--brand-deep);
}
.lines {
  list-style: none;
  display: grid;
  gap: 0.9rem;
  margin: 1.2rem 0;
  max-height: 20rem;
  overflow-y: auto;
}
.lines li {
  display: flex;
  align-items: center;
  gap: 0.8rem;
}
.thumb {
  flex-shrink: 0;
  width: 3.4rem;
  height: 4.4rem;
  border-radius: 10px;
  overflow: hidden;
  background: var(--brand-tint);
}
.meta {
  flex: 1;
  min-width: 0;
}
.meta b {
  display: block;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.meta small {
  color: var(--ink-soft);
}
.amt {
  font-weight: 600;
}
.sum dl {
  display: grid;
  gap: 0.5rem;
  padding-top: 1rem;
  border-top: 1px solid var(--line);
}
.sum dl div {
  display: flex;
  justify-content: space-between;
}
.sum .total {
  margin-top: 0.4rem;
  padding-top: 0.8rem;
  border-top: 1px solid var(--line);
  font-size: 1.3rem;
  font-weight: 700;
  color: var(--brand);
}
.free {
  margin-top: 0.8rem;
  font-size: 0.9rem;
  color: var(--ink-soft);
}
.go {
  width: 100%;
  margin-top: 1.2rem;
  min-height: 3.4rem;
}
.go:disabled {
  opacity: 0.6;
  cursor: wait;
}
.fine {
  margin-top: 0.8rem;
  text-align: center;
  font-size: 0.84rem;
  color: var(--ink-soft);
}
.done {
  display: flex;
  flex-direction: column;
  align-items: center;
  max-width: 36rem;
  margin: 2rem auto;
  text-align: center;
}
.tick {
  color: #0f8a5f;
}
.done h1 {
  margin: 0.8rem 0;
}
.lead {
  color: var(--ink-soft);
  line-height: 1.6;
}
.note {
  margin-top: 1rem;
  padding: 0.8rem 1rem;
  border-radius: 12px;
  background: var(--brand-tint);
  color: var(--brand-deep);
}
.note.warn {
  background: #fff4d6;
  color: #7a5200;
}
.recap {
  display: grid;
  gap: 0.7rem;
  width: 100%;
  margin: 1.6rem 0;
  padding: 1.2rem;
  border-radius: 16px;
  background: #fff;
  border: 1px solid var(--line);
  text-align: left;
}
.recap div {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
}
.recap dt {
  color: var(--ink-soft);
}
.recap dd {
  text-align: right;
  font-weight: 600;
}
.done-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.7rem;
  justify-content: center;
}
.empty-art {
  width: 150px;
  height: 200px;
}
</style>
