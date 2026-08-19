<script setup>
definePageMeta({
  middleware: ["auth"],
});

onMounted(() => {
  navigateTo("/admin/dashboard");
});
</script>

<template>
  <div class="flex items-center justify-center h-full">
    <Icon name="svg-spinners:ring-resize" class="text-brand text-4xl" />
  </div>
</template>

