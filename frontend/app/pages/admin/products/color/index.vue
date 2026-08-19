<script setup>
const columns = [
  {
    key: "name",
    label: "Color Set Name",
    sortable: true,
  },
  {
    key: "slug",
    label: "Slug",
    sortable: true,
  },

  {
    key: "colors",
    label: "Color",
    sortable: true,
  },
  {
    key: "actions",
  },
];

const propsItem = ref({
  apiEndpoint: "/color",
  columns: columns,
  pageTitle: "Color List",
  pageBackLink: "/admin/products/color/create",
});
</script>

<template>
  <!-- <div v-if="pending">Loading...</div> -->
  <div>
    <AdminDataTable :apiInfo="propsItem" />
  </div>
</template>

<style scoped></style>

