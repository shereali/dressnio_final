<script setup>
const columns = [
  {
    key: "dressType",
    label: "Dress Type",
    sortable: true,
  },
  {
    key: "standard",
    label: "Standard",
    sortable: true,
  },
  {
    key: "bust",
    label: "Bust",
    sortable: true,
  },
  {
    key: "lengths",
    label: "Lengths",
    sortable: true,
  },

  {
    key: "waist",
    label: "Waist",
    sortable: true,
  },
  {
    key: "hip",
    label: "Hip",
    sortable: true,
  },
  {
    key: "thighs",
    label: "Thighs",
    sortable: true,
  },
  {
    key: "actions",
  },
];

const propsItem = ref({
  apiEndpoint: "/size",
  columns: columns,
  pageTitle: "Size List",
  pageBackLink: "/admin/products/size/create",
});
</script>

<template>
  <!-- <div v-if="pending">Loading...</div> -->
  <div>
    <AdminDataTable :apiInfo="propsItem" />
  </div>
</template>

<style scoped></style>

