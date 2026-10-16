<script setup>
definePageMeta({
  layout: "admin",
  middleware: ["auth"],
});

const columns = [
  { key: "id", label: "ID", sortable: true },
  { key: "name", label: "Admin Name", sortable: true },
  { key: "email", label: "Email Address", sortable: true },
];

const propsItem = ref({
  apiEndpoint: "/user-list",
  columns: columns,
  pageTitle: "System Administrators",
  pageBackLink: "/admin/dashboard",
});
</script>

<template>
  <div>
    <AdminDataTable :apiInfo="propsItem" />
  </div>
</template>

<style scoped></style>
