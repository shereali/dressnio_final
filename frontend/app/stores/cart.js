import { defineStore } from "pinia";

export const useCartStore = defineStore("cart", {
  state: () => ({
    isOpen: false,
    items: [],
  }),

  getters: {
    totalAmount() {
      return this.items.reduce(
        (total, item) => total + item.price * item.quantity,
        0
      );
    },
  },

  actions: {
    toggleCart() {
      this.isOpen = !this.isOpen;
    },

    updateQuantity(product) {
      if (product.quantity < 1) {
        product.quantity = 1;
      }
    },

    incrementQuantity(product) {
      product.quantity++;
    },

    decrementQuantity(product) {
      if (product.quantity > 1) {
        product.quantity--;
      }
    },

    removeProduct(product) {
      const index = this.items.findIndex(
        (p) => p.id === product.id && p.size === product.size && p.color === product.color
      );
      if (index !== -1) {
        this.items.splice(index, 1);
      }
      if (this.items.length < 1) {
        // this.isOpen = false;
      }
    },

    closeCart() {
      this.isOpen = false;
      console.log("Closing Cart");
    },

    openCart() {
      this.isOpen = true;
    },

    addToCart(product) {
      // A size is only required when the product sells in sizes
      if (product.is_size_visible == true && !product.size) {
        return false;
      }

      let existingProductIndex = "";

      if (product.is_size_visible) {
        existingProductIndex = this.items.findIndex(
          (item) =>
            item.id === product.id &&
            item.size === product.size &&
            item.color === product.color
        );
      } else {
        existingProductIndex = this.items.findIndex(
          (item) => item.id === product.id && item.color === product.color
        );
      }

      if (existingProductIndex !== -1) {
        // If the product with the same size and color exists, update its quantity
        this.items[existingProductIndex].quantity += product.quantity;
      } else {
        // If not, add a new product to the cart
        this.items.push({
          ...product,
          quantity: product.quantity,
        });
      }

      this.isOpen = true;
      return true;
    },

    checkout() {
      // Implement checkout logic (e.g., send order to server)
      console.log("Checkout:", this.items);
      // Reset the cart after checkout
      this.items = [];
      this.isOpen = false; // Close the cart after checkout
    },
  },
  persist: { paths: ["items"] },
});
