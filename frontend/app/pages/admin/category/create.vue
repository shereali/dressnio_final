<script setup>
definePageMeta({
  middleware: ["auth"],
});

// VALIDATION: FUNCTION LOCATED IN utils directory

const store = useUtilityStore();
const validate = () => {
  let errors = [];
  if (!store.formData.name)
    errors.push({ path: "name", message: "Name field is required" });
  return errors;
};

let pageInfo = ref({
  apiEndpoint: "/category",
  pageBackLink: "/admin/category",
  pageTitle: "Create Category",
});
</script>
<template>
  <div>
    <AdminHtmlForm :pageInfo="pageInfo" :validate="validate">
      <AdminFormElementCategory />
    </AdminHtmlForm>
  </div>
</template>

