<script setup>
const columns = [
  {
    key: "name",
    label: "Name",
    sortable: true,
  },
  {
    key: "slug",
    label: "Slug",
    sortable: true,
  },

  {
    key: "is_active",
    label: "Active",
    sortable: true,
  },
  {
    key: "actions",
  },
];

const propsItem = ref({
  apiEndpoint: "/products",
  columns: columns,
  pageTitle: "Products List",
  pageBackLink: "/admin/products/create",
});
</script>

<template>
  <div>
    <AdminDataTable :apiInfo="propsItem" />
  </div>
</template>

<style scoped></style>

