<script setup>
definePageMeta({
  layout: "admin",
  middleware: ["auth"],
});
</script>

<template>
  <div>
    <AdminFormElementProduct />
  </div>
</template>
