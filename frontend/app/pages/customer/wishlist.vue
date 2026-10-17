<template>
  <CustomerLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-stone-100">
        <div>
          <h1 class="text-2xl font-extrabold text-stone-900">My Wishlist</h1>
          <p class="text-xs sm:text-sm text-stone-500 mt-1">
            Items you have saved for later. You can view or add them to your cart anytime.
          </p>
        </div>

        <NuxtLink
          to="/shop"
          class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs sm:text-sm font-semibold transition-colors self-start sm:self-auto"
        >
          <Icon name="solar:bag-3-bold" class="w-4 h-4 text-brand-500" />
          <span>Continue Shopping</span>
        </NuxtLink>
      </div>

      <!-- Loading State -->
      <div v-if="pending" class="py-16 text-center">
        <Icon name="svg-spinners:180-ring-with-bg" class="w-8 h-8 text-brand-500 mx-auto mb-2" />
        <p class="text-sm text-stone-500">Loading your saved items...</p>
      </div>

      <!-- Empty State -->
      <div
        v-else-if="wishlist.length === 0"
        class="text-center py-16 px-4 rounded-2xl bg-stone-50 border border-stone-200/70"
      >
        <div class="w-16 h-16 rounded-2xl bg-brand-100/60 text-brand-500 flex items-center justify-center mx-auto mb-3">
          <Icon name="solar:heart-bold" class="w-8 h-8" />
        </div>
        <h3 class="font-bold text-stone-800 text-lg">Your wishlist is empty</h3>
        <p class="text-xs text-stone-500 mt-1 max-w-sm mx-auto">
          Explore our collection of boutique dresses and tap the heart icon to save your favorites.
        </p>
        <NuxtLink
          to="/shop"
          class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 rounded-xl bg-stone-900 hover:bg-stone-800 text-white text-xs font-medium transition-colors shadow-sm"
        >
          <span>Explore Catalog</span>
          <Icon name="solar:arrow-right-linear" class="w-4 h-4" />
        </NuxtLink>
      </div>

      <!-- Wishlist Grid -->
      <div
        v-else
        class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4"
      >
        <CustomerWishlistItem
          v-for="item in wishlist"
          :key="item.id"
          :item="item"
          @remove="removeFromWishlist"
        />
      </div>
    </div>
  </CustomerLayout>
</template>

<script setup>
import { computed } from "vue";
import { toast } from "vue3-toastify";

definePageMeta({
  middleware: "auth",
});

const { data: wishlistData, pending, refresh } = await useApiFetch("/client/wishlist", {
  server: false,
  lazy: true,
});

const wishlist = computed(() => {
  const d = wishlistData.value?.data;
  return Array.isArray(d) ? d : [];
});

const removeFromWishlist = async (productId) => {
  try {
    const { error } = await useApiFetch(`/client/wishlist/${productId}`, {
      method: "DELETE",
    });

    if (error.value) {
      toast.error("Failed to remove item from wishlist");
    } else {
      toast.success("Removed from wishlist");
      refresh();
    }
  } catch (err) {
    toast.error("Error removing item");
  }
};
</script>
