<script setup>
const columns = [
  {
    key: "name",
    label: "Name",
    sortable: true,
  },
  {
    key: "description",
    label: "Description",
    sortable: true,
  },
  {
    key: "image",
    label: "Image",
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
  apiEndpoint: "/testimonial",
  columns: columns,
  pageTitle: "Testimonial/Community",
  pageBackLink: "/admin/testimonial/create",
});
</script>

<template>
  <!-- <div v-if="pending">Loading...</div> -->
  <div>
    <AdminDataTable :apiInfo="propsItem" />
  </div>
</template>

<style scoped></style>

