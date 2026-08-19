<script setup>
const store = useUtilityStore();
const validate = () => {
  let errors = [];
  if (!store.formData.name)
    errors.push({ path: "name", message: "Name field is required" });
  return errors;
};

let pageInfo = ref({
  apiEndpoint: "/testimonial",
  pageBackLink: "/admin/testimonial",
  pageTitle: "Update Testimonial",
});
</script>
<template>
  <div>
    <AdminHtmlForm :pageInfo="pageInfo" :validate="validate">
      <AdminFormElementTestimonial />
    </AdminHtmlForm>
  </div>
</template>
