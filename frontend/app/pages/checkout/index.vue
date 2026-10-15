<template>
  <div class="dn-container" style="padding-top: 2.5rem; padding-bottom: 4rem;">
    <!-- Page Header -->
    <div style="margin-bottom: 2rem; text-align: center;">
      <h1 style="font-size: 2rem; font-weight: 800; color: var(--dn-tertiary); letter-spacing: -0.02em;">
        Secure Express Checkout
      </h1>
      <p style="color: var(--dn-text-muted); font-size: 0.95rem; margin-top: 0.35rem;">
        Complete your order in 60 seconds with cash on delivery or instant payment
      </p>
    </div>

    <!-- Success View -->
    <div v-if="orderPlaced" class="dn-card dn-card-glass" style="max-width: 680px; margin: 0 auto; padding: 2.5rem; text-align: center;">
      <div style="width: 72px; height: 72px; background: var(--dn-accent-emerald-light); color: var(--dn-accent-emerald); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
        <Icon name="solar:check-circle-bold" style="font-size: 2.5rem;" />
      </div>

      <h2 style="font-size: 1.65rem; font-weight: 800; color: var(--dn-tertiary); margin-bottom: 0.5rem;">
        Thank You for Your Order!
      </h2>
      <p style="color: var(--dn-text-muted); font-size: 1rem; margin-bottom: 1.5rem;">
        Your order <strong style="color: var(--dn-primary);">#{{ confirmedOrder?.order_no }}</strong> has been placed successfully.
      </p>

      <div style="background: var(--dn-surface-muted); border-radius: var(--dn-radius-md); padding: 1.25rem; text-align: left; margin-bottom: 2rem; border: 1px solid var(--dn-border);">
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.9rem;">
          <span style="color: var(--dn-text-muted);">Recipient:</span>
          <strong>{{ confirmedOrder?.customer_name }} ({{ confirmedOrder?.customer_phone }})</strong>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.9rem;">
          <span style="color: var(--dn-text-muted);">Delivery Address:</span>
          <span>{{ confirmedOrder?.delivery_address }}, {{ confirmedOrder?.delivery_city }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.9rem;">
          <span style="color: var(--dn-text-muted);">Payment Method:</span>
          <span style="text-transform: uppercase; font-weight: 700;">{{ confirmedOrder?.payment_method }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; font-size: 1.1rem; font-weight: 800; color: var(--dn-primary); border-top: 1px solid var(--dn-border); padding-top: 0.75rem; margin-top: 0.75rem;">
          <span>Total Amount:</span>
          <span>৳{{ confirmedOrder?.total_amount?.toFixed(2) }}</span>
        </div>
      </div>

      <div class="dn-flex dn-gap-3 dn-justify-center" style="flex-wrap: wrap;">
        <NuxtLink :to="`/track-order?order_no=${confirmedOrder?.order_no}`" class="dn-btn dn-btn-primary">
          <Icon name="solar:box-minimalistic-bold" />
          <span>Track Order Timeline</span>
        </NuxtLink>
        <NuxtLink to="/shop" class="dn-btn dn-btn-outline">
          <span>Continue Shopping</span>
        </NuxtLink>
      </div>
    </div>

    <!-- Empty Cart View -->
    <div v-else-if="products.length === 0" class="dn-card" style="max-width: 500px; margin: 2rem auto; padding: 3rem; text-align: center;">
      <div style="width: 64px; height: 64px; background: var(--dn-primary-light); color: var(--dn-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
        <Icon name="solar:bag-3-bold" style="font-size: 2rem;" />
      </div>
      <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">Your Cart is Empty</h3>
      <p style="color: var(--dn-text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">
        Discover our latest designer kurti sets, sarees, and dresses.
      </p>
      <NuxtLink to="/shop" class="dn-btn dn-btn-primary">
        <span>Explore Collection</span>
      </NuxtLink>
    </div>

    <!-- Checkout Grid -->
    <div v-else class="checkout-grid">
      <!-- Left: Shipping & Payment Details -->
      <div class="dn-card" style="padding: 2rem;">
        <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--dn-tertiary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.6rem;">
          <Icon name="solar:user-id-bold" style="color: var(--dn-primary);" />
          <span>1. Customer & Shipping Information</span>
        </h2>

        <form @submit.prevent="handleCheckout">
          <!-- Full Name -->
          <div class="dn-input-group">
            <label class="dn-label">Full Name *</label>
            <input
              v-model="formData.name"
              type="text"
              class="dn-input"
              placeholder="e.g. Ayesha Rahman"
              required
            />
          </div>

          <!-- Phone Number -->
          <div class="dn-input-group">
            <label class="dn-label">Mobile Number (Active for delivery OTP) *</label>
            <input
              v-model="formData.mobile_no"
              type="tel"
              class="dn-input"
              placeholder="017XXXXXXXX"
              pattern="^01[3-9]\d{8}$"
              title="Please enter a valid 11-digit Bangladesh mobile number (e.g. 01712345678)"
              required
            />
          </div>

          <!-- Email (Optional) -->
          <div class="dn-input-group">
            <label class="dn-label">Email Address (Optional for invoice)</label>
            <input
              v-model="formData.email"
              type="email"
              class="dn-input"
              placeholder="ayesha@example.com"
            />
          </div>

          <!-- Delivery City / Region -->
          <div class="dn-input-group">
            <label class="dn-label">Delivery Region *</label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
              <label
                class="dn-choice-card"
                :class="{ active: deliveryCity === 'Dhaka' }"
                @click="setCity('Dhaka')"
              >
                <div>
                  <strong style="display: block; font-size: 0.9rem;">Inside Dhaka</strong>
                  <span style="font-size: 0.75rem; color: var(--dn-text-muted);">24-48 Hours (৳60)</span>
                </div>
                <Icon
                  :name="deliveryCity === 'Dhaka' ? 'solar:check-circle-bold' : 'solar:circle-outline'"
                  style="font-size: 1.25rem; color: var(--dn-primary);"
                />
              </label>

              <label
                class="dn-choice-card"
                :class="{ active: deliveryCity === 'Outside Dhaka' }"
                @click="setCity('Outside Dhaka')"
              >
                <div>
                  <strong style="display: block; font-size: 0.9rem;">Outside Dhaka</strong>
                  <span style="font-size: 0.75rem; color: var(--dn-text-muted);">48-72 Hours (৳120)</span>
                </div>
                <Icon
                  :name="deliveryCity === 'Outside Dhaka' ? 'solar:check-circle-bold' : 'solar:circle-outline'"
                  style="font-size: 1.25rem; color: var(--dn-primary);"
                />
              </label>
            </div>
          </div>

          <!-- Full Address -->
          <div class="dn-input-group">
            <label class="dn-label">Full Delivery Address *</label>
            <textarea
              v-model="formData.address"
              class="dn-textarea"
              placeholder="House #, Road #, Area / Thana, Landmark (e.g. House 14, Road 4, Banani, Dhaka)"
              required
            ></textarea>
          </div>

          <!-- Delivery Notes -->
          <div class="dn-input-group">
            <label class="dn-label">Delivery Instructions (Optional)</label>
            <input
              v-model="formData.delivery_notes"
              type="text"
              class="dn-input"
              placeholder="e.g. Call before delivery / Leave with guard"
            />
          </div>

          <!-- Payment Method -->
          <div style="margin-top: 2rem; margin-bottom: 2rem;">
            <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--dn-tertiary); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.6rem;">
              <Icon name="solar:card-2-bold" style="color: var(--dn-primary);" />
              <span>2. Payment Method</span>
            </h2>

            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
              <label
                class="dn-choice-card"
                :class="{ active: formData.payment_method === 'cod' }"
                @click="formData.payment_method = 'cod'"
              >
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                  <div style="width: 36px; height: 36px; border-radius: var(--dn-radius-sm); background: var(--dn-primary-light); color: var(--dn-primary); display: flex; align-items: center; justify-content: center;">
                    <Icon name="solar:hand-money-bold" style="font-size: 1.25rem;" />
                  </div>
                  <div>
                    <strong style="display: block; font-size: 0.95rem;">Cash on Delivery (COD)</strong>
                    <span style="font-size: 0.8rem; color: var(--dn-text-muted);">Pay with cash when your parcel arrives at your doorstep</span>
                  </div>
                </div>
                <Icon
                  :name="formData.payment_method === 'cod' ? 'solar:check-circle-bold' : 'solar:circle-outline'"
                  style="font-size: 1.35rem; color: var(--dn-primary);"
                />
              </label>

              <label
                class="dn-choice-card"
                :class="{ active: formData.payment_method === 'bkash' }"
                @click="formData.payment_method = 'bkash'"
              >
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                  <div style="width: 36px; height: 36px; border-radius: var(--dn-radius-sm); background: #FDE8E8; color: #E11D48; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.8rem;">
                    bKash
                  </div>
                  <div>
                    <strong style="display: block; font-size: 0.95rem;">bKash / Nagad Direct Pay</strong>
                    <span style="font-size: 0.8rem; color: var(--dn-text-muted);">Pay to Merchant: 01974-784848</span>
                  </div>
                </div>
                <Icon
                  :name="formData.payment_method === 'bkash' ? 'solar:check-circle-bold' : 'solar:circle-outline'"
                  style="font-size: 1.35rem; color: var(--dn-primary);"
                />
              </label>
            </div>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            class="dn-btn dn-btn-primary dn-btn-lg dn-btn-full"
            :disabled="isSubmitting"
            style="letter-spacing: 0.02em;"
          >
            <Icon v-if="isSubmitting" name="svg-spinners:180-ring-with-bg" style="font-size: 1.25rem;" />
            <Icon v-else name="solar:lock-password-bold" style="font-size: 1.25rem;" />
            <span>{{ isSubmitting ? 'Securing Order...' : `Place Order (৳${totalAmount.toFixed(2)})` }}</span>
          </button>
        </form>
      </div>

      <!-- Right: Order Summary -->
      <div>
        <div class="dn-card dn-card-glass" style="padding: 1.75rem; position: sticky; top: 90px;">
          <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--dn-tertiary); margin-bottom: 1.25rem;">
            Order Summary ({{ products.length }} items)
          </h3>

          <div style="max-height: 280px; overflow-y: auto; padding-right: 0.5rem; margin-bottom: 1.25rem;">
            <div
              v-for="item in products"
              :key="`${item.id}-${item.size}-${item.color}`"
              style="display: flex; gap: 0.85rem; padding: 0.75rem 0; border-bottom: 1px solid var(--dn-border-light);"
            >
              <img
                :src="item.image || '/brand/hero-chic.jpg'"
                :alt="item.name"
                style="width: 52px; height: 68px; object-fit: cover; border-radius: var(--dn-radius-sm);"
              />
              <div style="flex-grow: 1;">
                <h4 style="font-size: 0.875rem; font-weight: 600; line-height: 1.3; margin-bottom: 0.25rem;">{{ item.name }}</h4>
                <div style="font-size: 0.75rem; color: var(--dn-text-muted); display: flex; gap: 0.5rem;">
                  <span v-if="item.size">Size: <strong>{{ item.size }}</strong></span>
                  <span v-if="item.color">Color: <span class="dn-swatch" :style="{ backgroundColor: item.color, display: 'inline-block', verticalAlign: 'middle' }"></span></span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.4rem;">
                  <span style="font-size: 0.8rem; color: var(--dn-text-muted);">Qty: {{ item.quantity }}</span>
                  <strong style="font-size: 0.9rem; color: var(--dn-primary);">৳{{ (item.price * item.quantity).toFixed(2) }}</strong>
                </div>
              </div>
            </div>
          </div>

          <!-- Subtotal & Shipping Breakdown -->
          <div style="display: flex; flex-direction: column; gap: 0.6rem; font-size: 0.9rem; border-top: 1px solid var(--dn-border); padding-top: 1rem;">
            <div style="display: flex; justify-content: space-between;">
              <span style="color: var(--dn-text-muted);">Subtotal:</span>
              <strong>৳{{ subtotal.toFixed(2) }}</strong>
            </div>

            <div style="display: flex; justify-content: space-between;">
              <span style="color: var(--dn-text-muted);">Shipping ({{ deliveryCity }}):</span>
              <strong v-if="shippingFee === 0" style="color: var(--dn-accent-emerald);">FREE</strong>
              <strong v-else>৳{{ shippingFee.toFixed(2) }}</strong>
            </div>

            <div v-if="subtotal < 2000" style="background: var(--dn-primary-light); padding: 0.5rem 0.75rem; border-radius: var(--dn-radius-sm); font-size: 0.775rem; color: var(--dn-primary);">
              Add <strong>৳{{ (2000 - subtotal).toFixed(2) }}</strong> more for <strong>FREE Delivery</strong>!
            </div>

            <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 800; color: var(--dn-primary); border-top: 1.5px solid var(--dn-border); padding-top: 0.85rem; margin-top: 0.35rem;">
              <span>Total Payable:</span>
              <span>৳{{ totalAmount.toFixed(2) }}</span>
            </div>
          </div>

          <!-- Trust Badges -->
          <div style="margin-top: 1.5rem; display: flex; align-items: center; justify-content: space-around; font-size: 0.75rem; color: var(--dn-text-muted); border-top: 1px solid var(--dn-border-light); padding-top: 1rem;">
            <span style="display: flex; align-items: center; gap: 0.3rem;">
              <Icon name="solar:shield-check-bold" style="color: var(--dn-accent-emerald); font-size: 1rem;" /> 100% Genuine
            </span>
            <span style="display: flex; align-items: center; gap: 0.3rem;">
              <Icon name="solar:box-bold" style="color: var(--dn-primary); font-size: 1rem;" /> SteadFast Courier
            </span>
            <span style="display: flex; align-items: center; gap: 0.3rem;">
              <Icon name="solar:refresh-circle-bold" style="color: var(--dn-accent-gold); font-size: 1rem;" /> 7 Days Return
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useCartStore } from "~/stores/cart";
import { ref, computed } from "vue";

const cartStore = useCartStore();
const config = useRuntimeConfig();
const products = computed(() => cartStore.items);

const deliveryCity = ref("Dhaka");
const isSubmitting = ref(false);
const orderPlaced = ref(false);
const confirmedOrder = ref(null);

const formData = ref({
  name: "",
  mobile_no: "",
  email: "",
  address: "",
  city: "Dhaka",
  delivery_notes: "",
  payment_method: "cod",
});

const setCity = (city) => {
  deliveryCity.value = city;
  formData.value.city = city;
};

const subtotal = computed(() => {
  return products.value.reduce((total, item) => total + item.price * item.quantity, 0);
});

const shippingFee = computed(() => {
  if (subtotal.value >= 2000) return 0;
  return deliveryCity.value === "Dhaka" ? 60 : 120;
});

const totalAmount = computed(() => {
  return subtotal.value + shippingFee.value;
});

const handleCheckout = async () => {
  if (products.value.length === 0) return;

  isSubmitting.value = true;

  try {
    const payload = {
      name: formData.value.name,
      mobile_no: formData.value.mobile_no,
      email: formData.value.email || null,
      address: formData.value.address,
      city: deliveryCity.value,
      delivery_notes: formData.value.delivery_notes || null,
      payment_method: formData.value.payment_method,
      items: products.value.map((item) => ({
        id: item.id,
        name: item.name,
        image: item.image,
        size: item.size,
        color: item.color,
        quantity: item.quantity,
        price: item.price,
      })),
    };

    const res = await $fetch(`${config.public.apiBase}/client/checkout`, {
      method: "POST",
      body: payload,
    });

    if (res && res.data) {
      confirmedOrder.value = res.data;
      orderPlaced.value = true;
      cartStore.checkout(); // Clear cart store
    }
  } catch (error) {
    console.error("Order submission failed:", error);
    alert(error.data?.message || "Failed to place order. Please check your information and try again.");
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<style scoped>
.checkout-grid {
  display: grid;
  grid-template-columns: 1.3fr 0.9fr;
  gap: 2rem;
}

@media (max-width: 991px) {
  .checkout-grid {
    grid-template-columns: 1fr;
  }
}
</style>
