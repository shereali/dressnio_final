import { defineStore } from "pinia";

// Wishlist kept on the device and mirrored to the customer's account when signed in.
export const useWishlistStore = defineStore("wishlist", {
  state: () => ({ ids: [] }),

  getters: {
    count: (state) => state.ids.length,
    has: (state) => (id) => state.ids.includes(id),
  },

  actions: {
    async toggle(id) {
      const adding = !this.ids.includes(id);
      this.ids = adding ? [...this.ids, id] : this.ids.filter((x) => x !== id);

      const token = useTokenStore();
      if (!token.isAuthenticated) return adding;

      const config = useRuntimeConfig();
      try {
        await $fetch(
          adding
            ? `${config.public.apiBase}/client/wishlist`
            : `${config.public.apiBase}/client/wishlist/${id}`,
          {
            method: adding ? "POST" : "DELETE",
            body: adding ? { product_id: id } : undefined,
            headers: { Authorization: `Bearer ${token.getToken}`, Accept: "application/json" },
          }
        );
      } catch (e) {
        // the local list stays correct even if the account sync fails
      }
      return adding;
    },
  },

  persist: true,
});
