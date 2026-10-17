<template>
  <div class="bg-white border border-stone-200/90 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow duration-200 flex flex-col justify-between">
    <!-- Header -->
    <div>
      <div class="flex items-center justify-between pb-3 mb-3 border-b border-stone-100">
        <div>
          <span class="text-xs font-semibold uppercase tracking-wider text-stone-400">Order Number</span>
          <h3 class="font-bold text-stone-900 text-base">#{{ order.order_no || order.id }}</h3>
          <p class="text-xs text-stone-500 mt-0.5">{{ formatDate(order.created_at || order.date) }}</p>
        </div>

        <span
          class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide inline-flex items-center gap-1.5"
          :class="statusBadgeClasses[order.order_status || order.status] || 'bg-stone-100 text-stone-700'"
        >
          <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
          {{ order.order_status || order.status || "Pending" }}
        </span>
      </div>

      <!-- Items List -->
      <div class="space-y-2.5 my-3">
        <div
          v-for="(item, idx) in order.items || []"
          :key="idx"
          class="flex items-center justify-between text-xs sm:text-sm text-stone-700 bg-stone-50/70 p-2.5 rounded-xl border border-stone-100"
        >
          <div class="flex items-center gap-2 overflow-hidden pr-2">
            <span class="font-bold text-brand-500 flex-shrink-0">{{ item.quantity }}x</span>
            <span class="truncate font-medium text-stone-800">{{ item.product_name }}</span>
            <span v-if="item.size_name" class="text-stone-400 text-xs">({{ item.size_name }})</span>
          </div>
          <span class="font-semibold text-stone-900 flex-shrink-0">
            {{ formatBDT((Number(item.total_price) || Number(item.unit_price) * Number(item.quantity) || 0)) }}
          </span>
        </div>

        <!-- Fallback if items array empty -->
        <div v-if="!order.items || order.items.length === 0" class="text-xs text-stone-500 italic py-1">
          No item details available
        </div>
      </div>
    </div>

    <!-- Footer Summary & Actions -->
    <div class="pt-3 mt-3 border-t border-stone-100">
      <div class="flex items-center justify-between text-sm mb-3.5">
        <span class="text-stone-500 font-medium">Total Amount</span>
        <span class="text-base font-extrabold text-stone-900">
          {{ formatBDT((Number(order.total_amount) || Number(order.total) || 0)) }}
        </span>
      </div>

      <p class="text-xs text-stone-500 mb-3">Payment: {{ payLabel }}</p>

      <div class="flex flex-wrap items-center gap-2">
        <button
          v-if="canPay"
          type="button"
          :disabled="isPaying"
          class="py-2 px-3 rounded-xl bg-brand text-white hover:bg-brand-600 text-xs font-medium transition-colors"
          @click="payNow"
        >
          {{ isPaying ? "Opening..." : "Pay now" }}
        </button>
        <NuxtLink
          :to="{ path: '/track-order', query: { order_no: order.order_no } }"
          class="flex-1 text-center py-2 px-3 rounded-xl bg-stone-900 hover:bg-stone-800 text-white text-xs font-medium transition-colors inline-flex items-center justify-center gap-1.5"
        >
          <Icon name="solar:radar-2-bold" class="w-4 h-4" />
          <span>Track Order</span>
        </NuxtLink>

        <button
          v-if="order.order_status === 'pending'"
          @click="cancelOrder"
          :disabled="isCancelling"
          class="py-2 px-3 rounded-xl border border-brand-200 text-brand-600 hover:bg-brand-50 text-xs font-medium transition-colors inline-flex items-center justify-center gap-1"
        >
          <Icon v-if="isCancelling" name="svg-spinners:180-ring-with-bg" class="w-3.5 h-3.5" />
          <span>{{ isCancelling ? "Cancelling..." : "Cancel" }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

const props = defineProps({
  order: { type: Object, required: true },
});

const emit = defineEmits(["order-cancelled"]);
const snackbar = useSnackbar();
const config = useRuntimeConfig();
const token = useTokenStore();
const isPaying = ref(false);
const isCancelling = ref(false);

const statusBadgeClasses = {
  pending: "bg-amber-50 text-amber-700 border border-amber-200/60",
  processing: "bg-blue-50 text-blue-700 border border-blue-200/60",
  shipped: "bg-purple-50 text-purple-700 border border-purple-200/60",
  delivered: "bg-emerald-50 text-emerald-700 border border-emerald-200/60",
  cancelled: "bg-brand-50 text-brand-700 border border-brand-200/60",
};

const formatDate = (dateString) => {
  if (!dateString) return "";
  try {
    return new Date(dateString).toLocaleDateString("en-US", {
      year: "numeric",
      month: "short",
      day: "numeric",
    });
  } catch (e) {
    return dateString;
  }
};

const authHeaders = () => ({ Authorization: `Bearer ${token.getToken}`, Accept: "application/json" });

const payLabel = computed(
  () => ({ unpaid: "not paid yet", verifying: "we are checking it", paid: "paid", refunded: "refunded" })[props.order.payment_status] || props.order.payment_status
);
const canPay = computed(
  () => props.order.order_status === "pending" && props.order.payment_status === "unpaid" && props.order.payment_method === "card"
);

const cancelOrder = async () => {
  if (!confirm(`Cancel order #${props.order.order_no}?`)) return;

  isCancelling.value = true;
  try {
    await $fetch(`${config.public.apiBase}/client/my-orders/${props.order.order_no}/cancel`, { method: "POST", headers: authHeaders() });
    snackbar.add({ type: "success", text: "Order cancelled" });
    emit("order-cancelled", props.order.order_no);
  } catch (err) {
    snackbar.add({ type: "error", text: err?.data?.message || "We could not cancel this order.", duration: 6000 });
  } finally {
    isCancelling.value = false;
  }
};

const payNow = async () => {
  isPaying.value = true;
  try {
    const res = await $fetch(`${config.public.apiBase}/client/my-orders/${props.order.order_no}/pay`, { method: "POST", headers: authHeaders() });
    window.location.href = res.data.payment_url;
  } catch (err) {
    snackbar.add({ type: "error", text: err?.data?.message || "We could not open the payment page.", duration: 6000 });
    isPaying.value = false;
  }
};
</script>
