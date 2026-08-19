<script setup>
const receiveSelectedFile = (receivedFiles) => {
  console.log("receivedFiles", receivedFiles);
};

// const columns = [
//   {
//     key: "name",
//     label: "Name",
//     sortable: true,
//   },
//   {
//     key: "slug",
//     label: "Slug",
//     sortable: true,
//   },
//   {
//     key: "media_url",
//     label: "Image/Video",
//     sortable: true,
//   },

//   {
//     key: "media_type",
//     label: "Media Type",
//     sortable: true,
//   },
//   {
//     key: "actions",
//   },
// ];

// const propsItem = ref({
//   apiEndpoint: "/media",
//   apiMethod: "POST",
//   columns: columns,
//   pageTitle: "Media List",
//   pageBackLink: "/admin/media/create",
// });
//

let isMultipleFile = ref(true);
</script>

<template>
  <div>
    <ImageUploader
      @send-selected-file="receiveSelectedFile"
      :isMultiple="isMultipleFile"
    />

    <!-- <div> <AdminDataTable :apiInfo="propsItem" /> </div> -->
  </div>
</template>

