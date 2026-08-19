<script setup>
const store = useUtilityStore();
const validate = () => {
  let errors = [];
  // if (!store.formData.name)
  //   errors.push({ path: "name", message: "Title field is required" });
  return errors;
};

let pageInfo = ref({
  apiEndpoint: "/watch-n-buy",
  pageBackLink: "/admin/watch-n-buy",
  pageTitle: "Update Watch & Buy",
});
</script>
<template>
  <div>
    <AdminHtmlForm :pageInfo="pageInfo" :validate="validate">
      <AdminFormElementWatchAndBuy />
    </AdminHtmlForm>
  </div>
</template>
