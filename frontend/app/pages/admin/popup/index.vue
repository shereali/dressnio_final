<script setup>
definePageMeta({
  middleware: ["auth"],
});

const columns = [
  { key: "name", label: "Title" },
  { key: "image", label: "Image" },
  { key: "link", label: "Link" },
  { key: "is_active", label: "Active" },
  { key: "actions", label: "Actions" }
];

const propsItem = ref({
  apiEndpoint: "/popup",
  columns: columns,
  pageTitle: "Popup Advertisements",
  pageBackLink: "/admin/dashboard",
});
</script>

<template>
  <div>
    <AdminDataTable :apiInfo="propsItem" />
  </div>
</template>

