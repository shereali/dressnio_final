<script>
import { ref } from "vue";

export default {
  setup() {
    // State
    const selectedCategory = ref(null);

    // Dummy data for categories
    const categories = [
      { id: 1, name: "Category A", description: "Description for Category A" },
      { id: 2, name: "Category B", description: "Description for Category B" },
      // Add more categories as needed
    ];

    // Methods
    const openModal = (category) => {
      selectedCategory.value = category;
    };

    const closeModal = () => {
      selectedCategory.value = null;
    };

    const performAction = () => {
      if (selectedCategory.value) {
        // Add your action logic here
        console.log(`Performing action for ${selectedCategory.value.name}`);
        // For example, you can close the modal after performing the action
        closeModal();
      }
    };

    // Expose data and methods to the template
    return {
      selectedCategory,
      categories,
      openModal,
      closeModal,
      performAction,
    };
  },
};
</script>

<template>
  <div>
    <!-- Category Buttons -->
    <button
      v-for="category in categories"
      :key="category.id"
      class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded mr-2 mb-2"
      @click="openModal(category)"
    >
      {{ category.name }}
    </button>

    <!-- Modal Container -->
    <div
      v-show="selectedCategory !== null"
      class="fixed inset-0 z-50 flex items-center justify-center"
      style="background-color: rgba(0, 0, 0, 0.5)"
    >
      <!-- Modal Content -->
      <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <!-- Close Button -->
        <button
          class="absolute top-2 right-2 text-gray-600 hover:text-gray-700"
          @click="closeModal"
        >
          <svg
            class="h-6 w-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12"
            ></path>
          </svg>
        </button>

        <!-- Modal Content Goes Here -->
        <div>
          <h2 class="text-2xl font-bold mb-4">
            {{ selectedCategory ? selectedCategory.name : "" }} Modal
          </h2>
          <p class="text-gray-700 mb-4">
            {{ selectedCategory ? selectedCategory.description : "" }}
          </p>
          <!-- Add your content here -->

          <!-- Action Buttons -->
          <div class="flex justify-end">
            <button
              class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded mr-2"
              @click="performAction"
            >
              Action
            </button>
            <button
              class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-2 px-4 rounded"
              @click="closeModal"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
/* Add your custom styles here */
</style>
