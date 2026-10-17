<script setup>
definePageMeta({ layout: "auth", middleware: ["guest"] });
useSeoMeta({ title: "Sign in | Dressnio", robots: "noindex" });
</script>

<template>
  <AuthFlow />
</template>
