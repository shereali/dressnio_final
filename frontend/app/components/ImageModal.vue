<template>
  <div
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-75"
    @click="closeModal"
  >
    <div class="max-w-3xl w-full p-4 py-20">
      <img :src="currentImage.url" alt="Modal Image" class="w-full h-auto" />

      <div class="flex items-center justify-between mt-4">
        <button
          @click="prevImage"
          :disabled="currentIndex === 0"
          class="text-white"
        >
          &lt; Prev
        </button>
        <div class="relative w-32 h-4 bg-gray-400 rounded-full">
          <div
            :style="{ width: progress + '%' }"
            class="h-full bg-blue-500 rounded-full"
          ></div>
        </div>
        <button
          @click="nextImage"
          :disabled="currentIndex === images.length - 1"
          class="text-white"
        >
          Next &gt;
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, watch, onBeforeUnmount } from "vue";

export default {
  props: {
    images: Array,
    currentIndex: Number,
  },
  setup(props, { emit }) {
    const currentIndex = ref(props.currentIndex);
    const progress = ref(0);

    const currentImage = computed(() => props.images[currentIndex.value]);

    const nextImage = () => {
      currentIndex.value++;
      updateProgress();
    };

    const prevImage = () => {
      currentIndex.value--;
      updateProgress();
    };

    const updateProgress = () => {
      progress.value = ((currentIndex.value + 1) / props.images.length) * 100;
    };

    watch(
      () => props.currentIndex,
      (newIndex) => {
        currentIndex.value = newIndex;
        updateProgress();
      }
    );

    const closeModal = () => {
      emit("close");
    };

    onBeforeUnmount(() => {
      currentIndex.value = 0;
      progress.value = 0;
    });

    return {
      currentImage,
      progress,
      nextImage,
      prevImage,
      closeModal,
    };
  },
};
</script>
