<script setup>
const store = useUtilityStore();
const validate = () => {
  let errors = [];
  if (!store.formData.title)
    errors.push({ path: "title", message: "Title field is required" });
  return errors;
};

let pageInfo = ref({
  apiEndpoint: "/slider",
  pageBackLink: "/admin/slider",
  pageTitle: "Update Slider",
});
</script>
<template>
  <div>
    <AdminHtmlForm :pageInfo="pageInfo" :validate="validate">
      <AdminFormElementSlider />
    </AdminHtmlForm>
  </div>
</template>
