<script setup>
import VuePlayerVideo from "vue3-player-video";
import { useProductDetails } from "~/stores/product_details";
import { useCartStore } from "~/stores/cart";

const props = defineProps({
  product: {
    type: Object,
    default: () => ({}),
  },
  media: {
    type: String,
    default: "",
  },
  itemIndex: {
    type: Number,
    default: 0,
  },
});

const productStore = useProductDetails();
const cartStore = useCartStore();

const safeProduct = computed(() => props.product || {});

const sizeList = computed(() => {
  const sizes = safeProduct.value.sizes;
  if (Array.isArray(sizes)) return sizes;
  if (sizes && typeof sizes === "object") return Object.values(sizes);
  return [];
});

const colorList = computed(() => {
  const colors = safeProduct.value.colors;
  if (Array.isArray(colors)) return colors;
  if (colors && typeof colors === "object") return Object.values(colors);
  return [];
});

const selectedSize = ref("");
const selectedColor = ref("");
const quantity = ref(1);

// Initialize selected size and color safely when product updates
watch(
  safeProduct,
  (p) => {
    if (p) {
      if (sizeList.value.length > 0) {
        selectedSize.value = sizeList.value[0]?.standard || sizeList.value[0]?.name || "";
      }
      if (colorList.value.length > 0) {
        selectedColor.value = colorList.value[0]?.color_code || colorList.value[0]?.name || "";
      }
    }
  },
  { immediate: true, deep: true }
);

const currentUnitPrice = computed(() => {
  const p = safeProduct.value;
  if (!p) return 0;

  if (selectedSize.value && p.prices && typeof p.prices === "object") {
    // Check if prices has matching size key
    if (p.prices[selectedSize.value]) {
      return parseFloat(p.prices[selectedSize.value]);
    }
  }

  if (p.price?.price) return parseFloat(p.price.price);
  if (typeof p.price === "number") return parseFloat(p.price);
  if (p.base_price) return parseFloat(p.base_price);
  return 0;
});

const formattedPrice = computed(() => {
  return (currentUnitPrice.value * quantity.value).toFixed(2);
});

const openProductCard = (id) => {
  productStore.openProductCard(id);
};

const closeProductCard = () => {
  productStore.openProductCard();
};

const incrementQuantity = () => {
  quantity.value++;
};

const decrementQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--;
  }
};

const selectSize = (size) => {
  selectedSize.value = size;
};

const selectColor = (color) => {
  selectedColor.value = color;
};

const addToCart = () => {
  const p = safeProduct.value;
  if (!p || !p.id) return;

  const item = {
    id: p.id,
    name: p.name,
    image: p.image || (p.media && p.media[0]?.media_url) || "/brand/hero-chic.jpg",
    size: selectedSize.value || null,
    color: selectedColor.value || null,
    quantity: quantity.value,
    price: currentUnitPrice.value,
  };

  cartStore.addToCart(item);
  cartStore.openCart();
};
</script>

<template>
  <div class="lookbook-card bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 border border-white/10 group">
    <!-- Video Player or Preview Artwork -->
    <div class="relative aspect-[9/16] bg-black overflow-hidden">
      <VuePlayerVideo
        v-if="media && media.endsWith('.mp4')"
        theme="gradient"
        :controls="true"
        :src="media"
        class="w-full h-full object-cover"
      />
      <img
        v-else
        :src="media || safeProduct.image || '/brand/hero-evening.jpg'"
        :alt="safeProduct.name || 'Lookbook Reel'"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
      />

      <!-- Quick Action Overlay Tag -->
      <div class="absolute top-3 right-3 z-20">
        <button
          type="button"
          @click="openProductCard(safeProduct.id)"
          class="w-9 h-9 rounded-full bg-black/50 backdrop-blur-md text-white flex items-center justify-center hover:bg-brand transition-colors shadow-md"
          title="Quick View"
        >
          <Icon name="solar:eye-bold" class="w-4 h-4" />
        </button>
      </div>

      <!-- Bottom Floating Product Bar -->
      <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/90 via-black/60 to-transparent p-4 text-white z-20">
        <div class="flex items-center gap-3">
          <div class="w-12 h-14 rounded-lg overflow-hidden flex-shrink-0 border border-white/20">
            <img
              :src="safeProduct.image || (safeProduct.media && safeProduct.media[0]?.media_url) || media || '/brand/hero-chic.jpg'"
              :alt="safeProduct.name"
              class="w-full h-full object-cover"
            />
          </div>
          <div class="flex-1 min-w-0">
            <h4 class="text-xs font-bold truncate text-white leading-tight mb-1">
              {{ safeProduct.name || 'Signature Boutique Outfit' }}
            </h4>
            <p class="text-xs font-black text-brand-300">
              ৳{{ formattedPrice }}
            </p>
          </div>
          <button
            type="button"
            @click="addToCart"
            class="p-2.5 rounded-full bg-brand text-white hover:bg-brand-600 transition-transform active:scale-95 shadow-md flex-shrink-0"
            title="Add to Cart"
          >
            <Icon name="solar:bag-3-bold" class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- Quick Modal Card if active -->
    <div
      v-if="productStore.isCloseCard == safeProduct.id"
      class="absolute inset-0 z-50 bg-white p-6 flex flex-col justify-between"
    >
      <div class="flex justify-between items-center pb-3 border-b border-gray-100">
        <h3 class="font-black text-sm text-gray-900 truncate">{{ safeProduct.name }}</h3>
        <button type="button" @click="closeProductCard" class="text-gray-400 hover:text-gray-900">
          <Icon name="solar:close-circle-bold" class="w-6 h-6" />
        </button>
      </div>

      <div class="py-4 space-y-3">
        <!-- Sizes -->
        <div v-if="sizeList.length > 0">
          <label class="text-xs font-bold text-gray-600 block mb-1">Size:</label>
          <div class="flex gap-2 flex-wrap">
            <button
              v-for="(s, idx) in sizeList"
              :key="idx"
              type="button"
              class="px-2.5 py-1 text-xs font-bold rounded border"
              :class="selectedSize === (s.standard || s.name) ? 'bg-brand text-white border-brand' : 'bg-gray-50 text-gray-700 border-gray-200'"
              @click="selectSize(s.standard || s.name)"
            >
              {{ s.standard || s.name }}
            </button>
          </div>
        </div>

        <!-- Price & Quantity -->
        <div class="flex justify-between items-center pt-2">
          <span class="text-sm font-black text-brand">৳{{ formattedPrice }}</span>
          <div class="flex items-center gap-2 border rounded-lg p-1">
            <button type="button" @click="decrementQuantity" class="px-2 py-0.5 text-xs font-bold">-</button>
            <span class="text-xs font-black">{{ quantity }}</span>
            <button type="button" @click="incrementQuantity" class="px-2 py-0.5 text-xs font-bold">+</button>
          </div>
        </div>
      </div>

      <button
        type="button"
        @click="addToCart"
        class="w-full py-3 bg-brand text-white font-bold rounded-xl text-xs uppercase tracking-wider hover:bg-brand-600 transition-colors shadow-md flex items-center justify-center gap-2"
      >
        <Icon name="solar:bag-3-bold" class="w-4 h-4" />
        Add To Bag
      </button>
    </div>
  </div>
</template>

<style scoped>
.lookbook-card {
  position: relative;
}
</style>
