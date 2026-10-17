<template>
  <div class="dn-container" style="padding-top: 3rem; padding-bottom: 5rem; max-width: 800px;">
    <!-- Tracking Header -->
    <div style="text-align: center; margin-bottom: 2.5rem;">
      <div style="width: 64px; height: 64px; background: var(--dn-primary-light); color: var(--dn-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
        <Icon name="solar:box-minimalistic-bold" style="font-size: 2rem;" />
      </div>
      <h1 style="font-size: 2rem; font-weight: 800; color: var(--dn-tertiary);">Track Your Parcel</h1>
      <p style="color: var(--dn-text-muted); font-size: 0.95rem; margin-top: 0.25rem;">
        Enter your Order Tracking Number or Mobile Number below
      </p>
    </div>

    <!-- Search Box -->
    <div class="dn-card dn-card-glass" style="padding: 1.5rem; margin-bottom: 2.5rem;">
      <form @submit.prevent="fetchOrder" style="display: flex; gap: 0.75rem;">
        <input
          v-model="searchQuery"
          type="text"
          class="dn-input"
          placeholder="Enter Order # (e.g. DN-260824-XXXX)"
          required
          style="flex-grow: 1;"
        />
        <button type="submit" class="dn-btn dn-btn-primary" :disabled="isLoading">
          <Icon v-if="isLoading" name="svg-spinners:180-ring-with-bg" />
          <Icon v-else name="solar:magnifer-linear" />
          <span>Track</span>
        </button>
      </form>
    </div>

    <p v-if="notFound" role="alert" style="text-align:center;color:#c0285a;margin-bottom:1.5rem">We could not find that order. Check the order number from your confirmation and try again.</p>

    <!-- Order Timeline Result -->
    <div v-if="order" class="dn-card" style="padding: 2rem;">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--dn-border-light); padding-bottom: 1.25rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
          <span style="font-size: 0.8rem; font-weight: 700; color: var(--dn-text-muted); text-transform: uppercase;">Order Number</span>
          <h2 style="font-size: 1.4rem; font-weight: 800; color: var(--dn-primary);">#{{ order.order_no }}</h2>
          <span style="font-size: 0.85rem; color: var(--dn-text-muted);">Placed on {{ new Date(order.created_at).toLocaleDateString() }}</span>
        </div>

        <div style="text-align: right;">
          <span class="dn-status-badge" :class="`dn-status-${order.order_status}`" style="font-size: 0.85rem; padding: 0.35rem 0.85rem; text-transform: uppercase;">
            {{ order.order_status }}
          </span>
          <div v-if="order.steadfast_tracking_code" style="font-size: 0.8rem; color: var(--dn-text-muted); margin-top: 0.35rem;">
            SteadFast: <strong>{{ order.steadfast_tracking_code }}</strong>
          </div>
        </div>
      </div>

      <!-- Step Timeline -->
      <div style="margin: 2.5rem 0 2rem 1rem;">
        <div
          v-for="(step, idx) in steps"
          :key="idx"
          style="display: flex; gap: 1.25rem; position: relative; padding-bottom: 1.75rem;"
        >
          <!-- Vertical Line -->
          <div
            v-if="idx < steps.length - 1"
            style="position: absolute; top: 28px; left: 14px; width: 2px; height: calc(100% - 20px); background: var(--dn-border);"
            :style="{ background: isStepCompleted(step.key) ? 'var(--dn-primary)' : 'var(--dn-border)' }"
          ></div>

          <!-- Step Dot -->
          <div
            style="width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 2; flex-shrink: 0; font-size: 0.9rem;"
            :style="{
              backgroundColor: isStepCompleted(step.key) ? 'var(--dn-primary)' : '#E2E8F0',
              color: isStepCompleted(step.key) ? '#ffffff' : '#64748B',
            }"
          >
            <Icon :name="step.icon" />
          </div>

          <!-- Step Info -->
          <div>
            <strong style="font-size: 0.95rem; display: block; color: var(--dn-tertiary);">{{ step.title }}</strong>
            <p style="font-size: 0.85rem; color: var(--dn-text-muted); margin-top: 0.15rem;">{{ step.description }}</p>
          </div>
        </div>
      </div>

      <!-- Order Items Summary -->
      <div style="background: var(--dn-surface-muted); border-radius: var(--dn-radius-md); padding: 1.25rem; border: 1px solid var(--dn-border);">
        <h4 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.75rem;">Items in this parcel</h4>
        <div v-for="item in order.items" :key="item.id" style="display: flex; justify-content: space-between; font-size: 0.875rem; margin-bottom: 0.4rem;">
          <span>{{ item.quantity }}x {{ item.product_name }} <small v-if="item.size_name">({{ item.size_name }})</small></span>
          <strong>{{ formatBDT(item.total_price) }}</strong>
        </div>
        <div style="border-top: 1px solid var(--dn-border); margin-top: 0.75rem; padding-top: 0.75rem; display: flex; justify-content: space-between; font-weight: 800; color: var(--dn-primary);">
          <span>Total:</span>
          <span>{{ formatBDT(order.total_amount) }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
useSeoMeta({ title: "Track your order | Dressnio" });
import { ref, onMounted } from "vue";

const route = useRoute();
const config = useRuntimeConfig();
const searchQuery = ref("");
const order = ref(null);
const isLoading = ref(false);
const notFound = ref(false);

const steps = [
  { key: "pending", title: "Order Placed", description: "Your order has been recorded and is pending confirmation", icon: "solar:cart-check-bold" },
  { key: "processing", title: "Processing & Quality Check", description: "Our fashion team is preparing and tailoring your garments", icon: "solar:scissors-bold" },
  { key: "shipped", title: "Handed to SteadFast Courier", description: "Your package is on its way to your destination", icon: "solar:delivery-bold" },
  { key: "delivered", title: "Delivered", description: "Package safely delivered to your doorstep", icon: "solar:check-circle-bold" },
];

const statusWeights = {
  pending: 1,
  confirmed: 1,
  processing: 2,
  shipped: 3,
  delivered: 4,
};

const isStepCompleted = (stepKey) => {
  if (!order.value) return false;
  const currentWeight = statusWeights[order.value.order_status] || 1;
  const stepWeight = statusWeights[stepKey] || 1;
  return currentWeight >= stepWeight;
};

const fetchOrder = async () => {
  if (!searchQuery.value) return;
  notFound.value = false;
  isLoading.value = true;
  try {
    const res = await $fetch(`${config.public.apiBase}/client/my-orders/${searchQuery.value.trim()}`);
    if (res && res.data) {
      order.value = res.data;
    }
  } catch (err) {
    notFound.value = true;
    order.value = null;
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  if (route.query.order_no) {
    searchQuery.value = route.query.order_no;
    fetchOrder();
  }
});
</script>
