<script setup>
definePageMeta({
  middleware: ["auth"],
});

const columns = [
  { key: "id", label: "ID" },
  { key: "order_no", label: "Order #" },
  { key: "customer_name", label: "Customer" },
  { key: "total_amount", label: "Amount" },
  { key: "status", label: "Status" },
  { key: "actions", label: "Actions" }
];

const propsItem = ref({
  apiEndpoint: "/orders",
  columns: columns,
  pageTitle: "Order List",
  pageBackLink: "/admin/dashboard",
});
</script>

<template>
  <div>
    <AdminDataTable :apiInfo="propsItem" />
  </div>
</template>

