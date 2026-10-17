<template>
  <div class="dn-container" style="padding-top: 2.5rem; padding-bottom: 5rem;">
    <!-- Headline -->
    <div style="margin-bottom: 2rem;">
      <h1 style="font-size: 2rem; font-weight: 800; color: var(--dn-tertiary);">Your Shopping Bag</h1>
      <p style="color: var(--dn-text-muted); font-size: 0.95rem; margin-top: 0.25rem;">Review your selected garments before proceeding to secure checkout</p>
    </div>

    <div v-if="cart.length === 0" class="dn-card" style="max-width: 540px; margin: 3rem auto; padding: 3rem; text-align: center;">
      <div style="width: 72px; height: 72px; background: var(--dn-primary-light); color: var(--dn-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
        <Icon name="solar:bag-3-bold" style="font-size: 2.25rem;" />
      </div>
      <h3 style="font-size: 1.4rem; font-weight: 800; color: var(--dn-tertiary); margin-bottom: 0.5rem;">Your bag is empty</h3>
      <p style="color: var(--dn-text-muted); font-size: 0.95rem; margin-bottom: 1.75rem;">Explore our new seasonal kurta sets, sarees, and dresses.</p>
      <NuxtLink to="/shop" class="dn-btn dn-btn-primary dn-btn-lg">
        <span>Start Shopping</span>
      </NuxtLink>
    </div>

    <div v-else class="cart-grid">
      <!-- Cart Items List -->
      <div style="display: flex; flex-direction: column; gap: 1rem;">
        <div
          v-for="product in cart"
          :key="`${product.id}-${product.size}-${product.color}`"
          class="dn-card"
          style="padding: 1.25rem; display: flex; gap: 1.25rem; align-items: center; flex-wrap: wrap;"
        >
          <!-- Product Thumb -->
          <div style="width: 80px; height: 104px; border-radius: 12px; overflow: hidden; background: var(--brand-tint);"><ArtImage :src="product.image" :alt="product.name" :kind="garmentKindFor(product.name)" :color="product.color || toneHexAt(product.id)" /></div>

          <!-- Details -->
          <div style="flex: 1; min-width: 180px;">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--dn-tertiary); margin-bottom: 0.35rem;">
              {{ product.name }}
            </h3>
            <div style="display: flex; gap: 0.75rem; font-size: 0.8rem; color: var(--dn-text-muted); margin-bottom: 0.5rem;">
              <span v-if="product.size" class="dn-size-chip">{{ product.size }}</span>
              <span v-if="product.color" style="display: flex; align-items: center; gap: 0.35rem;">
                Color: <span class="dn-swatch" :style="{ backgroundColor: product.color }"></span>
              </span>
            </div>
            <strong style="font-size: 1.15rem; color: var(--dn-primary);">
              {{ formatCurrency(product.price * product.quantity) }}
            </strong>
          </div>

          <!-- Quantity Controls -->
          <div style="display: inline-flex; align-items: center; border: 1.5px solid var(--dn-border); border-radius: var(--dn-radius-lg); background: var(--dn-surface); padding: 2px;">
            <button
              type="button"
              @click="decrementQuantity(product)"
              style="width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; border-radius: var(--dn-radius-md); background: var(--dn-bg-subtle); color: var(--dn-text-main); cursor: pointer; transition: all 0.2s;"
              title="Decrease quantity"
            >
              <Icon name="solar:minus-bold" style="font-size: 1rem;" />
            </button>
            <span style="font-weight: 800; min-width: 2.25rem; text-align: center; font-size: 0.95rem; color: var(--dn-tertiary);">{{ product.quantity }}</span>
            <button
              type="button"
              @click="incrementQuantity(product)"
              style="width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; border-radius: var(--dn-radius-md); background: var(--dn-bg-subtle); color: var(--dn-text-main); cursor: pointer; transition: all 0.2s;"
              title="Increase quantity"
            >
              <Icon name="solar:add-bold" style="font-size: 1rem;" />
            </button>
          </div>

          <!-- Remove Item -->
          <button
            type="button"
            class="dn-btn"
            style="color: var(--dn-accent-rose); padding: 0.5rem;"
            title="Remove item"
            @click="removeFromCart(product)"
          >
            <Icon name="solar:trash-bin-trash-bold" style="font-size: 1.25rem;" />
          </button>
        </div>
      </div>

      <!-- Cart Order Summary -->
      <div>
        <div class="dn-card dn-card-glass" style="padding: 1.75rem; position: sticky; top: 90px;">
          <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--dn-tertiary); margin-bottom: 1.25rem;">
            Order Summary
          </h2>

          <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.925rem; margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between;">
              <span style="color: var(--dn-text-muted);">Subtotal:</span>
              <strong>{{ formatCurrency(subtotal) }}</strong>
            </div>

            <div style="display: flex; justify-content: space-between;">
              <span style="color: var(--dn-text-muted);">Estimated Delivery:</span>
              <span style="font-size: 0.825rem; color: var(--dn-text-muted);">Calculated at checkout</span>
            </div>

            <div style="display: flex; justify-content: space-between; font-size: 1.3rem; font-weight: 900; color: var(--dn-primary); border-top: 1.5px solid var(--dn-border); padding-top: 0.85rem; margin-top: 0.5rem;">
              <span>Estimated Total:</span>
              <span>{{ formatCurrency(total) }}</span>
            </div>
          </div>

          <NuxtLink to="/checkout" class="dn-btn dn-btn-primary dn-btn-lg dn-btn-full" style="margin-bottom: 0.75rem;">
            <span>Proceed to Checkout</span>
            <Icon name="solar:arrow-right-bold" />
          </NuxtLink>

          <NuxtLink to="/shop" class="dn-btn dn-btn-outline dn-btn-full">
            <span>Continue Shopping</span>
          </NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
useSeoMeta({ title: "Your bag | Dressnio" });
import { useCartStore } from "~/stores/cart";
import { computed } from "vue";

const cartStore = useCartStore();
const cart = computed(() => cartStore.items);

const removeFromCart = (product) => {
  cartStore.removeProduct(product);
};

const incrementQuantity = (product) => {
  cartStore.incrementQuantity(product);
};

const decrementQuantity = (product) => {
  cartStore.decrementQuantity(product);
};

const subtotal = computed(() => {
  return cart.value.reduce((acc, product) => acc + product.price * product.quantity, 0);
});

const total = computed(() => subtotal.value);

const formatCurrency = (price) => formatBDT(price);
</script>

<style scoped>
.cart-grid {
  display: grid;
  grid-template-columns: 1.35fr 0.85fr;
  gap: 2.5rem;
}

@media (max-width: 991px) {
  .cart-grid {
    grid-template-columns: 1fr;
  }
}
</style>
