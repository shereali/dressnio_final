<script setup>
const columns = [
  {
    key: "name",
    label: "Name",
    sortable: true,
  },
  {
    key: "product",
    label: "product",
    sortable: true,
  },
  {
    key: "video",
    label: "Video",
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
  apiEndpoint: "/watch-n-buy",
  columns: columns,
  pageTitle: "Watch & Buy",
  pageBackLink: "/admin/watch-n-buy/create",
});
</script>

<template>
  <!-- <div v-if="pending">Loading...</div> -->
  <div>
    <AdminDataTable :apiInfo="propsItem" />
  </div>
</template>

<style scoped></style>

