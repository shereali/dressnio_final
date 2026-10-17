<template>
  <div class="group bg-white border border-stone-200/90 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between">
    <!-- Image & Tag -->
    <div class="relative bg-stone-100 aspect-[3/4] overflow-hidden">
      <img
        :src="productImage"
        :alt="productName"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
        loading="lazy"
        @error="handleImgError"
      />
      <button
        @click="removeFromWishlist"
        class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/90 backdrop-blur-sm text-stone-500 hover:text-brand-600 hover:bg-white flex items-center justify-center shadow-sm transition-all"
        title="Remove from Wishlist"
      >
        <Icon name="solar:trash-bin-trash-bold" class="w-4 h-4" />
      </button>
    </div>

    <!-- Product Info -->
    <div class="p-4 flex flex-col flex-1 justify-between">
      <div>
        <NuxtLink
          :to="productSlug ? `/product/${productSlug}` : '#'"
          class="font-semibold text-stone-900 text-sm hover:text-brand-600 line-clamp-2 transition-colors"
        >
          {{ productName }}
        </NuxtLink>

        <div class="flex items-baseline gap-2 mt-2">
          <span class="text-base font-extrabold text-stone-900">{{ formatBDT(productPrice) }}</span>
          <span v-if="productRegularPrice > productPrice" class="text-xs text-stone-400 line-through">
            {{ formatBDT(productRegularPrice) }}
          </span>
        </div>
      </div>

      <!-- Action -->
      <div class="mt-4 pt-3 border-t border-stone-100 flex gap-2">
        <NuxtLink
          :to="productSlug ? `/product/${productSlug}` : '/shop'"
          class="w-full py-2 px-3 rounded-xl bg-stone-900 hover:bg-stone-800 text-white text-xs font-medium text-center transition-colors inline-flex items-center justify-center gap-1.5"
        >
          <Icon name="solar:bag-3-bold" class="w-3.5 h-3.5" />
          <span>View Product</span>
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  item: { type: Object, required: true },
});

const emit = defineEmits(["remove"]);

const product = computed(() => props.item?.product || props.item || {});

const productName = computed(() => product.value?.name || "Fashion Garment");
const productSlug = computed(() => product.value?.slug || "");

const productImage = computed(() => {
  if (product.value?.media && product.value.media.length > 0) {
    return product.value.media[0].file_url || product.value.media[0].url;
  }
  return product.value?.image || "/img/p3.jpg";
});

const productPrice = computed(() => {
  const p = product.value?.price;
  if (p) {
    return Number(p.discount_price || p.selling_price || p.regular_price || 0);
  }
  return Number(product.value?.price || 0);
});

const productRegularPrice = computed(() => {
  const p = product.value?.price;
  if (p) {
    return Number(p.regular_price || p.selling_price || 0);
  }
  return 0;
});

const handleImgError = (event) => {
  event.target.src = "/img/p3.jpg";
};

const removeFromWishlist = () => {
  emit("remove", product.value?.id || props.item?.id);
};
</script>
