<script setup>
const columns = [
  {
    key: "name",
    label: "Name",
    sortable: true,
  },
  {
    key: "image",
    label: "Story Media",
    sortable: true,
  },
  {
    key: "slug",
    label: "Slug",
    sortable: true,
  },

  {
    key: "actions",
  },
];

const propsItem = ref({
  apiEndpoint: "/story",
  columns: columns,
  pageTitle: "Story List",
  pageBackLink: "/admin/story/create",
});
</script>

<template>
  <!-- <div v-if="pending">Loading...</div> -->
  <div>
    <AdminDataTable :apiInfo="propsItem" />
  </div>
</template>

<style scoped></style>

