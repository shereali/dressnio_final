<script setup>
definePageMeta({
  middleware: ["auth"],
});

// VALIDATION: FUNCTION LOCATED IN utils directory

const store = useUtilityStore();
const validate = () => {
  let errors = [];
  if (!store.formData.title)
    errors.push({ path: "title", message: "Title field is required" });
  return errors;
};

let pageInfo = ref({
  apiEndpoint: "/post",
  pageBackLink: "/admin/post",
  pageTitle: "Create Post",
});
</script>
<template>
  <div>
    <AdminHtmlForm :pageInfo="pageInfo" :validate="validate">
      <AdminFormElementPost />
    </AdminHtmlForm>
  </div>
</template>

