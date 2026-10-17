<script setup>
// Sign in and sign up are one flow now.
await navigateTo({ path: "/login", query: useRoute().query }, { replace: true });
</script>

<template><div /></template>
