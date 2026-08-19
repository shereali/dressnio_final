<script setup>
const columns = [
  {
    key: "name",
    label: "Name",
    sortable: true,
  },
  {
    key: "image",
    label: "Category image",
    sortable: true,
  },

  {
    key: "is_active",
    label: "Status",
    sortable: true,
  },
  {
    key: "actions",
  },
];

const propsItem = ref({
  apiEndpoint: "/category",
  columns: columns,
  pageTitle: "Category List",
  pageBackLink: "/admin/category/create",
});
</script>

<template>
  <!-- <div v-if="pending">Loading...</div> -->
  <div>
    <AdminDataTable :apiInfo="propsItem" />
  </div>
</template>

<style scoped></style>

