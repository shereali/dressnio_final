<script setup>
definePageMeta({
  middleware: ["auth"],
});

const store = useUtilityStore();
const validate = () => {
  let errors = [];
  if (!store.formData.standard)
    errors.push({ path: "standard", message: "Standard size field is required" });
  return errors;
};

let pageInfo = ref({
  apiEndpoint: "/size",
  pageBackLink: "/admin/products/size",
  pageTitle: "Create Size",
});
</script>

<template>
  <div>
    <AdminHtmlForm :pageInfo="pageInfo" :validate="validate">
      <AdminFormElementSize />
    </AdminHtmlForm>
  </div>
</template>

