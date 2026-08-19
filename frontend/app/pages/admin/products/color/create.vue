<script setup>
definePageMeta({
  middleware: ["auth"],
});

const store = useUtilityStore();
const validate = () => {
  let errors = [];
  if (!store.formData.name)
    errors.push({ path: "name", message: "Name field is required" });
  return errors;
};

let pageInfo = ref({
  apiEndpoint: "/color",
  pageBackLink: "/admin/products/color",
  pageTitle: "Create Color",
});
</script>

<template>
  <div>
    <AdminHtmlForm :pageInfo="pageInfo" :validate="validate">
      <AdminFormElementColor />
    </AdminHtmlForm>
  </div>
</template>

