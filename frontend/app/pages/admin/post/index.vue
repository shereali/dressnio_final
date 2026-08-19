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
    label: "Post Image",
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
  apiEndpoint: "/post",
  columns: columns,
  pageTitle: "Post",
  pageBackLink: "/admin/post/create",
});
</script>

<template>
  <!-- <div v-if="pending">Loading...</div> -->
  <div>
    <AdminDataTable :apiInfo="propsItem" />
  </div>
</template>

<style scoped></style>

