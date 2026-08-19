<script setup>
const columns = [
  {
    key: "title",
    label: "Title",
    sortable: true,
  },
  {
    key: "description",
    label: "Description",
    sortable: true,
  },
  {
    key: "image",
    label: "Slider Image",
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
  apiEndpoint: "/slider",
  columns: columns,
  pageTitle: "Sliders",
  pageBackLink: "/admin/slider/create",
});
</script>

<template>
  <!-- <div v-if="pending">Loading...</div> -->
  <div>
    <AdminDataTable :apiInfo="propsItem" />
  </div>
</template>

<style scoped></style>

