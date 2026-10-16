<script setup>
import { ref, computed, watchEffect } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useCartStore } from "~/stores/cart";

const router = useRouter();
const route = useRoute();
const config = useRuntimeConfig();
const cartStore = useCartStore();

const activeImageIndex = ref(0);
const quantity = ref(1);
const selectedSize = ref("");
const selectedColor = ref("");
const activeTab = ref("details");

const slug = computed(() => route.params.slug);

const { data, pending, error } = useFetch(
  `${config.public.apiBase}/client/product-details/${slug.value}`,
  {
    key: `product-details-${slug.value}`,
  }
);

const product = computed(() => data.value?.data || {});

// Calculate prices map
const pricesMap = computed(() => {
  const map = {};
  if (product.value.sizes && Array.isArray(product.value.sizes)) {
    product.value.sizes.forEach((sz, idx) => {
      const key = sz.standard || sz.name;
      const priceVal = product.value.prices && product.value.prices[idx]
        ? (product.value.prices[idx][key] || product.value.prices[idx].price || product.value.price?.price || 0)
        : (product.value.price?.price || 0);
      map[key] = priceVal;
    });
  }
  return map;
});

watchEffect(() => {
  if (product.value && product.value.id) {
    if (!selectedSize.value && product.value.sizes?.length) {
      selectedSize.value = product.value.sizes[0].standard || product.value.sizes[0].name;
    }
    if (!selectedColor.value && product.value.colors?.length) {
      selectedColor.value = product.value.colors[0].color_code || product.value.colors[0].name;
    }
  }
});

const currentUnitPrice = computed(() => {
  if (product.value.is_size_visible && selectedSize.value && pricesMap.value[selectedSize.value]) {
    return parseFloat(pricesMap.value[selectedSize.value]);
  }
  return parseFloat(product.value.price?.price || product.value.base_price || 0);
});

const formattedPrice = computed(() => {
  return (currentUnitPrice.value * quantity.value).toFixed(2);
});

const mediaList = computed(() => {
  if (product.value.media && product.value.media.length > 0) {
    return product.value.media.map(m => m.url || m.media_url || m.file_url || '/brand/hero-chic.jpg');
  }
  if (product.value.image) {
    return [product.value.image];
  }
  return ['/brand/hero-chic.jpg'];
});

const incrementQuantity = () => {
  quantity.value++;
};

const decrementQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--;
  }
};

const addToCart = () => {
  const item = {
    id: product.value.id,
    name: product.value.name,
    image: mediaList.value[activeImageIndex.value] || mediaList.value[0],
    size: product.value.is_size_visible ? selectedSize.value : null,
    color: product.value.is_color_visible ? selectedColor.value : null,
    quantity: quantity.value,
    price: currentUnitPrice.value,
    is_size_visible: product.value.is_size_visible,
  };

  cartStore.addToCart(item);
};

const buyNow = () => {
  addToCart();
  router.push("/checkout");
};
</script>

<template>
  <div class="dn-container" style="padding-top: 2rem; padding-bottom: 4rem;">
    <!-- Loading Skeleton -->
    <div v-if="pending" style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">
      <div class="dn-skeleton" style="height: 520px; border-radius: var(--dn-radius-xl);"></div>
      <div style="display: flex; flex-direction: column; gap: 1rem;">
        <div class="dn-skeleton" style="height: 40px; width: 80%;"></div>
        <div class="dn-skeleton" style="height: 30px; width: 40%;"></div>
        <div class="dn-skeleton" style="height: 120px; width: 100%;"></div>
      </div>
    </div>

    <!-- Product Layout -->
    <div v-else-if="product && product.id" class="product-layout-grid">
      <!-- Left: Media Gallery -->
      <div>
        <!-- Main Large Image -->
        <div class="dn-card" style="aspect-ratio: 3/4; overflow: hidden; margin-bottom: 1rem; border-radius: var(--dn-radius-xl);">
          <img
            :src="mediaList[activeImageIndex]"
            :alt="product.name"
            style="width: 100%; height: 100%; object-fit: cover; object-position: top center; transition: transform 0.4s ease;"
          />
        </div>

        <!-- Thumbnails -->
        <div v-if="mediaList.length > 1" style="display: flex; gap: 0.75rem; overflow-x: auto; padding-bottom: 0.5rem;">
          <button
            v-for="(img, idx) in mediaList"
            :key="idx"
            type="button"
            class="dn-card"
            :style="{
              width: '72px',
              height: '96px',
              flexShrink: 0,
              padding: 0,
              cursor: 'pointer',
              border: activeImageIndex === idx ? '2px solid var(--dn-primary)' : '1px solid var(--dn-border)'
            }"
            @click="activeImageIndex = idx"
          >
            <img :src="img" :alt="`${product.name} ${idx}`" style="width: 100%; height: 100%; object-fit: cover;" />
          </button>
        </div>
      </div>

      <!-- Right: Product Details & Purchase Actions -->
      <div style="display: flex; flex-direction: column;">
        <!-- Category & Title -->
        <span v-if="product.categories?.[0]?.name" class="dn-product-category" style="font-size: 0.85rem; margin-bottom: 0.5rem;">
          {{ product.categories[0].name }}
        </span>

        <h1 style="font-size: 1.85rem; font-weight: 800; color: var(--dn-tertiary); line-height: 1.3; margin-bottom: 0.75rem;">
          {{ product.name }}
        </h1>

        <!-- Rating & Stock Badge -->
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.25rem;">
          <div style="display: flex; align-items: center; color: var(--dn-accent-gold); font-size: 0.95rem;">
            <Icon name="solar:star-bold" />
            <Icon name="solar:star-bold" />
            <Icon name="solar:star-bold" />
            <Icon name="solar:star-bold" />
            <Icon name="solar:star-bold" />
            <span style="color: var(--dn-text-muted); font-size: 0.8rem; margin-left: 0.35rem;">(5.0 • 48 reviews)</span>
          </div>

          <span class="dn-status-badge dn-status-delivered" style="font-size: 0.75rem;">
            <Icon name="solar:check-circle-bold" /> In Stock & Ready to Ship
          </span>
        </div>

        <!-- Price Section -->
        <div style="display: flex; align-items: baseline; gap: 0.75rem; margin-bottom: 1.5rem; background: var(--dn-surface-muted); padding: 1rem 1.25rem; border-radius: var(--dn-radius-md); border: 1px solid var(--dn-border-light);">
          <span style="font-size: 2rem; font-weight: 900; color: var(--dn-primary);">৳{{ formattedPrice }}</span>
          <span v-if="product.old_price" style="font-size: 1.15rem; color: var(--dn-text-subtle); text-decoration: line-through;">
            ৳{{ product.old_price }}
          </span>
          <span v-if="product.discount > 0" class="dn-product-badge dn-badge-sale" style="position: static;">
            Save {{ product.discount }}%
          </span>
        </div>

        <!-- Color Selection -->
        <div v-if="product.is_color_visible && product.colors?.length" style="margin-bottom: 1.5rem;">
          <label class="dn-label" style="margin-bottom: 0.5rem;">
            Color: <strong style="color: var(--dn-primary);">{{ selectedColor }}</strong>
          </label>
          <div style="display: flex; gap: 0.6rem;">
            <button
              v-for="(color, idx) in product.colors"
              :key="idx"
              type="button"
              class="dn-swatch"
              :class="{ active: selectedColor === (color.color_code || color.name) }"
              :style="{ backgroundColor: color.color_code || '#4D4DDB', width: '2rem', height: '2rem' }"
              :title="color.name || color.color_code"
              @click="selectedColor = (color.color_code || color.name)"
            ></button>
          </div>
        </div>

        <!-- Size Selection -->
        <div v-if="product.is_size_visible && product.sizes?.length" style="margin-bottom: 1.75rem;">
          <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
            <label class="dn-label">
              Size: <strong style="color: var(--dn-primary);">{{ selectedSize }}</strong>
            </label>
            <span style="font-size: 0.8rem; color: var(--dn-secondary); cursor: pointer; text-decoration: underline;">
              Size Guide
            </span>
          </div>
          <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
            <button
              v-for="(size, idx) in product.sizes"
              :key="idx"
              type="button"
              class="dn-size-chip"
              :class="{ active: selectedSize === (size.standard || size.name) }"
              style="padding: 0.5rem 1rem; font-size: 0.85rem;"
              @click="selectedSize = (size.standard || size.name)"
            >
              {{ size.standard || size.name }}
            </button>
          </div>
        </div>

        <!-- Quantity & Primary CTA Buttons -->
        <div style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
          <!-- Quantity Counter -->
          <div style="display: inline-flex; align-items: center; border: 1.5px solid var(--dn-border); border-radius: var(--dn-radius-lg); background: var(--dn-surface); padding: 3px;">
            <button
              type="button"
              @click="decrementQuantity"
              style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: var(--dn-radius-md); background: var(--dn-bg-subtle); color: var(--dn-text-main); cursor: pointer; transition: all 0.2s;"
              title="Decrease quantity"
            >
              <Icon name="solar:minus-bold" style="font-size: 1.15rem;" />
            </button>
            <span style="font-weight: 800; min-width: 2.75rem; text-align: center; font-size: 1.05rem; color: var(--dn-tertiary);">{{ quantity }}</span>
            <button
              type="button"
              @click="incrementQuantity"
              style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: var(--dn-radius-md); background: var(--dn-bg-subtle); color: var(--dn-text-main); cursor: pointer; transition: all 0.2s;"
              title="Increase quantity"
            >
              <Icon name="solar:add-bold" style="font-size: 1.15rem;" />
            </button>
          </div>

          <!-- Add to Cart Button (Primary #4D4DDB) -->
          <button
            type="button"
            class="dn-btn dn-btn-primary dn-btn-add-to-cart"
            style="flex: 1; min-width: 160px;"
            @click="addToCart"
          >
            <Icon name="solar:bag-3-bold" style="font-size: 1.25rem;" />
            <span>Add to Cart</span>
          </button>

          <!-- Buy Now Button (Instant Checkout) -->
          <button
            type="button"
            class="dn-btn dn-btn-dark"
            style="flex: 1; min-width: 160px;"
            @click="buyNow"
          >
            <Icon name="solar:bolt-bold" style="font-size: 1.25rem;" />
            <span>Buy Now</span>
          </button>
        </div>

        <!-- Delivery & Service Highlights -->
        <div class="dn-card" style="padding: 1.25rem; background: var(--dn-surface-muted); margin-bottom: 2rem;">
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.85rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
              <Icon name="solar:delivery-bold" style="color: var(--dn-primary); font-size: 1.25rem;" />
              <div>
                <strong>Dhaka 24-48h</strong>
                <span style="display: block; font-size: 0.75rem; color: var(--dn-text-muted);">Nationwide 48-72h</span>
              </div>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
              <Icon name="solar:hand-money-bold" style="color: var(--dn-accent-emerald); font-size: 1.25rem;" />
              <div>
                <strong>Cash on Delivery</strong>
                <span style="display: block; font-size: 0.75rem; color: var(--dn-text-muted);">Pay upon inspection</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Description & Fabric Details -->
        <div class="dn-card" style="padding: 1.5rem;">
          <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--dn-tertiary); margin-bottom: 0.75rem;">
            Product Details & Fabric
          </h3>
          <div
            v-if="product.description"
            style="color: var(--dn-text-muted); font-size: 0.925rem; line-height: 1.7;"
            v-html="product.description"
          ></div>
          <p v-else style="color: var(--dn-text-muted); font-size: 0.925rem; line-height: 1.7;">
            Experience unparalleled elegance with our curated collection of festive and daily wear garments. Crafted with breathable premium fabrics and tailored with meticulous care.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.product-layout-grid {
  display: grid;
  grid-template-columns: 1fr 1.15fr;
  gap: 3.5rem;
  align-items: start;
}

@media (max-width: 991px) {
  .product-layout-grid {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
}
</style>
