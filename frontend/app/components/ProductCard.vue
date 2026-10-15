<script setup>
import { useCartStore } from "~/stores/cart";
import { ref, computed, watchEffect } from "vue";

const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
});

const cartStore = useCartStore();

const selectedSize = ref("");
const selectedColor = ref("");
const quantity = ref(1);

watchEffect(() => {
  if (props.product) {
    if (!selectedSize.value && props.product.sizes?.length) {
      selectedSize.value = props.product.sizes[0].standard || props.product.sizes[0].name || "";
    }
    if (!selectedColor.value && props.product.colors?.length) {
      selectedColor.value = props.product.colors[0].color_code || props.product.colors[0].name || "";
    }
  }
});

const currentUnitPrice = computed(() => {
  const p = props.product;
  if (!p) return 0;

  if (p.is_size_visible && selectedSize.value && p.prices?.length) {
    const sizeIdx = p.sizes?.findIndex(
      (sz) => (sz.standard || sz.name) === selectedSize.value
    );
    if (sizeIdx !== -1 && p.prices[sizeIdx]) {
      const prObj = p.prices[sizeIdx];
      return parseFloat(prObj.price || prObj[selectedSize.value] || prObj || 0);
    }
  }

  if (p.price?.price) return parseFloat(p.price.price);
  if (typeof p.price === 'number') return parseFloat(p.price);
  if (p.base_price) return parseFloat(p.base_price);
  return 0;
});

const formattedPrice = computed(() => {
  return (currentUnitPrice.value * quantity.value).toFixed(2);
});

const selectSize = (size) => {
  selectedSize.value = size;
};

const selectColor = (color) => {
  selectedColor.value = color;
};

const incrementQuantity = () => {
  quantity.value++;
};

const decrementQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--;
  }
};

const addToCart = () => {
  const p = props.product;
  if (!p) return;

  const item = {
    id: p.id,
    name: p.name,
    image: p.image || p.media?.[0]?.url || "/brand/hero-chic.jpg",
    size: p.is_size_visible ? selectedSize.value : null,
    color: p.is_color_visible ? selectedColor.value : null,
    quantity: quantity.value,
    price: currentUnitPrice.value,
    is_size_visible: p.is_size_visible,
  };

  cartStore.addToCart(item);
};
</script>

<template>
  <div v-if="product" class="dn-product-card group">
    <!-- Badges -->
    <span v-if="product.best_seller" class="dn-product-badge dn-badge-bestseller">Best Seller</span>
    <span v-else-if="product.new_arrival" class="dn-product-badge dn-badge-new">New</span>
    <span v-else-if="product.discount > 0" class="dn-product-badge dn-badge-sale">-{{ product.discount }}%</span>

    <!-- Product Image -->
    <NuxtLink :to="`/product/${product.slug || product.id}`" class="dn-product-media">
      <img
        :src="product.image || product.media?.[0]?.media_url || product.media?.[0]?.url || '/brand/hero-chic.jpg'"
        :alt="product.name"
        loading="lazy"
        class="dn-product-img"
      />
    </NuxtLink>

    <!-- Product Body -->
    <div class="dn-product-body">
      <span v-if="product.categories?.[0]?.name" class="dn-product-category">
        {{ product.categories[0].name }}
      </span>

      <NuxtLink :to="`/product/${product.slug || product.id}`" class="dn-product-title">
        {{ product.name }}
      </NuxtLink>

      <!-- Color Swatches Preview -->
      <div v-if="product.is_color_visible && product.colors?.length" class="dn-swatch-list">
        <span
          v-for="(color, idx) in product.colors"
          :key="idx"
          class="dn-swatch"
          :class="{ active: selectedColor === (color.color_code || color.name) }"
          :style="{ backgroundColor: color.color_code || '#4D4DDB' }"
          :title="color.name || color.color_code"
          @click.stop="selectColor(color.color_code || color.name)"
        ></span>
      </div>

      <!-- Size Chips Preview -->
      <div v-if="product.is_size_visible && product.sizes?.length" class="dn-flex dn-gap-1" style="margin-bottom: 0.5rem; flex-wrap: wrap;">
        <span
          v-for="(size, idx) in product.sizes"
          :key="idx"
          class="dn-size-chip"
          :class="{ active: selectedSize === (size.standard || size.name) }"
          @click.stop="selectSize(size.standard || size.name)"
        >
          {{ size.standard || size.name }}
        </span>
      </div>

      <!-- Price Row -->
      <div class="dn-product-price-row">
        <span class="dn-product-price">৳{{ formattedPrice }}</span>
        <span v-if="product.old_price" class="dn-product-old-price">৳{{ product.old_price }}</span>
      </div>

      <!-- Add to Cart Action -->
      <button
        type="button"
        class="dn-btn dn-btn-primary dn-btn-add-to-cart dn-btn-full dn-btn-sm"
        @click.stop="addToCart"
      >
        <Icon name="solar:bag-3-bold" style="font-size: 1.05rem;" />
        <span>Add to Cart</span>
      </button>
    </div>
  </div>
</template>

<style scoped>
.dn-product-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
</style>
