<script setup>
// One order: move it through its statuses, check payments, book the courier, print the invoice or label.
definePageMeta({ middleware: ["auth"] });

const api = useAdminApi();
const route = useRoute();
const snackbar = useSnackbar();
const { phone: storePhone, address: storeAddress } = useStoreSettings();

const order = ref(null);
const loading = ref(true);
const busy = ref("");
const printMode = ref("");

const load = async () => {
  try {
    const res = await api(`/orders/${route.params.id}`);
    order.value = res?.data || null;
  } catch (e) {
    order.value = null;
  } finally {
    loading.value = false;
  }
};
onMounted(load);

const NEXT = {
  pending: [["confirmed", "Confirm order", "primary"], ["cancelled", "Cancel order", "danger"]],
  confirmed: [["processing", "Start packing", "primary"], ["cancelled", "Cancel order", "danger"]],
  processing: [["shipped", "Mark as shipped", "primary"], ["cancelled", "Cancel order", "danger"]],
  shipped: [["delivered", "Mark as delivered", "primary"], ["returned", "Mark as returned", "danger"]],
  delivered: [["returned", "Mark as returned", "danger"]],
};
const actions = computed(() => NEXT[order.value?.order_status] || []);

const run = async (key, fn, ok) => {
  busy.value = key;
  try {
    const res = await fn();
    if (res?.data?.id) order.value = { ...order.value, ...res.data };
    await load();
    snackbar.add({ type: "success", text: res?.message || ok });
  } catch (e) {
    snackbar.add({ type: "error", text: apiErrorText(e), duration: 6000 });
  } finally {
    busy.value = "";
  }
};

const setStatus = (status, label) => {
  if (["cancelled", "returned"].includes(status) && !confirm(`${label}? Stock goes back on the shelf.`)) return;
  run(`s-${status}`, () => api(`/orders/${order.value.id}`, { method: "PUT", body: { order_status: status } }), "Order updated");
};
const markPaid = () => run("paid", () => api(`/orders/${order.value.id}`, { method: "PUT", body: { payment_status: "paid" } }), "Marked as paid");
const review = (payment, action) =>
  run(`p-${payment.id}`, () => api(`/orders/${order.value.id}/payments/${payment.id}/review`, { method: "POST", body: { action } }), "Payment updated");
const dispatch = () => run("dispatch", () => api(`/orders/${order.value.id}/steadfast/dispatch`, { method: "POST" }), "Booked with SteadFast");
const sync = () => run("sync", () => api(`/orders/${order.value.id}/steadfast/sync`, { method: "POST" }), "Courier status refreshed");

const dispatchBlock = computed(() => {
  const o = order.value;
  if (!o) return "";
  if (o.steadfast_consignment_id) return "";
  if (!["confirmed", "processing"].includes(o.order_status)) return "Confirm the order first, then book the courier.";
  if (o.payment_method !== "cod" && o.payment_status !== "paid") return "Verify the payment before booking the courier.";
  return "";
});

const date = (d) => new Date(d).toLocaleString("en-GB", { day: "numeric", month: "short", year: "numeric", hour: "2-digit", minute: "2-digit" });
const payLabel = { cod: "Cash on delivery", bkash: "bKash", nagad: "Nagad", card: "Card or mobile banking" };
const waLink = computed(() => {
  const d = (order.value?.customer_phone || "").replace(/\D/g, "");
  return d ? `https://wa.me/880${d.replace(/^0/, "")}?text=${encodeURIComponent(`Hi ${order.value.customer_name}, this is Dressnio about order ${order.value.order_no}.`)}` : "";
});
const toCollect = computed(() => (order.value?.payment_status === "paid" ? 0 : order.value?.total_amount || 0));

const print = async (mode) => {
  printMode.value = mode;
  await nextTick();
  window.print();
  printMode.value = "";
};

useSeoMeta({ title: () => (order.value ? `Order ${order.value.order_no} | Dressnio admin` : "Order | Dressnio admin"), robots: "noindex" });
</script>

<template>
  <div class="ad-page od" style="max-width: 1200px">
    <NuxtLink to="/admin/order" class="back">Back to orders</NuxtLink>

    <p v-if="loading" class="ad-muted">Loading order...</p>
    <p v-else-if="!order" class="ad-muted">We could not find that order.</p>

    <template v-else>
      <header class="head">
        <div>
          <h1>Order #{{ order.order_no }}</h1>
          <p class="ad-muted">Placed {{ date(order.created_at) }}</p>
          <div class="badges">
            <span class="ad-badge" :class="order.order_status">{{ order.order_status }}</span>
            <span class="ad-badge" :class="order.payment_status">{{ order.payment_status === "verifying" ? "check TrxID" : order.payment_status }}</span>
          </div>
        </div>
        <div class="actions">
          <button v-for="[s, label, kind] in actions" :key="s" class="ad-btn" :class="kind" :disabled="!!busy" @click="setStatus(s, label)">{{ label }}</button>
          <button class="ad-btn" @click="print('invoice')">Print invoice</button>
          <button class="ad-btn" @click="print('label')">Print label</button>
        </div>
      </header>

      <div class="cols">
        <div class="main">
          <section class="ad-card">
            <h2>Items</h2>
            <table class="ad-table items">
              <tbody>
                <tr v-for="i in order.items" :key="i.id">
                  <td>
                    <b>{{ i.product_name }}</b>
                    <small class="ad-muted">
                      <template v-if="i.size_name">Size {{ i.size_name }}</template>
                      <span v-if="i.color_code" class="dot" :style="{ background: i.color_code }" :title="i.color_code"></span>
                    </small>
                  </td>
                  <td class="num">{{ formatBDT(i.unit_price) }} x {{ i.quantity }}</td>
                  <td class="num"><b>{{ formatBDT(i.total_price) }}</b></td>
                </tr>
              </tbody>
            </table>
            <dl class="sum">
              <div><dt>Subtotal</dt><dd>{{ formatBDT(order.subtotal) }}</dd></div>
              <div><dt>Delivery</dt><dd>{{ order.shipping_cost ? formatBDT(order.shipping_cost) : "Free" }}</dd></div>
              <div v-if="order.discount_amount"><dt>Discount</dt><dd>-{{ formatBDT(order.discount_amount) }}</dd></div>
              <div class="total"><dt>Total</dt><dd>{{ formatBDT(order.total_amount) }}</dd></div>
            </dl>
          </section>

          <section class="ad-card">
            <h2>Timeline</h2>
            <ol class="timeline">
              <li v-for="(t, i) in [...(order.delivery_timeline || [])].reverse()" :key="i">
                <span class="ad-badge" :class="t.status">{{ t.status }}</span>
                <p>{{ t.note }}</p>
                <small class="ad-muted">{{ date(t.time) }}</small>
              </li>
            </ol>
          </section>
        </div>

        <aside class="side">
          <section class="ad-card">
            <h2>Customer</h2>
            <p><b>{{ order.customer_name }}</b></p>
            <p><a :href="`tel:${order.customer_phone}`" class="link">{{ order.customer_phone }}</a> <a v-if="waLink" :href="waLink" target="_blank" rel="noopener" class="link">WhatsApp</a></p>
            <p v-if="order.customer_email" class="ad-muted">{{ order.customer_email }}</p>
            <p class="addr">{{ order.delivery_address }}<br />{{ order.delivery_city }}</p>
            <p v-if="order.delivery_notes" class="ad-muted">Note: {{ order.delivery_notes }}</p>
          </section>

          <section class="ad-card">
            <h2>Payment</h2>
            <p>{{ payLabel[order.payment_method] || order.payment_method }}: <span class="ad-badge" :class="order.payment_status">{{ order.payment_status }}</span></p>

            <div v-for="p in order.payments" :key="p.id" class="pay">
              <div class="pay-top">
                <b>{{ formatBDT(p.amount) }}</b>
                <span class="ad-badge" :class="p.status">{{ p.status }}</span>
              </div>
              <small v-if="p.trx_id" class="ad-muted">TrxID <b>{{ p.trx_id }}</b><template v-if="p.sender_number"> from {{ p.sender_number }}</template></small>
              <small v-else-if="p.provider === 'sslcommerz'" class="ad-muted">SSLCommerz {{ p.gateway_ref ? `ref ${p.gateway_ref}` : "" }}</small>
              <div v-if="p.status === 'verifying'" class="ad-row">
                <button class="ad-btn sm primary" :disabled="!!busy" @click="review(p, 'verify')">Mark as received</button>
                <button class="ad-btn sm danger" :disabled="!!busy" @click="review(p, 'reject')">Not received</button>
              </div>
            </div>

            <p v-if="order.payment_method === 'cod' && order.payment_status === 'unpaid'" class="ad-muted">Cash is collected on delivery. It is marked paid when the order is delivered.</p>
            <button v-if="order.payment_status === 'unpaid' && order.payment_method === 'cod'" class="ad-btn sm" :disabled="!!busy" @click="markPaid">Mark as paid</button>
          </section>

          <section class="ad-card">
            <h2>Courier</h2>
            <template v-if="order.steadfast_consignment_id">
              <p>SteadFast tracking code <b>{{ order.steadfast_tracking_code }}</b></p>
              <p class="ad-muted">Consignment {{ order.steadfast_consignment_id }}</p>
              <div class="ad-row">
                <a v-if="order.tracking_url" :href="order.tracking_url" target="_blank" rel="noopener" class="ad-btn sm">Open tracking</a>
                <button class="ad-btn sm" :disabled="!!busy" @click="sync">{{ busy === "sync" ? "Refreshing..." : "Refresh status" }}</button>
              </div>
            </template>
            <template v-else>
              <p class="ad-muted">Cash to collect at the door: <b>{{ formatBDT(toCollect) }}</b></p>
              <button class="ad-btn primary" :disabled="!!busy || !!dispatchBlock" @click="dispatch">{{ busy === "dispatch" ? "Booking..." : "Book with SteadFast" }}</button>
              <p v-if="dispatchBlock" class="ad-muted">{{ dispatchBlock }}</p>
            </template>
          </section>
        </aside>
      </div>

      <!-- Print views -->
      <div v-if="printMode" class="print-area">
        <div v-if="printMode === 'invoice'" class="inv">
          <h1>Dressnio</h1>
          <p>{{ storeAddress }} {{ storePhone }}</p>
          <h2>Invoice {{ order.order_no }}</h2>
          <p>{{ date(order.created_at) }}</p>
          <p><b>{{ order.customer_name }}</b>, {{ order.customer_phone }}<br />{{ order.delivery_address }}, {{ order.delivery_city }}</p>
          <table>
            <thead><tr><th>Item</th><th>Size</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
            <tbody>
              <tr v-for="i in order.items" :key="i.id"><td>{{ i.product_name }}</td><td>{{ i.size_name || "-" }}</td><td>{{ i.quantity }}</td><td>{{ formatBDT(i.unit_price) }}</td><td>{{ formatBDT(i.total_price) }}</td></tr>
            </tbody>
          </table>
          <p>Delivery: {{ order.shipping_cost ? formatBDT(order.shipping_cost) : "Free" }}</p>
          <p><b>Total: {{ formatBDT(order.total_amount) }}</b> ({{ order.payment_status }})</p>
        </div>
        <div v-else class="label">
          <h2>{{ order.customer_name }}</h2>
          <p class="big">{{ order.customer_phone }}</p>
          <p>{{ order.delivery_address }}<br />{{ order.delivery_city }}</p>
          <hr />
          <p>Order <b>{{ order.order_no }}</b> / {{ order.items?.reduce((n, i) => n + i.quantity, 0) }} pcs</p>
          <p class="cod">{{ toCollect > 0 ? `COLLECT ${formatBDT(toCollect)}` : "PAID: NO COLLECTION" }}</p>
          <p v-if="order.steadfast_tracking_code">Tracking {{ order.steadfast_tracking_code }}</p>
        </div>
      </div>
    </template>
  </div>
</template>

<style scoped>
.back {
  display: inline-block;
  margin-bottom: 1rem;
  font-weight: 600;
  color: #4d4ddb;
}
.head {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 1rem;
}
.head h1 {
  font-size: 1.7rem;
  font-weight: 700;
  color: #151552;
}
.badges {
  display: flex;
  gap: 0.4rem;
  margin-top: 0.6rem;
}
.actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  align-items: flex-start;
}
.cols {
  display: grid;
  gap: 1.2rem;
  margin-top: 1.4rem;
}
@media (min-width: 1000px) {
  .cols {
    grid-template-columns: 1.6fr 1fr;
    align-items: start;
  }
}
.main,
.side {
  display: grid;
  gap: 1.2rem;
}
.items td {
  padding: 0.7rem 0.4rem;
}
.items small {
  display: flex;
  align-items: center;
  gap: 0.4rem;
}
.dot {
  width: 0.9rem;
  height: 0.9rem;
  border-radius: 50%;
  box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.15);
}
.num {
  text-align: right;
  white-space: nowrap;
}
.sum {
  display: grid;
  gap: 0.4rem;
  margin-left: auto;
  min-width: 16rem;
}
.sum div {
  display: flex;
  justify-content: space-between;
}
.sum .total {
  padding-top: 0.5rem;
  border-top: 1px solid #e4e4f2;
  font-size: 1.15rem;
  font-weight: 700;
}
.timeline {
  list-style: none;
  display: grid;
  gap: 1rem;
}
.timeline p {
  margin: 0.3rem 0 0.1rem;
}
.addr {
  line-height: 1.55;
}
.link {
  color: #4d4ddb;
  font-weight: 600;
  margin-right: 0.8rem;
}
.pay {
  display: grid;
  gap: 0.4rem;
  padding: 0.8rem;
  border-radius: 12px;
  background: #fafaff;
}
.pay-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.print-area {
  display: none;
}
@media print {
  :global(body *) {
    visibility: hidden;
  }
  .print-area,
  .print-area * {
    visibility: visible;
  }
  .print-area {
    display: block;
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    color: #000;
    font-family: Arial, sans-serif;
  }
  .inv table {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0;
  }
  .inv th,
  .inv td {
    border-bottom: 1px solid #999;
    padding: 0.4rem;
    text-align: left;
  }
  .label {
    width: 105mm;
    padding: 6mm;
    border: 1px solid #000;
  }
  .label .big {
    font-size: 1.4rem;
    font-weight: 700;
  }
  .label .cod {
    margin-top: 4mm;
    font-size: 1.5rem;
    font-weight: 800;
  }
}
</style>
