<template>
  <div class="grid grid-cols-3 gap-4 p-4">
    <div
      v-for="(image, index) in images"
      :key="index"
      @click="openModal(index)"
    >
      <img
        :src="image.url"
        alt="Gallery Image"
        class="w-full h-40 object-cover cursor-pointer"
      />
    </div>
    <ImageModal
      :images="images"
      :currentIndex="currentIndex"
      v-if="modalOpen"
      @close="closeModal"
    />
  </div>
</template>

<script>
import { ref } from "vue";

export default {
  data() {
    return {
      images: [
        { url: "/images/product/p-1.webp" },
        { url: "/images/product/p-2.webp" },
        { url: "/images/product/p-3.webp" },
        // Add more images as needed
      ],
      modalOpen: false,
      currentIndex: 0,
    };
  },
  methods: {
    openModal(index) {
      this.currentIndex = index;
      this.modalOpen = true;
    },
    closeModal() {
      this.modalOpen = false;
    },
  },
};
</script>
