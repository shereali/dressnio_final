<script setup>
// Order list: status tabs, search, payment filter and paging. A row opens the full order.
definePageMeta({ middleware: ["auth"] });

const api = useAdminApi();
const route = useRoute();
const router = useRouter();
const snackbar = useSnackbar();

const tabs = [
  { key: "", label: "All" },
  { key: "pending", label: "New" },
  { key: "confirmed", label: "Confirmed" },
  { key: "processing", label: "Packing" },
  { key: "shipped", label: "Shipped" },
  { key: "delivered", label: "Delivered" },
  { key: "cancelled", label: "Cancelled" },
  { key: "returned", label: "Returned" },
];

const status = ref(String(route.query.status || ""));
const payment = ref(String(route.query.payment || ""));
const search = ref("");
const page = ref(1);
const orders = ref([]);
const meta = ref({});
const loading = ref(true);

const load = async () => {
  loading.value = true;
  try {
    const res = await api("/orders", {
      params: { status: status.value || undefined, payment_status: payment.value || undefined, search: search.value || undefined, page: page.value },
    });
    orders.value = res?.data || [];
    meta.value = res?.meta || {};
  } catch (e) {
    snackbar.add({ type: "error", text: apiErrorText(e, "Could not load orders.") });
  } finally {
    loading.value = false;
  }
};

onMounted(load);

let timer;
watch(search, () => {
  clearTimeout(timer);
  timer = setTimeout(() => {
    page.value = 1;
    load();
  }, 350);
});
watch([status, payment], () => {
  page.value = 1;
  router.replace({ query: { status: status.value || undefined, payment: payment.value || undefined } });
  load();
});
watch(page, load);

const count = (key) => (key === "" ? Object.values(meta.value.counts || {}).reduce((a, b) => a + Number(b), 0) : meta.value.counts?.[key] || 0);
const review = computed(() => meta.value.needs_payment_review || 0);
const date = (d) => new Date(d).toLocaleString("en-GB", { day: "numeric", month: "short", hour: "2-digit", minute: "2-digit" });
const payLabel = { cod: "Cash", bkash: "bKash", nagad: "Nagad", card: "Card" };
</script>

<template>
  <div class="ad-page" style="max-width: 1300px">
    <header class="ad-head">
      <h1>Orders</h1>
      <p>Confirm new orders, check payments and send parcels to the courier.</p>
    </header>

    <div v-if="review" class="alert">
      <b>{{ review }} {{ review === 1 ? "payment is" : "payments are" }} waiting for you to check the TrxID.</b>
      <button type="button" class="ad-btn sm" @click="payment = 'verifying'">Show them</button>
    </div>

    <div class="tabs" role="tablist">
      <button v-for="t in tabs" :key="t.key" role="tab" :aria-selected="status === t.key" :class="{ on: status === t.key }" @click="status = t.key">
        {{ t.label }} <span>{{ count(t.key) }}</span>
      </button>
    </div>

    <div class="bar">
      <input v-model="search" class="ad-input" type="search" placeholder="Search by order number, name or phone" aria-label="Search orders" />
      <select v-model="payment" class="ad-input" aria-label="Payment status">
        <option value="">Any payment</option>
        <option value="unpaid">Unpaid</option>
        <option value="verifying">Needs checking</option>
        <option value="paid">Paid</option>
        <option value="refunded">Refunded</option>
      </select>
    </div>

    <div class="ad-table-wrap">
      <table class="ad-table">
        <thead>
          <tr>
            <th>Order</th>
            <th>Customer</th>
            <th>Placed</th>
            <th>Total</th>
            <th>Payment</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="6" class="ad-muted">Loading orders...</td>
          </tr>
          <tr v-else-if="!orders.length">
            <td colspan="6" class="ad-muted">No orders match. Try a different tab or search.</td>
          </tr>
          <tr v-for="o in orders" :key="o.id" class="o-row" @click="navigateTo(`/admin/order/${o.id}`)">
            <td>
              <NuxtLink :to="`/admin/order/${o.id}`" class="no" @click.stop>#{{ o.order_no }}</NuxtLink>
              <small class="ad-muted">{{ o.items?.length || 0 }} {{ o.items?.length === 1 ? "item" : "items" }}</small>
            </td>
            <td>
              {{ o.customer_name }}
              <small class="ad-muted">{{ o.customer_phone }}</small>
            </td>
            <td>{{ date(o.created_at) }}</td>
            <td><b>{{ formatBDT(o.total_amount) }}</b></td>
            <td>
              <span class="ad-badge" :class="o.payment_status">{{ o.payment_status === "verifying" ? "check TrxID" : o.payment_status }}</span>
              <small class="ad-muted">{{ payLabel[o.payment_method] || o.payment_method }}</small>
            </td>
            <td><span class="ad-badge" :class="o.order_status">{{ o.order_status }}</span></td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="meta.last_page > 1" class="pager">
      <button class="ad-btn sm" :disabled="page <= 1" @click="page--">Previous</button>
      <span>Page {{ meta.current_page }} of {{ meta.last_page }}</span>
      <button class="ad-btn sm" :disabled="page >= meta.last_page" @click="page++">Next</button>
    </div>
  </div>
</template>

<style scoped>
.alert {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  margin-top: 1.2rem;
  padding: 0.8rem 1rem;
  border-radius: 12px;
  background: #eef0ff;
  color: #2a2ab0;
}
.tabs {
  display: flex;
  gap: 0.4rem;
  margin: 1.4rem 0 1rem;
  overflow-x: auto;
  padding-bottom: 0.2rem;
}
.tabs button {
  display: inline-flex;
  gap: 0.45rem;
  align-items: center;
  padding: 0.5rem 0.9rem;
  border-radius: 999px;
  border: 1.5px solid #e4e4f2;
  background: #fff;
  font-weight: 600;
  font-size: 0.9rem;
  white-space: nowrap;
}
.tabs button span {
  padding: 0 0.45rem;
  border-radius: 999px;
  background: #f0f0fe;
  font-size: 0.78rem;
  color: #4d4ddb;
}
.tabs button.on {
  background: #4d4ddb;
  border-color: #4d4ddb;
  color: #fff;
}
.tabs button.on span {
  background: rgba(255, 255, 255, 0.25);
  color: #fff;
}
.bar {
  display: grid;
  gap: 0.6rem;
  margin-bottom: 1rem;
}
@media (min-width: 700px) {
  .bar {
    grid-template-columns: 1fr 14rem;
  }
}
.o-row {
  cursor: pointer;
}
.no {
  display: block;
  font-weight: 700;
  color: #4d4ddb;
}
td small {
  display: block;
}
.pager {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  margin-top: 1rem;
}
</style>
