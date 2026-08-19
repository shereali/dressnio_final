<!-- components/Dialog.vue -->

<template>
  <div
    v-if="visible"
    class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center"
  >
    <div class="bg-white p-8 rounded shadow-md w-1/2">
      <p class="text-lg">{{ message }}</p>
      <div class="mt-4 flex justify-end space-x-4">
        <button
          @click="confirmAction"
          class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
        >
          Confirm
        </button>
        <button
          @click="cancelAction"
          class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
        >
          Cancel
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const { visible, message } = defineProps(["visible", "message"]);
const { emit } = defineEmits(["onConfirm", "onCancel"]);

const confirmAction = () => {
  emit("onConfirm");
};

const cancelAction = () => {
  emit("onCancel");
};
</script>
