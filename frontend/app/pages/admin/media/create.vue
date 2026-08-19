<script setup>
definePageMeta({
  middleware: ["auth"],
});
let apiEndpoint = "/media";
const crudStore = useCrudStore();
const validate = () => {
  let errors = [];
  // if (!crudStore.formData.name)
  //   errors.push({ path: "name", message: "Name field is required" });
  return errors;
};
</script>
<template>
  <div>
    <AdminHtmlForm :apiEndpoint="apiEndpoint" :validate="validate">
      <AdminFormElementMedia />
    </AdminHtmlForm>
  </div>
</template>

