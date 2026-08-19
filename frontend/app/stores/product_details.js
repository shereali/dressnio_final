import { createPinia, defineStore } from "pinia";

export const useProductDetails = defineStore("product_details", {
  state: () => ({
    isCloseCard: null,
    selectedSize: "S",
    quantity: 1,
    product: {},
  }),
  getters: {},

  actions: {
    closeProductCard(index) {
      this.isCloseCard = null;
    },

    openProductCard(index) {
      this.isCloseCard = index;
    },

    incrementQuantity() {
      this.quantity += 1;
    },
    decrementQuantity() {
      if (this.quantity > 1) {
        this.quantity -= 1;
      }
    },

    updatePrice() {
      // Update the price when the selected size changes
      price = this.product.prices[this.selectedSize];
    },
    addToCart() {
      console.log(
        "Product added to cart:",
        this.product,
        "Size:",
        this.selectedSize,
        "Color:",
        this.selectedColor,
        "Quantity:",
        this.quantity,
        "Price:",
        this.price
      );
      // Implement the logic to add the product to the cart
    },
  },
});
