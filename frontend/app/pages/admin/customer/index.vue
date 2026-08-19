<script setup>
definePageMeta({
  middleware: ["auth"],
});

const columns = [
  { key: "name", label: "Name" },
  { key: "email", label: "Email" },
  { key: "mobile_no", label: "Phone" },
  { key: "is_active", label: "Status" },
  { key: "actions", label: "Actions" }
];

const propsItem = ref({
  apiEndpoint: "/customer",
  columns: columns,
  pageTitle: "Customer List",
  pageBackLink: "/admin/dashboard",
});
</script>

<template>
  <div>
    <AdminDataTable :apiInfo="propsItem" />
  </div>
</template>

